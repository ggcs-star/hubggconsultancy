<x-modal name="quick-add-campaign" max-width="md">
    <form id="quick-add-campaign-form" onsubmit="return submitQuickAddCampaign(event)">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-800">Add Campaign</h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <div class="space-y-3 px-6 py-6">
            <div>
                <label class="form-label">Campaign Name</label>
                <input type="text" id="quick-campaign-name" required placeholder="e.g. GG Prime August Campaign" class="form-input">
            </div>
            <p id="quick-campaign-error" class="hidden text-sm text-red-600"></p>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
            <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</button>
            <button type="submit" class="btn-primary">Add Campaign</button>
        </div>
    </form>
</x-modal>

<script>
    async function submitQuickAddCampaign(event) {
        event.preventDefault();

        const nameInput = document.getElementById('quick-campaign-name');
        const errorEl = document.getElementById('quick-campaign-error');
        errorEl.classList.add('hidden');

        try {
            const response = await fetch('{{ route('user.campaigns.store') }}', {
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
                errorEl.textContent = data.errors?.name?.[0] ?? data.message ?? 'Could not add campaign.';
                errorEl.classList.remove('hidden');
                return false;
            }

            const select = document.getElementById('campaign-select');
            select.add(new Option(data.name, data.id, true, true));

            nameInput.value = '';
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'quick-add-campaign' }));
        } catch (error) {
            errorEl.textContent = 'Something went wrong. Please try again.';
            errorEl.classList.remove('hidden');
        }

        return false;
    }
</script>
