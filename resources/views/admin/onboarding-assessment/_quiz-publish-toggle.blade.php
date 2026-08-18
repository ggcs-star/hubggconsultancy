{{-- Single badge showing current state — click opens a small dropdown to switch it.
     Same Alpine dropdown idiom as the "..." quiz-list menu on this same page. --}}
<div class="relative z-10 inline-block" x-data="{ open: false }" x-on:click.outside="open = false">
    <button type="button" x-on:click.stop="open = !open"
        class="badge inline-flex items-center gap-1 {{ $quiz->is_published ? 'badge-green' : 'badge-slate' }}">
        {{ $quiz->is_published ? 'Published' : 'Draft' }}
        <x-icon name="chevron-down" class="h-3 w-3" />
    </button>

    <div x-show="open" x-cloak x-on:click.stop x-on:submit="open = false"
        class="absolute left-0 top-full z-20 mt-1 w-36 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
        <form method="POST" action="{{ route('admin.onboarding-assessment.quizzes.toggle-published', $quiz) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_published" value="0">
            <button type="submit" class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-xs font-medium {{ ! $quiz->is_published ? 'text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">
                <span class="h-2 w-2 shrink-0 rounded-full bg-slate-400"></span>
                Draft
                @if (! $quiz->is_published) <x-icon name="check" class="ml-auto h-3.5 w-3.5" /> @endif
            </button>
        </form>
        <form method="POST" action="{{ route('admin.onboarding-assessment.quizzes.toggle-published', $quiz) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_published" value="1">
            <button type="submit" class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-xs font-medium {{ $quiz->is_published ? 'text-slate-800' : 'text-slate-500 hover:bg-slate-50' }}">
                <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
                Published
                @if ($quiz->is_published) <x-icon name="check" class="ml-auto h-3.5 w-3.5" /> @endif
            </button>
        </form>
    </div>
</div>
