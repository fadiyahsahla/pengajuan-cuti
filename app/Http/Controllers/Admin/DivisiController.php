<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisi = Divisi::withCount('users')->latest()->paginate(15);
        return view('admin.divisi.index', compact('divisi'));
    }

    public function create()
    {
        return view('admin.divisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|unique:divisi,nama_divisi|max:100',
        ]);

        Divisi::create($request->all());

        return redirect()->route('admin.divisi.index')->with('success', 'Divisi berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $divisi = Divisi::findOrFail($id);
        return view('admin.divisi.edit', compact('divisi'));
    }

    public function update(Request $request, $id)
    {
        $divisi = Divisi::findOrFail($id);

        $request->validate([
            'nama_divisi' => 'required|max:100|unique:divisi,nama_divisi,'.$id,
        ]);

        $divisi->update($request->all());

        return redirect()->route('admin.divisi.index')->with('success', 'Divisi berhasil diupdate!');
    }

    public function destroy($id)
    {
        $divisi = Divisi::findOrFail($id);

        if ($divisi->users()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus divisi yang masih memiliki user!');
        }

        $divisi->delete();
        return redirect()->route('admin.divisi.index')->with('success', 'Divisi berhasil dihapus!');
    }
}
