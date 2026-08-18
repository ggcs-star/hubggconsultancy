{{-- Shared single-page course player — used by both the client "show" page
     and the admin "preview as a client" page. Needs $modules (for the
     server-rendered sidebar) and $playerData (the JSON payload built by
     App\Services\CoursePlayerPayloadBuilder) from the including view. --}}

{{-- Defined as a plain (non-module) script so it runs during HTML parsing,
     before course-player.js and Alpine (both loaded from the <head>, in that
     order — see components/layout.blade.php). course-player.js talks to this
     component only via window CustomEvents, never direct calls. --}}
<script>
    window.coursePlayerData = @json($playerData);

    function courseCurriculumPlayer(data) {
        return {
            items: data.items,
            order: data.order,
            activeKey: data.initialItemKey,
            modalOpen: false,
            activeCheckpoint: null,
            answers: {},
            submitting: false,

            init() {
                if (this.activeKey) {
                    this.resetAnswersForItem(this.items[this.activeKey]);
                    window.dispatchEvent(new CustomEvent('course:before-switch'));
                    this.$nextTick(() => this.dispatchLoadItem());
                }

                window.addEventListener('popstate', () => {
                    const params = new URLSearchParams(window.location.search);
                    const key = params.get('item');
                    if (key && this.items[key] && key !== this.activeKey) {
                        window.dispatchEvent(new CustomEvent('course:before-switch'));
                        this.activeKey = key;
                        this.resetAnswersForItem(this.items[key]);
                        this.modalOpen = false;
                        this.$nextTick(() => this.dispatchLoadItem());
                    }
                });
            },

            get activeItem() {
                return this.items[this.activeKey] || null;
            },

            get activeIndex() {
                return this.order.indexOf(this.activeKey);
            },

            get nextKey() {
                let idx = this.activeIndex + 1;
                while (idx < this.order.length) {
                    const candidate = this.items[this.order[idx]];
                    if (candidate.type === 'module_quiz' && (! candidate.isRequired || candidate.completed)) {
                        idx++;
                        continue;
                    }
                    return this.order[idx];
                }
                return null;
            },

            get quizAllAnswered() {
                if (! this.activeItem || this.activeItem.type !== 'module_quiz') return false;
                return this.allSubmitted(this.activeItem.questions);
            },

            allSubmitted(questions) {
                return (questions || []).every((q) => this.answers[q.id]?.submitted);
            },

            /**
             * Points earned/possible among already-submitted questions in
             * this list — pending text answers count toward neither until
             * graded. `pointsAwarded` (set by the admin, may be partial
             * credit for text answers) is the source of truth; auto-graded
             * answers always carry it as either the full points or zero.
             */
            scoreForQuestions(questions) {
                let earned = 0, possible = 0, pending = 0;

                (questions || []).forEach((q) => {
                    const a = this.answers[q.id];
                    if (! a?.submitted) return;
                    if (a.pointsAwarded === null) { pending++; return; }
                    possible += q.points;
                    earned += a.pointsAwarded;
                });

                return { earned, possible, pending };
            },

            selectItem(key) {
                if (! this.items[key] || key === this.activeKey) return;

                // Fires while the outgoing item's DOM (e.g. the YouTube
                // container) is still present, so course-player.js can
                // tear down cleanly before Alpine swaps the pane.
                window.dispatchEvent(new CustomEvent('course:before-switch'));

                this.activeKey = key;
                this.resetAnswersForItem(this.items[key]);
                this.modalOpen = false;
                this.activeCheckpoint = null;

                const url = new URL(window.location);
                url.searchParams.set('item', key);
                window.history.pushState({}, '', url);

                this.$nextTick(() => this.dispatchLoadItem());
            },

            next() {
                const key = this.nextKey;
                if (key) this.selectItem(key);
            },

            dispatchLoadItem() {
                window.dispatchEvent(new CustomEvent('course:load-item', { detail: { key: this.activeKey, item: this.activeItem } }));
            },

            resetAnswersForItem(item) {
                const answers = {};
                const seed = (question) => {
                    answers[question.id] = {
                        selectedIds: [...(question.yourSelectedIds || [])],
                        text: question.yourText || '',
                        submitted: question.answered,
                        isCorrect: question.answered ? question.isCorrect : null,
                        pointsAwarded: question.answered ? (question.pointsAwarded ?? null) : null,
                        correctOptionIds: question.correctOptionIds || [],
                    };
                };

                (item.questions || []).forEach(seed);
                (item.checkpoints || []).forEach((checkpoint) => checkpoint.questions.forEach(seed));

                this.answers = answers;
            },

            onCheckpoint(checkpoint) {
                this.activeCheckpoint = checkpoint;
                this.modalOpen = true;
            },

            setSingleSelection(questionId, optionId) {
                this.answers[questionId].selectedIds = [optionId];
            },

            toggleSelection(questionId, optionId, checked) {
                const current = this.answers[questionId].selectedIds;
                this.answers[questionId].selectedIds = checked
                    ? [...current, optionId]
                    : current.filter((id) => id !== optionId);
            },

            setText(questionId, value) {
                this.answers[questionId].text = value;
            },

            /**
             * Submits every not-yet-answered question in the given list
             * with one click, instead of a per-question submit button —
             * important once a quiz has more than a couple of questions.
             */
            submitAllAnswers(questions, answerUrl) {
                if (this.submitting) return;

                const unanswered = (questions || []).filter((q) => ! this.answers[q.id]?.submitted);
                if (unanswered.length === 0) return;

                this.submitting = true;

                const requests = unanswered.map((question) =>
                    window.axios.post(answerUrl, {
                        question_id: question.id,
                        selected_option_ids: this.answers[question.id].selectedIds,
                        answer_text: this.answers[question.id].text,
                    }).then(({ data }) => {
                        this.answers[question.id] = {
                            ...this.answers[question.id],
                            submitted: true,
                            isCorrect: data.is_correct,
                            pointsAwarded: data.points_awarded ?? null,
                            correctOptionIds: data.correct_option_ids || [],
                        };

                        // `question` here is the same object stored inside
                        // this.items[...] (arrays/objects are referenced,
                        // not copied) — mutating it is what makes the answer
                        // stick when the user leaves this item and comes
                        // back. Without this, resetAnswersForItem() would
                        // rebuild `answers` from the original, still-
                        // "unanswered" snapshot embedded at page load and
                        // wipe out what was just submitted.
                        question.answered = true;
                        question.isCorrect = data.is_correct;
                        question.pointsAwarded = data.points_awarded ?? null;
                        question.yourSelectedIds = [...this.answers[question.id].selectedIds];
                        question.yourText = this.answers[question.id].text;
                        question.correctOptionIds = data.correct_option_ids || [];
                    })
                );

                Promise.all(requests).finally(() => {
                    this.submitting = false;

                    if (this.activeItem?.type === 'module_quiz' && this.allSubmitted(this.activeItem.questions)) {
                        this.activeItem.completed = true;
                    }
                });
            },

            continuePlayback() {
                this.modalOpen = false;
                window.dispatchEvent(new CustomEvent('course:continue'));
            },

            onLessonCompleted() {
                if (this.activeItem) {
                    this.activeItem.completed = true;
                }
            },

            toggleFullscreen() {
                const el = document.getElementById('course-video-wrapper');
                if (! document.fullscreenElement) {
                    el?.requestFullscreen?.();
                } else {
                    document.exitFullscreen?.();
                }
            },
        };
    }
