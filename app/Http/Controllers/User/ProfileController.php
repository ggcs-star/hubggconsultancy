<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SaasProduct;
use App\Models\User;
use App\Services\TeamApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private TeamApiService $teamApi)
    {
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('user.profile', [
            'user' => $user,
            'saasProducts' => SaasProduct::active()->ordered()->get(),
            'interestIds' => $user->interests()->pluck('saas_products.id')->all(),
            'isBypassAccount' => User::isGgBypassEmail($user->email),
            'ggProfile' => $this->ggProfile($user),
        ]);
    }

    /**
     * GG Prime's own account snapshot (balance, income, team size, KYC
     * status, sponsor, ...) shown on the profile page — cached briefly since
     * this is a live external call and the figures don't need to be
     * second-by-second fresh. Returns null when the user isn't linked to a
     * GG Prime account or the API can't be reached.
     */
    private function ggProfile(User $user): ?array
    {
        if (! $user->gg_user_id) {
            return null;
        }

        return Cache::remember("gg_profile_{$user->id}", now()->addMinutes(10), function () use ($user) {
            $result = $this->teamApi->profile(['user_id' => $user->gg_user_id]);

            return $result->status === 'found' ? $result->data : null;
        });
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isBypassAccount = User::isGgBypassEmail($user->email);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'highest_qualification' => ['nullable', 'string', 'max:255'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'education_year' => ['nullable', 'string', 'max:10'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['integer', 'exists:saas_products,id'],
        ];

        // gg_user_id/phone are auto-synced from GG Prime for real accounts —
        // only test/QA bypass accounts (which never get that sync) may set
        // them by hand.
        if ($isBypassAccount) {
            $rules['phone'] = ['nullable', 'string', 'max:30'];
            $rules['gg_user_id'] = ['nullable', 'string', 'max:100', Rule::unique('users', 'gg_user_id')->ignore($user->id)];
        }

        $data = $request->validate($rules);

        $interestIds = $data['interests'] ?? [];
        unset($data['interests']);

        $wasIncomplete = ! $user->profile_completed;

        $data['profile_completed'] = true;

        $user->update($data);
        $user->interests()->sync($interestIds);

        if ($wasIncomplete) {
            return redirect()->route('user.onboarding-assessment.index')
                ->with('status', 'Profile completed. You can now take the onboarding assessment.');
        }

        return back()->with('status', 'Profile updated successfully.');
    }
}
