<x-layout :title="$title" subtitle="Coming soon">

    <div class="card flex flex-col items-center justify-center px-6 py-20 text-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
            <x-icon :name="$icon" class="h-8 w-8" />
        </div>
        <h2 class="mt-5 text-xl font-bold text-slate-800">{{ $title }}</h2>
        <p class="mt-2 max-w-md text-sm text-slate-500">{{ $description }}</p>
        <span class="badge badge-slate mt-6">Coming soon</span>
    </div>

</x-layout>
