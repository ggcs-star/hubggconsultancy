<x-layout title="Edit Contest" title-icon="gift" :subtitle="$contest->name">

    <a href="{{ route('admin.contests.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Contests
    </a>

    <form method="POST" action="{{ route('admin.contests.update', $contest) }}" class="mt-4">
        @csrf
        @method('PUT')

        @include('admin.contests._form-fields', ['contest' => $contest])

        <div class="sticky bottom-0 mt-6 flex justify-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <button type="submit" name="submit_action" value="draft" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Save as Draft</button>
            <button type="submit" name="submit_action" value="publish" class="btn-primary">{{ $contest->is_active ? 'Save Changes' : 'Publish / Activate' }}</button>
        </div>
    </form>

    @include('admin.contests._quick-add-target-type-modal')

</x-layout>
