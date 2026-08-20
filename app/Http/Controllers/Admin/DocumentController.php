<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $documents = Document::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->ordered()
            ->get();

        return view('admin.documents.index', [
            'documents' => $documents,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Document::create($this->validateDocument($request));

        return redirect()->route('admin.documents.index')->with('status', 'Document added.');
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $document->update($this->validateDocument($request));

        return redirect()->route('admin.documents.index')->with('status', 'Document updated.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()->route('admin.documents.index')->with('status', 'Document deleted.');
    }

    public function togglePublish(Request $request, Document $document): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $document->update($data);

        return back()->with('status', $document->is_published ? "\"{$document->title}\" published." : "\"{$document->title}\" set to draft.");
    }

    private function validateDocument(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'url' => ['required', 'string', 'max:2000', 'url'],
        ]);
    }
}
