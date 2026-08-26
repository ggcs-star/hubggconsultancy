<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqSection;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('user.faqs.index', [
            'tabs' => $this->buildTabs(),
        ]);
    }

    /**
     * One tab per section that has published FAQs, plus a trailing "General"
     * tab for FAQs left unsectioned — skipped entirely if there's nothing.
     */
    private function buildTabs(): Collection
    {
        $sections = FaqSection::with(['faqs' => fn ($query) => $query->published()->ordered()])
            ->ordered()
            ->get()
            ->filter(fn (FaqSection $section) => $section->faqs->isNotEmpty())
            ->map(fn (FaqSection $section) => [
                'key' => 'section-' . $section->id,
                'label' => $section->name,
                'faqs' => $section->faqs,
            ])
            ->values();

        $uncategorized = Faq::published()->whereNull('faq_section_id')->ordered()->get();

        if ($uncategorized->isNotEmpty()) {
            $sections->push([
                'key' => 'uncategorized',
                'label' => 'General',
                'faqs' => $uncategorized,
            ]);
        }

        return $sections;
    }
}
