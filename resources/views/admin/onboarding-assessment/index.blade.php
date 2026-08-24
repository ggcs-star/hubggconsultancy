@php
    // Page heading always matches whichever sidebar item leads here — "Assessments" for the
    // Quizzes/Settings tabs, "My Results" for the results view — not the underlying route's name.
    $pageTitle = $activeTab === 'results' ? 'My Results' : 'Assessments';
    $pageSubtitle = $activeTab === 'results'
        ? "Every salesperson's onboarding assessment score."
        : 'Configure the assessment salespeople take.';
@endphp

<x-layout :title="$pageTitle" :subtitle="$pageSubtitle">

    @php
        $tabs = [
            'quizzes' => ['label' => 'Quizzes', 'icon' => 'list'],
            'settings' => ['label' => 'Settings', 'icon' => 'edit'],
        ];
    @endphp

    @if ($activeTab === 'results')
        {{-- Reached only via the "My Results" sidebar link — its own section, not part of the Quizzes/Settings tab switcher. --}}
        @include('admin.onboarding-assessment._results-tab')
    @else
        <div class="flex items-center justify-between border-b border-slate-200">
            <nav class="-mb-px flex gap-6 overflow-x-auto">
                @foreach ($tabs as $key => $tab)
                    <x-tab-link :href="route('admin.onboarding-assessment.index', ['tab' => $key])" :active="$activeTab === $key" :icon="$tab['icon']">
                        {{ $tab['label'] }}
                    </x-tab-link>
                @endforeach
            </nav>

            @if ($activeTab === 'quizzes')
                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-quiz')" class="btn-primary mb-2 shrink-0">
                    <x-icon name="plus" class="h-4 w-4" />
                    Add Quiz
                </button>
            @endif
        </div>

        @if ($activeTab === 'quizzes')
            @include('admin.onboarding-assessment._quizzes-tab')
        @else
            @include('admin.onboarding-assessment._settings-tab')
        @endif
    @endif

</x-layout>
