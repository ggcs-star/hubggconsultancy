<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScriptItem;
use App\Models\ScriptTopic;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScriptItemController extends Controller
{
    private const MAX_VIDEO_UPLOAD_KB = 1048576; // 1GB

    private const MAX_DOCUMENT_UPLOAD_KB = 51200; // 50MB

    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function store(Request $request, ScriptTopic $topic): RedirectResponse
    {
        $data = $this->validateItem($request, isCreate: true);
        $data = $this->attachSource($request, $data);
        $data['script_topic_id'] = $topic->id;

        ScriptItem::create($data);

        return redirect()->route('admin.scripts.show', $topic)->with('status', 'Item added.');
    }

    public function update(Request $request, ScriptItem $item): RedirectResponse
    {
        $data = $this->validateItem($request, isCreate: false);

        if ($request->hasFile('file') || ($data['type'] === 'video' && $request->input('source') === 'link')) {
            $this->fileUploadService->delete($item->is_external ? null : $item->url);
            $data = $this->attachSource($request, $data);
        }

        $item->update($data);

        return redirect()->route('admin.scripts.show', $item->script_topic_id)->with('status', 'Item updated.');
    }

    public function destroy(ScriptItem $item): RedirectResponse
    {
        $topicId = $item->script_topic_id;

        $this->fileUploadService->delete($item->is_external ? null : $item->url);
        $item->delete();

        return redirect()->route('admin.scripts.show', $topicId)->with('status', 'Item deleted.');
    }

    public function togglePublish(Request $request, ScriptItem $item): RedirectResponse
    {
        $data = $request->validate(['is_published' => ['required', 'boolean']]);

        $item->update($data);

        return back()->with('status', $item->is_published ? "\"{$item->title}\" published." : "\"{$item->title}\" set to draft.");
    }

    private function validateItem(Request $request, bool $isCreate): array
    {
        $type = $request->input('type');
        $source = $type === 'video' ? $request->input('source', 'upload') : 'upload';

        $rules = [
            'type' => ['required', 'in:video,document'],
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($type === 'video') {
            $rules['source'] = ['required', 'in:upload,link'];
        }

        if ($source === 'link') {
            $rules['video_url'] = ['required', 'url', 'max:2048'];
        } else {
            $maxKb = $type === 'video' ? self::MAX_VIDEO_UPLOAD_KB : self::MAX_DOCUMENT_UPLOAD_KB;
            $rules['file'] = [$isCreate ? 'required' : 'nullable', 'file', "max:{$maxKb}"];
        }

        return $request->validate($rules);
    }

    private function attachSource(Request $request, array $data): array
    {
        if (($data['type'] ?? null) === 'video' && ($data['source'] ?? null) === 'link') {
            $data['url'] = $data['video_url'];
            $data['is_external'] = true;
            $data['original_filename'] = null;
            $data['mime_type'] = null;
            $data['file_size'] = null;
        } elseif ($request->hasFile('file')) {
            $file = $request->file('file');

            $data['url'] = $this->fileUploadService->store($file, 'scripts');
            $data['is_external'] = false;
            $data['original_filename'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getClientMimeType();
            $data['file_size'] = $file->getSize();
        }

        unset($data['file'], $data['video_url'], $data['source']);

        return $data;
    }
}
