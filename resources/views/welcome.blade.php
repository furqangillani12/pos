<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almufeed Super Store</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

<div class="min-h-screen flex flex-col justify-center items-center px-4">
    <div class="text-center mb-8">
        <h1 class="text-5xl font-extrabold text-green-700 mb-4">Welcome to</h1>
        <h2 class="text-4xl font-bold text-gray-900">Almufeed Super Store</h2>
        <p class="text-lg text-gray-600 mt-4">Your trusted place for quality and affordability.</p>
    </div>

    <div class="flex gap-4 mt-8">
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="px-6 py-2 bg-green-600 text-white font-semibold rounded hover:bg-green-700 transition">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('home') }}"
                   class="px-6 py-2 bg-green-600 text-white font-semibold rounded hover:bg-green-700 transition">
                    Go to Home
                </a>
            @endif
        @else
            <a href="{{ route('login') }}"
               class="px-6 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
                Login
            </a>
        @endauth
    </div>

    <footer class="absolute bottom-4 text-sm text-gray-500">
        &copy; {{ now()->year }} Almufeed Super Store. All rights reserved.
    </footer>
</div>

</body>
</html>
