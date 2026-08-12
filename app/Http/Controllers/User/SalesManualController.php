<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SalesManual;
use Illuminate\Http\Request;

class SalesManualController extends Controller
{
    /**
     * User: Published and active sales manuals
     */
    public function index(Request $request)
    {
        $query = SalesManual::with('attachments')
            ->where('status', 'published')
            ->where('is_active', true)
            ->orderByDesc('is_pinned')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Type Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('type')) {

            $query->where(
                'type',
                $request->type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Manuals
        |--------------------------------------------------------------------------
        */

        $manuals = $query
            ->paginate(12)
            ->withQueryString();

        return view(
            'user.sales-manuals.index',
            compact('manuals')
        );
    }


    /**
     * User: View single manual
     */
    public function show(SalesManual $manual)
    {
        /*
        |--------------------------------------------------------------------------
        | Only Published + Active Resources
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $manual->status === 'published'
            && $manual->is_active,
            404
        );


        $manual->load('attachments');


        return view(
            'user.sales-manuals.show',
            compact('manual')
        );
    }
}