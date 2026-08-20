<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $resources = Resource::withCount('checkpoints')
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->ordered()
            ->get();

        return view('admin.resources.index', [
            'resources' => $resources,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateResource($request);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'resources');
        }

        $resource = Resource::create($data);

        return redirect()
            ->route('admin.resources.show', $resource)
            ->with('status', 'Resource added.');
    }

    public function show(Resource $resource): View
    {
        $resource->load(['checkpoints.questions.options']);

        return view('admin.resources.show', [
            'resource' => $resource,
            'hindiCheckpoints' => $resource->checkpoints->where('language', 'hindi')->sortBy('sort_order')->values(),
            'englishCheckpoints' => $resource->checkpoints->where('language', 'english')->sortBy('sort_order')->values(),
        ]);
    }

    public function update(Request $request, Resource $resource): RedirectResponse
    {
        $data = $this->validateResource($request);

        if ($request->hasFile('thumbnail')) {
            $this->fileUploadService->delete($resource->thumbnail);
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'resources');
        }

        $resource->update($data);

        return redirect()
            ->route('admin.resources.index')
            ->with('status', 'Resource updated.');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $this->fileUploadService->delete($resource->thumbnail);
        $resource->delete();

        return redirect()->route('admin.resources.index')->with('status', 'Resource deleted.');
    }

    public function togglePublish(Request $request, Resource $resource): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $resource->update($data);

        return back()->with('status', $resource->is_published ? "\"{$resource->title}\" published." : "\"{$resource->title}\" set to draft.");
    }

    private function validateResource(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
            'hindi_youtube_url' => ['nullable', 'string', 'max:1000'],
            'english_youtube_url' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
