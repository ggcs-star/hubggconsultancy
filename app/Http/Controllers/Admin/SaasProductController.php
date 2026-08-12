<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaasProduct;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SaasProductController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $saasProducts = SaasProduct::when($request->filled('search'), function ($query) use ($request) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('admin.saas-products.index', [
            'saasProducts' => $saasProducts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'emi_available' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['emi_available'] = $request->boolean('emi_available');
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->fileUploadService->store($request->file('logo'), 'saas-products');
        }

        SaasProduct::create($data);

        return back()->with('status', 'SaaS product created.');
    }

    public function update(Request $request, SaasProduct $saasProduct): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'emi_available' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['emi_available'] = $request->boolean('emi_available');

        if ($request->hasFile('logo')) {
            $this->fileUploadService->delete($saasProduct->logo);
            $data['logo'] = $this->fileUploadService->store($request->file('logo'), 'saas-products');
        }

        $saasProduct->update($data);

        return back()->with('status', 'SaaS product updated.');
    }

    public function toggleStatus(Request $request, SaasProduct $saasProduct): RedirectResponse
    {
        $data = $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $saasProduct->update($data);

        return back()->with('status', 'SaaS product status updated.');
    }

    public function destroy(SaasProduct $saasProduct): RedirectResponse
    {
        $this->fileUploadService->delete($saasProduct->logo);
        $saasProduct->delete();

        return back()->with('status', 'SaaS product deleted.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (SaasProduct::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $base . '-' . $suffix;
        }

        return $slug;
    }
}
