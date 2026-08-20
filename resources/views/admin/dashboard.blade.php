<x-layout title="Dashboard" subtitle="Overview of users, applications and training across GG Hub">

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card icon="users" color="primary" :value="$stats['total_users']" label="Total Users" :trend="$usersTrend" />
        <x-stat-card icon="briefcase" color="warning" :value="$stats['pending_applications']" label="Pending Applications" />
        <x-stat-card icon="badge" color="success" :value="$stats['approved_salespeople']" label="Approved Salespeople" />
        <x-stat-card icon="check-circle" color="chart-4" :value="$stats['profile_completed']" label="Profiles Completed" />
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card icon="academic-cap" color="primary" :value="$stats['total_courses']" label="Courses Published" />
        <x-stat-card icon="users" color="chart-4" :value="$stats['total_enrollments']" label="Course Enrollments" :trend="$enrollmentsTrend" />
        <x-stat-card icon="badge" color="success" :value="$stats['certificates_issued']" label="Certificates Issued" :trend="$certificatesTrend" />
        <x-stat-card icon="briefcase" color="warning" :value="$stats['open_tickets']" label="Open Support Tickets" :trend="$ticketsTrend" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-bold text-slate-800">User Signups (Last 6 Months)</h2>
            <div class="mt-4 h-64">
                <canvas id="signupsChart"></canvas>
            </div>
        </div>
        <div class="card p-5">
            <h2 class="font-bold text-slate-800">Top Courses by Enrollment</h2>
            <div class="mt-4 h-64">
                <canvas id="topCoursesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-bold text-slate-800">Salesperson Application Status</h2>
            <div class="mt-4 h-64">
                <canvas id="applicationStatusChart"></canvas>
            </div>
        </div>
        <div class="card p-5">
            <h2 class="font-bold text-slate-800">Support Ticket Status</h2>
            <div class="mt-4 h-64">
                <canvas id="ticketStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Top 5 Users by Points</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">User</th>
                        <th class="px-5 py-3 font-semibold">Email</th>
                        <th class="px-5 py-3 font-semibold">Points Earned</th>
                        <th class="px-5 py-3 font-semibold">Accuracy</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topUsers as $entry)
                        <tr>
                            <td class="flex items-center gap-3 px-5 py-3.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                    {{ strtoupper(substr($entry->user->name, 0, 1)) }}
                                </span>
                                <span class="font-medium text-slate-700">{{ $entry->user->name }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $entry->user->email }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $entry->earned }} / {{ $entry->total }} pts</td>
                            <td class="px-5 py-3.5">
                                @if (is_null($entry->percent))
                                    <span class="badge badge-slate">—</span>
                                @else
                                    <span class="badge {{ $entry->percent >= 70 ? 'badge-green' : 'badge-amber' }}">{{ $entry->percent }}%</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">No quiz points have been earned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-6">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Recent Users</h2>
            <a href="{{ route('admin.clients') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">User</th>
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
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">No users have registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-6">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">Recent Support Tickets</h2>
            <a href="{{ route('admin.support.tickets.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">Ticket</th>
                        <th class="px-5 py-3 font-semibold">User</th>
                        <th class="px-5 py-3 font-semibold">Issue Type</th>
                        <th class="px-5 py-3 font-semibold">Priority</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentTickets as $ticket)
                        <tr>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.support.tickets.show', $ticket) }}" class="font-semibold text-brand-700 hover:text-brand-800">
                                    #{{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-700">{{ $ticket->user->name ?? 'Unknown User' }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->user->email ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $ticket->issueType->name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $priorityMap = ['urgent' => 'badge-amber', 'high' => 'badge-amber', 'medium' => 'badge-slate', 'low' => 'badge-slate'];
                                @endphp
                                <span class="badge {{ $priorityMap[$ticket->priority] ?? 'badge-slate' }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $ticketStatusMap = ['open' => 'badge-amber', 'in_progress' => 'badge-amber', 'waiting_for_user' => 'badge-slate', 'resolved' => 'badge-green', 'closed' => 'badge-slate'];
                                    $ticketStatusLabel = ucfirst(str_replace('_', ' ', $ticket->status));
                                @endphp
                                <span class="badge {{ $ticketStatusMap[$ticket->status] ?? 'badge-slate' }}">{{ $ticketStatusLabel }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">{{ $ticket->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No support tickets have been raised yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                PSSCharts.renderLineChart('signupsChart', @json($signups['labels']), @json($signups['data']), { label: 'Signups' });
                PSSCharts.renderBarChart('topCoursesChart', @json($topCourses['labels']), @json($topCourses['data']), { horizontal: true, color: PSSCharts.PALETTE.sky });
                PSSCharts.renderDoughnutChart('applicationStatusChart', @json($applicationStatus['labels']), @json($applicationStatus['data']));
                PSSCharts.renderDoughnutChart('ticketStatusChart', @json($ticketStatus['labels']), @json($ticketStatus['data']));
            });
        </script>
    @endpush

</x-layout>
