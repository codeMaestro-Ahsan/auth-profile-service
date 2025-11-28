@extends('layouts.main')

@section('title', $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header with Avatar -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-start gap-6">
            <!-- Avatar -->
            @if ($user->profile && $user->profile->avatar)
                <img src="/storage/{{ $user->profile->avatar }}" class="w-24 h-24 rounded-full">
            @else
                <div class="w-24 h-24 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-3xl">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

            <!-- User Info -->
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                <p class="text-lg text-gray-600 mb-2">{{ $user->email }}</p>
                <p class="badge badge-success mb-4">Email Verified ✓</p>

                @if ($user->profile && $user->profile->bio)
                    <p class="text-gray-700 italic">{{ $user->profile->bio }}</p>
                @else
                    <p class="text-gray-500 italic">No bio provided</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    @if ($user->profile)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Contact Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4">Contact Information</h3>
                <div class="space-y-3">
                    <p><strong>Email:</strong> <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:underline">{{ $user->email }}</a></p>
                    @if ($user->profile->phone)
                        <p><strong>Phone:</strong> {{ $user->profile->phone }}</p>
                    @else
                        <p><strong>Phone:</strong> <span class="text-gray-500">Not provided</span></p>
                    @endif
                </div>
            </div>

            <!-- Location Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4">Location</h3>
                <div class="space-y-3">
                    @if ($user->profile->country || $user->profile->city)
                        <p><strong>Country:</strong> {{ $user->profile->country ?? 'Not provided' }}</p>
                        <p><strong>City:</strong> {{ $user->profile->city ?? 'Not provided' }}</p>
                    @else
                        <p class="text-gray-500">Location not provided</p>
                    @endif
                </div>
            </div>

            <!-- Personal Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4">Personal Information</h3>
                <div class="space-y-3">
                    @if ($user->profile->gender)
                        <p><strong>Gender:</strong> <span class="capitalize">{{ $user->profile->gender }}</span></p>
                    @else
                        <p><strong>Gender:</strong> <span class="text-gray-500">Not provided</span></p>
                    @endif

                    @if ($user->profile->dob)
                        <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($user->profile->dob)->format('M d, Y') }}</p>
                    @else
                        <p><strong>Date of Birth:</strong> <span class="text-gray-500">Not provided</span></p>
                    @endif
                </div>
            </div>

            <!-- Membership -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4">Membership</h3>
                <div class="space-y-3">
                    <p><strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    <p><strong>Account Age:</strong> {{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
            This user has not completed their profile yet.
        </div>
    @endif

    <!-- Back Button -->
    <div class="mt-8">
        <a href="/users" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500">← Back to Users</a>
    </div>
</div>
@endsection
