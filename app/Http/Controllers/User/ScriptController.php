<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ScriptItem;
use App\Models\ScriptTopic;
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

        $search = trim((string) $request->query('search'));

        $type = $request->query('type');
        $type = in_array($type, ['video', 'document'], true) ? $type : null;

        $topics = ScriptTopic::published()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->whereHas('items', fn ($query) => $query->published()->language($language)->when($type, fn ($q) => $q->where('type', $type)))
            ->with(['items' => fn ($query) => $query->published()->language($language)->when($type, fn ($q) => $q->where('type', $type))->ordered()])
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return view('user.scripts.index', [
            'topics' => $topics,
            'language' => $language,
            'availableLanguages' => $availableLanguages,
            'search' => $search,
            'type' => $type,
        ]);
    }
}
