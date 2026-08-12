<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesManual;
use App\Models\SalesManualAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SalesManualController extends Controller
{
    /**
     * Admin: All sales manuals
     */
    public function index(Request $request)
    {
        $query = SalesManual::with('attachments')
            ->withCount('attachments')
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
        | Active Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('active')) {

            $query->where(
                'is_active',
                $request->active === 'active'
            );
        }

        $manuals = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.sales-manuals.index',
            compact('manuals')
        );
    }


    /**
     * Admin: Create page
     */
    public function create()
    {
        return view(
            'admin.sales-manuals.create'
        );
    }


    /**
     * Admin: Store manual
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'in:manual,guide,cheat_sheet,faq,sop,script',
            ],

            'category' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],

            'is_pinned' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'attachments' => [
                'nullable',
                'array',
            ],

            'attachments.*' => [
                'file',
                'max:51200',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $slug = Str::slug(
            $validated['title']
        );

        $originalSlug = $slug;

        $counter = 1;

        while (
            SalesManual::where(
                'slug',
                $slug
            )->exists()
        ) {

            $slug = $originalSlug . '-' . $counter;

            $counter++;
        }


        /*
        |--------------------------------------------------------------------------
        | Cover Image
        |--------------------------------------------------------------------------
        */

        $coverImagePath = null;

        if ($request->hasFile('cover_image')) {

            $coverImagePath = $request
                ->file('cover_image')
                ->store(
                    'sales-manuals/covers',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Published At
        |--------------------------------------------------------------------------
        */

        $publishedAt = null;

        if (
            $validated['status'] === 'published'
        ) {
            $publishedAt = now();
        }


        /*
        |--------------------------------------------------------------------------
        | Create Manual
        |--------------------------------------------------------------------------
        */

        $manual = SalesManual::create([

            'title' => $validated['title'],

            'slug' => $slug,

            'type' => $validated['type'],

            'category' =>
                $validated['category'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'content' =>
                $validated['content'] ?? null,

            'cover_image' => $coverImagePath,

            'status' =>
                $validated['status'],

            'is_active' =>
                $request->boolean('is_active'),

            'is_featured' =>
                $request->boolean('is_featured'),

            'is_pinned' =>
                $request->boolean('is_pinned'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'published_at' =>
                $publishedAt,

            'created_by' =>
                auth()->id(),

            'updated_by' =>
                auth()->id(),

        ]);


        /*
        |--------------------------------------------------------------------------
        | Attachments
        |--------------------------------------------------------------------------
        */

        $this->storeAttachments(
            $request,
            $manual
        );


        return redirect()
            ->route(
                'admin.manuals.index'
            )
            ->with(
                'success',
                'Sales manual created successfully.'
            );
    }


    /**
     * Admin: Show manual
     */
    public function show(SalesManual $manual)
    {
        $manual->load('attachments');

        return view(
            'admin.sales-manuals.show',
            compact('manual')
        );
    }


    /**
     * Admin: Edit page
     */
    public function edit(SalesManual $manual)
    {
        $manual->load('attachments');

        return view(
            'admin.sales-manuals.edit',
            compact('manual')
        );
    }


    /**
     * Admin: Update manual
     */
    public function update(
        Request $request,
        SalesManual $manual
    ) {
        $validated = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'in:manual,guide,cheat_sheet,faq,sop,script',
            ],

            'category' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],

            'is_pinned' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'attachments' => [
                'nullable',
                'array',
            ],

            'attachments.*' => [
                'file',
                'max:51200',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $slug = Str::slug(
            $validated['title']
        );

        $originalSlug = $slug;

        $counter = 1;

        while (
            SalesManual::where('slug', $slug)
                ->where('id', '!=', $manual->id)
                ->exists()
        ) {

            $slug = $originalSlug . '-' . $counter;

            $counter++;
        }


        /*
        |--------------------------------------------------------------------------
        | Cover Image
        |--------------------------------------------------------------------------
        */

        $coverImagePath =
            $manual->cover_image;


        if ($request->hasFile('cover_image')) {

            if ($manual->cover_image) {

                Storage::disk('public')
                    ->delete(
                        $manual->cover_image
                    );
            }

            $coverImagePath = $request
                ->file('cover_image')
                ->store(
                    'sales-manuals/covers',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Published At
        |--------------------------------------------------------------------------
        */

        $publishedAt =
            $manual->published_at;


        if (
            $validated['status'] === 'published'
            && !$manual->published_at
        ) {

            $publishedAt = now();
        }


        if (
            $validated['status'] === 'draft'
        ) {

            $publishedAt = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $manual->update([

            'title' =>
                $validated['title'],

            'slug' =>
                $slug,

            'type' =>
                $validated['type'],

            'category' =>
                $validated['category'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'content' =>
                $validated['content'] ?? null,

            'cover_image' =>
                $coverImagePath,

            'status' =>
                $validated['status'],

            'is_active' =>
                $request->boolean('is_active'),

            'is_featured' =>
                $request->boolean('is_featured'),

            'is_pinned' =>
                $request->boolean('is_pinned'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'published_at' =>
                $publishedAt,

            'updated_by' =>
                auth()->id(),

        ]);


        /*
        |--------------------------------------------------------------------------
        | New Attachments
        |--------------------------------------------------------------------------
        */

        $this->storeAttachments(
            $request,
            $manual
        );


        return redirect()
            ->route(
                'admin.manuals.index'
            )
            ->with(
                'success',
                'Sales manual updated successfully.'
            );
    }


    /**
     * Admin: Delete manual
     */
    public function destroy(
        SalesManual $manual
    ) {
        /*
        |--------------------------------------------------------------------------
        | Delete Cover
        |--------------------------------------------------------------------------
        */

        if ($manual->cover_image) {

            Storage::disk('public')
                ->delete(
                    $manual->cover_image
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Attachments
        |--------------------------------------------------------------------------
        */

        $manual->load('attachments');

        foreach (
            $manual->attachments as $attachment
        ) {

            Storage::disk('public')
                ->delete(
                    $attachment->file_path
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Manual
        |--------------------------------------------------------------------------
        */

        $manual->delete();


        return redirect()
            ->route(
                'admin.manuals.index'
            )
            ->with(
                'success',
                'Sales manual deleted successfully.'
            );
    }


    /**
     * Publish / Unpublish
     */
    public function togglePublish(
        SalesManual $manual
    ) {
        if (
            $manual->status === 'published'
        ) {

            $manual->update([

                'status' => 'draft',

                'published_at' => null,

                'updated_by' => auth()->id(),

            ]);

            $message =
                'Sales manual moved to draft.';

        } else {

            $manual->update([

                'status' => 'published',

                'published_at' => now(),

                'is_active' => true,

                'updated_by' => auth()->id(),

            ]);

            $message =
                'Sales manual published successfully.';
        }


        return back()->with(
            'success',
            $message
        );
    }


    /**
     * Active / Inactive
     */
    public function toggleActive(
        SalesManual $manual
    ) {
        $manual->update([

            'is_active' =>
                !$manual->is_active,

            'updated_by' =>
                auth()->id(),

        ]);


        return back()->with(
            'success',
            'Manual visibility updated.'
        );
    }


    /**
     * Featured / Unfeatured
     */
    public function toggleFeatured(
        SalesManual $manual
    ) {
        $manual->update([

            'is_featured' =>
                !$manual->is_featured,

            'updated_by' =>
                auth()->id(),

        ]);


        return back()->with(
            'success',
            'Featured status updated.'
        );
    }


    /**
     * Pinned / Unpinned
     */
    public function togglePinned(
        SalesManual $manual
    ) {
        $manual->update([

            'is_pinned' =>
                !$manual->is_pinned,

            'updated_by' =>
                auth()->id(),

        ]);


        return back()->with(
            'success',
            'Pinned status updated.'
        );
    }


    /**
     * Delete attachment
     */
    public function deleteAttachment(
        SalesManualAttachment $attachment
    ) {
        Storage::disk('public')
            ->delete(
                $attachment->file_path
            );

        $attachment->delete();


        return back()->with(
            'success',
            'Attachment deleted successfully.'
        );
    }


    /**
     * Store multiple attachments
     */
    private function storeAttachments(
        Request $request,
        SalesManual $manual
    ): void {

        if (
            !$request->hasFile(
                'attachments'
            )
        ) {
            return;
        }


        $currentCount =
            $manual->attachments()->count();


        foreach (
            $request->file('attachments')
            as $index => $file
        ) {

            $path = $file->store(
                'sales-manuals/attachments',
                'public'
            );


            SalesManualAttachment::create([

                'sales_manual_id' =>
                    $manual->id,

                'file_name' =>
                    $file->getClientOriginalName(),

                'file_path' =>
                    $path,

                'file_type' =>
                    $file->getClientOriginalExtension(),

                'mime_type' =>
                    $file->getMimeType(),

                'file_size' =>
                    $file->getSize(),

                'sort_order' =>
                    $currentCount + $index,

            ]);
        }
    }
}