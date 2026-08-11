<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLmsCategoryRequest;
use App\Models\LmsCategory;
use App\Models\LmsProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LmsCategoryController extends Controller
{
    public function store(StoreLmsCategoryRequest $request, LmsProduct $lmsProduct): RedirectResponse|JsonResponse
    {
        $category = $lmsProduct->categories()->create([
            'name' => $request->string('name'),
            'slug' => $this->uniqueSlug($lmsProduct, $request->string('name')),
            'description' => $request->input('description'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
            ], 201);
        }

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $lmsProduct, 'tab' => 'categories'])
            ->with('success', 'Category added.');
    }

    public function update(StoreLmsCategoryRequest $request, LmsCategory $lmsCategory): RedirectResponse
    {
        $lmsCategory->update([
            'name' => $request->string('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $lmsCategory->lms_product_id, 'tab' => 'categories'])
            ->with('success', 'Category updated.');
    }

    public function destroy(LmsCategory $lmsCategory): RedirectResponse
    {
        $productId = $lmsCategory->lms_product_id;
        $lmsCategory->delete();

        return redirect()
            ->route('admin.lms.products.show', ['lmsProduct' => $productId, 'tab' => 'categories'])
            ->with('success', 'Category removed.');
    }

    public function reorder(Request $request, LmsProduct $lmsProduct): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            LmsCategory::where('id', $id)->where('lms_product_id', $lmsProduct->id)->update(['sort_order' => $index + 1]);
        }

        return redirect()->back();
    }

    private function uniqueSlug(LmsProduct $lmsProduct, string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 1;

        while ($lmsProduct->categories()->where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        return $slug;
    }
}
