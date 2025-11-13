<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['divisi', 'jabatan'])->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $divisi = Divisi::all();
        $jabatan = Jabatan::all();
        return view('admin.users.create', compact('divisi', 'jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:users,nip|max:20',
            'username' => 'required|unique:users,username|max:100',
            'nama' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'divisi_id' => 'required|exists:divisi,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'sisa_cuti' => 'required|integer|min:0|max:30',
        ]);

        User::create([
            'nip' => $request->nip,
            'username' => $request->username,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->nip), // Password default = NIP
            'divisi_id' => $request->divisi_id,
            'jabatan_id' => $request->jabatan_id,
            'sisa_cuti' => $request->sisa_cuti,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan! Password default = NIP');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $divisi = Divisi::all();
        $jabatan = Jabatan::all();
        return view('admin.users.edit', compact('user', 'divisi', 'jabatan'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nip' => 'required|max:20|unique:users,nip,'.$id,
            'username' => 'required|max:100|unique:users,username,'.$id,
            'nama' => 'required|max:100',
            'email' => 'required|email|unique:users,email,'.$id,
            'divisi_id' => 'required|exists:divisi,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'sisa_cuti' => 'required|integer|min:0|max:30',
        ]);

        $user->update($request->only([
            'nip', 'username', 'nama', 'email',
            'divisi_id', 'jabatan_id', 'sisa_cuti'
        ]));

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus diri sendiri!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($user->nip) // Reset ke NIP
        ]);

        return back()->with('success', 'Password user direset ke NIP: ' . $user->nip);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}!");
    }
}
