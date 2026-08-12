<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaasProduct;
use App\Models\SupportIssueType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportIssueTypeController extends Controller
{
    /**
     * Admin: All support issue types
     */
    public function index(Request $request)
    {
        $query = SupportIssueType::with('product');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'module',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas('product', function ($productQuery) use ($search) {

                    $productQuery->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );

                });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | SaaS Product Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('saas_product_id')) {

            $query->where(
                'saas_product_id',
                $request->saas_product_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Module Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('module')) {

            $query->where(
                'module',
                $request->module
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Issue Types
        |--------------------------------------------------------------------------
        */

        $issueTypes = $query
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | SaaS Products
        |--------------------------------------------------------------------------
        */

        $products = SaasProduct::query()
            ->orderBy('name')
            ->get();


        return view(
            'admin.support.issue-types.index',
            compact(
                'issueTypes',
                'products'
            )
        );
    }


    /**
     * Admin: Create issue type
     */
    public function create()
    {
        $products = SaasProduct::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.support.issue-types.create',
            compact('products')
        );
    }


    /**
     * Admin: Store issue type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'saas_product_id' => [
                'required',
                'exists:saas_products,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'module' => [
                'nullable',
                'string',
                'max:255',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'default_priority' => [
                'required',
                'in:low,medium,high,urgent',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Verify SaaS Product
        |--------------------------------------------------------------------------
        */

        $product = SaasProduct::find(
            $validated['saas_product_id']
        );


        if (!$product) {

            return back()
                ->withInput()
                ->withErrors([
                    'saas_product_id' =>
                        'Selected SaaS product is unavailable.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Create Issue Type
        |--------------------------------------------------------------------------
        */

        SupportIssueType::create([

            'saas_product_id' =>
                $validated['saas_product_id'],

            'name' =>
                $validated['name'],

            'slug' =>
                Str::slug(
                    $validated['name']
                ),

            'description' =>
                $validated['description'] ?? null,

            'module' =>
                $validated['module'] ?? null,

            'icon' =>
                $validated['icon'] ?? null,

            'default_priority' =>
                $validated['default_priority'],

            'status' =>
                $request->boolean('status'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

        ]);


        return redirect()
            ->route(
                'admin.support.issue-types.index'
            )
            ->with(
                'success',
                'Support issue type created successfully.'
            );
    }


    /**
     * Admin: Edit issue type
     */
    public function edit(
        SupportIssueType $issueType
    ) {
        $products = SaasProduct::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.support.issue-types.edit',
            compact(
                'issueType',
                'products'
            )
        );
    }


    /**
     * Admin: Update issue type
     */
    public function update(
        Request $request,
        SupportIssueType $issueType
    ) {
        $validated = $request->validate([

            'saas_product_id' => [
                'required',
                'exists:saas_products,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'module' => [
                'nullable',
                'string',
                'max:255',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'default_priority' => [
                'required',
                'in:low,medium,high,urgent',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Verify SaaS Product
        |--------------------------------------------------------------------------
        */

        $product = SaasProduct::find(
            $validated['saas_product_id']
        );


        if (!$product) {

            return back()
                ->withInput()
                ->withErrors([
                    'saas_product_id' =>
                        'Selected SaaS product is unavailable.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Update Issue Type
        |--------------------------------------------------------------------------
        */

        $issueType->update([

            'saas_product_id' =>
                $validated['saas_product_id'],

            'name' =>
                $validated['name'],

            'slug' =>
                Str::slug(
                    $validated['name']
                ),

            'description' =>
                $validated['description'] ?? null,

            'module' =>
                $validated['module'] ?? null,

            'icon' =>
                $validated['icon'] ?? null,

            'default_priority' =>
                $validated['default_priority'],

            'status' =>
                $request->boolean('status'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

        ]);


        return redirect()
            ->route(
                'admin.support.issue-types.index'
            )
            ->with(
                'success',
                'Support issue type updated successfully.'
            );
    }


    /**
     * Admin: Delete issue type
     */
    public function destroy(
        SupportIssueType $issueType
    ) {
        /*
        |--------------------------------------------------------------------------
        | Prevent deleting issue type if tickets already use it
        |--------------------------------------------------------------------------
        */

        if ($issueType->tickets()->exists()) {

            return back()->with(
                'error',
                'This issue type cannot be deleted because tickets are already using it.'
            );
        }


        $issueType->delete();


        return redirect()
            ->route(
                'admin.support.issue-types.index'
            )
            ->with(
                'success',
                'Support issue type deleted successfully.'
            );
    }


    /**
     * Admin: Toggle issue type status
     */
    public function toggleStatus(
        SupportIssueType $issueType
    ) {
        $issueType->update([

            'status' =>
                !$issueType->status,

        ]);


        return back()->with(
            'success',
            'Status updated successfully.'
        );
    }
}