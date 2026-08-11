<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLmsSubCategoryRequest;
use App\Models\LmsCategory;
use App\Models\LmsSubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LmsSubCategoryController extends Controller
{
    public function store(StoreLmsSubCategoryRequest $request, LmsCategory $lmsCategory): RedirectResponse|JsonResponse
    {
        $subCategory = $lmsCategory->subCategories()->create([
            'name' => $request->string('name'),
            'slug' => $this->uniqueSlug($lmsCategory, $request->string('name')),
            'description' => $request->input('description'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $subCategory->id,
                'name' => $subCategory->name,
                'lms_category_id' => $subCategory->lms_category_id,
            ], 201);
        }

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $lmsCategory->lms_product_id, 'tab' => 'categories'])
            ->with('success', 'Sub-category added.');
    }

    public function update(StoreLmsSubCategoryRequest $request, LmsSubCategory $lmsSubCategory): RedirectResponse
    {
        $lmsSubCategory->update([
            'name' => $request->string('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $lmsSubCategory->category->lms_product_id, 'tab' => 'categories'])
            ->with('success', 'Sub-category updated.');
    }

    public function destroy(LmsSubCategory $lmsSubCategory): RedirectResponse
    {
        $productId = $lmsSubCategory->category->lms_product_id;
        $lmsSubCategory->delete();

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $productId, 'tab' => 'categories'])
            ->with('success', 'Sub-category removed.');
    }

    public function reorder(Request $request, LmsCategory $lmsCategory): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            LmsSubCategory::where('id', $id)->where('lms_category_id', $lmsCategory->id)->update(['sort_order' => $index + 1]);
        }

        return redirect()->back();
    }

    private function uniqueSlug(LmsCategory $lmsCategory, string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 1;

        while ($lmsCategory->subCategories()->where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        return $slug;
    }
}
