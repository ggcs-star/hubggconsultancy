<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
            ],
        ]);

        $search = trim($validated['search'] ?? '');
        $fromDate = $validated['from_date'] ?? '';
        $toDate = $validated['to_date'] ?? '';

        $applications = Application::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($subQuery) use ($like) {
                    $subQuery
                        ->where('full_name', 'like', $like)
                        ->orWhere('mobile_number', 'like', $like)
                        ->orWhere('whatsapp_number', 'like', $like)
                        ->orWhere('referral_name', 'like', $like)
                        ->orWhere('referral_mobile', 'like', $like)
                        ->orWhere('profession', 'like', $like)
                        ->orWhere('primary_interest', 'like', $like);
                });
            })
            ->when($fromDate !== '', function ($query) use ($fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            })
            ->when($toDate !== '', function ($query) use ($toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            })
            ->latest('created_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.referrals.index', compact(
            'applications',
            'search',
            'fromDate',
            'toDate'
        ));
    }
}
