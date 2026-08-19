<x-layout title="Users" subtitle="Everyone who has registered on Pre Sales School">

    @php
        $clientFilterFields = ['search', 'status', 'profile_status', 'salesperson_status', 'min_points', 'max_points'];
        $salespersonMap = ['none' => 'badge-slate', 'pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-slate'];
        $statusMap = ['active' => 'badge-green', 'inactive' => 'badge-slate', 'blocked' => 'bg-danger-light text-danger'];
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card icon="users" color="primary" :value="$stats['total_users']" label="Total Users" description="All registered users" :trend="$stats['signups_trend']" />
        <x-stat-card icon="check-circle" color="success" :value="$stats['active_users']" label="Active Users" description="Currently active" />
        <x-stat-card icon="star" color="chart-4" :value="$stats['completed_profiles']" label="Completed Profiles" description="Profile completion" />
        <x-stat-card icon="trending-up" color="warning" :value="$stats['avg_points']" label="Avg. Points" description="Average earning" />
    </div>

    <div class="mt-4 rounded-2xl border border-primary/20 bg-primary-light/40 p-5">
        <h2 class="mb-4 text-base font-bold text-primary">Filter Users</h2>
        <form method="GET" class="flex flex-wrap items-end gap-4">
        <div class="w-full sm:w-52">
            <label class="form-label">Searchh</label>
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="form-input pl-10">
            </div>
        </div>

        <div class="w-full sm:w-36">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">All Statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
            </select>
        </div>

        <div class="w-full sm:w-36">
            <label class="form-label">Profile</label>
            <select name="profile_status" class="form-input">
                <option value="">All Profiles</option>
                <option value="complete" @selected(request('profile_status') === 'complete')>Complete</option>
                <option value="incomplete" @selected(request('profile_status') === 'incomplete')>Incomplete</option>
            </select>
        </div>

        <div class="w-full sm:w-44">
            <label class="form-label">Salesperson</label>
            <select name="salesperson_status" class="form-input">
                <option value="">All Statuses</option>
                <option value="none" @selected(request('salesperson_status') === 'none')>None</option>
                <option value="pending" @selected(request('salesperson_status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('salesperson_status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('salesperson_status') === 'rejected')>Rejected</option>
            </select>
        </div>

        <div class="w-20">
            <label class="form-label">Min Pts</label>
            <input type="number" name="min_points" min="0" value="{{ request('min_points') }}" placeholder="0" class="form-input">
        </div>

        <div class="w-20">
            <label class="form-label">Max Pts</label>
            <input type="number" name="max_points" min="0" value="{{ request('max_points') }}" placeholder="—" class="form-input">
        </div>

        <button type="submit" class="btn-primary">
            <x-icon name="filter" class="h-4 w-4" />
            Apply Filters
        </button>

        @if (request()->anyFilled($clientFilterFields))
            <a href="{{ route('admin.clients') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                <x-icon name="refresh-cw" class="h-4 w-4" />
                Reset
            </a>
        @endif
        </form>
    </div>

    <div class="mt-4 card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">User</th>
                        <th class="px-5 py-3 font-semibold">Phone</th>
                        <th class="px-5 py-3 font-semibold">Earning Points</th>
                        <th class="px-5 py-3 font-semibold">Onboarding Score</th>
                        <th class="px-5 py-3 font-semibold">Profile</th>
                        <th class="px-5 py-3 font-semibold">Salesperson</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <tr>
                            <td class="flex items-center gap-3 px-5 py-3.5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </span>
                                <div class="leading-tight">
                                    <p class="font-medium text-slate-700">{{ $client->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $client->email }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $client->phone ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    <x-icon name="trending-up" class="h-3.5 w-3.5" />
                                    {{ $client->points->earned }}/{{ $client->points->total }} pts
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($client->assessmentScore->attempted)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-light px-2.5 py-1 text-xs font-semibold text-primary">
                                        <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                        {{ $client->assessmentScore->earned_points }}/{{ $client->assessmentScore->total_points }} pts
                                    </span>
                                @else
                                    <span class="badge badge-slate">Not attempted</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($client->profile_completed)
                                    <span class="badge badge-green"><x-icon name="check-circle" class="h-3.5 w-3.5" /> Complete</span>
                                @else
                                    <span class="badge badge-slate">Incomplete</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="badge {{ $salespersonMap[$client->salesperson_status] ?? 'badge-slate' }}">{{ ucfirst($client->salesperson_status) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <form method="POST" action="{{ route('admin.clients.status.update', $client) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="badge border-0 {{ $statusMap[$client->status] ?? 'badge-slate' }}">
                                        <option value="active" @selected($client->status === 'active')>Active</option>
                                        <option value="inactive" @selected($client->status === 'inactive')>Inactive</option>
                                        <option value="blocked" @selected($client->status === 'blocked')>Blocked</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.clients.show', $client) }}" title="View" class="rounded-lg bg-primary-light p-1.5 text-primary transition hover:bg-brand-200">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-client-{{ $client->id }}')" title="Edit" class="rounded-lg bg-primary-light p-1.5 text-primary transition hover:bg-brand-200">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>
                                    <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete {{ $client->name }}? This permanently removes their account and all related data.', target: $el })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete" class="rounded-lg bg-danger-light p-1.5 text-danger transition hover:bg-red-200">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <x-modal name="edit-client-{{ $client->id }}" max-width="sm">
                            <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                                @csrf
                                @method('PUT')

                                <div class="border-b border-slate-100 px-6 py-5">
                                    <h2 class="text-lg font-bold text-slate-800">Edit User</h2>
                                    <p class="text-sm text-slate-400">Update {{ $client->name }}'s basic details</p>
                                </div>

                                <div class="space-y-4 px-6 py-5">
                                    <div>
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" value="{{ $client->name }}" required class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" value="{{ $client->email }}" required class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" value="{{ $client->phone }}" class="form-input">
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                                    <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                                    <button type="submit" class="btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </x-modal>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                @if (request()->anyFilled($clientFilterFields))
                                    No users match your filters.
                                @else
                                    No users have registered yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($clients->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

</x-layout>
