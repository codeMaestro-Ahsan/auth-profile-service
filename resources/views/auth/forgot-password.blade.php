@extends('layouts.main')

@section('title', 'Forgot Password')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 gradient-bg text-transparent bg-clip-text">Reset Password</h2>
    
    <p class="text-gray-600 mb-6 text-sm">
        Enter your email address and we'll send you a link to reset your password.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                   class="w-full px-4 py-2 border rounded @error('email') border-red-500 @enderror" 
                   required>
            @error('email') 
                <span class="text-red-500 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <button type="submit" class="w-full gradient-bg text-white py-2 rounded font-bold hover:opacity-90 transition">
            Send Reset Link
        </button>

        <p class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Back to login</a>
        </p>
    </form>
</div>
@endsection
