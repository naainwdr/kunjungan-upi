<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tempat;
use Illuminate\Http\Request;

class AdminTempatController extends Controller
{
    public function index()
    {
        $tempat = Tempat::orderBy('nama')->get();
        return view('admin.tempat.index', compact('tempat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        Tempat::create([
            'nama'      => $request->nama,
            'kapasitas' => $request->kapasitas,
            'deskripsi' => $request->deskripsi,
            'aktif'     => $request->has('aktif') ? true : false,
        ]);

        return redirect()->route('admin.tempat.index')->with('success', 'Tempat berhasil ditambahkan.');
    }

    public function update(Request $request, Tempat $tempat)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $tempat->update([
            'nama'      => $request->nama,
            'kapasitas' => $request->kapasitas,
            'deskripsi' => $request->deskripsi,
            'aktif'     => $request->has('aktif') ? true : false,
        ]);

        return redirect()->route('admin.tempat.index')->with('success', 'Tempat berhasil diperbarui.');
    }

    public function destroy(Tempat $tempat)
    {
        // Simple protection (if you have relations to check before delete, you would do it here)
        try {
            $tempat->delete();
            return redirect()->route('admin.tempat.index')->with('success', 'Tempat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.tempat.index')->with('error', 'Tempat tidak dapat dihapus karena masih digunakan.');
        }
    }
}
