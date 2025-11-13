@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h2 class="text-xl font-bold text-white">Ubah Password</h2>
            <p class="text-indigo-100 text-sm mt-1">Ubah password Anda untuk keamanan</p>
        </div>

        <form action="{{ route('profile.change-password.post') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Current Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password Lama
                </label>
                <input type="password"
                       name="current_password"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password Baru
                </label>
                <input type="password"
                       name="new_password"
                       required
                       minlength="6"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('new_password') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Minimal 6 karakter</p>
                @error('new_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password Baru
                </label>
                <input type="password"
                       name="new_password_confirmation"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('profile.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
