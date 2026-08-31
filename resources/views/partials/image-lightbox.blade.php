<div
    x-data="{ show: false, src: null }"
    x-on:open-image-preview.window="src = $event.detail; show = true"
    x-show="show"
    x-cloak
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-slate-900/80"
        x-on:click="show = false"
    ></div>

    <div
        x-show="show"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="relative max-h-[90vh] max-w-[90vw]"
    >
        <button type="button" x-on:click="show = false" class="absolute -right-3 -top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-600 shadow-lg transition hover:text-slate-900">
            <x-icon name="x" class="h-4 w-4" />
        </button>
        <img :src="src" alt="" class="max-h-[90vh] max-w-[90vw] rounded-xl object-contain shadow-2xl">
    </div>
</div>
