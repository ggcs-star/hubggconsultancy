<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()->with('section')->ordered()->get();

        return view('admin.faqs.index', [
            'faqs' => $faqs,
            'sections' => FaqSection::ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateFaq($request);

        Faq::create($data);

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ added.');
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $data = $this->validateFaq($request);

        $faq->update($data);

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ deleted.');
    }

    public function togglePublish(Request $request, Faq $faq): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $faq->update($data);

        return back()->with('status', $faq->is_published ? 'FAQ published.' : 'FAQ set to draft.');
    }

    public function storeSection(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:faq_sections,name'],
        ]);

        $section = FaqSection::create($data);

        if ($request->wantsJson()) {
            return response()->json(['id' => $section->id, 'name' => $section->name]);
        }

        return redirect()->route('admin.faqs.index')->with('status', 'Section added.');
    }

    private function validateFaq(Request $request): array
    {
        return $request->validate([
            'faq_section_id' => ['nullable', 'exists:faq_sections,id'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
