@extends('layouts.main')

@section('title', 'Register')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 gradient-bg text-transparent bg-clip-text">Register</h2>
    
    <form method="POST" action="/register">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border rounded @error('name') border-red-500 @enderror" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border rounded @error('email') border-red-500 @enderror" required>
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Password</label>
            <input type="password" name="password" class="w-full px-4 py-2 border rounded @error('password') border-red-500 @enderror" required>
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full px-4 py-2 border rounded" required>
        </div>

        <button type="submit" class="w-full gradient-bg text-white py-2 rounded font-bold hover:opacity-90 transition">Register</button>
    </form>

    <p class="text-center mt-4">Already have an account? <a href="/login" class="text-blue-600 hover:underline">Login here</a></p>
</div>
@endsection
