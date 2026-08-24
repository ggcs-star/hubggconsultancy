<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()->ordered()->get();

        return view('admin.faqs.index', [
            'faqs' => $faqs,
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

    private function validateFaq(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
