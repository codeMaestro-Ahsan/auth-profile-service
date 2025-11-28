@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold mb-8 gradient-bg text-transparent bg-clip-text">My Dashboard</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- User Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold mb-4">Account Info</h3>
            <p class="mb-2"><strong>Name:</strong> {{ $user->name }}</p>
            <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="mb-4"><strong>Status:</strong> <span class="badge badge-success">Verified</span></p>
            <a href="/account/edit" class="gradient-bg text-white px-4 py-2 rounded text-sm">Edit Account</a>
        </div>

        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold mb-4">My Profile</h3>
            @if ($profile && $profile->avatar)
                <img src="/storage/{{ $profile->avatar }}" class="w-20 h-20 rounded-full mb-2">
            @else
                <div class="w-20 h-20 bg-gray-300 rounded-full mb-2 flex items-center justify-center">No Image</div>
            @endif
            <p class="text-sm text-gray-600 mb-4">{{ $profile?->bio ?? 'No bio' }}</p>
            <a href="/profile/edit" class="gradient-bg text-white px-4 py-2 rounded text-sm">Edit Profile</a>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold mb-4">Quick Stats</h3>
            <p class="mb-2">Member since: {{ $user->created_at->format('M d, Y') }}</p>
            <p class="mb-4">{{ \App\Models\User::count() }} total users</p>
            <a href="/users" class="gradient-bg text-white px-4 py-2 rounded text-sm">View Users</a>
        </div>
    </div>

    <!-- Profile Details -->
    @if ($profile)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-bold mb-4">Profile Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <p><strong>Phone:</strong> {{ $profile->phone ?? 'N/A' }}</p>
            <p><strong>Gender:</strong> {{ $profile->gender ?? 'N/A' }}</p>
            <p><strong>Date of Birth:</strong> {{ $profile->dob ?? 'N/A' }}</p>
            <p><strong>Country:</strong> {{ $profile->country ?? 'N/A' }}</p>
            <p><strong>City:</strong> {{ $profile->city ?? 'N/A' }}</p>
            <p><strong>Bio:</strong> {{ $profile->bio ?? 'N/A' }}</p>
        </div>
    </div>
    @endif

    <!-- Danger Zone -->
    <div class="bg-red-50 rounded-lg border border-red-200 p-6">
        <h3 class="text-lg font-bold text-red-600 mb-4">Danger Zone</h3>
        <form method="POST" action="/account/delete" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
            @csrf
            <p class="text-gray-600 mb-4">Delete your account and all associated data permanently.</p>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete Account</button>
        </form>
    </div>
</div>
@endsection
