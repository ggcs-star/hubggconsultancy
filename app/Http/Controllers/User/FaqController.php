<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $tabs = $this->buildTabs($request);

        $requestedTab = $request->query('active_tab');
        $activeTab = $tabs->contains(fn (array $tab) => $tab['key'] === $requestedTab)
            ? $requestedTab
            : ($tabs->first()['key'] ?? null);

        return view('user.faqs.index', [
            'tabs' => $tabs,
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * One tab per section that has published FAQs, plus a trailing "General"
     * tab for FAQs left unsectioned — skipped entirely if there's nothing.
     * Each tab gets its own search box and its own 10-per-page pagination,
     * using a page/search query-string key namespaced to that tab so every
     * tab's state coexists independently within one page load.
     */
    private function buildTabs(Request $request): Collection
    {
        $sections = FaqSection::withCount(['faqs' => fn (Builder $query) => $query->published()])
            ->ordered()
            ->get()
            ->filter(fn (FaqSection $section) => $section->faqs_count > 0)
            ->map(fn (FaqSection $section) => $this->buildTab(
                $request,
                'section-' . $section->id,
                $section->name,
                Faq::published()->where('faq_section_id', $section->id)
            ))
            ->values();

        $uncategorizedCount = Faq::published()->whereNull('faq_section_id')->count();

        if ($uncategorizedCount > 0) {
            $sections->push($this->buildTab(
                $request,
                'uncategorized',
                'General',
                Faq::published()->whereNull('faq_section_id')
            ));
        }

        return $sections;
    }

    private function buildTab(Request $request, string $key, string $label, Builder $baseQuery): array
    {
        $searchKey = "search_{$key}";
        $pageKey = "page_{$key}";
        $search = trim((string) $request->query($searchKey));

        $faqs = $baseQuery
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            }))
            ->ordered()
            ->paginate(10, ['*'], $pageKey)
            ->withQueryString()
            ->appends(['active_tab' => $key]);

        return [
            'key' => $key,
            'label' => $label,
            'search_key' => $searchKey,
            'search_value' => $search,
            'faqs' => $faqs,
        ];
    }
}
