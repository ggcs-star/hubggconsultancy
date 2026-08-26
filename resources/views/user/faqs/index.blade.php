<x-layout title="FAQ" title-icon="help-circle" subtitle="Answers to common questions">

    @if ($tabs->isEmpty())
        <div class="card p-16 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                <x-icon name="help-circle" class="h-7 w-7 text-brand-600" />
            </div>
            <h3 class="mt-4 font-bold text-slate-800">No FAQs yet</h3>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Check back later for answers to common questions.</p>
        </div>
    @else
        <div x-data="{ tab: '{{ $tabs->first()['key'] }}' }">
            <div class="flex flex-wrap gap-2">
                @foreach ($tabs as $tabItem)
                    <button
                        type="button"
                        x-on:click="tab = '{{ $tabItem['key'] }}'"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="tab === '{{ $tabItem['key'] }}' ? 'bg-brand-600 text-white' : 'bg-white text-slate-500 hover:bg-slate-50'"
                    >
                        {{ $tabItem['label'] }}
                    </button>
                @endforeach
            </div>

            @foreach ($tabs as $tabItem)
                <div x-show="tab === '{{ $tabItem['key'] }}'" x-cloak class="mt-6 space-y-4">
                    @foreach ($tabItem['faqs'] as $faq)
                        <div
                            class="card overflow-hidden transition"
                            x-data="{ open: false }"
                            :class="open ? 'ring-1 ring-brand-200 shadow-md' : ''"
                        >
                            <button
                                type="button"
                                x-on:click="open = !open"
                                class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                            >
                                <span class="text-base font-semibold text-slate-800">{{ $faq->question }}</span>

                                <span
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 transition"
                                    :class="open ? 'bg-brand-600 text-white rotate-45' : ''"
                                >
                                    <x-icon name="plus" class="h-4 w-4" />
                                </span>
                            </button>

                            <div x-show="open" x-transition x-cloak class="border-t border-slate-100 bg-slate-50/60 px-6 py-5 text-sm leading-relaxed text-slate-600">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

</x-layout>
