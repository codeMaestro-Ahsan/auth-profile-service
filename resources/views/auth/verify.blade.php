@extends('layouts.main')

@section('title', 'Verify Email')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-4 gradient-bg text-transparent bg-clip-text">Email Verification</h2>
    
    @if ($verified)
        <div class="text-center">
            <div class="mb-4 text-5xl">✓</div>
            <p class="text-green-600 text-lg mb-6">Your email has been verified successfully!</p>
            <p class="text-gray-600 mb-6">You can now login to your account.</p>
            <a href="/login" class="gradient-bg text-white px-6 py-2 rounded hover:opacity-90 transition inline-block">
                Go to Login
            </a>
        </div>
    @else
        <div class="text-center">
            <p class="text-gray-600 mb-6">{{ $message ?? 'Verification link is invalid or expired.' }}</p>
            <a href="/register" class="gradient-bg text-white px-6 py-2 rounded hover:opacity-90 transition inline-block">
                Back to Register
            </a>
        </div>
    @endif
</div>
@endsection
