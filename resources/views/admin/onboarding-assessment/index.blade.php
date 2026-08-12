<x-layout title="Onboarding Assessment" subtitle="Configure the assessment salespeople take, and review results">

    @php
        $questionTypeLabels = ['radio' => 'Single choice', 'checkbox' => 'Multiple choice', 'text' => 'Text answer'];
        $statusBadge = [
            'not_started' => ['label' => 'Not Started', 'class' => 'badge-slate'],
            'pending_review' => ['label' => 'Pending Review', 'class' => 'badge-amber'],
            'passed' => ['label' => 'Passed', 'class' => 'badge-green'],
            'failed' => ['label' => 'Failed', 'class' => 'bg-red-50 text-red-600'],
        ];
    @endphp

    <div class="border-b border-slate-200">
        <nav class="-mb-px flex gap-6">
            <a href="{{ route('admin.onboarding-assessment.index', ['tab' => 'manage']) }}"
                class="border-b-2 px-1 py-3 text-sm font-semibold {{ $activeTab === 'manage' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                Questions &amp; Settings
            </a>
            <a href="{{ route('admin.onboarding-assessment.index', ['tab' => 'results']) }}"
                class="border-b-2 px-1 py-3 text-sm font-semibold {{ $activeTab === 'results' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                Results
            </a>
        </nav>
    </div>

    @if ($activeTab === 'manage')
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <div class="card p-6">
                    <h2 class="font-bold text-slate-800">Assessment Settings</h2>
                    <p class="text-sm text-slate-400">Control visibility and the passing score</p>

                    <form method="POST" action="{{ route('admin.onboarding-assessment.settings.update') }}" class="mt-4 space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="form-label">Title</label>
                            <input type="text" name="title" value="{{ old('title', $settings->title) }}" required class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-input" placeholder="Shown to salespeople before they start">{{ old('description', $settings->description) }}</textarea>
                        </div>

                        <div>
                            <label class="form-label">Passing Score (%)</label>
                            <input type="number" name="passing_score_percent" min="1" max="100" value="{{ old('passing_score_percent', $settings->passing_score_percent) }}" required class="form-input">
                        </div>

                        <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $settings->is_published)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            Published — visible to salespeople
                        </label>

                        <button type="submit" class="btn-primary w-full sm:w-auto">Save Settings</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-slate-800">Questions</h2>
                        <p class="text-sm text-slate-400">{{ $questions->count() }} questions &middot; {{ $questions->sum('points') }} total points</p>
                    </div>
                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-question')" class="btn-primary">
                        <x-icon name="plus" class="h-4 w-4" />
                        Add Question
                    </button>
                </div>

                <div class="mt-4 card">
                    <div class="divide-y divide-slate-100">
                        @forelse ($questions as $question)
                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $questionTypeLabels[$question->type] }}</span>
                                        <span class="text-xs text-slate-400">&middot; {{ $question->points }} {{ Str::plural('pt', $question->points) }}</span>
                                        <p class="mt-0.5 text-sm text-slate-700">{{ $question->question_text }}</p>
                                        @if ($question->type !== 'text')
                                            <ul class="mt-1.5 space-y-0.5">
                                                @foreach ($question->options as $option)
                                                    <li class="flex items-center gap-1.5 text-xs {{ $option->is_correct ? 'font-medium text-emerald-600' : 'text-slate-400' }}">
                                                        <x-icon name="{{ $option->is_correct ? 'check' : 'x' }}" class="h-3 w-3" />
                                                        {{ $option->option_text }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="mt-1 text-xs italic text-slate-400">Graded manually from the Results tab.</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-question-{{ $question->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                        <form method="POST" action="{{ route('admin.onboarding-assessment.questions.destroy', $question) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this question?', target: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <x-modal name="edit-question-{{ $question->id }}" max-width="lg">
                                @include('admin.onboarding-assessment._question-form', ['question' => $question])
                            </x-modal>
                        @empty
                            <p class="px-5 py-8 text-center text-sm text-slate-400">No questions yet — click "Add Question" to create the first one.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <x-modal name="add-question" :show="$errors->isNotEmpty()" max-width="lg">
            @include('admin.onboarding-assessment._question-form', ['question' => null])
        </x-modal>
    @else
        @php
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
                            <th class="px-5 py-3 font-semibold">Student</th>
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
                                            <form method="POST" action="{{ route('admin.onboarding-assessment.results.retake', $row['user']) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Allow {{ $row['user']->name }} to retake the assessment? Their current answers and score will be cleared.', target: $el })">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Retake</button>
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
    @endif

</x-layout>
