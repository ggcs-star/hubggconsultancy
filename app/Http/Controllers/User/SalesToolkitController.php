<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SalesToolkitItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesToolkitController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $category = trim((string) $request->query('category'));

        $items = SalesToolkitItem::published()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        $categories = SalesToolkitItem::published()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('user.sales-toolkit.index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }
}
