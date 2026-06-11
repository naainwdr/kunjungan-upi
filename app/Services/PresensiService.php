<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\KunjunganPresensi;
use App\Mail\EvaluasiKunjunganMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service untuk mengelola operasi presensi kunjungan (check-in dan check-out).
 *
 * Service ini adalah solusi untuk masalah duplikasi kode antara:
 * - Admin\PresensiController
 * - Petugas\PetugasPresensiController
 *
 * Keduanya memiliki logika check-in/check-out yang IDENTIK. Dengan service ini,
 * kedua controller dapat menggunakan implementasi yang sama, sehingga:
 * - Perubahan logika bisnis hanya perlu dilakukan di satu tempat
 * - Konsistensi terjaga antara admin dan petugas
 * - Lebih mudah diuji (unit test cukup untuk service ini)
 */
class PresensiService
{
    /**
     * Mencari data kunjungan berdasarkan nomor registrasi (dari QR code scan).
     *
     * Digunakan oleh endpoint lookup di admin dan petugas. Validasi status
     * 'approved' dilakukan di sini agar hanya kunjungan valid yang bisa di-scan.
     *
     * @param  string $kode Nomor registrasi yang di-scan dari QR code
     * @return array{success: bool, data: array<string, mixed>|null, error: string|null, httpCode: int}
     */
    public function lookup(string $kode): array
    {
        // Jika kode kosong, kembalikan error validasi
        if (empty(trim($kode))) {
            return ['success' => false, 'data' => null, 'error' => 'Kode tidak boleh kosong.', 'httpCode' => 422];
        }

        // Cari kunjungan beserta relasi yang diperlukan untuk ditampilkan di UI scanner
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat', 'presensi'])
            ->where('nomor_registrasi', trim($kode))
            ->first();

        // Jika kunjungan tidak ditemukan, kembalikan error 404
        if (! $kunjungan) {
            return [
                'success'  => false,
                'data'     => null,
                'error'    => "Kunjungan tidak ditemukan untuk kode: {$kode}",
                'httpCode' => 404,
            ];
        }

        // Hanya kunjungan berstatus 'approved' yang bisa melakukan presensi
        if ($kunjungan->status !== 'approved') {
            return [
                'success'  => false,
                'data'     => null,
                'error'    => "Kunjungan ini berstatus '{$kunjungan->status_label}', bukan Disetujui.",
                'httpCode' => 422,
            ];
        }

        // Ambil presensi yang ada (jika sudah pernah check-in)
        $presensi = $kunjungan->presensi;

        // Susun response data — format konsisten antara admin dan petugas
        return [
            'success'  => true,
            'data'     => [
                'id'               => $kunjungan->id,
                'nomor_registrasi' => $kunjungan->nomor_registrasi,
                'nama_sekolah'     => $kunjungan->sekolah->nama,
                'tanggal'          => $kunjungan->tanggal_format, // Accessor: format hari, tanggal dalam Indonesia
                'sesi'             => $kunjungan->sesi->label ?? '-',
                'tempat'           => $kunjungan->tempat->nama ?? '-',
                'jumlah_peserta'   => $kunjungan->jumlah_peserta,
                'kontak_nama'      => $kunjungan->kontak->nama,
                'kontak_telepon'   => $kunjungan->kontak->telepon,
                'presensi_status'  => $presensi?->status ?? 'belum', // Accessor: 'belum', 'checkin', 'checkout'
                'waktu_masuk'      => $presensi?->waktu_masuk?->format('H:i:s'),
                'waktu_keluar'     => $presensi?->waktu_keluar?->format('H:i:s'),
                'durasi'           => $presensi?->durasi, // Accessor: "Xj Ym" atau null
            ],
            'error'    => null,
            'httpCode' => 200,
        ];
    }

