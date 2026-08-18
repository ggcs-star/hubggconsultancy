<div class="mt-6 max-w-2xl">
    <div class="card p-6">
        <h2 class="font-bold text-slate-800">Assessment Settings</h2>
        <p class="text-sm text-slate-400">Control visibility and the passing score across every quiz</p>

        <form method="POST" action="{{ route('admin.onboarding-assessment.settings.update') }}" class="mt-4 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">Passing Score (%)</label>
                <input type="number" name="passing_score_percent" min="1" max="100" value="{{ old('passing_score_percent', $settings->passing_score_percent) }}" required class="form-input">
                <p class="mt-1 text-xs text-slate-400">Applied to the combined score across every quiz (total points earned &divide; total points possible).</p>
            </div>

            <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $settings->is_published)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Published — visible to salespeople
            </label>
            <p class="text-xs text-slate-400">Each quiz also needs its own Published toggle (from the Quizzes tab) to actually be reachable.</p>

            <button type="submit" class="btn-primary w-full sm:w-auto">Save Settings</button>
        </form>
    </div>
</div>
