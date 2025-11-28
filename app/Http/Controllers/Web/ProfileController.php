<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Show user dashboard
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('profile.dashboard', compact('user', 'profile'));
    }

    // Edit profile page
    public function edit()
    {
        $profile = Auth::user()->profile;
        return view('profile.edit', compact('profile'));
    }

    // Update profile (web form)
    public function updateWeb(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);

        $profile = Auth::user()->profile;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $profile->update($validated);

        return redirect('/dashboard')->with('success', 'Profile updated successfully!');
    }

    // Delete profile
    public function deleteWeb(Request $request)
    {
        $profile = Auth::user()->profile;
        
        if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
            Storage::disk('public')->delete($profile->avatar);
        }

        $profile->delete();

        return redirect('/dashboard')->with('success', 'Profile deleted successfully!');
    }
}
