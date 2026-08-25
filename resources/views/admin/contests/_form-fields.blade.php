@php
    $isEdit = (bool) $contest;
    $selectedUserIds = old('selected_user_ids', $isEdit ? $contest->participants()->pluck('users.id')->all() : []);
    $existingPointRules = $isEdit ? $contest->pointRules->pluck('points', 'lead_status') : collect();
@endphp

<div class="space-y-8">
    <div class="card p-6">
        <h2 class="font-bold text-slate-800">Basic Information</h2>
        <div class="mt-4 space-y-5">
            <div>
                <label class="form-label">Contest Name</label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $contest->name : '') }}" required placeholder="e.g. GG Prime Mega Contest" class="form-input">
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input" placeholder="Optional details shown to participants">{{ old('description', $isEdit ? $contest->description : '') }}</textarea>
            </div>

            @php
                $startsAtValue = old('starts_at', $isEdit ? $contest->starts_at->format('Y-m-d') : '');
                $endsAtValue = old('ends_at', $isEdit ? $contest->ends_at->format('Y-m-d') : '');
            @endphp

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2" x-data="{ startsAt: @js($startsAtValue) }">
                <div>
                    <label class="form-label">Start Date</label>
                    <input type="date" name="starts_at" x-model="startsAt" required class="form-input">
                    <x-input-error :messages="$errors->get('starts_at')" class="mt-1" />
                </div>
                <div>
                    <label class="form-label">End Date</label>
                    <input type="date" name="ends_at" value="{{ $endsAtValue }}" :min="startsAt" required class="form-input">
                    <x-input-error :messages="$errors->get('ends_at')" class="mt-1" />
                </div>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="font-bold text-slate-800">Target</h2>
        <div class="mt-4 space-y-5">
            <div>
                <label class="form-label">Target Type</label>
                <select name="target_type" required class="form-input">
                    @foreach (['sales' => 'Sales', 'revenue' => 'Revenue', 'orders' => 'Orders', 'new_customers' => 'New Customers'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('target_type', $isEdit ? $contest->target_type : 'sales') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="form-label">Target Value</label>
                    <input type="number" name="target_value" step="0.01" min="0.01" required value="{{ old('target_value', $isEdit ? $contest->target_value : '') }}" placeholder="2500000" class="form-input">
                    <p class="mt-1 text-xs text-slate-400">The raw number used to calculate progress %.</p>
                </div>
                <div>
                    <label class="form-label">Target Display Label</label>
                    <input type="text" name="target" value="{{ old('target', $isEdit ? $contest->target : '') }}" placeholder="e.g. ₹25 Lakh" class="form-input">
                    <p class="mt-1 text-xs text-slate-400">Optional — shown instead of the raw number.</p>
                </div>
            </div>

            <div>
                <label class="form-label">Contest Type</label>
                <div class="flex gap-3">
                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                        <input type="radio" name="participation_type" value="individual" class="text-brand-600" @checked(old('participation_type', $isEdit ? $contest->participation_type : 'individual') === 'individual')>
                        Individual
                    </label>
                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                        <input type="radio" name="participation_type" value="team" class="text-brand-600" @checked(old('participation_type', $isEdit ? $contest->participation_type : '') === 'team')>
                        Team
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-6" x-data="{ source: @js(old('achievement_source', $isEdit ? $contest->achievement_source : 'manual')) }">
        <h2 class="font-bold text-slate-800">Achievement Source</h2>
        <div class="mt-4 space-y-5">
            <div class="flex gap-3">
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="achievement_source" value="manual" x-model="source" class="text-brand-600">
                    Manual (admin logs each sale)
                </label>
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="achievement_source" value="crm" x-model="source" class="text-brand-600">
                    CRM Activity (auto points per lead stage)
                </label>
            </div>

            <div x-show="source === 'crm'" x-cloak>
                <label class="form-label">Points Per Lead Stage</label>
                <p class="mt-1 text-xs text-slate-400">When a participant's lead reaches a stage below, they're automatically awarded that many points toward this contest's target. Leave a stage at 0 to ignore it.</p>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach (\App\Models\Lead::statusLabels() as $value => $label)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-3 py-2">
                            <span class="text-sm text-slate-600">{{ $label }}</span>
                            <input type="number" name="point_rules[{{ $value }}]" min="0" value="{{ old('point_rules.' . $value, $existingPointRules[$value] ?? 0) }}" class="form-input w-24 text-right">
                        </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-slate-400">With this source, target values above and the tracker/leaderboard show "pts" instead of ₹.</p>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="font-bold text-slate-800">Reward</h2>
        <div class="mt-4 space-y-5">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="form-label">Winner Reward</label>
                    <input type="text" name="reward" value="{{ old('reward', $isEdit ? $contest->reward : '') }}" placeholder="e.g. 25,000 Bonus" class="form-input">
                </div>
                <div>
                    <label class="form-label">Reward Type</label>
                    <select name="reward_type" class="form-input">
                        <option value="">Select type</option>
                        @foreach (['points' => 'Points', 'bonus' => 'Bonus', 'cash' => 'Cash', 'gift' => 'Gift'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('reward_type', $isEdit ? $contest->reward_type : '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="form-label">2nd Place Reward <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="text" name="reward_second" value="{{ old('reward_second', $isEdit ? $contest->reward_second : '') }}" placeholder="e.g. 15,000 Bonus" class="form-input">
                </div>
                <div>
                    <label class="form-label">3rd Place Reward <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="text" name="reward_third" value="{{ old('reward_third', $isEdit ? $contest->reward_third : '') }}" placeholder="e.g. 10,000 Bonus" class="form-input">
                </div>
            </div>
        </div>
    </div>

    <div class="card p-6" x-data="{ mode: @js(old('participant_mode', $isEdit ? $contest->participant_mode : 'open')) }">
        <h2 class="font-bold text-slate-800">Participants</h2>
        <div class="mt-4 space-y-5">
            <div class="flex gap-3">
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="participant_mode" value="open" x-model="mode" class="text-brand-600">
                    Open to All Eligible Users
                </label>
                <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                    <input type="radio" name="participant_mode" value="selected" x-model="mode" class="text-brand-600">
                    Select Individual Users
                </label>
            </div>

            <p class="text-xs text-slate-400" x-show="mode === 'open'" x-cloak>Every approved salesperson can see and join this contest themselves.</p>

            <div x-show="mode === 'selected'" x-cloak>
                <label class="form-label">Select Participants</label>
                <div class="max-h-56 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-3">
                    @forelse ($eligibleUsers as $eligibleUser)
                        <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
                            <input type="checkbox" name="selected_user_ids[]" value="{{ $eligibleUser->id }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(in_array($eligibleUser->id, $selectedUserIds))>
                            {{ $eligibleUser->name }} <span class="text-slate-400">({{ $eligibleUser->email }})</span>
                        </label>
                    @empty
                        <p class="px-2 py-1.5 text-sm text-slate-400">No approved salespersons yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="font-bold text-slate-800">Rules</h2>
        <div class="mt-4 space-y-5">
            <div>
                <label class="form-label">Minimum Achievement <span class="font-normal text-slate-400">(optional)</span></label>
                <input type="number" name="min_achievement" step="0.01" min="0" value="{{ old('min_achievement', $isEdit ? $contest->min_achievement : '') }}" placeholder="e.g. 500000" class="form-input w-48">
                <p class="mt-1 text-xs text-slate-400">A participant must reach at least this amount to be eligible for a reward.</p>
            </div>

            <div>
                <label class="form-label">How Sales Are Counted <span class="font-normal text-slate-400">(optional)</span></label>
                <input type="text" name="counting_method" value="{{ old('counting_method', $isEdit ? $contest->counting_method : '') }}" placeholder="e.g. Only fully paid orders count" class="form-input">
            </div>

            <div>
                <label class="form-label">Tie-Breaker Rule <span class="font-normal text-slate-400">(optional)</span></label>
                <input type="text" name="tie_breaker" value="{{ old('tie_breaker', $isEdit ? $contest->tie_breaker : '') }}" placeholder="e.g. Whoever reached the amount first wins" class="form-input">
            </div>

            <div>
                <label class="form-label">Eligibility Conditions <span class="font-normal text-slate-400">(optional)</span></label>
                <textarea name="eligibility" rows="2" class="form-input" placeholder="e.g. Only salespersons approved before the contest start date">{{ old('eligibility', $isEdit ? $contest->eligibility : '') }}</textarea>
            </div>

            <div>
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $isEdit ? $contest->sort_order : '') }}" min="0" placeholder="0" class="form-input w-32">
            </div>
        </div>
    </div>
</div>
