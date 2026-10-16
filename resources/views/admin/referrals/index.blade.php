
<x-layout
    title="Referral List"
    title-icon="users"
    subtitle="Manage and view all referral applications"
>
    <div class="space-y-5">

        {{-- Search and Date Filters --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"/>
                            <path stroke-linecap="round" d="m16 16 4 4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Search & Filters
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Search applications by applicant, profession, interest or referral details.
                        </p>
                    </div>
                </div>
            </div>

            <form method="GET"
                  action="{{ route('admin.referrals.index') }}"
                  class="space-y-4 p-5 sm:p-6">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="xl:col-span-2">
                        <label for="search"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Search applications
                        </label>
                        <input
                            id="search"
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Name, mobile, profession, interest..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                        >
                    </div>

                    <div>
                        <label for="from_date"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            From date
                        </label>
                        <input
                            id="from_date"
                            type="date"
                            name="from_date"
                            value="{{ $fromDate }}"
                            max="{{ $toDate ?: '' }}"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                        >
                    </div>

                    <div>
                        <label for="to_date"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            To date
                        </label>
                        <input
                            id="to_date"
                            type="date"
                            name="to_date"
                            value="{{ $toDate }}"
                            min="{{ $fromDate ?: '' }}"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                        >
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.referrals.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 12a9 9 0 1 0 2.64-6.36L3 8"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 3v5h5"/>
                        </svg>
                        Reset
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <circle cx="11" cy="11" r="7"/>
                            <path stroke-linecap="round" d="m16 16 4 4"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        {{-- Referral Applications Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Referral Applications
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Applicant details, profession, interests and referral contacts
                    </p>
                </div>

                <div class="inline-flex w-fit items-center gap-2 rounded-full bg-indigo-50 px-3.5 py-2 text-sm font-semibold text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20 8v6m3-3h-6"/>
                    </svg>
                    {{ number_format($applications->total()) }} records
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] text-left text-sm">
                    <thead class="bg-slate-50">
                        <tr class="border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th class="whitespace-nowrap px-5 py-4">#</th>
                            <th class="px-5 py-4">Applicant</th>
                            <th class="px-5 py-4">Mobile</th>
                            <!-- <th class="px-5 py-4">WhatsApp</th> -->
                            <th class="px-5 py-4">Profession</th>
                            <th class="px-5 py-4">Interest</th>
                            <th class="px-5 py-4">Referred By</th>
                            <th class="px-5 py-4">Referrer Mobile</th>
                            <th class="whitespace-nowrap px-5 py-4">Submitted On</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($applications as $application)
                            <tr class="transition-colors hover:bg-indigo-50/30">

                                <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                                    {{ $applications->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-700">
                                            {{ \Illuminate\Support\Str::upper(
                                                \Illuminate\Support\Str::substr(
                                                    trim($application->full_name ?? 'A'),
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-800">
                                                {{ $application->full_name ?: '—' }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-400">
                                                ID: {{ $application->id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($application->mobile_number)
                                        <a href="tel:{{ $application->mobile_number }}"
                                           class="font-medium text-slate-700 hover:text-indigo-600">
                                            {{ $application->mobile_number }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                               
                                <td class="px-5 py-4">
                                    @if ($application->profession)
                                        <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700">
                                            {{ $application->profession }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if ($application->primary_interest)
                                        <span class="inline-flex max-w-[220px] whitespace-normal rounded-lg bg-violet-50 px-2.5 py-1.5 text-xs font-semibold leading-5 text-violet-700">
                                            {{ $application->primary_interest }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="font-medium text-slate-700">
                                        {{ $application->referral_name ?: '—' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($application->referral_mobile)
                                        <a href="tel:{{ $application->referral_mobile }}"
                                           class="font-medium text-slate-700 hover:text-indigo-600">
                                            {{ $application->referral_mobile }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($application->created_at)
                                        <p class="font-medium text-slate-700">
                                            {{ $application->created_at->format('d M Y') }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $application->created_at->format('h:i A') }}
                                        </p>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                             stroke-width="1.6">
                                            <circle cx="11" cy="11" r="7"/>
                                            <path stroke-linecap="round" d="m16 16 4 4"/>
                                        </svg>
                                    </div>

                                    <h3 class="mt-4 text-base font-semibold text-slate-800">
                                        No applications found
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Try a different search term or date range.
                                    </p>

                                    <a href="{{ route('admin.referrals.index') }}"
                                       class="mt-4 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                        Clear filters
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($applications->total() > 0)
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-sm text-slate-500">
                        Showing
                        <span class="font-semibold text-slate-800">
                            {{ $applications->firstItem() }}
                        </span>
                        to
                        <span class="font-semibold text-slate-800">
                            {{ $applications->lastItem() }}
                        </span>
                        of
                        <span class="font-semibold text-slate-800">
                            {{ number_format($applications->total()) }}
                        </span>
                        applications
                    </p>

                    <div class="shrink-0">
                        {{ $applications->onEachSide(1)->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
