<x-layout title="My Team" title-icon="users" subtitle="Salespersons and the teams they've referred">

    <form method="GET" class="w-full sm:max-w-sm">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="form-input pl-10 {{ request('search') ? 'pr-9' : '' }}">
            @if (request('search'))
                <a href="{{ route('admin.teams.index') }}" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <x-icon name="x" class="h-4 w-4" />
                </a>
            @endif
        </div>
    </form>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Salespersons With a Team</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $referrers->total() }} {{ Str::plural('salesperson', $referrers->total()) }}</p>
        </div>

        @if ($referrers->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="users" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No teams yet</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Once someone signs up using a salesperson's referral link, their team will show up here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Salesperson</th>
                            <th class="px-5 py-3 font-semibold">Team Size</th>
                            <th class="px-5 py-3 font-semibold">Total Earnings</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($referrers as $referrer)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                            {{ strtoupper(substr($referrer->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $referrer->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $referrer->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $referrer->team_members_count }}</td>
                                <td class="px-5 py-4 font-semibold text-brand-700">₹{{ number_format($referrer->total_earnings, 2) }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.teams.show', $referrer) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View Team</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($referrers->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $referrers->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layout>
