@props(['name' => 'language', 'selected' => 'english'])

@php
    $languageOptions = \App\Models\Language::ordered()->get(['code', 'name']);
    $modalName = 'add-language-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    x-data="{
        lang: '{{ $selected }}',
        prevLang: '{{ $selected }}',
        languages: @js($languageOptions),
        newLanguageName: '',
        saving: false,
        error: null,
        onSelect() {
            if (this.lang === '__new__') {
                this.lang = this.prevLang;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: '{{ $modalName }}' }));
            } else {
                this.prevLang = this.lang;
            }
        },
        saveLanguage() {
            if (! this.newLanguageName.trim()) {
                this.error = 'Enter a name for the new language.';
                return;
            }
            this.saving = true;
            this.error = null;
            window.axios.post('{{ route('admin.languages.store') }}', { name: this.newLanguageName })
                .then(({ data }) => {
                    this.languages.push(data);
                    this.lang = data.code;
                    this.prevLang = data.code;
                    this.newLanguageName = '';
                    this.saving = false;
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: '{{ $modalName }}' }));
                })
                .catch((err) => {
                    this.saving = false;
                    this.error = err.response?.data?.errors?.name?.[0] || 'Something went wrong — try again.';
                });
        },
    }"
>
    <select name="{{ $name }}" {{ $attributes->except('class') }} {{ $attributes->class(['form-input' => ! $attributes->has('class')]) }} x-model="lang" x-on:change="onSelect()">
        <template x-for="option in languages" :key="option.code">
            <option :value="option.code" x-text="option.name"></option>
        </template>
        <option value="__new__">+ Add Language</option>
    </select>

    <x-modal :name="$modalName" max-width="sm">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">Add Language</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="mt-4">
                <label class="form-label">Language Name</label>
                <input type="text" x-model="newLanguageName" x-on:keydown.enter.prevent="saveLanguage()" placeholder="e.g. Bengali" class="form-input" autofocus>
                <p x-show="error" x-text="error" class="mt-1 text-xs text-red-600"></p>
                <p class="mt-1 text-xs text-slate-400">This will be added to the language list for everyone, everywhere.</p>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="button" x-on:click="saveLanguage()" x-bind:disabled="saving" class="btn-primary disabled:cursor-not-allowed disabled:opacity-50">
                    <span x-text="saving ? 'Saving…' : 'Save'"></span>
                </button>
            </div>
        </div>
    </x-modal>
</div>
