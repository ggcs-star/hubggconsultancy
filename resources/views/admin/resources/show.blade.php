@php
    $questionTypeLabels = ['radio' => 'Single Choice', 'checkbox' => 'Multiple Choice', 'text' => 'Text Answer'];

    $formatTimestamp = fn (int $seconds) => sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);

    $languages = [
        'hindi' => ['label' => 'Hindi', 'url' => $resource->hindi_youtube_url, 'checkpoints' => $hindiCheckpoints],
        'english' => ['label' => 'English', 'url' => $resource->english_youtube_url, 'checkpoints' => $englishCheckpoints],
    ];
@endphp

<x-layout title="Manage Checkpoints" title-icon="video" subtitle="{{ $resource->title }}">

    <a href="{{ route('admin.resources.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
        <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
        Back to Resources
    </a>

    <div class="mt-4 card flex items-center gap-4 p-5">
        @if ($resource->thumbnail)
            <img src="{{ asset('storage/' . $resource->thumbnail) }}" alt="" class="h-16 w-24 shrink-0 rounded-lg object-cover">
        @else
            <div class="flex h-16 w-24 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-300">
                <x-icon name="video" class="h-6 w-6" />
            </div>
        @endif
        <div class="min-w-0">
            <p class="truncate font-bold text-slate-800">{{ $resource->title }}</p>
            <p class="mt-0.5 truncate text-sm text-slate-400">{{ $resource->description }}</p>
        </div>
    </div>

    <div class="mt-6" x-data="{ activeLang: 'hindi' }">
        {{-- Language tabs — same pattern as the user-facing Resources page --}}
        <div class="flex items-center gap-6 border-b border-slate-200">
            @foreach ($languages as $language => $col)
                <button type="button" x-on:click="activeLang = '{{ $language }}'" class="flex items-center gap-2 border-b-2 px-1 pb-3 text-sm font-semibold transition"
                    :class="activeLang === '{{ $language }}' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-400 hover:text-slate-600'">
                    {{ $col['label'] }}
                    <span class="badge badge-slate">{{ $col['checkpoints']->count() }}</span>
                </button>
            @endforeach
        </div>

        @foreach ($languages as $language => $col)
            <div x-show="activeLang === '{{ $language }}'" x-cloak class="mt-6">
                <div class="card">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-5">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800">{{ $col['label'] }} Checkpoints</p>
                            @if ($col['url'])
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $col['url'] }}</p>
                            @else
                                <p class="mt-0.5 text-xs text-amber-600">No {{ $col['label'] }} link set on this resource yet.</p>
                            @endif
                        </div>
                        @if ($col['url'])
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-checkpoint-{{ $language }}')" class="btn-primary shrink-0 text-sm">
                                <x-icon name="plus" class="h-4 w-4" />
                                Checkpoint
                            </button>
                        @endif
                    </div>

                    @forelse ($col['checkpoints'] as $index => $checkpoint)
                        <div class="flex gap-4 border-b border-slate-100 p-5 last:border-b-0">
                            <div class="flex shrink-0 flex-col items-center">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-700">{{ $index + 1 }}</span>
                                @if (! $loop->last)
                                    <span class="mt-1 w-px flex-1 bg-slate-100"></span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1 pb-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            <x-icon name="clock" class="mr-1 inline h-4 w-4 text-slate-400" />
                                            {{ $formatTimestamp($checkpoint->timestamp_seconds) }}
                                            @if ($checkpoint->title)
                                                &middot; {{ $checkpoint->title }}
                                            @endif
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $checkpoint->questions->count() }} {{ Str::plural('question', $checkpoint->questions->count()) }} &middot; {{ $checkpoint->questions->sum('points') }} pts</p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-question-{{ $checkpoint->id }}')" title="Add Question" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                            <x-icon name="plus" class="h-4 w-4" />
                                        </button>
                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-checkpoint-{{ $checkpoint->id }}')" title="Edit Checkpoint" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                        <form method="POST" action="{{ route('admin.resource-checkpoints.destroy', $checkpoint) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this checkpoint and its questions?', target: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete Checkpoint" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if ($checkpoint->questions->isNotEmpty())
                                    <ul class="mt-3 space-y-2">
                                        @foreach ($checkpoint->questions as $question)
                                            <li class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm text-slate-700">{{ Str::limit($question->question_text, 60) }}</p>
                                                    <p class="text-xs text-slate-400">{{ $questionTypeLabels[$question->type] }} &middot; {{ $question->points }} {{ Str::plural('pt', $question->points) }}</p>
                                                </div>
                                                <div class="flex shrink-0 items-center gap-1">
                                                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-question-{{ $question->id }}')" title="Edit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                                        <x-icon name="pencil" class="h-4 w-4" />
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.resource-quiz-questions.destroy', $question) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this question?', target: $el })">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Delete" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                                            <x-icon name="trash" class="h-4 w-4" />
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>

                                            <x-modal name="edit-question-{{ $question->id }}" max-width="lg">
                                                @include('admin.resources._question-form', ['question' => $question, 'checkpoint' => $checkpoint])
                                            </x-modal>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <x-modal name="edit-checkpoint-{{ $checkpoint->id }}" max-width="md">
                            @include('admin.resources._checkpoint-form', ['checkpoint' => $checkpoint, 'resource' => $resource, 'language' => $language])
                        </x-modal>

                        <x-modal name="add-question-{{ $checkpoint->id }}" max-width="lg">
                            @include('admin.resources._question-form', ['question' => null, 'checkpoint' => $checkpoint])
                        </x-modal>
                    @empty
                        <p class="p-8 text-center text-sm text-slate-400">
                            @if ($col['url'])
                                No checkpoints yet — click "Checkpoint" to pause the video and show a quiz at a specific time.
                            @else
                                Add a {{ $col['label'] }} YouTube link on this resource first.
                            @endif
                        </p>
                    @endforelse
                </div>
            </div>

            @if ($col['url'])
                <x-modal name="add-checkpoint-{{ $language }}" max-width="md">
                    @include('admin.resources._checkpoint-form', ['checkpoint' => null, 'resource' => $resource, 'language' => $language])
                </x-modal>
            @endif
        @endforeach
    </div>

</x-layout>
