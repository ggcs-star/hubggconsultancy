@php
    // Same auto-assigned palette used on the SaaS Products list (resources/views/admin/saas-products/index.blade.php).
    $accentColors = [
        'bg-orange-50 text-orange-600', 'bg-emerald-50 text-emerald-600', 'bg-violet-50 text-violet-600',
        'bg-amber-50 text-amber-600', 'bg-sky-50 text-sky-600', 'bg-fuchsia-50 text-fuchsia-600',
        'bg-rose-50 text-rose-600', 'bg-red-50 text-red-600', 'bg-blue-50 text-blue-600', 'bg-indigo-50 text-indigo-600',
    ];
    $quizIcons = ['document', 'badge', 'help-circle', 'star', 'users', 'book-open', 'academic-cap', 'list'];
    $questionTypeLabels = ['radio' => 'Single Choice', 'checkbox' => 'Multiple Choice', 'text' => 'Text Answer'];

    // Drag-to-reorder only makes sense against the plain, unfiltered list — otherwise
    // "reordering" a search result would silently rewrite sort_order to match whatever
    // narrowed set happened to be on screen.
    $canReorder = ! request('search');
@endphp

<div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-12">
    {{-- Quiz list --}}
    <div class="lg:col-span-5">
        <div class="card">
            <div class="border-b border-slate-100 p-4">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-slate-800">Quizzes</h2>
                    <span class="badge badge-slate">{{ $quizzes->count() }} {{ Str::plural('quiz', $quizzes->count()) }}</span>
                </div>
                <p class="mt-0.5 text-xs text-slate-400">Manage all your onboarding quizzes</p>

                <form method="GET" class="mt-3 flex items-center gap-2">
                    <input type="hidden" name="tab" value="quizzes">
                    <div class="relative flex-1">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quizzes..." class="form-input pl-9">
                    </div>
                </form>
            </div>

            <div class="max-h-[36rem] divide-y divide-slate-100 overflow-y-auto"
                @if ($canReorder) data-sortable data-sortable-url="{{ route('admin.onboarding-assessment.quizzes.reorder') }}" @endif>
                @forelse ($quizzes as $index => $quiz)
                    @php
                        $isActive = $selectedQuiz && $selectedQuiz->id === $quiz->id;
                        $color = $accentColors[$index % count($accentColors)];
                        $icon = $quizIcons[$index % count($quizIcons)];
                    @endphp
                    <div class="relative flex items-start gap-3 p-4 {{ $isActive ? 'bg-brand-50' : 'hover:bg-slate-50' }} {{ $canReorder ? 'cursor-move' : '' }}"
                        @if ($canReorder) draggable="true" data-sortable-item data-sortable-id="{{ $quiz->id }}" title="Drag to reorder" @endif>
                        <a href="{{ request()->fullUrlWithQuery(['quiz' => $quiz->id]) }}" class="absolute inset-0" aria-label="View {{ $quiz->title }}"></a>

                        @if ($canReorder)
                            <x-icon name="grip" class="mt-2 h-4 w-4 shrink-0 text-slate-300" />
                        @endif

                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $color }}">
                            <x-icon name="{{ $icon }}" class="h-5 w-5" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-800">{{ $quiz->title }}</p>
                            @if ($quiz->description)
                                <p class="truncate text-xs text-slate-400">{{ $quiz->description }}</p>
                            @endif
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-slate-400">
                                <span>{{ $quiz->questions_count }} {{ Str::plural('question', $quiz->questions_count) }}</span>
                                <span class="inline-flex items-center gap-0.5"><x-icon name="star" class="h-3 w-3" />{{ $quiz->questions_sum_points ?? 0 }} pts</span>
                                @include('admin.onboarding-assessment._quiz-publish-toggle', ['quiz' => $quiz])
                            </div>
                        </div>

                        <div class="relative z-10 flex shrink-0 items-center gap-0.5" x-data="{ menuOpen: false }" x-on:click.outside="menuOpen = false">
                            <button type="button" x-on:click.stop="menuOpen = !menuOpen" class="rounded-md p-1.5 text-slate-400 hover:bg-white hover:text-slate-600" title="More options">
                                <x-icon name="more-vertical" class="h-4 w-4" />
                            </button>
                            <div x-show="menuOpen" x-cloak class="absolute right-0 top-8 z-20 w-44 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                                <button type="button" x-on:click="menuOpen = false; $dispatch('open-modal', 'edit-quiz-{{ $quiz->id }}')" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                                    <x-icon name="pencil" class="h-4 w-4" /> Edit Quiz
                                </button>
                                <form method="POST" action="{{ route('admin.onboarding-assessment.quizzes.destroy', $quiz) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $quiz->title }}\' and all its questions? This cannot be undone.', target: $el })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                        <x-icon name="trash" class="h-4 w-4" /> Delete
                                    </button>
                                </form>
                            </div>
                            <x-icon name="chevron-right" class="h-4 w-4 text-slate-300" />
                        </div>
                    </div>
                @empty
                    <p class="p-8 text-center text-sm text-slate-400">
                        @if (request('search'))
                            No quizzes match your search.
                        @else
                            No quizzes yet — click "Add Quiz" to create the first one (e.g. "Sales Quiz", "Technical Quiz").
                        @endif
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Selected quiz detail --}}
    <div class="lg:col-span-7">
        @if ($selectedQuiz)
            @php
                $selectedIndex = $quizzes->search(fn ($q) => $q->id === $selectedQuiz->id);
                $selectedColor = $accentColors[$selectedIndex % count($accentColors)];
                $selectedIcon = $quizIcons[$selectedIndex % count($quizIcons)];
            @endphp
            <div class="card">
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $selectedColor }}">
                            <x-icon name="{{ $selectedIcon }}" class="h-5 w-5" />
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-800">{{ $selectedQuiz->title }}</p>
                                @include('admin.onboarding-assessment._quiz-publish-toggle', ['quiz' => $selectedQuiz])
                            </div>
                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ $selectedQuiz->questions_count }} {{ Str::plural('question', $selectedQuiz->questions_count) }}
                                &middot; {{ $selectedQuiz->questions_sum_points ?? 0 }} points
                                @if ($selectedQuiz->description)
                                    &middot; {{ $selectedQuiz->description }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-question')" title="Add Question" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="plus" class="h-4 w-4" />
                        </button>
                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-quiz-{{ $selectedQuiz->id }}')" title="Edit Quiz" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </button>
                        <form method="POST" action="{{ route('admin.onboarding-assessment.quizzes.destroy', $selectedQuiz) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete \'{{ $selectedQuiz->title }}\' and all its questions? This cannot be undone.', target: $el })">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete Quiz" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-2 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider text-slate-400">
                                <th class="px-5 py-3 font-semibold">#</th>
                                <th class="px-5 py-3 font-semibold">Question</th>
                                <th class="px-5 py-3 font-semibold">Type</th>
                                <th class="px-5 py-3 font-semibold">Points</th>
                                <th class="px-5 py-3 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($questionsPage as $i => $question)
                                <tr>
                                    <td class="px-5 py-3.5 text-slate-400">{{ $questionsPage->firstItem() + $i }}</td>
                                    <td class="px-5 py-3.5 text-slate-700">
                                        {{ Str::limit($question->question_text, 60) }}
                                        @if ($question->type !== 'text')
                                            <p class="text-xs text-slate-400">{{ $question->options->count() }} options</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="badge badge-slate">{{ $questionTypeLabels[$question->type] }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500">{{ $question->points }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-1">
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400">No questions in this quiz yet — click "Add Question" to create the first one.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($questionsPage->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $questionsPage->onEachSide(1)->links() }}
                    </div>
                @endif

                {{-- Modals are rendered outside the <table> — a <div> directly inside
                     <tbody> is invalid HTML, and browsers "foster-parent" it elsewhere
                     in the DOM, which breaks the Alpine x-data scope for the form inside. --}}
                @foreach ($questionsPage as $question)
                    <x-modal name="edit-question-{{ $question->id }}" max-width="lg">
                        @include('admin.onboarding-assessment._question-form', ['question' => $question, 'quiz' => $selectedQuiz])
                    </x-modal>
                @endforeach

                <x-modal name="add-question" :show="$errors->has('question_text') && old('_form') === 'question'" max-width="lg">
                    @include('admin.onboarding-assessment._question-form', ['question' => null, 'quiz' => $selectedQuiz])
                </x-modal>
            </div>
        @else
            <div class="card flex h-full min-h-[16rem] items-center justify-center p-10 text-center text-sm text-slate-400">
                Select a quiz on the left, or click "Add Quiz" to create your first one.
            </div>
        @endif
    </div>
</div>

{{-- Rendered outside the draggable list — same reasoning as the question-edit modals
     above: a modal <div> as a direct child of the [data-sortable] container would get
     dragged around as if it were a quiz card, and would show up in the reorder payload. --}}
@foreach ($quizzes as $quiz)
    <x-modal name="edit-quiz-{{ $quiz->id }}" :show="$errors->has('title') && old('_form') === 'quiz' && (int) old('_edit_id') === $quiz->id" max-width="md">
        @include('admin.onboarding-assessment._quiz-form', ['quiz' => $quiz])
    </x-modal>
@endforeach

<x-modal name="add-quiz" :show="$errors->has('title') && old('_form') === 'quiz' && ! old('_edit_id')" max-width="md">
    @include('admin.onboarding-assessment._quiz-form', ['quiz' => null])
</x-modal>
