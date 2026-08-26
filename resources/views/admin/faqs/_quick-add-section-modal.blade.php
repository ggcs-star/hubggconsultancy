<x-modal name="quick-add-faq-section" max-width="md">
    <form id="quick-add-faq-section-form" onsubmit="return submitQuickAddFaqSection(event)">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-800">Add Section</h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <div class="space-y-3 px-6 py-6">
            <div>
                <label class="form-label">Section Name</label>
                <input type="text" id="quick-faq-section-name" required placeholder="e.g. Billing, Courses, Getting Started" class="form-input">
            </div>
            <p id="quick-faq-section-error" class="hidden text-sm text-red-600"></p>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
            <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
            <button type="submit" class="btn-primary">Add Section</button>
        </div>
    </form>
</x-modal>

<script>
    async function submitQuickAddFaqSection(event) {
        event.preventDefault();

        const nameInput = document.getElementById('quick-faq-section-name');
        const errorEl = document.getElementById('quick-faq-section-error');
        errorEl.classList.add('hidden');

        try {
            const response = await fetch('{{ route('admin.faq-sections.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ name: nameInput.value }),
            });

            const data = await response.json();

            if (!response.ok) {
                errorEl.textContent = data.errors?.name?.[0] ?? data.message ?? 'Could not add section.';
                errorEl.classList.remove('hidden');
                return false;
            }

            document.querySelectorAll('.faq-section-select').forEach((select) => {
                select.add(new Option(data.name, data.id, true, true));
            });

            nameInput.value = '';
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'quick-add-faq-section' }));
        } catch (error) {
            errorEl.textContent = 'Something went wrong. Please try again.';
            errorEl.classList.remove('hidden');
        }

        return false;
    }
</script>
