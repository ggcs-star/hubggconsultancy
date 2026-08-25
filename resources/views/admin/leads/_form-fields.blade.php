@php
    $isEdit = (bool) $lead;
@endphp

<div class="card p-6">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $isEdit ? $lead->name : '') }}" required placeholder="e.g. Rohit Malhotra" class="form-input">
        </div>
        <div>
            <label class="form-label">Company</label>
            <input type="text" name="company" value="{{ old('company', $isEdit ? $lead->company : '') }}" placeholder="e.g. Malhotra Traders" class="form-input">
        </div>
        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $isEdit ? $lead->email : '') }}" placeholder="e.g. rohit@example.com" class="form-input">
        </div>
        <div>
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $isEdit ? $lead->phone : '') }}" placeholder="e.g. +91 90000 00000" class="form-input">
        </div>
        <div>
            <label class="form-label">Interested In (Product)</label>
            <input type="text" name="product" value="{{ old('product', $isEdit ? $lead->product : '') }}" placeholder="e.g. UPOS" class="form-input">
        </div>
        <div>
            <label class="form-label">Expected Value (₹)</label>
            <input type="number" name="expected_value" step="0.01" min="0" value="{{ old('expected_value', $isEdit ? $lead->expected_value : '') }}" placeholder="e.g. 15000" class="form-input">
        </div>
        <div>
            <label class="form-label">Source</label>
            <input type="text" name="source" value="{{ old('source', $isEdit ? $lead->source : '') }}" list="lead-sources" placeholder="e.g. Website, Referral, Cold Call" class="form-input">
            <datalist id="lead-sources">
                <option value="Website"></option>
                <option value="Referral"></option>
                <option value="Cold Call / Direct"></option>
                <option value="Instagram"></option>
                <option value="Facebook"></option>
                <option value="LinkedIn"></option>
                <option value="WhatsApp"></option>
                <option value="Event / Exhibition"></option>
                <option value="Import"></option>
            </datalist>
        </div>
        <div>
            <label class="form-label">Campaign <span class="font-normal text-slate-400">(optional)</span></label>
            <div class="flex gap-2">
                <select name="campaign_id" id="campaign-select" class="form-input flex-1">
                    <option value="">No campaign</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->id }}" @selected(old('campaign_id', $isEdit ? $lead->campaign_id : '') == $campaign->id)>{{ $campaign->name }}</option>
                    @endforeach
                </select>
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'quick-add-campaign')" title="Add Campaign" class="shrink-0 rounded-xl border border-slate-200 px-3 text-slate-500 transition hover:border-brand-300 hover:text-brand-700">
                    <x-icon name="plus" class="h-4 w-4" />
                </button>
            </div>
        </div>
        <div>
            <label class="form-label">Priority</label>
            <select name="priority" required class="form-input">
                @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('priority', $isEdit ? $lead->priority : 'medium') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Assign To</label>
            <select name="assigned_to" class="form-input">
                <option value="">Unassigned</option>
                @foreach ($salespersons as $salesperson)
                    <option value="{{ $salesperson->id }}" @selected(old('assigned_to', $isEdit ? $lead->assigned_to : '') == $salesperson->id)>{{ $salesperson->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" required class="form-input">
                @foreach (\App\Models\Lead::statusLabels() as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $isEdit ? $lead->status : 'new') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Next Follow-up Date</label>
            <input type="date" name="next_follow_up_at" value="{{ old('next_follow_up_at', $isEdit && $lead->next_follow_up_at ? $lead->next_follow_up_at->format('Y-m-d') : '') }}" class="form-input">
        </div>
    </div>
</div>
