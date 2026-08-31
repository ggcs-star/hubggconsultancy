<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuccessStoryController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(): View
    {
        $successStories = SuccessStory::ordered()->paginate(10)->withQueryString();

        return view('admin.success-stories.index', [
            'successStories' => $successStories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSuccessStory($request);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->fileUploadService->store($request->file('photo'), 'success-stories');
        }

        SuccessStory::create($data);

        return redirect()->route('admin.success-stories.index')->with('status', 'Success story added.');
    }

    public function update(Request $request, SuccessStory $successStory): RedirectResponse
    {
        $data = $this->validateSuccessStory($request);

        if ($request->hasFile('photo')) {
            $this->fileUploadService->delete($successStory->photo);
            $data['photo'] = $this->fileUploadService->store($request->file('photo'), 'success-stories');
        }

        $successStory->update($data);

        return redirect()->route('admin.success-stories.index')->with('status', 'Success story updated.');
    }

    public function destroy(SuccessStory $successStory): RedirectResponse
    {
        $this->fileUploadService->delete($successStory->photo);
        $successStory->delete();

        return redirect()->route('admin.success-stories.index')->with('status', 'Success story deleted.');
    }

    public function toggleActive(Request $request, SuccessStory $successStory): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);

        $successStory->update($data);

        return back()->with('status', $successStory->is_active ? 'Success story shown.' : 'Success story hidden.');
    }

    private function validateSuccessStory(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'testimonial' => ['required', 'string'],
            'business_impact' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'metrics' => ['nullable', 'array'],
            'metrics.*.label' => ['nullable', 'string', 'max:255'],
            'metrics.*.before' => ['nullable', 'string', 'max:100'],
            'metrics.*.after' => ['nullable', 'string', 'max:100'],
        ]);

        $data['metrics'] = collect($data['metrics'] ?? [])
            ->filter(fn ($metric) => filled($metric['label'] ?? null))
            ->values()
            ->all();

        return $data;
    }
}
