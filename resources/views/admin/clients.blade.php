<x-layout title="Clients" subtitle="Everyone who has registered on Pre Sales School">

    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-5 py-3 font-semibold">Client</th>
                        <th class="px-5 py-3 font-semibold">Email</th>
                        <th class="px-5 py-3 font-semibold">Phone</th>
                        <th class="px-5 py-3 font-semibold">Profile</th>
                        <th class="px-5 py-3 font-semibold">Salesperson Status</th>
                        <th class="px-5 py-3 font-semibold">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <tr>
                            <td class="flex items-center gap-3 px-5 py-3.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-700 text-xs font-semibold text-white">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </span>
                                <span class="font-medium text-slate-700">{{ $client->name }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $client->email }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $client->phone ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if ($client->profile_completed)
                                    <span class="badge badge-green"><x-icon name="check-circle" class="h-3.5 w-3.5" /> Complete</span>
                                @else
                                    <span class="badge badge-slate">Incomplete</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $map = ['none' => 'badge-slate', 'pending' => 'badge-amber', 'approved' => 'badge-green', 'rejected' => 'badge-slate'];
                                @endphp
                                <span class="badge {{ $map[$client->salesperson_status] }}">{{ ucfirst($client->salesperson_status) }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">{{ $client->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">No clients have registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($clients->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

</x-layout>
