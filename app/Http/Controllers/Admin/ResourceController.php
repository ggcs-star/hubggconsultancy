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
            ->paginate(10)
            ->withQueryString();

        return view('admin.resources.index', [
            'resources' => $resources,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateResource($request);
        $data = $this->attachThumbnails($request, $data);

        $resource = Resource::create($data);

        return redirect()
            ->route('admin.resources.show', $resource)
            ->with('status', 'Resource added.');
    }

    public function show(Resource $resource): View
    {
        $resource->load(['checkpoints.questions.options']);

        $checkpointsByLanguage = collect(Resource::LANGUAGES)->mapWithKeys(
            fn (string $language) => [
                $language . 'Checkpoints' => $resource->checkpoints->where('language', $language)->sortBy('sort_order')->values(),
            ]
        );

        return view('admin.resources.show', [
            'resource' => $resource,
            ...$checkpointsByLanguage->all(),
        ]);
    }

    public function update(Request $request, Resource $resource): RedirectResponse
    {
        $data = $this->validateResource($request);

        if ($request->hasFile('thumbnail')) {
            $this->fileUploadService->delete($resource->thumbnail);
        }
        foreach (Resource::LANGUAGES as $language) {
            if ($request->hasFile("{$language}_thumbnail")) {
                $this->fileUploadService->delete($resource->{"{$language}_thumbnail"});
            }
        }

        $data = $this->attachThumbnails($request, $data);

        $resource->update($data);

        return redirect()
            ->route('admin.resources.index')
            ->with('status', 'Resource updated.');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $this->fileUploadService->delete($resource->thumbnail);
        foreach (Resource::LANGUAGES as $language) {
            $this->fileUploadService->delete($resource->{"{$language}_thumbnail"});
        }
        $resource->delete();

        return redirect()->route('admin.resources.index')->with('status', 'Resource deleted.');
    }

    private function attachThumbnails(Request $request, array $data): array
    {
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'resources');
        }

        foreach (Resource::LANGUAGES as $language) {
            if ($request->hasFile("{$language}_thumbnail")) {
                $data["{$language}_thumbnail"] = $this->fileUploadService->store($request->file("{$language}_thumbnail"), 'resources');
            }
        }

        return $data;
    }

    public function togglePublish(Request $request, Resource $resource): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $resource->update($data);

        return back()->with('status', $resource->is_published ? "\"{$resource->title}\" published." : "\"{$resource->title}\" set to draft.");
    }

    private function validateResource(Request $request): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
        ];

        foreach (Resource::LANGUAGES as $language) {
            $rules["{$language}_thumbnail"] = ['nullable', 'image', 'max:5120'];
            $rules["{$language}_youtube_url"] = ['nullable', 'string', 'max:1000'];
        }

        return $request->validate($rules);
    }
}
