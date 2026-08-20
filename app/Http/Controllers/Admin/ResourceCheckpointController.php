<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceCheckpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResourceCheckpointController extends Controller
{
    public function store(Request $request, Resource $resource): RedirectResponse
    {
        $data = $this->validateCheckpoint($request);

        ResourceCheckpoint::create([
            'resource_id' => $resource->id,
            'language' => $data['language'],
            'timestamp_seconds' => $data['timestamp_seconds'],
            'title' => $data['title'],
        ]);

        return redirect()->route('admin.resources.show', $resource)->with('status', 'Checkpoint added.');
    }

    public function update(Request $request, ResourceCheckpoint $checkpoint): RedirectResponse
    {
        $data = $this->validateCheckpoint($request);

        $checkpoint->update([
            'language' => $data['language'],
            'timestamp_seconds' => $data['timestamp_seconds'],
            'title' => $data['title'],
        ]);

        return redirect()->route('admin.resources.show', $checkpoint->resource_id)->with('status', 'Checkpoint updated.');
    }

    public function destroy(ResourceCheckpoint $checkpoint): RedirectResponse
    {
        $resourceId = $checkpoint->resource_id;
        $checkpoint->delete();

        return redirect()->route('admin.resources.show', $resourceId)->with('status', 'Checkpoint deleted.');
    }

    private function validateCheckpoint(Request $request): array
    {
        $validated = $request->validate([
            'language' => ['required', 'in:hindi,english'],
            'minutes' => ['required', 'integer', 'min:0'],
            'seconds' => ['required', 'integer', 'min:0', 'max:59'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        return [
            'language' => $validated['language'],
            'timestamp_seconds' => ($validated['minutes'] * 60) + $validated['seconds'],
            'title' => $validated['title'] ?? null,
        ];
    }
}
