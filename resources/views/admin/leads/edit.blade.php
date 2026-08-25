<x-layout title="Edit Lead" title-icon="users" :subtitle="$lead->name">

    <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Leads
    </a>

    <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="mt-4">
        @csrf
        @method('PUT')

        @include('admin.leads._form-fields', ['lead' => $lead])

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.leads.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="btn-primary">Save Changes</button>
        </div>
    </form>

    @include('admin.leads._quick-add-campaign-modal')

</x-layout>
