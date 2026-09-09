<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesToolkitItem;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesToolkitController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $category = trim((string) $request->query('category'));
        $language = trim((string) $request->query('language'));

        $items = SalesToolkitItem::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($language !== '', fn ($query) => $query->where('language', $language))
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        $categories = SalesToolkitItem::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.sales-toolkit.index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateItem($request, isCreate: true);

        $data = $request->input('link_type') === 'drive'
            ? $this->attachDriveLink($request, $data)
            : $this->attachFile($request, $data);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'sales-toolkit-thumbnails');
        }

        SalesToolkitItem::create($data);

        return redirect()->route('admin.sales-toolkit.index')->with('status', 'Toolkit item added.');
    }

    public function update(Request $request, SalesToolkitItem $salesToolkitItem): RedirectResponse
    {
        $data = $this->validateItem($request, isCreate: false);
        $switchingToDrive = $request->input('link_type') === 'drive';

        if ($switchingToDrive) {
            // Uploaded files are stored on disk and need cleaning up whether
            // we're switching away from an old one or just clearing it out.
            if (! $salesToolkitItem->is_drive_link) {
                $this->fileUploadService->delete($salesToolkitItem->url);
            }

            $data = $this->attachDriveLink($request, $data);
        } elseif ($request->hasFile('file')) {
            if (! $salesToolkitItem->is_drive_link) {
                $this->fileUploadService->delete($salesToolkitItem->url);
            }

            $data = $this->attachFile($request, $data);
        }

        if ($request->hasFile('thumbnail')) {
            $this->fileUploadService->delete($salesToolkitItem->thumbnail);
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'sales-toolkit-thumbnails');
        }

        $salesToolkitItem->update($data);

        return redirect()->route('admin.sales-toolkit.index')->with('status', 'Toolkit item updated.');
    }

    public function destroy(SalesToolkitItem $salesToolkitItem): RedirectResponse
    {
        if (! $salesToolkitItem->is_drive_link) {
            $this->fileUploadService->delete($salesToolkitItem->url);
        }

        $this->fileUploadService->delete($salesToolkitItem->thumbnail);

        $salesToolkitItem->delete();

        return redirect()->route('admin.sales-toolkit.index')->with('status', 'Toolkit item deleted.');
    }

    public function togglePublish(Request $request, SalesToolkitItem $salesToolkitItem): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $salesToolkitItem->update($data);

        return back()->with('status', $salesToolkitItem->is_published ? "\"{$salesToolkitItem->title}\" published." : "\"{$salesToolkitItem->title}\" set to draft.");
    }

    private function validateItem(Request $request, bool $isCreate): array
    {
        $linkType = $request->input('link_type', 'upload');

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'language' => ['required', 'in:english,hindi,gujarati,marathi,telugu,kannada'],
            'link_type' => ['required', 'in:upload,drive'],
            'file' => [$isCreate && $linkType === 'upload' ? 'required' : 'nullable', 'file', 'max:20480'],
            'drive_url' => [$linkType === 'drive' ? 'required' : 'nullable', 'url', 'max:2000'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function attachFile(Request $request, array $data): array
    {
        $file = $request->file('file');

        $data['url'] = $this->fileUploadService->store($file, 'sales-toolkit');
        $data['is_drive_link'] = false;
        $data['original_filename'] = $file->getClientOriginalName();
        $data['mime_type'] = $file->getClientMimeType();
        $data['file_size'] = $file->getSize();

        unset($data['file'], $data['link_type'], $data['drive_url']);

        return $data;
    }

    private function attachDriveLink(Request $request, array $data): array
    {
        $data['url'] = $data['drive_url'];
        $data['is_drive_link'] = true;
        $data['original_filename'] = null;
        $data['mime_type'] = null;
        $data['file_size'] = null;

        unset($data['drive_url'], $data['file'], $data['link_type']);

        return $data;
    }
}
