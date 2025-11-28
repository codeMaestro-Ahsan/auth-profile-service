<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use App\Notifications\VerifyEmailWithCustomUrl;
class EmailVerificationController extends Controller
{
    /**
     * Send verification email
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent successfully.',
        ]);
    }

    /**
     * Verify email - API
     */
    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 400);
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 400);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            'message' => 'Email verified successfully!',
        ]);
    }

    /**
     * Verify email - Web View
     */
    public function verifyWeb($id, $hash)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')
                ->with('success', 'Email already verified! You can now login.');
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return redirect('/login')
                ->with('error', 'Email verification failed. Invalid or expired link.')
                ->with('show_resend', true)
                ->with('user_email', $user->email);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect('/login')
            ->with('success', 'Email verified successfully! You can now login.');
    }

    /**
     * Resend verification email - Web
     */
    public function resendWeb(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find user
        $user = User::where('email', $request->email)->firstOrFail();

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect('/login')
                ->with('success', 'Email already verified! You can now login.');
        }

        // Send verification email
        $user->notify(new VerifyEmailWithCustomUrl(
            \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'verification.verify',
                now()->addHours(24),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            )
        ));

        return redirect('/login')
            ->with('success', 'Verification email sent! Check your inbox.');
    }
}
