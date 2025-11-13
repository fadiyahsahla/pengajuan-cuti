<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun Anda tidak aktif.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'nip' => $user->nip,
                'nama' => $user->nama,
                'email' => $user->email,
                'jabatan' => $user->jabatan ? $user->jabatan->nama_jabatan : null,
                'divisi' => $user->divisi ? $user->divisi->nama_divisi : null,
                'sisa_cuti' => $user->sisa_cuti,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'nip' => $user->nip,
                'nama' => $user->nama,
                'email' => $user->email,
                'jabatan' => $user->jabatan ? $user->jabatan->nama_jabatan : null,
                'divisi' => $user->divisi ? $user->divisi->nama_divisi : null,
                'sisa_cuti' => $user->sisa_cuti,
                'can_approve' => $user->canApproveCuti(),
                'is_admin' => $user->isAdmin(),
                'is_hrd' => $user->isHRD(),
            ],
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.'
        ]);
    }
}
