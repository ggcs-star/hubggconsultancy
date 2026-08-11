<x-layout title="Dashboard" subtitle="Overview of clients, applications and training across Pre Sales School">

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="rounded-xl bg-brand-50 p-2.5 text-brand-700"><x-icon name="users" /></span>
            </div>
            <p class="mt-4 text-2xl font-extrabold text-slate-800">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-slate-400">Total Clients</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="rounded-xl bg-amber-50 p-2.5 text-amber-600"><x-icon name="briefcase" /></span>
            </div>
            <p class="mt-4 text-2xl font-extrabold text-slate-800">{{ $stats['pending_applications'] }}</p>
            <p class="text-sm text-slate-400">Pending Applications</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600"><x-icon name="badge" /></span>
            </div>
            <p class="mt-4 text-2xl font-extrabold text-slate-800">{{ $stats['approved_salespeople'] }}</p>
            <p class="text-sm text-slate-400">Approved Salespeople</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="rounded-xl bg-sky-50 p-2.5 text-sky-600"><x-icon name="check-circle" /></span>
            </div>
            <p class="mt-4 text-2xl font-extrabold text-slate-800">{{ $stats['profile_completed'] }}</p>
            <p class="text-sm text-slate-400">Profiles Completed</p>
        </div>
    </div>

    <div class="card mt-6">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Recent Clients</h2>
            <a href="{{ route('admin.clients') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">Client</th>
                        <th class="px-5 py-3 font-semibold">Email</th>
                        <th class="px-5 py-3 font-semibold">Profile</th>
                        <th class="px-5 py-3 font-semibold">Salesperson Status</th>
                        <th class="px-5 py-3 font-semibold">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentUsers as $user)
                        <tr>
                            <td class="flex items-center gap-3 px-5 py-3.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <span class="font-medium text-slate-700">{{ $user->name }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $user->email }}</td>
                            <td class="px-5 py-3.5">
                                @if ($user->profile_completed)
                                    <span class="badge badge-green"><x-icon name="check-circle" class="h-3.5 w-3.5" /> Complete</span>
                                @else
                                    <span class="badge badge-slate">Incomplete</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $map = ['none' => 'badge-slate', 'pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-slate'];
                                @endphp
                                <span class="badge {{ $map[$user->salesperson_status] }}">{{ ucfirst($user->salesperson_status) }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">No clients have registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layout>
