<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $language = trim((string) $request->query('language'));

        $documents = Document::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->when($language !== '', fn ($query) => $query->where('language', $language))
            ->ordered()
            ->paginate(9)
            ->withQueryString();

        return view('admin.documents.index', [
            'documents' => $documents,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateDocument($request, isCreate: true);
        $data = $this->attachSource($request, $data);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'documents');
        }

        Document::create($data);

        return redirect()->route('admin.documents.index')->with('status', 'Document added.');
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $data = $this->validateDocument($request, isCreate: false);

        if ($data['source'] === 'link' || $request->hasFile('file')) {
            if (! $document->is_external) {
                $this->fileUploadService->delete($document->url);
            }

            $data = $this->attachSource($request, $data);
        } else {
            unset($data['file'], $data['source']);
        }

        if ($request->hasFile('thumbnail')) {
            $this->fileUploadService->delete($document->thumbnail);
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'documents');
        }

        $document->update($data);

        return redirect()->route('admin.documents.index')->with('status', 'Document updated.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if (! $document->is_external) {
            $this->fileUploadService->delete($document->url);
        }

        $this->fileUploadService->delete($document->thumbnail);
        $document->delete();

        return redirect()->route('admin.documents.index')->with('status', 'Document deleted.');
    }

    public function togglePublish(Request $request, Document $document): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $document->update($data);

        return back()->with('status', $document->is_published ? "\"{$document->title}\" published." : "\"{$document->title}\" set to draft.");
    }

    private function validateDocument(Request $request, bool $isCreate): array
    {
        $source = $request->input('source', 'link');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'language' => ['required', 'in:english,hindi,gujarati'],
            'source' => ['required', 'in:upload,link'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ];

        if ($source === 'link') {
            $rules['url'] = ['required', 'string', 'max:2000', 'url'];
        } else {
            $rules['file'] = [$isCreate ? 'required' : 'nullable', 'file', 'max:51200'];
        }

        return $request->validate($rules);
    }

    private function attachSource(Request $request, array $data): array
    {
        if (($data['source'] ?? null) === 'upload' && $request->hasFile('file')) {
            $file = $request->file('file');

            $data['url'] = $this->fileUploadService->store($file, 'documents');
            $data['is_external'] = false;
            $data['original_filename'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        } else {
            $data['is_external'] = true;
            $data['original_filename'] = null;
            $data['mime_type'] = null;
            $data['file_size'] = null;
        }

        unset($data['file'], $data['source']);

        return $data;
    }
}
