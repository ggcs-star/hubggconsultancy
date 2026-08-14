<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SaasProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
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
        ]);

        $interestIds = $data['interests'] ?? [];
        unset($data['interests']);

        $user = $request->user();
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
