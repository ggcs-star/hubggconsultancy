<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLmsArticleRequest;
use App\Http\Requests\Admin\UpdateLmsArticleRequest;
use App\Models\LmsArticle;
use App\Models\LmsCategory;
use App\Models\LmsProduct;
use App\Models\LmsSubCategory;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LmsArticleController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function create(LmsProduct $lmsProduct): View
    {
        $lmsProduct->load('categories.subCategories');

        return view('admin.lms.articles.create', [
            'lmsProduct' => $lmsProduct,
        ]);
    }

    public function store(StoreLmsArticleRequest $request, LmsProduct $lmsProduct): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($lmsProduct, $request->string('title'));

        $lmsProduct->articles()->create($data);

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $lmsProduct, 'tab' => 'categories'])
            ->with('success', 'Article created.');
    }

    public function edit(LmsArticle $lmsArticle): View
    {
        $lmsArticle->load('product.categories.subCategories');

        return view('admin.lms.articles.edit', [
            'lmsProduct' => $lmsArticle->product,
            'article' => $lmsArticle,
        ]);
    }

    public function update(UpdateLmsArticleRequest $request, LmsArticle $lmsArticle): RedirectResponse
    {
        $lmsArticle->update($request->validated());

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $lmsArticle->lms_product_id, 'tab' => 'categories'])
            ->with('success', 'Article updated.');
    }

    public function destroy(LmsArticle $lmsArticle): RedirectResponse
    {
        $productId = $lmsArticle->lms_product_id;
        $lmsArticle->delete();

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $productId, 'tab' => 'categories'])
            ->with('success', 'Article removed.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:4096'],
        ]);

        $path = $this->fileUploadService->store($request->file('file'), 'lms-articles');

        return response()->json(['location' => asset('storage/' . $path)]);
    }

    public function reorderInCategory(Request $request, LmsCategory $lmsCategory): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            LmsArticle::where('id', $id)
                ->where('lms_category_id', $lmsCategory->id)
                ->whereNull('lms_sub_category_id')
                ->update(['sort_order' => $index + 1]);
        }

        return redirect()->back();
    }

    public function reorderInSubCategory(Request $request, LmsSubCategory $lmsSubCategory): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            LmsArticle::where('id', $id)->where('lms_sub_category_id', $lmsSubCategory->id)->update(['sort_order' => $index + 1]);
        }

        return redirect()->back();
    }

    private function uniqueSlug(LmsProduct $lmsProduct, string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 1;

        while ($lmsProduct->articles()->where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        return $slug;
    }
}
