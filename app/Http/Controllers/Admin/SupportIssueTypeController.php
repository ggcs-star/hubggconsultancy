<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportIssueType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportIssueTypeController extends Controller
{
    public function index(Request $request)
{
    $query = SupportIssueType::query();

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('module', 'like', "%{$search}%");
        });
    }

    // Module filter
    if ($request->filled('module')) {
        $query->where('module', $request->module);
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where(
            'status',
            $request->status
        );
    }

    $issueTypes = $query
        ->orderBy('sort_order')
        ->orderByDesc('id')
        ->paginate(15)
        ->withQueryString();

    return view(
        'admin.support.issue-types.index',
        compact('issueTypes')
    );
}


    public function create()
    {
        return view('admin.support.issue-types.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
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


        SupportIssueType::create([
            'name' => $validated['name'],

            'slug' => Str::slug(
                $validated['name']
            ),

            'description' => $validated['description'] ?? null,

            'module' => $validated['module'] ?? null,

            'icon' => $validated['icon'] ?? null,

            'default_priority' => $validated['default_priority'],

            'status' => $request->boolean('status'),

            'sort_order' => $validated['sort_order'] ?? 0,
        ]);


        return redirect()
            ->route('admin.support.issue-types.index')
            ->with(
                'success',
                'Support issue type created successfully.'
            );
    }


    public function edit(SupportIssueType $issueType)
    {
        return view(
            'admin.support.issue-types.edit',
            compact('issueType')
        );
    }


    public function update(
        Request $request,
        SupportIssueType $issueType
    ) {
        $validated = $request->validate([
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


        $issueType->update([
            'name' => $validated['name'],

            'slug' => Str::slug(
                $validated['name']
            ),

            'description' => $validated['description'] ?? null,

            'module' => $validated['module'] ?? null,

            'icon' => $validated['icon'] ?? null,

            'default_priority' => $validated['default_priority'],

            'status' => $request->boolean('status'),

            'sort_order' => $validated['sort_order'] ?? 0,
        ]);


        return redirect()
            ->route('admin.support.issue-types.index')
            ->with(
                'success',
                'Support issue type updated successfully.'
            );
    }


    public function destroy(SupportIssueType $issueType)
    {
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
            ->route('admin.support.issue-types.index')
            ->with(
                'success',
                'Support issue type deleted successfully.'
            );
    }


    public function toggleStatus(SupportIssueType $issueType)
    {
        $issueType->update([
            'status' => !$issueType->status,
        ]);


        return back()->with(
            'success',
            'Status updated successfully.'
        );
    }
}