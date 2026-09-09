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

    private const MAX_AUDIO_UPLOAD_KB = 102400; // 100MB

    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function store(Request $request, ScriptTopic $topic): RedirectResponse
    {
        $data = $this->validateItem($request, isCreate: true);
        $data = $this->attachSource($request, $data);
        $data['script_topic_id'] = $topic->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'script-thumbnails');
        }

        ScriptItem::create($data);

        return redirect()->route('admin.scripts.show', $topic)->with('status', 'Item added.');
    }

    public function update(Request $request, ScriptItem $item): RedirectResponse
    {
        $data = $this->validateItem($request, isCreate: false);

        if ($request->hasFile('file') || $request->input('source') === 'link') {
            $this->fileUploadService->delete($item->is_external ? null : $item->url);
            $data = $this->attachSource($request, $data);
        }

        if ($request->hasFile('thumbnail')) {
            $this->fileUploadService->delete($item->thumbnail);
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'script-thumbnails');
        }

        $item->update($data);

        return redirect()->route('admin.scripts.show', $item->script_topic_id)->with('status', 'Item updated.');
    }

    public function destroy(ScriptItem $item): RedirectResponse
    {
        $topicId = $item->script_topic_id;

        $this->fileUploadService->delete($item->is_external ? null : $item->url);
        $this->fileUploadService->delete($item->thumbnail);
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
        $source = $request->input('source', 'upload');

        $rules = [
            'type' => ['required', 'in:video,document,audio'],
            'title' => ['required', 'string', 'max:255'],
            'language' => ['required', 'in:english,hindi,gujarati,marathi,telugu,kannada'],
            'source' => ['required', 'in:upload,link'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($source === 'link') {
            $rules['media_url'] = ['required', 'url', 'max:2048'];
        } else {
            $maxKb = match ($type) {
                'video' => self::MAX_VIDEO_UPLOAD_KB,
                'audio' => self::MAX_AUDIO_UPLOAD_KB,
                default => self::MAX_DOCUMENT_UPLOAD_KB,
            };
            $rules['file'] = [$isCreate ? 'required' : 'nullable', 'file', "max:{$maxKb}"];
        }

        return $request->validate($rules);
    }

    private function attachSource(Request $request, array $data): array
    {
        if (($data['source'] ?? null) === 'link') {
            $data['url'] = $data['media_url'];
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

        unset($data['file'], $data['media_url'], $data['source']);

        return $data;
    }
}