</script>

<div id="course-player" class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12" x-data="courseCurriculumPlayer(window.coursePlayerData)"
    x-on:course:checkpoint.window="onCheckpoint($event.detail)"
    x-on:course:lesson-completed.window="onLessonCompleted()">

    {{-- Main pane --}}
    <div class="lg:col-span-8">
        <template x-if="! activeItem">
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                This course doesn't have any content yet.
            </div>
        </template>

        <template x-if="activeItem && activeItem.type === 'lesson'">
            <div>
                <div id="course-video-wrapper" class="group relative mx-auto aspect-video w-full max-w-3xl overflow-hidden rounded-xl bg-black [&:fullscreen]:max-w-none">
                    <template x-if="activeItem.videoSource === 'upload'">
                        <video id="course-native-video" :src="activeItem.videoSrc" controls playsinline class="h-full w-full"></video>
                    </template>
                    <template x-if="activeItem.videoSource === 'youtube'">
                        <div id="course-youtube-player" class="h-full w-full"></div>
                    </template>

                    <button type="button" x-on:click="toggleFullscreen()" title="Full screen"
                        class="absolute right-3 top-3 hidden items-center justify-center rounded-md bg-black/60 p-1.5 text-white opacity-0 transition group-hover:flex group-hover:opacity-100 hover:bg-black/80">
                        <x-icon name="maximize" class="w-4 h-4" />
                    </button>
                </div>

                <div class="mx-auto mt-4 max-w-3xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-lg font-semibold text-secondary-dark" x-text="activeItem.title"></h1>
                            <p class="mt-1 text-sm text-secondary" x-show="activeItem.description" x-text="activeItem.description"></p>
                        </div>
                        <p class="shrink-0 pt-1 text-sm text-secondary" x-show="activeItem.completed">
                            <span class="inline-flex items-center gap-1.5 text-success">
                                <x-icon name="check" class="w-4 h-4" />
                                Completed
                            </span>
                        </p>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="button" x-show="activeItem.completed && nextKey" x-on:click="next()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                            Next
                            <x-icon name="chevron-right" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="activeItem && activeItem.type === 'module_quiz'">
            <div class="mx-auto max-w-3xl">
                <div class="flex items-center gap-2">
                    <x-icon name="help-circle" class="w-5 h-5 text-warning" />
                    <h1 class="text-lg font-semibold text-secondary-dark" x-text="activeItem.title"></h1>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium"
                        :class="activeItem.isRequired ? 'bg-warning-light text-warning' : 'bg-surface-alt text-secondary'"
                        x-text="activeItem.isRequired ? 'Required' : 'Optional'"></span>
                </div>
                <p class="mt-1 text-sm text-secondary">Answer every question, then submit them all at once.</p>

                <div class="mt-5 space-y-5">
                    @include('user.courses._quiz-questions', ['questionsExpr' => 'activeItem.questions'])
                </div>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <template x-if="quizAllAnswered">
                        @php $s = 'scoreForQuestions(activeItem.questions)'; @endphp
                        <p class="text-sm text-secondary-dark">
                            <span x-show="{{ $s }}.possible > 0">
                                Scored <span class="font-medium" x-text="{{ $s }}.earned"></span>/<span x-text="{{ $s }}.possible"></span> marks
                            </span>
                            <span x-show="{{ $s }}.pending > 0">
                                <span x-show="{{ $s }}.possible > 0">&mdash; </span><span x-text="{{ $s }}.pending"></span> pending manual review.
                            </span>
                        </p>
                    </template>
                    <div class="ml-auto flex gap-3">
                        <button type="button" x-show="!quizAllAnswered" x-on:click="submitAllAnswers(activeItem.questions, activeItem.answerUrl)"
                            x-bind:disabled="submitting"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark disabled:cursor-not-allowed disabled:opacity-50">
                            <span x-text="submitting ? 'Submitting…' : 'Submit Answers'"></span>
                        </button>
                        <button type="button" x-show="quizAllAnswered && nextKey" x-on:click="next()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                            Next
                            <x-icon name="chevron-right" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Course Content sidebar — server-rendered, no lock icons, every item switches the pane via JS --}}
    <div class="lg:col-span-4">
        <div class="sticky top-6 rounded-xl border border-app-border bg-white">
            <div class="flex items-center gap-2 border-b border-app-border px-4 py-3">
                <x-icon name="list" class="w-4 h-4 text-secondary" />
                <h2 class="text-sm font-semibold text-secondary-dark">Course Content</h2>
            </div>
            <div class="max-h-[36rem] divide-y divide-app-border overflow-y-auto">
                @foreach ($modules as $module)
                    <div>
                        <p class="bg-surface-alt px-4 py-2 text-xs font-semibold uppercase tracking-wide text-secondary">{{ $module->title }}</p>
                        @foreach ($module->items_for_client as $item)
                            @php
                                $itemKey = $item->item_type === 'lesson' ? "lesson-{$item->id}" : "quiz-{$item->id}";
                                $isQuiz = $item->item_type === 'module_quiz';
                            @endphp
                            <a href="?item={{ $itemKey }}" x-on:click.prevent="selectItem('{{ $itemKey }}')"
                                class="flex items-center gap-2.5 px-4 py-2.5"
                                x-bind:class="activeKey === '{{ $itemKey }}' ? 'bg-primary-light' : 'hover:bg-surface-alt'">
                                <x-icon name="{{ $isQuiz ? 'help-circle' : ($item->client_completed ? 'check' : 'play-circle') }}"
                                    class="w-3.5 h-3.5 shrink-0 {{ $item->client_completed ? 'text-success' : ($isQuiz ? 'text-warning' : 'text-secondary') }}" />
                                <span class="text-sm text-secondary-dark" x-bind:class="activeKey === '{{ $itemKey }}' && 'font-medium text-primary'">{{ $item->title }}</span>
                                @if ($isQuiz && ! $item->is_required)
                                    <span class="shrink-0 text-xs text-secondary">Optional</span>
                                @elseif (! $isQuiz && $item->duration)
                                    <span class="ml-auto shrink-0 text-xs text-secondary">{{ $item->duration }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mid-video checkpoint quiz overlay --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark" x-text="activeCheckpoint?.title || 'Quick Check'"></h2>
                <x-icon name="help-circle" class="w-5 h-5 text-primary" />
            </div>
            <p class="mt-1 text-xs text-secondary">Answer to continue the video.</p>

            <div class="mt-5 space-y-5">
                @include('user.courses._quiz-questions', ['questionsExpr' => '(activeCheckpoint?.questions || [])'])
            </div>

            <div class="mt-6 flex items-center justify-between gap-4">
                <template x-if="activeCheckpoint && allSubmitted(activeCheckpoint.questions)">
                    @php $s = 'scoreForQuestions(activeCheckpoint?.questions)'; @endphp
                    <p class="text-sm text-secondary-dark">
                        <span x-show="{{ $s }}.possible > 0">
                            Scored <span class="font-medium" x-text="{{ $s }}.earned"></span>/<span x-text="{{ $s }}.possible"></span> marks
                        </span>
                        <span x-show="{{ $s }}.pending > 0">
                            <span x-show="{{ $s }}.possible > 0">&mdash; </span><span x-text="{{ $s }}.pending"></span> pending manual review.
                        </span>
                    </p>
                </template>
                <button type="button" x-show="activeCheckpoint && !allSubmitted(activeCheckpoint.questions)"
                    x-on:click="submitAllAnswers(activeCheckpoint.questions, activeCheckpoint.answerUrl)" x-bind:disabled="submitting"
                    class="ml-auto rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark disabled:cursor-not-allowed disabled:opacity-50">
                    <span x-text="submitting ? 'Submitting…' : 'Submit Answers'"></span>
                </button>
                <button type="button" x-show="activeCheckpoint && allSubmitted(activeCheckpoint.questions)" x-on:click="continuePlayback()"
                    class="ml-auto rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>
