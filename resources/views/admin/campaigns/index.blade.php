<x-layout title="Campaigns" title-icon="calendar" subtitle="Group leads by marketing campaign and track performance">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
            Back to Leads
        </a>

        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-campaign')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add Campaign
        </button>
    </div>

    <form method="GET" class="mt-4 w-full sm:max-w-sm">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search campaigns..." class="form-input pl-10 {{ request('search') ? 'pr-9' : '' }}">
            @if (request('search'))
                <a href="{{ route('admin.campaigns.index') }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <x-icon name="x" class="h-4 w-4" />
                </a>
            @endif
        </div>
    </form>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($campaigns as $campaign)
            <div class="card flex flex-col p-5">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-bold text-slate-800">{{ $campaign->name }}</p>
                    <div class="flex shrink-0 items-center gap-1">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-campaign-{{ $campaign->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </button>
                        <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $campaign->name }}\'? Its leads will be kept but unlinked from this campaign.', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                @if ($campaign->description)
                    <p class="mt-2 text-sm text-slate-500 line-clamp-2">{{ $campaign->description }}</p>
                @endif

                @if ($campaign->starts_at || $campaign->ends_at)
                    <p class="mt-2 text-xs text-slate-400">
                        {{ $campaign->starts_at?->format('d M Y') ?? '—' }} &ndash; {{ $campaign->ends_at?->format('d M Y') ?? '—' }}
                    </p>
                @endif

                <p class="mt-3 flex-1 text-sm text-slate-600">{{ $campaign->leads_count }} {{ Str::plural('lead', $campaign->leads_count) }}</p>

                <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View Performance</a>

                    <form method="POST" action="{{ route('admin.campaigns.active.toggle', $campaign) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="{{ $campaign->is_active ? '0' : '1' }}">
                        <button type="submit" class="badge {{ $campaign->is_active ? 'badge-green' : 'badge-slate' }}">
                            {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                </div>
            </div>

            <x-modal name="edit-campaign-{{ $campaign->id }}" :show="false" max-width="lg">
                @include('admin.campaigns._form', ['campaign' => $campaign])
            </x-modal>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400">
                @if (request('search'))
                    No campaigns match your search.
                @else
                    No campaigns yet. Click "Add Campaign" to create the first one.
                @endif
            </div>
        @endforelse
    </div>

    @if ($campaigns->hasPages())
        <div class="mt-6">
            {{ $campaigns->links() }}
        </div>
    @endif

    <x-modal name="add-campaign" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.campaigns._form', ['campaign' => null])
    </x-modal>

</x-layout>
