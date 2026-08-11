<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LmsArticle;
use App\Models\LmsProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LmsController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $assignedProducts = $this->assignedProducts($request);

        $firstProduct = $assignedProducts->first();

        if (! $firstProduct) {
            return view('client.lms.empty');
        }

        return redirect()->route('client.lms.product', $firstProduct);
    }

    public function product(Request $request, LmsProduct $lmsProduct): View
    {
        $assignedProducts = $this->assignedProducts($request);

        abort_unless($assignedProducts->contains('id', $lmsProduct->id), 403);

        $this->loadPublishedTree($lmsProduct);

        $firstArticle = $lmsProduct->categories
            ->flatMap(fn ($category) => $category->articles->concat($category->subCategories->flatMap->articles))
            ->first();

        return view('client.lms.show', [
            'assignedProducts' => $assignedProducts,
            'lmsProduct' => $lmsProduct,
            'activeArticle' => $firstArticle,
        ]);
    }

    public function article(Request $request, LmsProduct $lmsProduct, LmsArticle $lmsArticle): View
    {
        $assignedProducts = $this->assignedProducts($request);

        abort_unless($assignedProducts->contains('id', $lmsProduct->id), 403);
        abort_unless($lmsArticle->lms_product_id === $lmsProduct->id, 404);
        abort_unless($lmsArticle->is_published, 404);

        $this->loadPublishedTree($lmsProduct);

        return view('client.lms.show', [
            'assignedProducts' => $assignedProducts,
            'lmsProduct' => $lmsProduct,
            'activeArticle' => $lmsArticle,
        ]);
    }

    private function loadPublishedTree(LmsProduct $lmsProduct): void
    {
        $lmsProduct->load([
            'categories.articles' => fn ($query) => $query->where('is_published', true),
            'categories.subCategories.articles' => fn ($query) => $query->where('is_published', true),
        ]);
    }

    private function assignedProducts(Request $request)
    {
        $client = $request->user()->client;

        if (! $client) {
            return collect();
        }

        $purchasedProductIds = $client->products()
            ->wherePivot('status', true)
            ->pluck('products.id');

        return LmsProduct::with('product')
            ->whereIn('product_id', $purchasedProductIds)
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }
}
