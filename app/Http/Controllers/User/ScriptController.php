<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ContentView;
use App\Models\ScriptItem;
use App\Models\ScriptTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScriptController extends Controller
{
    public function index(Request $request): View
    {
        $availableLanguages = collect(['english', 'hindi', 'gujarati'])
            ->filter(fn ($lang) => ScriptItem::published()->language($lang)->whereHas('topic', fn ($query) => $query->published())->exists())
            ->values()
            ->all();

        $language = $request->query('language');

        if (! $language || ! in_array($language, $availableLanguages, true)) {
            $language = $availableLanguages[0] ?? 'english';
        }

        $topics = ScriptTopic::published()
            ->whereHas('items', fn ($query) => $query->published()->language($language))
            ->with(['items' => fn ($query) => $query->published()->language($language)->ordered()])
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return view('user.scripts.index', [
            'topics' => $topics,
            'language' => $language,
            'availableLanguages' => $availableLanguages,
        ]);
    }

    public function open(Request $request, ScriptItem $scriptItem): RedirectResponse
    {
        abort_unless($scriptItem->is_published, 404);

        ContentView::record($request->user(), $scriptItem);

        return redirect()->away($scriptItem->fileUrl());
    }
}
