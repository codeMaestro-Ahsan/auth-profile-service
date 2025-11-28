@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
    <h1>My Profile</h1>

    @if (auth()->check())
        <div class="profile-card">
            <div class="profile-item">
                <span class="profile-label">Name:</span>
                <span class="profile-value">{{ auth()->user()->name }}</span>
            </div>
            <div class="profile-item">
                <span class="profile-label">Email:</span>
                <span class="profile-value">{{ auth()->user()->email }}</span>
            </div>
            <div class="profile-item">
                <span class="profile-label">Status:</span>
                <span class="profile-value">
                    @if (auth()->user()->hasVerifiedEmail())
                        <span style="color: green;">✓ Verified</span>
                    @else
                        <span style="color: orange;">⚠ Unverified</span>
                    @endif
                </span>
            </div>
            <div class="profile-item">
                <span class="profile-label">Joined:</span>
                <span class="profile-value">{{ auth()->user()->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <h2 style="margin-top: 30px; margin-bottom: 20px;">Profile Information</h2>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="3">{{ auth()->user()->profile->bio ?? '' }}</textarea>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="{{ auth()->user()->profile->phone ?? '' }}">
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <input type="text" id="gender" name="gender" value="{{ auth()->user()->profile->gender ?? '' }}">
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="{{ auth()->user()->profile->dob ?? '' }}">
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" value="{{ auth()->user()->profile->country ?? '' }}">
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="{{ auth()->user()->profile->city ?? '' }}">
            </div>

            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
                @if (auth()->user()->profile->avatar)
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">Current: {{ auth()->user()->profile->avatar }}</p>
                @endif
            </div>

            <button type="submit">Save Changes</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
            @csrf
            <button type="submit" class="btn-secondary">Logout</button>
        </form>

        <form method="POST" action="{{ route('delete-account') }}" style="margin-top: 10px;" onsubmit="return confirm('Are you sure? This cannot be undone!');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Delete Account</button>
        </form>
    @else
        <div class="alert alert-error">
            You are not logged in. <a href="{{ route('login') }}">Click here to login</a>
        </div>
    @endif
@endsection
