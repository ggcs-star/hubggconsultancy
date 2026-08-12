{{--
    Global confirm dialog — replaces native window.confirm() popups.
    Any form can trigger it instead of onsubmit="return confirm('...')":

        <form x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this?', target: $el })">

    Calling target.submit() bypasses the 'submit' event entirely (a native
    browser quirk), so it submits directly without re-triggering this same
    confirm prompt.
--}}
<div
    x-data="{ show: false, message: '', target: null }"
    x-on:confirm-action.window="show = true; message = $event.detail.message; target = $event.detail.target"
    x-show="show"
    x-cloak
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-[60] overflow-y-auto px-4 py-6 sm:px-0"
>
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50"
        x-on:click="show = false"
    ></div>

    <div
        x-show="show"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
        class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl"
    >
        <p class="text-sm font-medium text-slate-700" x-text="message"></p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50" x-on:click="show = false">
                Cancel
            </button>
            <button type="button" class="btn-primary" x-on:click="target.submit(); show = false">
                Confirm
            </button>
        </div>
    </div>
</div>
