<x-layout title="Success Stories" title-icon="lightbulb" subtitle="Stories that inspire the team">

    @if ($successStories->isEmpty())
        <div class="card px-6 py-16 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                <x-icon name="lightbulb" class="h-7 w-7 text-brand-600" />
            </div>
            <h3 class="mt-4 font-bold text-slate-800">No success stories yet</h3>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Check back soon for inspiring stories from the team.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($successStories as $successStory)
                <div class="card p-6" x-data="{ open: false }">
                    @if ($successStory->headline)
                        <p class="text-lg font-bold text-slate-800">{{ $successStory->headline }}</p>
                    @endif

                    <div class="mt-3 flex items-start gap-3">
                        @if ($successStory->photoUrl())
                            <img src="{{ $successStory->photoUrl() }}" alt="{{ $successStory->name }}" class="h-11 w-11 shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <x-icon name="user" class="h-5 w-5" />
                            </span>
                        @endif
                        <div>
                            <p class="font-semibold text-slate-800">{{ $successStory->name }}</p>
                            @if ($successStory->designation)
                                <p class="text-sm text-slate-400">{{ $successStory->designation }}</p>
                            @endif
                        </div>
                    </div>

                    <p class="mt-4 italic text-slate-600">&ldquo;{{ $successStory->testimonial }}&rdquo;</p>

                    @if (!empty($successStory->metrics))
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Before Training</p>
                                <ul class="mt-2 space-y-1 text-sm text-slate-600">
                                    @foreach ($successStory->metrics as $metric)
                                        <li>{{ $metric['label'] }}: {{ $metric['before'] ?: '—' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="rounded-xl bg-brand-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">After Training</p>
                                <ul class="mt-2 space-y-1 text-sm text-slate-700">
                                    @foreach ($successStory->metrics as $metric)
                                        <li>{{ $metric['label'] }}: {{ $metric['after'] ?: '—' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if ($successStory->business_impact || $successStory->video_url)
                        <div x-show="open" x-transition x-cloak class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                            @if ($successStory->video_url)
                                <a href="{{ $successStory->video_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700">
                                    <x-icon name="play-circle" class="h-4 w-4" />
                                    Watch the video
                                </a>
                            @endif
                            @if ($successStory->business_impact)
                                <p class="text-sm text-slate-600">{{ $successStory->business_impact }}</p>
                            @endif
                        </div>

                        <button type="button" x-on:click="open = !open" class="mt-3 text-sm font-semibold text-brand-600 hover:text-brand-700">
                            <span x-show="!open">Read Full Story →</span>
                            <span x-show="open" x-cloak>Show Less</span>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $successStories->links() }}
        </div>
    @endif

</x-layout>
