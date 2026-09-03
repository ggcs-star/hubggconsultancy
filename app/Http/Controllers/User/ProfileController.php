<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SaasProduct;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('user.profile', [
            'user' => $user,
            'saasProducts' => SaasProduct::active()->ordered()->get(),
            'interestIds' => $user->interests()->pluck('saas_products.id')->all(),
            'isBypassAccount' => User::isGgBypassEmail($user->email),
        ]);
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
