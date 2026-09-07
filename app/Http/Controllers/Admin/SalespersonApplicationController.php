<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OnboardingAssessmentScorer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalespersonApplicationController extends Controller
{
    public function index(Request $request, OnboardingAssessmentScorer $scorer): View
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');

        $applications = User::where('role', 'user')
            ->where('salesperson_status', '!=', 'none')
            ->with(['interests'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('salesperson_status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $applications->through(function (User $applicant) use ($scorer) {
            $applicant->assessmentScore = $scorer->score($applicant);

            return $applicant;
        });

        return view('admin.salesperson-applications', [
            'applications' => $applications,
        ]);
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['salesperson_status' => 'approved']);

        return back()->with('status', "{$user->name}'s application was approved.");
    }

    public function reject(User $user): RedirectResponse
    {
        $user->update(['salesperson_status' => 'rejected']);

        return back()->with('status', "{$user->name}'s application was rejected.");
    }
}
