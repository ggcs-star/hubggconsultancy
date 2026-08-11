<x-layout title="Salesperson Applications" subtitle="Review and approve clients who applied to become salespeople">

    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">Applicant</th>
                        <th class="px-5 py-3 font-semibold">Email</th>
                        <th class="px-5 py-3 font-semibold">City</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($applications as $applicant)
                        <tr>
                            <td class="flex items-center gap-3 px-5 py-3.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                    {{ strtoupper(substr($applicant->name, 0, 1)) }}
                                </span>
                                <span class="font-medium text-slate-700">{{ $applicant->name }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $applicant->email }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $applicant->city ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $map = ['pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-slate'];
                                @endphp
                                <span class="badge {{ $map[$applicant->salesperson_status] ?? 'badge-slate' }}">{{ ucfirst($applicant->salesperson_status) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($applicant->salesperson_status === 'pending')
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.salesperson-applications.approve', $applicant) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-100">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.salesperson-applications.reject', $applicant) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">No action needed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">No applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($applications->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</x-layout>
