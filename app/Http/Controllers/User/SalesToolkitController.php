<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ContentView;
use App\Models\SalesToolkitItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesToolkitController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $category = trim((string) $request->query('category'));

        $availableLanguages = collect(['english', 'hindi', 'gujarati'])
            ->filter(fn ($lang) => SalesToolkitItem::published()->where('language', $lang)->exists())
            ->values()
            ->all();

        $language = $request->query('language');

        if (! $language || ! in_array($language, $availableLanguages, true)) {
            $language = $availableLanguages[0] ?? 'english';
        }

        $items = SalesToolkitItem::published()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->where('language', $language)
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
            'language' => $language,
            'availableLanguages' => $availableLanguages,
        ]);
    }

    public function open(Request $request, SalesToolkitItem $salesToolkitItem): RedirectResponse
    {
        abort_unless($salesToolkitItem->is_published, 404);

        ContentView::record($request->user(), $salesToolkitItem);

        return redirect()->away($salesToolkitItem->fileUrl());
    }
}
