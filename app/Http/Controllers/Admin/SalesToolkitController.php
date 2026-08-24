<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesToolkitItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SalesToolkitController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $category = trim((string) $request->query('category'));

        $items = SalesToolkitItem::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->ordered()
            ->get();

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
        $data = $this->attachFile($request, $data);

        SalesToolkitItem::create($data);

        return redirect()->route('admin.sales-toolkit.index')->with('status', 'Toolkit item added.');
    }

    public function update(Request $request, SalesToolkitItem $salesToolkitItem): RedirectResponse
    {
        $data = $this->validateItem($request, isCreate: false);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($salesToolkitItem->url);
            $data = $this->attachFile($request, $data);
        }

        $salesToolkitItem->update($data);

        return redirect()->route('admin.sales-toolkit.index')->with('status', 'Toolkit item updated.');
    }

    public function destroy(SalesToolkitItem $salesToolkitItem): RedirectResponse
    {
        Storage::disk('public')->delete($salesToolkitItem->url);

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
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => [$isCreate ? 'required' : 'nullable', 'file', 'max:20480'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function attachFile(Request $request, array $data): array
    {
        $file = $request->file('file');

        $data['url'] = $file->store('sales-toolkit', 'public');
        $data['original_filename'] = $file->getClientOriginalName();
        $data['mime_type'] = $file->getClientMimeType();
        $data['file_size'] = $file->getSize();

        unset($data['file']);

        return $data;
    }
}