    /**
     * Melakukan check-in untuk kunjungan yang di-scan.
     *
     * Validasi yang dilakukan:
     * - Kunjungan harus berstatus 'approved'
     * - Kunjungan belum pernah check-in
     *
     * @param  Kunjungan $kunjungan Instance kunjungan yang akan check-in
     * @param  int       $petugasId ID user yang melakukan check-in (admin atau petugas)
     * @return array{success: bool, message: string}
     */
    public function checkIn(Kunjungan $kunjungan, int $petugasId): array
    {
        // Guard: hanya kunjungan yang disetujui yang bisa check-in
        if ($kunjungan->status !== 'approved') {
            return [
                'success' => false,
                'message' => 'Hanya kunjungan berstatus Disetujui yang dapat check-in.',
            ];
        }

        // Guard: pastikan belum pernah check-in sebelumnya (cegah duplikat)
        $presensi = $kunjungan->presensi;
        if ($presensi?->waktu_masuk) {
            return [
                'success' => false,
                'message' => 'Check-in sudah tercatat pada ' . $presensi->waktu_masuk->format('H:i:s') . '.',
            ];
        }

        // Gunakan updateOrCreate untuk keamanan: buat record baru jika belum ada,
        // update jika sudah ada (bisa terjadi jika ada race condition)
        KunjunganPresensi::updateOrCreate(
            ['kunjungan_id' => $kunjungan->id],
            [
                'waktu_masuk'      => now(), // Catat timestamp check-in
                'petugas_masuk_id' => $petugasId, // Catat siapa yang melakukan check-in
            ]
        );

        return [
            'success' => true,
            'message' => "Check-in berhasil untuk {$kunjungan->sekolah->nama} pada " . now()->format('H:i:s') . '.',
        ];
    }

    /**
     * Melakukan check-out untuk kunjungan yang di-scan.
     *
     * Selain mencatat waktu keluar, check-out juga:
     * - Mengubah status kunjungan menjadi 'completed' (auto-complete)
     * - Mengirim email survei kepuasan ke PIC
     *
     * Validasi yang dilakukan:
     * - Kunjungan sudah check-in (waktu_masuk tidak null)
     * - Kunjungan belum pernah check-out
     *
     * @param  Kunjungan $kunjungan Instance kunjungan yang akan check-out
     * @param  int       $petugasId ID user yang melakukan check-out (admin atau petugas)
     * @return array{success: bool, message: string}
     */
    public function checkOut(Kunjungan $kunjungan, int $petugasId): array
    {
        $presensi = $kunjungan->presensi;

        // Guard: check-out tidak bisa dilakukan sebelum check-in
        if (! $presensi?->waktu_masuk) {
            return [
                'success' => false,
                'message' => 'Check-in belum dilakukan.',
            ];
        }

        // Guard: pastikan belum pernah check-out (cegah pencatatan ganda)
        if ($presensi->waktu_keluar) {
            return [
                'success' => false,
                'message' => 'Check-out sudah tercatat pada ' . $presensi->waktu_keluar->format('H:i:s') . '.',
            ];
        }

        // Catat waktu keluar dan petugas yang melakukan check-out
        $presensi->update([
            'waktu_keluar'      => now(),
            'petugas_keluar_id' => $petugasId,
        ]);

        // Auto-complete: ubah status kunjungan menjadi 'completed' setelah check-out
        // Cek kondisi agar tidak menimpa status yang mungkin sudah diubah admin
        if ($kunjungan->status !== 'completed') {
            $kunjungan->logStatus('completed', 'Auto-completed saat check-out.', $petugasId);
            $kunjungan->update(['status' => 'completed']);
        }

        // Kirim email survei kepuasan ke PIC sekolah
        $this->kirimEmailSurvei($kunjungan);

        // Refresh presensi untuk mendapatkan durasi yang sudah terhitung
        $durasiFormatted = $presensi->fresh()->durasi ?? '-';

        return [
            'success' => true,
            'message' => "Check-out berhasil. Durasi kunjungan: {$durasiFormatted}. Link survei dikirim ke {$kunjungan->kontak->email}.",
        ];
    }

    // ─────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────

    /**
     * Mengirim email berisi link survei kepuasan kepada PIC setelah check-out.
     *
     * Kegagalan pengiriman email tidak menghalangi proses check-out yang sudah berhasil.
     * Error dicatat ke log untuk investigasi.
     *
     * @param  Kunjungan $kunjungan Instance kunjungan yang sudah check-out
     * @return void
     */
    private function kirimEmailSurvei(Kunjungan $kunjungan): void
    {
        try {
            // Kirim email yang berisi link survei kepuasan
            Mail::to($kunjungan->kontak->email)->send(new EvaluasiKunjunganMail($kunjungan));
        } catch (\Exception $e) {
            // Warning — check-out tetap berhasil meski email survei gagal terkirim
            Log::warning('[PresensiService] Gagal kirim email survei setelah check-out', [
                'nomor_registrasi' => $kunjungan->nomor_registrasi,
                'email'            => $kunjungan->kontak->email ?? 'N/A',
                'error'            => $e->getMessage(),
            ]);
        }
    }
}
