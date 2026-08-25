<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SaasProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaasProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $saasProducts = SaasProduct::active()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%");
            }))
            ->ordered()
            ->get();

        return view('user.saas-products.index', [
            'saasProducts' => $saasProducts,
            'interestIds' => $request->user()->interests()->pluck('saas_products.id')->all(),
        ]);
    }

    public function toggleInterest(Request $request, SaasProduct $saasProduct): RedirectResponse
    {
        $user = $request->user();

        if ($user->interests()->where('saas_products.id', $saasProduct->id)->exists()) {
            $user->interests()->detach($saasProduct->id);
            $status = "Removed \"{$saasProduct->name}\" from your interests.";
        } else {
            $user->interests()->attach($saasProduct->id);
            $status = "Marked interest in \"{$saasProduct->name}\".";
        }

        return back()->with('status', $status);
    }
}
