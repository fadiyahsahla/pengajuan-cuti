<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::withCount('users')->latest()->paginate(15);
        return view('admin.jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        return view('admin.jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|unique:jabatan,nama_jabatan|max:100',
            'level_jabatan' => 'required|integer|min:1|max:10',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $request->validate([
            'nama_jabatan' => 'required|max:100|unique:jabatan,nama_jabatan,'.$id,
            'level_jabatan' => 'required|integer|min:1|max:10',
        ]);

        $jabatan->update($request->all());

        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil diupdate!');
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);

        if ($jabatan->users()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus jabatan yang masih memiliki user!');
        }

        $jabatan->delete();
        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan berhasil dihapus!');
    }
}
