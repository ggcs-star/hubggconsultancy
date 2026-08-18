<x-layout title="Onboarding Assessment" subtitle="Configure the assessment salespeople take, and review results">

    @php
        $tabs = [
            'quizzes' => ['label' => 'Quizzes', 'icon' => 'list'],
            'settings' => ['label' => 'Settings', 'icon' => 'edit'],
            'results' => ['label' => 'Results', 'icon' => 'badge'],
        ];
    @endphp

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
    @elseif ($activeTab === 'settings')
        @include('admin.onboarding-assessment._settings-tab')
    @else
        @include('admin.onboarding-assessment._results-tab')
    @endif

</x-layout>
