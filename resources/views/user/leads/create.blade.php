<x-layout title="Add Lead" title-icon="users" subtitle="Add a new lead for yourself to work">

    <a href="{{ route('user.leads.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Leads
    </a>

    <form method="POST" action="{{ route('user.leads.store') }}" class="mt-4">
        @csrf

        @include('user.leads._form-fields')

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('user.leads.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="btn-primary">Add Lead</button>
        </div>
    </form>

    @include('user.leads._quick-add-campaign-modal')

</x-layout>
