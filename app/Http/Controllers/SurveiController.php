<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\SurveiKepuasan;
use Illuminate\Http\Request;

class SurveiController extends Controller
{
    /** Form survei publik */
    public function form($nomorRegistrasi)
    {
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'presensi', 'survei'])
            ->where('nomor_registrasi', $nomorRegistrasi)
            ->firstOrFail();

        // Validasi: harus sudah check-out
        if (!$kunjungan->presensi?->waktu_keluar) {
            return view('public.survei', ['kunjungan' => $kunjungan, 'belumCheckout' => true]);
        }

        // Validasi: sudah pernah isi
        if ($kunjungan->survei) {
            return view('public.survei', ['kunjungan' => $kunjungan, 'sudahIsi' => true]);
        }

        // Validasi: max 7 hari setelah checkout
        if ($kunjungan->presensi->waktu_keluar->diffInDays(now()) > 7) {
            return view('public.survei', ['kunjungan' => $kunjungan, 'kadaluarsa' => true]);
        }

        return view('public.survei', compact('kunjungan'));
    }

    /** Simpan survei */
    public function store(Request $request, $nomorRegistrasi)
    {
        $kunjungan = Kunjungan::with(['presensi', 'survei'])
            ->where('nomor_registrasi', $nomorRegistrasi)
            ->firstOrFail();

        if (!$kunjungan->presensi?->waktu_keluar || $kunjungan->survei) {
            return back()->with('error', 'Survei tidak dapat diisi saat ini.');
        }

        $request->validate([
            'rating_pelayanan' => 'required|integer|min:1|max:5',
            'rating_fasilitas' => 'required|integer|min:1|max:5',
            'rating_informasi' => 'required|integer|min:1|max:5',
            'komentar'         => 'nullable|string|max:1000',
            'saran'            => 'nullable|string|max:1000',
        ], [
            'rating_pelayanan.required' => 'Rating pelayanan wajib diisi.',
            'rating_fasilitas.required' => 'Rating fasilitas wajib diisi.',
            'rating_informasi.required' => 'Rating informasi wajib diisi.',
        ]);

        SurveiKepuasan::create([
            'kunjungan_id'     => $kunjungan->id,
            'rating_pelayanan' => $request->rating_pelayanan,
            'rating_fasilitas' => $request->rating_fasilitas,
            'rating_informasi' => $request->rating_informasi,
            'komentar'         => $request->komentar,
            'saran'            => $request->saran,
            'tampilkan_publik' => true,
        ]);

        return redirect()->route('survei.terima-kasih', $nomorRegistrasi);
    }

    /** Terima kasih setelah survei */
    public function terimaKasih($nomorRegistrasi)
    {
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'survei'])->where('nomor_registrasi', $nomorRegistrasi)->firstOrFail();
        return view('public.survei-terima-kasih', compact('kunjungan'));
    }
}
