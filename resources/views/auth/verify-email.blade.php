<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Feelith</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-400 via-purple-400 to-blue-600 min-h-screen">

    <!-- Flash Notifications -->
    @if (session()->has('success'))
        <x-flash-notification
            type="success"
            message="{{ session('success') }}"
            :autoHide="true"
            :autoHideDelay="3000"
        />
    @endif

    <div class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <!-- Email Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 text-center mb-4">
                Verify Your Email
            </h1>

            <!-- Message -->
            <p class="text-gray-600 text-center mb-6">
                Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
            </p>

            <p class="text-gray-600 text-center mb-8">
                If you didn't receive the email, we will gladly send you another.
            </p>

            <!-- Resend Button -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-4 px-6 rounded-full transition-all duration-200 shadow-lg">
                    Resend Verification Email
                </button>
            </form>

            <!-- Logout Link -->
            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm underline">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
