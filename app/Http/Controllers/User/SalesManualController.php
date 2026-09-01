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
        $visible = fn () => SalesManual::where('status', 'published')->where('is_active', true);

        $availableLanguages = collect(['english', 'hindi', 'gujarati'])
            ->filter(fn ($lang) => $visible()->where('language', $lang)->exists())
            ->values()
            ->all();

        $language = $request->query('language');

        if (! $language || ! in_array($language, $availableLanguages, true)) {
            $language = $availableLanguages[0] ?? 'english';
        }

        $query = SalesManual::query()
            ->with('attachments')
            ->where('status', 'published')
            ->where('is_active', true)
            ->where('language', $language)
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

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                // Manual title
                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )

                // Description
                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                )

                // Category
                ->orWhere(
                    'category',
                    'like',
                    "%{$search}%"
                )

                // Uploaded file name
                ->orWhereHas('attachments', function ($attachmentQuery) use ($search) {

                    $attachmentQuery->where(
                        'file_name',
                        'like',
                        "%{$search}%"
                    );

                });

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
            ->paginate(10)
            ->withQueryString();


        return view(
            'user.sales-manuals.index',
            compact('manuals', 'language', 'availableLanguages')
        );
    }


    /**
     * User: View single manual
     */
    public function show(SalesManual $manual)
    {
        /*
        |--------------------------------------------------------------------------
        | Only Published + Active Manuals
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $manual->status === 'published'
            && $manual->is_active,
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Load Attachments
        |--------------------------------------------------------------------------
        */

        $manual->load('attachments');


        return view(
            'user.sales-manuals.show',
            compact('manual')
        );
    }
}