<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sesi;
use Illuminate\Http\Request;

class AdminSesiController extends Controller
{
    public function index()
    {
        $sesi = Sesi::orderBy('jam_mulai')->get();
        return view('admin.sesi.index', compact('sesi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        Sesi::create([
            'nama'        => $request->nama,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'aktif'       => $request->has('aktif') ? true : false,
        ]);

        return redirect()->route('admin.sesi.index')->with('success', 'Sesi Kalender berhasil ditambahkan.');
    }

    public function update(Request $request, Sesi $sesi)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        $sesi->update([
            'nama'        => $request->nama,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'aktif'       => $request->has('aktif') ? true : false,
        ]);

        return redirect()->route('admin.sesi.index')->with('success', 'Sesi Kalender berhasil diperbarui.');
    }

    public function destroy(Sesi $sesi)
    {
        try {
            $sesi->delete();
            return redirect()->route('admin.sesi.index')->with('success', 'Sesi Kalender berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.sesi.index')->with('error', 'Sesi Kalender tidak dapat dihapus karena masih digunakan.');
        }
    }
}
