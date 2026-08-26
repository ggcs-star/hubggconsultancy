@php
    $isEdit = (bool) $successStory;
    $existingMetrics = old('metrics', $isEdit ? ($successStory->metrics ?? []) : []);
    if (empty($existingMetrics)) {
        $existingMetrics = [['label' => '', 'before' => '', 'after' => '']];
    }
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route('admin.success-stories.update', $successStory) : route('admin.success-stories.store') }}"
    class="flex max-h-[85vh] flex-col"
    enctype="multipart/form-data"
    x-data="{ metrics: @js($existingMetrics) }"
>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h2 class="text-lg font-bold text-slate-800">{{ $isEdit ? 'Edit Success Story' : 'Add Success Story' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <div class="space-y-5 overflow-y-auto px-6 py-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $successStory->name : '') }}" required class="form-input" placeholder="e.g. Priya Patel">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div>
                <label class="form-label">Designation <span class="font-normal text-slate-400">(optional)</span></label>
                <input type="text" name="designation" value="{{ old('designation', $isEdit ? $successStory->designation : '') }}" class="form-input" placeholder="e.g. Sales Executive">
            </div>
        </div>

        <div>
            <label class="form-label">Headline <span class="font-normal text-slate-400">(optional)</span></label>
            <input type="text" name="headline" value="{{ old('headline', $isEdit ? $successStory->headline : '') }}" class="form-input" placeholder="e.g. From New Joiner to Top Performer">
        </div>

        <div>
            <label class="form-label">Testimonial</label>
            <textarea name="testimonial" rows="3" required class="form-input" placeholder="Quote from the person">{{ old('testimonial', $isEdit ? $successStory->testimonial : '') }}</textarea>
            <x-input-error :messages="$errors->get('testimonial')" class="mt-1" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label class="form-label">Before / After Metrics <span class="font-normal text-slate-400">(optional)</span></label>
                <button type="button" x-on:click="metrics.push({ label: '', before: '', after: '' })" class="text-xs font-semibold text-brand-600 hover:text-brand-700">+ Add Metric</button>
            </div>
            <div class="mt-2 space-y-2">
                <template x-for="(metric, index) in metrics" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="text" :name="`metrics[${index}][label]`" x-model="metric.label" placeholder="Metric (e.g. Sales Conversion)" class="form-input">
                        <input type="text" :name="`metrics[${index}][before]`" x-model="metric.before" placeholder="Before (e.g. 12%)" class="form-input w-28 shrink-0">
                        <input type="text" :name="`metrics[${index}][after]`" x-model="metric.after" placeholder="After (e.g. 27%)" class="form-input w-28 shrink-0">
                        <button type="button" x-on:click="metrics.splice(index, 1)" class="shrink-0 text-slate-400 hover:text-red-600">
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>
                </template>
            </div>
            <x-input-error :messages="$errors->get('metrics')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">Business Impact <span class="font-normal text-slate-400">(optional)</span></label>
            <textarea name="business_impact" rows="2" class="form-input" placeholder="Wider impact on the business">{{ old('business_impact', $isEdit ? $successStory->business_impact : '') }}</textarea>
        </div>

        <div>
            <label class="form-label">Video URL <span class="font-normal text-slate-400">(optional)</span></label>
            <input type="url" name="video_url" value="{{ old('video_url', $isEdit ? $successStory->video_url : '') }}" class="form-input" placeholder="https://...">
            <x-input-error :messages="$errors->get('video_url')" class="mt-1" />
        </div>

        <div>
            <label class="form-label">Photo <span class="font-normal text-slate-400">(optional)</span></label>
            @if ($isEdit && $successStory->photoUrl())
                <img src="{{ $successStory->photoUrl() }}" alt="" class="mb-2 h-16 w-16 rounded-full object-cover">
            @endif
            <input type="file" name="photo" accept="image/*" class="form-input">
            <x-input-error :messages="$errors->get('photo')" class="mt-1" />
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
        <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Save Changes' : 'Add Success Story' }}</button>
    </div>
</form>
