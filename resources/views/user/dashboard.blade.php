<x-layout title="Dashboard" subtitle="Track your onboarding journey with Pre Sales School">

    <div class="card bg-gradient-to-r from-brand-700 to-brand-600 p-6 text-white">
        <p class="text-sm text-brand-100">Welcome back,</p>
        <h2 class="text-2xl font-extrabold">{{ $user->name }}</h2>
        <p class="mt-2 max-w-xl text-sm text-brand-100">
            Complete your profile, apply to become a salesperson, and work through your training to get certified.
        </p>
    </div>

    @php
        $steps = [
            ['label' => 'Onboard', 'done' => true],
            ['label' => 'Complete Profile', 'done' => $user->profile_completed],
            ['label' => 'Apply as Salesperson', 'done' => in_array($user->salesperson_status, ['pending', 'approved'])],
            ['label' => 'Get Approved', 'done' => $user->salesperson_status === 'approved'],
        ];
    @endphp

    <div class="card mt-6 p-6">
        <h3 class="font-bold text-slate-800">Your Progress</h3>
        <div class="mt-6 flex items-center">
            @foreach ($steps as $i => $step)
                <div class="flex flex-1 flex-col items-center text-center">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold
                        {{ $step['done'] ? 'bg-brand-700 text-white' : 'border-2 border-slate-200 text-slate-400' }}">
                        @if ($step['done'])
                            <x-icon name="check-circle" class="h-5 w-5" />
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <p class="mt-2 text-xs font-medium text-slate-500">{{ $step['label'] }}</p>
                </div>
                @if (!$loop->last)
                    <div class="-mt-6 h-0.5 flex-1 {{ $step['done'] ? 'bg-brand-700' : 'bg-slate-200' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('user.profile') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-brand-50 p-2.5 text-brand-700 inline-flex"><x-icon name="user" /></span>
            <p class="mt-4 font-semibold text-slate-800">My Profile</p>
            <p class="text-sm text-slate-400">{{ $user->profile_completed ? 'Completed' : 'Complete your profile' }}</p>
        </a>
        <a href="{{ route('user.apply-salesperson') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-amber-50 p-2.5 text-amber-600 inline-flex"><x-icon name="briefcase" /></span>
            <p class="mt-4 font-semibold text-slate-800">Apply as Salesperson</p>
            <p class="text-sm text-slate-400">Status: {{ ucfirst($user->salesperson_status) }}</p>
        </a>
        <a href="{{ route('user.training') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-sky-50 p-2.5 text-sky-600 inline-flex"><x-icon name="academic-cap" /></span>
            <p class="mt-4 font-semibold text-slate-800">Training / LMS</p>
            <p class="text-sm text-slate-400">Courses & lessons</p>
        </a>
        <a href="{{ route('user.manuals') }}" class="card p-5 transition hover:border-brand-200 hover:shadow-md">
            <span class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 inline-flex"><x-icon name="document" /></span>
            <p class="mt-4 font-semibold text-slate-800">Sales Manuals</p>
            <p class="text-sm text-slate-400">PPTs & documents</p>
        </a>
    </div>

</x-layout>
