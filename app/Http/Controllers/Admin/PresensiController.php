<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\KunjunganPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PresensiController extends Controller
{
    /** Halaman Scanner QR */
    public function scanner()
    {
        return view('admin.scanner');
    }

    /** API: cari kunjungan by nomor_registrasi (dari QR scan) */
    public function lookup(Request $request)
    {
        $kode = trim($request->input('kode', ''));

        if (!$kode) {
            return response()->json(['error' => 'Kode tidak boleh kosong.'], 422);
        }

        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat', 'presensi'])
            ->where('nomor_registrasi', $kode)
            ->first();

        if (!$kunjungan) {
            return response()->json(['error' => 'Kunjungan tidak ditemukan untuk kode: ' . $kode], 404);
        }

        if ($kunjungan->status !== 'approved') {
            return response()->json([
                'error' => "Kunjungan ini berstatus '{$kunjungan->status_label}', bukan Disetujui."
            ], 422);
        }

        $presensi = $kunjungan->presensi;

        return response()->json([
            'id'                => $kunjungan->id,
            'nomor_registrasi'  => $kunjungan->nomor_registrasi,
            'nama_sekolah'      => $kunjungan->sekolah->nama,
            'tanggal'           => $kunjungan->tanggal_format,
            'sesi'              => $kunjungan->sesi->label ?? '-',
            'tempat'            => $kunjungan->tempat->nama ?? '-',
            'jumlah_peserta'    => $kunjungan->jumlah_peserta,
            'kontak_nama'       => $kunjungan->kontak->nama,
            'kontak_telepon'    => $kunjungan->kontak->telepon,
            'presensi_status'   => $presensi?->status ?? 'belum',
            'waktu_masuk'       => $presensi?->waktu_masuk?->format('H:i:s'),
            'waktu_keluar'      => $presensi?->waktu_keluar?->format('H:i:s'),
            'durasi'            => $presensi?->durasi,
        ]);
    }

    /** Check-In via scanner atau tombol */
    public function checkIn(Request $request, Kunjungan $kunjungan)
    {
        if ($kunjungan->status !== 'approved') {
            return $this->errorResponse($request, 'Hanya kunjungan berstatus Disetujui yang dapat check-in.');
        }

        $presensi = $kunjungan->presensi;

        if ($presensi?->waktu_masuk) {
            return $this->errorResponse($request, 'Check-in sudah tercatat pada ' . $presensi->waktu_masuk->format('H:i:s') . '.');
        }

        KunjunganPresensi::updateOrCreate(
            ['kunjungan_id' => $kunjungan->id],
            [
                'waktu_masuk'      => now(),
                'petugas_masuk_id' => auth()->id(),
            ]
        );

        $msg = "Check-in berhasil untuk {$kunjungan->sekolah->nama} pada " . now()->format('H:i:s') . '.';
        return $this->successResponse($request, $msg, $kunjungan);
    }

    /** Check-Out via scanner atau tombol */
    public function checkOut(Request $request, Kunjungan $kunjungan)
    {
        $presensi = $kunjungan->presensi;

        if (!$presensi?->waktu_masuk) {
            return $this->errorResponse($request, 'Check-in belum dilakukan.');
        }

        if ($presensi->waktu_keluar) {
            return $this->errorResponse($request, 'Check-out sudah tercatat pada ' . $presensi->waktu_keluar->format('H:i:s') . '.');
        }

        $presensi->update([
            'waktu_keluar'      => now(),
            'petugas_keluar_id' => auth()->id(),
        ]);

        // Update kunjungan status to completed
        if ($kunjungan->status !== 'completed') {
            $kunjungan->logStatus('completed', 'Auto-completed upon check-out', auth()->id());
            $kunjungan->update(['status' => 'completed']);
        }

        // Kirim link survei ke kontak
        $surveiUrl = route('evaluasi.form', ['id' => $kunjungan->nomor_registrasi]);
        try {
            \Mail::to($kunjungan->kontak->email)->send(
                new \App\Mail\EvaluasiKunjunganMail($kunjungan)
            );
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email survei: ' . $e->getMessage());
        }

        $msg = "Check-out berhasil. Durasi kunjungan: {$presensi->fresh()->durasi}. Kunjungan diselesaikan dan link survei dikirim ke {$kunjungan->kontak->email}.";
        return $this->successResponse($request, $msg, $kunjungan);
    }

    /** Halaman Daftar Presensi */
    public function index(Request $request)
    {
        $query = KunjunganPresensi::with(['kunjungan.sekolah', 'kunjungan.sesi', 'kunjungan.tempat', 'petugasMasuk', 'petugasKeluar'])
            ->orderByDesc('updated_at');

        // Filter
        $filter = $request->input('filter', 'all');
        if ($filter === 'masuk')   $query->whereNotNull('waktu_masuk')->whereNull('waktu_keluar');
        if ($filter === 'keluar')  $query->whereNotNull('waktu_keluar');
        if ($filter === 'belum')   $query->whereNull('waktu_masuk');

        if ($request->filled('tgl')) {
            $query->whereDate('waktu_masuk', $request->tgl);
        }

        $presensi = $query->paginate(20)->withQueryString();

        $counts = [
            'all'    => KunjunganPresensi::count(),
            'masuk'  => KunjunganPresensi::whereNotNull('waktu_masuk')->whereNull('waktu_keluar')->count(),
            'keluar' => KunjunganPresensi::whereNotNull('waktu_keluar')->count(),
        ];

        return view('admin.presensi', compact('presensi', 'filter', 'counts'));
    }

    private function successResponse(Request $request, string $msg, Kunjungan $kunjungan)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $msg]);
        }
        return redirect()->route('admin.kunjungan.show', $kunjungan)->with('success', $msg);
    }

    private function errorResponse(Request $request, string $msg)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $msg], 422);
        }
        return back()->with('error', $msg);
    }
}
