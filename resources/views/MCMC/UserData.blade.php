@extends('layouts.dashboard')
@section('content')
<div class="py-12">
    <div class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800 tracking-tight">Manage User Data</h2>

        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded bg-green-100 text-green-800 font-semibold shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 text-right">
            <a href="{{ route('MCMC.RegisterUser', ['user_id' => Auth::id()]) }}"
                class="inline-block px-4 py-2 bg-blue-500 text-white font-bold rounded hover:bg-blue-700">
                + Register New User
            </a>

        </div>

        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-r from-blue-400 to-purple-400 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold">No.</th>
                        <th class="px-4 py-3 text-center font-semibold">Name</th>
                        <th class="px-4 py-3 text-center font-semibold">Email</th>
                        <th class="px-4 py-3 text-center font-semibold">Role</th>
                        <th class="px-4 py-3 text-center font-semibold">Registered At</th>
                        <th class="px-4 py-3 text-center font-semibold">Activity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr class="transition hover:bg-green-50">
                        <td class="px-4 py-3 text-center text-gray-700 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-center">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-sm font-bold 
                            {{ $user->role == 'MCMC' ? 'bg-blue-200 text-blue-800' : ($user->role == 'Agency' ? 'bg-purple-200 text-purple-800' : 'bg-gray-200 text-gray-800') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('MCMC.ViewUserActivity', ['user_id' => Auth::id(), 'target_user_id' => $user->id]) }}"
                               class="inline-block px-4 py-2 bg-indigo-500 text-white font-bold rounded hover:bg-indigo-700">View Logs</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-lg">
                            No users registered.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-6">
            <a href="{{ route('MCMC.UserReport', ['user_id' => Auth::id()]) }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow">
                📈 View User Report
            </a>
        </div>
    </div>
</div>
@endsection