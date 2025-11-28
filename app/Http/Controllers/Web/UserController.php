<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // List all users with their profiles
    public function index()
    {
        $users = User::with('profile')->where('email_verified_at', '!=', null)->paginate(10);
        return view('users.index', compact('users'));
    }

    // Show single user profile
    public function show(User $user)
    {
        if (!$user->hasVerifiedEmail()) {
            return redirect('/users')->with('error', 'User profile not available.');
        }

        return view('users.show', compact('user'));
    }
}
