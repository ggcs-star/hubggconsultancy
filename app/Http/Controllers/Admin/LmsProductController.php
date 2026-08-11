<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLmsProductRequest;
use App\Http\Requests\Admin\UpdateLmsProductRequest;
use App\Models\LmsArticle;
use App\Models\LmsProduct;
use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LmsProductController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $lmsProducts = LmsProduct::with('product')
            ->withCount(['categories', 'subCategories', 'articles'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('tagline', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('active', $request->input('status') === 'active'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.lms.products.index', [
            'lmsProducts' => $lmsProducts,
            'products' => Product::whereDoesntHave('lmsProduct')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreLmsProductRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->integer('product_id'));

        $data = $request->safe()->except('image');
        $data['name'] = $product->name;
        $data['slug'] = $this->uniqueSlug($product->name);

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->store($request->file('image'), 'lms-products');
        }

        LmsProduct::create($data);

        return redirect()->route('admin.lms.products.index')->with('success', 'LMS product created successfully.');
    }

    public function show(Request $request, LmsProduct $lmsProduct): View
    {
        $tabs = ['details', 'categories'];

        $activeTab = $request->query('tab', 'details');
        if (! in_array($activeTab, $tabs, true)) {
            $activeTab = 'details';
        }

        $lmsProduct->load(['categories.subCategories.articles', 'categories.articles']);

        return view('admin.lms.products.show', [
            'lmsProduct' => $lmsProduct,
            'activeTab' => $activeTab,
        ]);
    }

    public function preview(LmsProduct $lmsProduct): View
    {
        $lmsProduct->load(['categories.subCategories.articles', 'categories.articles']);

        $firstArticle = $lmsProduct->categories
            ->flatMap(fn ($category) => $category->articles->concat($category->subCategories->flatMap->articles))
            ->first();

        return view('admin.lms.products.preview', [
            'lmsProduct' => $lmsProduct,
            'activeArticle' => $firstArticle,
        ]);
    }

    public function previewArticle(LmsProduct $lmsProduct, LmsArticle $lmsArticle): View
    {
        abort_unless($lmsArticle->lms_product_id === $lmsProduct->id, 404);

        $lmsProduct->load(['categories.subCategories.articles', 'categories.articles']);

        return view('admin.lms.products.preview', [
            'lmsProduct' => $lmsProduct,
            'activeArticle' => $lmsArticle,
        ]);
    }

    public function update(UpdateLmsProductRequest $request, LmsProduct $lmsProduct): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['active'] = $request->boolean('active');

        if ($request->hasFile('image')) {
            $this->fileUploadService->delete($lmsProduct->image);
            $data['image'] = $this->fileUploadService->store($request->file('image'), 'lms-products');
        }

        $lmsProduct->update($data);

        return redirect()
            ->route('admin.lms.products.show', $lmsProduct)
            ->with('success', 'LMS product updated.');
    }

    public function destroy(LmsProduct $lmsProduct): RedirectResponse
    {
        $this->fileUploadService->delete($lmsProduct->image);
        $lmsProduct->delete();

        return redirect()->route('admin.lms.products.index')->with('success', 'LMS product deleted.');
    }

    public function updateStatus(Request $request, LmsProduct $lmsProduct): RedirectResponse
    {
        $data = $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $lmsProduct->update($data);

        return back()->with('success', 'LMS product status updated.');
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 1;

        while (LmsProduct::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        return $slug;
    }
}
