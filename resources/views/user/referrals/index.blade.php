<x-layout
    title="Referral List"
    subtitle="Applicants registered through your referral link"
>
    <div class="rounded-xl border border-slate-200 bg-white">

        {{-- Search --}}
        <form
            method="GET"
            action="{{ route('user.referrals.index') }}"
            class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row"
        >
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search name, mobile, profession, interest, city..."
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-100"
            />

            <button
                type="submit"
                class="rounded-lg bg-violet-600 px-5 py-2 text-sm font-medium text-white hover:bg-violet-700"
            >
                Search
            </button>

            @if ($search !== '')
                <a
                    href="{{ route('user.referrals.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm text-slate-600 hover:bg-slate-50"
                >
                    Clear
                </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Name</th>
                        <th class="px-4 py-3 font-semibold">Mobile</th>
                        <th class="px-4 py-3 font-semibold">WhatsApp</th>
                        <th class="px-4 py-3 font-semibold">Profession</th>
                        <th class="px-4 py-3 font-semibold">Primary Interest</th>
                        <th class="px-4 py-3 font-semibold">City</th>
                        <th class="px-4 py-3 font-semibold">State</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($applications as $application)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-800">
                                {{ $application->full_name ?: '—' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $application->mobile_number ?: '—' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $application->whatsapp_number ?: '—' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $application->profession ?: '—' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $application->primary_interest ?: '—' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $application->city ?: '—' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $application->state ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                {{ $search !== '' ? 'No matching applicants found.' : 'No referred applicants found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($applications->hasPages())
            <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Showing {{ $applications->firstItem() }}–{{ $applications->lastItem() }}
                    of {{ $applications->total() }} applicants
                </p>

                <div>
                    {{ $applications->withQueryString()->links() }}
                </div>
            </div>
        @endif

    </div>
</x-layout>
