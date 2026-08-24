@php
    $statusBadge = [
        'not_started' => ['label' => 'Not Started', 'class' => 'badge-slate'],
        'in_progress' => ['label' => 'In Progress', 'class' => 'badge-amber'],
        'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
        'passed' => ['label' => 'Passed', 'class' => 'badge-green'],
        'failed' => ['label' => 'Failed', 'class' => 'bg-red-50 text-red-600'],
    ];
    $resultFilterFields = ['search', 'status', 'min_score', 'max_score', 'submitted_from', 'submitted_to'];
@endphp

<form method="GET" class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
    <input type="hidden" name="tab" value="results">

    <div class="flex flex-wrap items-end gap-4">
        <div class="w-full sm:w-52">
            <label class="form-label">Search</label>
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="form-input pl-10">
            </div>
        </div>

        <div class="w-full sm:w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">All Statuses</option>
                <option value="not_started" @selected(request('status') === 'not_started')>Not Started</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                <option value="pending_review" @selected(request('status') === 'pending_review')>Pending Review</option>
                <option value="passed" @selected(request('status') === 'passed')>Passed</option>
                <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            </select>
        </div>

        <div class="w-20">
            <label class="form-label">Min %</label>
            <input type="number" name="min_score" min="0" max="100" value="{{ request('min_score') }}" placeholder="0" class="form-input">
        </div>

        <div class="w-20">
            <label class="form-label">Max %</label>
            <input type="number" name="max_score" min="0" max="100" value="{{ request('max_score') }}" placeholder="100" class="form-input">
        </div>

        <div class="w-full sm:w-40">
            <label class="form-label">Submitted From</label>
            <input type="date" name="submitted_from" value="{{ request('submitted_from') }}" class="form-input">
        </div>

        <div class="w-full sm:w-40">
            <label class="form-label">Submitted To</label>
            <input type="date" name="submitted_to" value="{{ request('submitted_to') }}" class="form-input">
        </div>
    </div>

    <div class="mt-4 flex items-center gap-4">
        <button type="submit" class="btn-primary">Apply Filters</button>

        @if (request()->anyFilled($resultFilterFields))
            <a href="{{ route('admin.onboarding-assessment.index', ['tab' => 'results']) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                Reset Filters
            </a>
        @endif
    </div>
</form>

<div class="mt-4 card">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-xs uppercase tracking-wider text-slate-400">
                    <th class="px-5 py-3 font-semibold">User</th>
                    <th class="px-5 py-3 font-semibold">Interest</th>
                    <th class="px-5 py-3 font-semibold">Score</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold">Submitted</th>
                    <th class="px-5 py-3 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($results as $row)
                    @php $rowScore = $row['score']; @endphp
                    <tr>
                        <td class="flex items-center gap-3 px-5 py-3.5">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                {{ strtoupper(substr($row['user']->name, 0, 1)) }}
                            </span>
                            <div class="leading-tight">
                                <p class="font-medium text-slate-700">{{ $row['user']->name }}</p>
                                <p class="text-xs text-slate-400">{{ $row['user']->email }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @forelse ($row['user']->interests as $interest)
                                <span class="badge badge-slate mb-1 mr-1">{{ $interest->name }}</span>
                            @empty
                                <span class="text-slate-400">—</span>
                            @endforelse
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">
                            @if ($rowScore->attempted)
                                {{ $rowScore->earned_points }}/{{ $rowScore->total_points }} pts
                                @if (! is_null($rowScore->percent))
                                    <span class="text-xs text-slate-400">({{ $rowScore->percent }}%)</span>
                                @endif
                                <p class="text-xs text-slate-400">{{ $rowScore->attempted_quiz_count }}/{{ $rowScore->quiz_count }} {{ Str::plural('quiz', $rowScore->quiz_count) }} done</p>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="badge {{ $statusBadge[$rowScore->status]['class'] }}">{{ $statusBadge[$rowScore->status]['label'] }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-400">{{ $rowScore->submitted_at?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if ($row['user']->salesperson_status === 'approved')
                                    <span class="badge badge-green">Salesperson</span>
                                @else
                                    <form method="POST" action="{{ route('admin.salesperson-applications.approve', $row['user']) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Approve {{ $row['user']->name }} as a salesperson?', target: $el })">
                                        @csrf
                                        <button type="submit" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Approve Salesperson</button>
                                    </form>
                                @endif
                                @if ($rowScore->attempted)
                                    <a href="{{ route('admin.onboarding-assessment.results.show', $row['user']) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View</a>
                                    <form method="POST" action="{{ route('admin.onboarding-assessment.results.retake', $row['user']) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Allow {{ $row['user']->name }} to retake every quiz? Their current answers and score will be cleared.', target: $el })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Retake All</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                            @if (request()->anyFilled($resultFilterFields))
                                No users match your filters.
                            @else
                                No users yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($results->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">
            {{ $results->links() }}
        </div>
    @endif
</div>
