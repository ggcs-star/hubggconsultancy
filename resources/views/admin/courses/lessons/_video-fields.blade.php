<div>
    <x-input-label value="Video Source *" class="uppercase text-xs tracking-wide" />
    <div class="mt-1.5 flex gap-4">
        <label class="flex items-center gap-2 text-sm text-secondary-dark">
            <input type="radio" name="video_source" value="upload" x-model="source" class="border-app-border text-primary focus:ring-primary">
            Upload video file
        </label>
        <label class="flex items-center gap-2 text-sm text-secondary-dark">
            <input type="radio" name="video_source" value="youtube" x-model="source" class="border-app-border text-primary focus:ring-primary">
            YouTube / embeddable URL
        </label>
    </div>
    <x-input-error :messages="$errors->get('video_source')" class="mt-2" />
</div>

<div x-show="source === 'upload'" x-cloak>
    @isset($lesson)
        @if ($lesson->video_path)
            <p class="mb-2 text-xs text-secondary">Current file: {{ basename($lesson->video_path) }} — uploading a new one replaces it.</p>
        @endif
    @endisset
    <input name="video" type="file" accept="video/mp4,video/webm,video/ogg"
        class="w-full rounded-lg border-app-border text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-surface-alt file:px-3 file:py-1.5 file:text-sm" />
    <p class="mt-1 text-xs text-secondary">MP4/WebM/Ogg, up to 300MB.</p>
    <x-input-error :messages="$errors->get('video')" class="mt-2" />
</div>

<div x-show="source === 'youtube'" x-cloak>
    <textarea name="video_url" rows="2" placeholder="Paste a YouTube link, e.g. https://www.youtube.com/watch?v=... — or paste the full &lt;iframe&gt; embed code from YouTube's Share &rarr; Embed option"
        class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('video_url', $lesson->video_url ?? '') }}</textarea>
    <p class="mt-1 text-xs text-secondary">Accepts a plain URL or a full pasted embed code — the link is extracted automatically.</p>
    <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
</div>
