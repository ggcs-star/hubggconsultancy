<x-layout title="Resources" title-icon="video" subtitle="Watch, learn and earn points along the way">

    <script>
        window.resourcePlayerData = @json($playerData);

        function resourceLibrary(data) {
            return {
                data,
                selectedTab: 'hindi',
                searchTerm: '',
                activeResourceId: null,
                activeLanguage: null,
                activeCheckpoint: null,
                answers: {},
                submitting: false,

                isPlaying(resourceId, language) {
                    return this.activeResourceId === resourceId && this.activeLanguage === language;
                },

                get activeVideo() {
                    if (! this.activeResourceId || ! this.activeLanguage) return null;
                    return this.data[this.activeResourceId]?.[this.activeLanguage] || null;
                },

                openVideo(resourceId, language) {
                    if (this.isPlaying(resourceId, language)) return;

                    const video = this.data[resourceId]?.[language];
                    if (! video) return;

                    if (video.isPlaylist || ! video.videoId) {
                        window.open(video.url, '_blank');
                        return;
                    }

                    window.stopResourcePlayer();

                    this.activeResourceId = resourceId;
                    this.activeLanguage = language;
                    this.activeCheckpoint = null;
                    this.resetAnswersFor(video.checkpoints);

                    this.$nextTick(() => window.startResourcePlayer(video.videoId, video.checkpoints));
                },

                stopVideo() {
                    window.stopResourcePlayer();
                    this.activeResourceId = null;
                    this.activeLanguage = null;
                    this.activeCheckpoint = null;
                },

                toggleFullscreen(resourceId) {
                    const el = document.getElementById('resource-frame-' + resourceId);
                    if (! document.fullscreenElement) {
                        el?.requestFullscreen?.();
                    } else {
                        document.exitFullscreen?.();
                    }
                },

                onCheckpoint(checkpoint) {
                    this.activeCheckpoint = checkpoint;
                },

                resetAnswersFor(checkpoints) {
                    const answers = {};

                    (checkpoints || []).forEach((checkpoint) => {
                        (checkpoint.questions || []).forEach((question) => {
                            answers[question.id] = {
                                selectedIds: [...(question.yourSelectedIds || [])],
                                text: question.yourText || '',
                                submitted: question.answered,
                                isCorrect: question.answered ? question.isCorrect : null,
                                pointsAwarded: question.answered ? (question.pointsAwarded ?? null) : null,
                                correctOptionIds: question.correctOptionIds || [],
                            };
                        });
                    });

                    this.answers = answers;
                },

                allSubmitted(questions) {
                    return (questions || []).every((q) => this.answers[q.id]?.submitted);
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

                            question.answered = true;
                            question.isCorrect = data.is_correct;
                            question.pointsAwarded = data.points_awarded ?? null;
                            question.correctOptionIds = data.correct_option_ids || [];
                        })
                    );

                    Promise.all(requests).finally(() => {
                        this.submitting = false;
                        // Points awarded (if any) just changed server-side — refresh the
                        // topbar badge immediately instead of waiting for a page reload.
                        window.refreshPointsBadge?.();
                    });
                },

                continuePlayback() {
                    this.activeCheckpoint = null;
                    window.dispatchEvent(new CustomEvent('resource:continue'));
                },
            };
        }
    </script>

    <div
        x-data="resourceLibrary(window.resourcePlayerData)"
        x-on:resource:checkpoint.window="onCheckpoint($event.detail)"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="text" x-model="searchTerm" placeholder="Search resources..." class="form-input pl-10" :class="searchTerm ? 'pr-9' : ''">
                <button type="button" x-show="searchTerm" x-cloak x-on:click="searchTerm = ''" title="Clear search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            {{-- Language switch — pick Hindi or English to see that language's videos only --}}
            <div class="flex items-center gap-2">
                <button type="button" x-on:click="stopVideo(); selectedTab = 'hindi'" class="rounded-lg px-5 py-2 text-sm font-semibold transition"
                    :class="selectedTab === 'hindi' ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                    Hindi
                </button>
                <button type="button" x-on:click="stopVideo(); selectedTab = 'english'" class="rounded-lg px-5 py-2 text-sm font-semibold transition"
                    :class="selectedTab === 'english' ? 'bg-brand-700 text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                    English
                </button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($resources as $resource)
                @continue(! $resource->hindi_youtube_url && ! $resource->english_youtube_url)
                <div class="card flex flex-col overflow-hidden" x-data="{ expanded: false }"
                    x-show="(selectedTab === 'hindi' ? {{ $resource->hindi_youtube_url ? 'true' : 'false' }} : {{ $resource->english_youtube_url ? 'true' : 'false' }}) && (searchTerm.trim() === '' || {{ \Illuminate\Support\Js::from(strtolower($resource->title)) }}.includes(searchTerm.trim().toLowerCase()))"
                    x-cloak>
                    {{-- The frame itself is the player — click anywhere on it to play, no separate button. --}}
                    <div id="resource-frame-{{ $resource->id }}" class="relative aspect-video w-full shrink-0 bg-black">
                        <template x-if="! isPlaying({{ $resource->id }}, selectedTab)">
                            <div class="group relative h-full w-full cursor-pointer" x-on:click="openVideo({{ $resource->id }}, selectedTab)">
                                @if ($resource->thumbnailFor('hindi'))
                                    <img x-show="selectedTab === 'hindi'" src="{{ asset('storage/' . $resource->thumbnailFor('hindi')) }}" alt="{{ $resource->title }}" class="h-full w-full object-cover transition group-hover:brightness-90">
                                @endif
                                @if ($resource->thumbnailFor('english'))
                                    <img x-show="selectedTab === 'english'" src="{{ asset('storage/' . $resource->thumbnailFor('english')) }}" alt="{{ $resource->title }}" class="h-full w-full object-cover transition group-hover:brightness-90">
                                @endif
                                @if (! $resource->thumbnailFor('hindi') && ! $resource->thumbnailFor('english'))
                                    <div class="flex h-full w-full items-center justify-center bg-slate-800 text-slate-500">
                                        <x-icon name="video" class="h-10 w-10" />
                                    </div>
                                @endif
                            </div>
                        </template>

                        <template x-if="isPlaying({{ $resource->id }}, selectedTab)">
                            <div class="relative h-full w-full">
                                <div id="resource-youtube-player" class="h-full w-full"></div>
                                {{-- YouTube's own title/channel overlay can't be disabled via the embed API
                                     (the old showinfo param was retired) — this masks it instead. --}}
                                <div class="pointer-events-none absolute inset-x-0 top-0 z-[5] h-14 bg-gradient-to-b from-black/95 via-black/60 to-transparent"></div>
                                <div class="absolute right-2 top-2 z-10 flex items-center gap-1">
                                    <button type="button" x-on:click="toggleFullscreen({{ $resource->id }})" title="Full screen" class="rounded-md bg-black/60 p-1.5 text-white hover:bg-black/80">
                                        <x-icon name="maximize" class="h-4 w-4" />
                                    </button>
                                    <button type="button" x-on:click="stopVideo()" title="Close" class="rounded-md bg-black/60 p-1.5 text-white hover:bg-black/80">
                                        <x-icon name="x" class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <p class="font-bold text-slate-800">{{ $resource->title }}</p>

                        @if ($resource->description)
                            <p class="mt-1.5 text-sm text-slate-500" :class="expanded ? '' : 'line-clamp-2'">{{ $resource->description }}</p>
                            <button type="button" x-on:click="expanded = !expanded" class="mt-1 self-start text-xs font-semibold text-brand-700 hover:text-brand-800" x-text="expanded ? 'See Less' : 'See More'"></button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card col-span-full p-10 text-center text-sm text-slate-400">No resources have been added yet.</div>
            @endforelse

            @php
                $hindiEmpty = $resources->where('hindi_youtube_url', '!=', null)->isEmpty() ? 'true' : 'false';
                $englishEmpty = $resources->where('english_youtube_url', '!=', null)->isEmpty() ? 'true' : 'false';
            @endphp
            <p class="card col-span-full p-10 text-center text-sm text-slate-400" x-show="selectedTab === 'hindi' && {{ $hindiEmpty }}" x-cloak>
                No Hindi resources yet.
            </p>
            <p class="card col-span-full p-10 text-center text-sm text-slate-400" x-show="selectedTab === 'english' && {{ $englishEmpty }}" x-cloak>
                No English resources yet.
            </p>
        </div>

        {{-- Mid-video checkpoint quiz overlay --}}
        <div x-show="activeCheckpoint" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark" x-text="activeCheckpoint?.title || 'Quick Check'"></h2>
                    <x-icon name="help-circle" class="h-5 w-5 text-primary" />
                </div>
                <p class="mt-1 text-xs text-secondary">Answer to continue the video.</p>

                <div class="mt-5 space-y-5">
                    @include('user.courses._quiz-questions', ['questionsExpr' => '(activeCheckpoint?.questions || [])'])
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <button type="button" x-show="activeCheckpoint && !allSubmitted(activeCheckpoint.questions)"
                        x-on:click="submitAllAnswers(activeCheckpoint.questions, activeCheckpoint.answerUrl)" x-bind:disabled="submitting"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark disabled:cursor-not-allowed disabled:opacity-50">
                        <span x-text="submitting ? 'Submitting…' : 'Submit Answers'"></span>
                    </button>
                    <button type="button" x-show="activeCheckpoint && allSubmitted(activeCheckpoint.questions)" x-on:click="continuePlayback()"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-layout>
