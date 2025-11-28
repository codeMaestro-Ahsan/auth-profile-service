@extends('layouts.main')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-3xl font-bold mb-8 gradient-bg text-transparent bg-clip-text">Edit My Profile</h2>

    <form method="POST" action="/profile/update" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
        @csrf

        <!-- Current Avatar -->
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Current Avatar</label>
            @if ($profile->avatar)
                <img src="/storage/{{ $profile->avatar }}" class="w-24 h-24 rounded-full">
            @else
                <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center">No Image</div>
            @endif
        </div>

        <!-- Avatar Upload -->
        <div class="mb-6">
            <label for="avatar" class="block text-sm font-bold mb-2">Upload New Avatar</label>
            <input type="file" id="avatar" name="avatar" class="w-full border rounded px-3 py-2 @error('avatar') border-red-500 @enderror" accept="image/*">
            @error('avatar')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Bio -->
        <div class="mb-6">
            <label for="bio" class="block text-sm font-bold mb-2">Bio</label>
            <textarea id="bio" name="bio" rows="3" class="w-full border rounded px-3 py-2 @error('bio') border-red-500 @enderror">{{ old('bio', $profile->bio) }}</textarea>
            @error('bio')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mb-6">
            <label for="phone" class="block text-sm font-bold mb-2">Phone</label>
            <input type="tel" id="phone" name="phone" class="w-full border rounded px-3 py-2 @error('phone') border-red-500 @enderror" value="{{ old('phone', $profile->phone) }}">
            @error('phone')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Gender -->
        <div class="mb-6">
            <label for="gender" class="block text-sm font-bold mb-2">Gender</label>
            <select id="gender" name="gender" class="w-full border rounded px-3 py-2 @error('gender') border-red-500 @enderror">
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender', $profile->gender) === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $profile->gender) === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender', $profile->gender) === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Date of Birth -->
        <div class="mb-6">
            <label for="dob" class="block text-sm font-bold mb-2">Date of Birth</label>
            <input type="date" id="dob" name="dob" class="w-full border rounded px-3 py-2 @error('dob') border-red-500 @enderror" value="{{ old('dob', $profile->dob) }}">
            @error('dob')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Country -->
        <div class="mb-6">
            <label for="country" class="block text-sm font-bold mb-2">Country</label>
            <input type="text" id="country" name="country" class="w-full border rounded px-3 py-2 @error('country') border-red-500 @enderror" value="{{ old('country', $profile->country) }}">
            @error('country')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- City -->
        <div class="mb-6">
            <label for="city" class="block text-sm font-bold mb-2">City</label>
            <input type="text" id="city" name="city" class="w-full border rounded px-3 py-2 @error('city') border-red-500 @enderror" value="{{ old('city', $profile->city) }}">
            @error('city')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-2">
            <button type="submit" class="gradient-bg text-white px-6 py-2 rounded hover:opacity-90">Save Changes</button>
            <a href="/profile/dashboard" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500">Cancel</a>
        </div>
    </form>
</div>
@endsection
