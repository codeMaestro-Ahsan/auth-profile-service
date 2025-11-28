<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth System') - Laravel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .badge {
            @apply inline-block px-3 py-1 text-xs font-semibold rounded;
        }
        .badge-success {
            @apply bg-green-200 text-green-800;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold">AuthApp</a>
            <div class="flex gap-4">
                @auth
                    <span class="text-sm">Welcome, {{ Auth::user()->name }}</span>
                    <a href="/dashboard" class="hover:text-gray-200">Dashboard</a>
                    <a href="/users" class="hover:text-gray-200">Users</a>
                    <form method="POST" action="/logout" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-gray-200">Logout</button>
                    </form>
                @else
                    <a href="/login" class="hover:text-gray-200">Login</a>
                    <a href="/register" class="hover:text-gray-200">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mx-auto px-4 mt-4">
        @if ($message = Session::get('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 alert-message">
                {{ $message }}
                <button type="button" class="float-right" onclick="this.parentElement.style.display='none';">×</button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 alert-message">
                {{ $message }}
                <button type="button" class="float-right" onclick="this.parentElement.style.display='none';">×</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="gradient-bg text-white mt-12 py-6 text-center">
        <p>&copy; 2025 AuthApp. All rights reserved.</p>
    </footer>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-message');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>
