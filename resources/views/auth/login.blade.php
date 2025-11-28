@extends('layouts.main')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto">
    <!-- Resend Verification Email Modal -->
    @if(Session::has('show_resend') && Session::get('show_resend'))
        <div id="resendModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 shadow-lg max-w-sm">
                <h3 class="text-xl font-bold mb-4 text-orange-600">Resend Verification Email?</h3>
                <p class="text-gray-700 mb-6">
                    We can send another verification email to <strong>{{ Session::get('user_email') }}</strong>
                </p>
                <div class="flex gap-3">
                    <form method="POST" action="/resend-verification-email" class="flex-1">
                        @csrf
                        <input type="hidden" name="email" value="{{ Session::get('user_email') }}">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Resend Email
                        </button>
                    </form>
                    <button onclick="document.getElementById('resendModal').style.display='none'" 
                            class="flex-1 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                        Skip
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 gradient-bg text-transparent bg-clip-text">Login</h2>
        
        <form method="POST" action="/login">
            @csrf
            
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

            <div class="mb-6 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-gray-700">Remember me</label>
            </div>

            <button type="submit" class="w-full gradient-bg text-white py-2 rounded font-bold hover:opacity-90 transition">Login</button>
        </form>

        <p class="text-center mt-4">
            <a href="/forgot-password" class="text-blue-600 hover:underline">Forgot password?</a>
        </p>

        <p class="text-center mt-4">Don't have an account? <a href="/register" class="text-blue-600 hover:underline">Register here</a></p>
    </div>
</div>

<script>
// Auto-show resend modal if needed
document.addEventListener('DOMContentLoaded', function() {
    const resendModal = document.getElementById('resendModal');
    if (resendModal) {
        // Auto-hide after 10 seconds
        setTimeout(function() {
            resendModal.style.display = 'none';
        }, 10000);
    }
});
</script>
@endsection
