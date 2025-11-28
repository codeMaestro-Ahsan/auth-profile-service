@extends('layouts.main')

@section('title', 'Welcome')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Hero Section -->
    @guest
        <div class="text-center py-12">
            <h1 class="text-5xl font-bold mb-4 gradient-bg text-transparent bg-clip-text">Welcome to AuthApp</h1>
            <p class="text-xl text-gray-600 mb-8">A secure authentication system with profile management</p>
            
            <div class="flex gap-4 justify-center">
                <a href="/register" class="gradient-bg text-white px-8 py-3 rounded-lg text-lg hover:opacity-90 transition">
                    Create Account
                </a>
                <a href="/login" class="border-2 border-gray-600 text-gray-600 px-8 py-3 rounded-lg text-lg hover:bg-gray-100 transition">
                    Sign In
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-3xl mb-3">🔐</div>
                <h3 class="text-lg font-bold mb-2">Secure Authentication</h3>
                <p class="text-gray-600">Email verification and secure password reset for your account safety.</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-3xl mb-3">👤</div>
                <h3 class="text-lg font-bold mb-2">Profile Management</h3>
                <p class="text-gray-600">Create and manage your profile with photos, bio, and personal information.</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-3xl mb-3">👥</div>
                <h3 class="text-lg font-bold mb-2">Community</h3>
                <p class="text-gray-600">Connect with other users and view their profiles in our community.</p>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg shadow-lg p-8 mt-12 text-white text-center">
            <h2 class="text-3xl font-bold mb-3">Ready to get started?</h2>
            <p class="text-lg mb-6">Join our community today and create your profile</p>
            <a href="/register" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition inline-block">
                Register Now
            </a>
        </div>
    @else
        <!-- For Authenticated Users -->
        <div class="text-center py-12">
            <h1 class="text-4xl font-bold mb-4 gradient-bg text-transparent bg-clip-text">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-xl text-gray-600 mb-8">Here's what you can do next:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                <a href="/dashboard" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="text-4xl mb-3">📊</div>
                    <h3 class="text-lg font-bold mb-2">View Dashboard</h3>
                    <p class="text-gray-600 mb-4">Check your profile and account information</p>
                    <span class="text-purple-600 font-bold">Go →</span>
                </a>

                <a href="/profile/edit" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="text-4xl mb-3">✏️</div>
                    <h3 class="text-lg font-bold mb-2">Edit Profile</h3>
                    <p class="text-gray-600 mb-4">Update your profile information and avatar</p>
                    <span class="text-purple-600 font-bold">Go →</span>
                </a>

                <a href="/users" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="text-4xl mb-3">👥</div>
                    <h3 class="text-lg font-bold mb-2">View Users</h3>
                    <p class="text-gray-600 mb-4">Browse community profiles</p>
                    <span class="text-purple-600 font-bold">Go →</span>
                </a>
            </div>
        </div>
    @endguest
</div>
@endsection
        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
