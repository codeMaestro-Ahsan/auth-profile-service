@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
    <h1>Verify Your Email</h1>

    <div class="profile-card">
        <p style="text-align: center; margin-bottom: 20px;">
            A verification link has been sent to your email address. 
            Please click the link in the email to complete verification.
        </p>
        
        <p style="text-align: center; color: #666; font-size: 14px;">
            Check your email and follow the verification link. If you don't see the email, 
            check your spam folder or <a href="{{ route('resend-verification') }}" style="color: #667eea;">request a new link</a>.
        </p>
    </div>

    <div class="text-center">
        <a href="{{ route('login') }}">Back to login</a>
    </div>
@endsection
