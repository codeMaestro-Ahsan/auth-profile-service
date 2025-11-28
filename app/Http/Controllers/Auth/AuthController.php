<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Notifications\VerifyEmailWithCustomUrl;
use App\Notifications\PasswordResetNotification;

class AuthController extends Controller
{
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create empty profile
        $user->profile()->create([]);
        $this->sendVerificationEmail($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))
            ->additional([
                'success' => true,
                'message' => 'User registered successfully, please verify your email.',
                'requires_verification'=>true,
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
    }

    public function update(UpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        DB::beginTransaction();

        try {
            $user->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 422);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email before logging in.',
                'requires_verification' => true,
            ], 403);
        }

        // Remove old tokens (optional but good practice)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        DB::beginTransaction();

        try {
            // Delete avatar if exists
            if ($user->profile && $user->profile->avatar) {
                if (Storage::disk('public')->exists($user->profile->avatar)) {
                    Storage::disk('public')->delete($user->profile->avatar);
                }
            }

            // Delete all tokens (IMPORTANT)
            $user->tokens()->delete();

            // Delete profile
            $user->profile()->delete();

            // Delete user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

      public function sendVerificationEmail(User $user)
    {
        // Generate the verification URL
        $url = URL::temporarySignedRoute(
            'verification.verify', 
            Carbon::now()->addMinutes(60), // URL expiration time
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Send the email with the verification URL
        $user->sendEmailVerificationNotification();
    }

    /**
     * Forgot Password - Send reset link to email
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Generate password reset token
        $token = Password::broker()->createToken($user);

        // Send notification
        $user->notify(new PasswordResetNotification($token));

        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email.',
        ]);
    }

    /**
     * Reset Password - Update user password with token
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

        // Attempt to reset the password
        $response = Password::broker()->reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($response == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. You can now login with your new password.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to reset password. Token may have expired.',
        ], 400);
    }

    // ========== WEB ROUTES ==========

    /**
     * Register - Web Form Submission
     */
    public function registerWeb(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->profile()->create([]);
        $this->sendVerificationEmail($user);

        return redirect('/verify-email/' . $user->id)
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }

    /**
     * Login - Web Form Submission
     */
    public function loginWeb(\App\Http\Requests\Auth\LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        if (!$user->hasVerifiedEmail()) {
            return back()->with('error', 'Please verify your email before logging in.');
        }

        Auth::login($user, $request->remember);

        return redirect('/dashboard')->with('success', 'Logged in successfully!');
    }

    /**
     * Logout - Web
     */
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully!');
    }

    /**
     * Forgot Password - Web Form
     */
    public function forgotPasswordWeb(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found.');
        }

        $token = Password::broker()->createToken($user);
        $user->notify(new PasswordResetNotification($token));

        return back()->with('success', 'Password reset link sent to your email!');
    }

    /**
     * Reset Password - Web Form
     */
    public function resetPasswordWeb(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $response = Password::broker()->reset($validated, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($response == Password::PASSWORD_RESET) {
            return redirect('/login')->with('success', 'Password reset successfully! Please login with your new password.');
        }

        return back()->with('error', 'Failed to reset password. Token may have expired.');
    }

    /**
     * Edit Account Page
     */
    public function editAccount()
    {
        $user = Auth::user();
        return view('account.edit', compact('user'));
    }

    /**
     * Update Account - Web
     */
    public function updateAccountWeb(UpdateRequest $request)
    {
        $user = Auth::user();
        $user->update($request->validated());

        return redirect('/dashboard')->with('success', 'Account updated successfully!');
    }

    /**
     * Delete Account - Web
     */
    public function deleteAccountWeb(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);

        $user = Auth::user();

        // Delete avatar
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Delete profile and user
        $user->profile()->delete();
        $user->tokens()->delete();
        $user->delete();

        return redirect('/')->with('success', 'Account deleted successfully!');
    }
}