@extends('layouts.main')

@section('title', 'Users')

@section('content')
<div class="max-w-6xl mx-auto">
    <h2 class="text-3xl font-bold mb-8 gradient-bg text-transparent bg-clip-text">Community Users</h2>

    @if ($users->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
            No verified users found.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($users as $user)
                <a href="/users/{{ $user->id }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <!-- User Avatar -->
                    @if ($user->profile && $user->profile->avatar)
                        <img src="/storage/{{ $user->profile->avatar }}" class="w-16 h-16 rounded-full mb-3">
                    @else
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full mb-3 flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <!-- User Info -->
                    <h3 class="text-lg font-bold mb-1">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $user->email }}</p>

                    <!-- Bio Preview -->
                    @if ($user->profile && $user->profile->bio)
                        <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ $user->profile->bio }}</p>
                    @else
                        <p class="text-sm text-gray-500 mb-3 italic">No bio</p>
                    @endif

                    <!-- Location -->
                    @if ($user->profile && $user->profile->city)
                        <p class="text-xs text-gray-600 mb-3">📍 {{ $user->profile->city }}{{ $user->profile->country ? ', ' . $user->profile->country : '' }}</p>
                    @endif

                    <!-- View Button -->
                    <span class="inline-block gradient-bg text-white text-sm px-3 py-1 rounded">View Profile →</span>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $users->links() }}
        </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
