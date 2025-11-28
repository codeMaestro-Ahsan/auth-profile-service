@extends('layouts.main')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 gradient-bg text-transparent bg-clip-text">Create New Password</h2>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Email Address</label>
            <input type="email" name="email" 
                   class="w-full px-4 py-2 border rounded bg-gray-100" 
                   value="{{ old('email', request()->query('email')) }}" 
                   readonly>
            @error('email') 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">New Password</label>
            <input type="password" name="password" 
                   class="w-full px-4 py-2 border rounded @error('password') border-red-500 @enderror" 
                   required>
            @error('password') 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" 
                   class="w-full px-4 py-2 border rounded @error('password_confirmation') border-red-500 @enderror" 
                   required>
            @error('password_confirmation') 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <button type="submit" class="w-full gradient-bg text-white py-2 rounded font-bold hover:opacity-90 transition">
            Reset Password
        </button>

        <p class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Back to login</a>
        </p>
    </form>
</div>
@endsection
