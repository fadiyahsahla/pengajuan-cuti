<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Cuti</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Sistem Cuti</h1>
            <p class="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf

            <!-- Username Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="username">
                    Username
                </label>
                <input type="text"
                       name="username"
                       id="username"
                       required
                       autofocus
                       value="{{ old('username') }}"
                       placeholder="Masukkan username Anda"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" for="password">
                    Password
                </label>
                <input type="password"
                       name="password"
                       id="password"
                       required
                       placeholder="Masukkan password Anda"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition duration-200 transform hover:-translate-y-0.5">
                Login
            </button>
        </form>

        <!-- Info Demo -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-center text-sm text-gray-600 mb-3">Demo Accounts:</p>
            <div class="space-y-2 text-xs text-gray-500">
                <p><strong>Admin:</strong> Admin System / 999999</p>
                <p><strong>Operator:</strong> Andi Operator / 200001</p>
                <p><strong>Atasan:</strong> Rudi Kepala Regu / 100005</p>
            </div>
        </div>
    </div>
</body>
</html>
