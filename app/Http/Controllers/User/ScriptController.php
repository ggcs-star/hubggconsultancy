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
    private const OFFICE_EXTENSIONS = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

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

    public function open(Request $request, ScriptItem $scriptItem): RedirectResponse
    {
        abort_unless($scriptItem->is_published, 404);

        ContentView::record($request->user(), $scriptItem);

        return redirect()->away($this->previewUrl($scriptItem->fileUrl()));
    }

    /**
     * Browsers can't preview Word/Excel/PowerPoint files inline — route
     * those through Microsoft's free web viewer instead of the raw file so
     * they open in a new tab like everything else, instead of downloading.
     */
    private function previewUrl(string $url): string
    {
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? $url, PATHINFO_EXTENSION));

        if (in_array($extension, self::OFFICE_EXTENSIONS, true)) {
            return 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($url);
        }

        return $url;
    }
}
