<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Mail\EvaluasiKunjunganMail;
use App\Mail\StatusKunjunganMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service untuk mengelola transisi status kunjungan oleh Admin.
 *
 * Kelas ini mengisolasi logika perubahan status (approve, reject, complete)
 * dari AdminKunjunganController. Setiap transisi status mencakup:
 * - Pencatatan log audit (siapa, kapan, dari status apa, ke status apa)
 * - Update record di database
 * - Pengiriman email notifikasi kepada pemohon
 *
 * Dengan service ini, AdminKunjunganController menjadi tipis (thin controller)
 * yang hanya bertugas menerima request dan mendelegasikan ke service.
 */
class KunjunganStatusService
{
    /**
     * Menyetujui permohonan kunjungan.
     *
     * Mengubah status kunjungan menjadi 'approved', mencatat log audit,
     * dan mengirimkan email notifikasi persetujuan kepada PIC sekolah.
     *
     * @param  Kunjungan  $kunjungan    Instance kunjungan yang akan disetujui
     * @param  string|null $catatanAdmin Catatan opsional dari admin
     * @param  int         $adminId     ID user admin yang melakukan aksi (untuk audit)
     * @return void
     *
     * @throws \Exception Jika update database gagal
     */
    public function approve(Kunjungan $kunjungan, ?string $catatanAdmin, int $adminId): void
    {
        // Catat transisi status ke log audit sebelum update — agar status_sebelum tercatat benar
        $kunjungan->logStatus('approved', $catatanAdmin, $adminId);

        // Update status dan simpan catatan admin ke database
        $kunjungan->update([
            'status'        => 'approved',
            'catatan_admin' => $catatanAdmin,
        ]);

        // Kirim notifikasi email ke PIC sekolah bahwa permohonan disetujui
        $this->kirimEmailStatus($kunjungan);
    }

    /**
     * Menolak permohonan kunjungan.
     *
     * Mengubah status kunjungan menjadi 'rejected', mencatat log audit,
     * dan mengirimkan email notifikasi penolakan beserta alasannya kepada PIC.
     * Catatan admin bersifat wajib saat reject (sudah divalidasi di RejectKunjunganRequest).
     *
     * @param  Kunjungan $kunjungan    Instance kunjungan yang akan ditolak
     * @param  string    $catatanAdmin Alasan penolakan (wajib diisi)
     * @param  int       $adminId      ID user admin yang melakukan aksi (untuk audit)
     * @return void
     *
     * @throws \Exception Jika update database gagal
     */
    public function reject(Kunjungan $kunjungan, string $catatanAdmin, int $adminId): void
    {
        // Catat ke log audit agar ada rekam jejak penolakan
        $kunjungan->logStatus('rejected', $catatanAdmin, $adminId);

        // Update status dan catatan di database
        $kunjungan->update([
            'status'        => 'rejected',
            'catatan_admin' => $catatanAdmin,
        ]);

        // Kirim notifikasi penolakan — catatan admin akan ditampilkan dalam email
        $this->kirimEmailStatus($kunjungan);
    }

    /**
     * Menandai kunjungan sebagai selesai (complete).
     *
     * Aksi ini hanya valid jika:
     * - Status kunjungan saat ini adalah 'approved'
     * - Tanggal kunjungan sudah tiba (tidak boleh complete sebelum hari-H)
     *
     * Selain mengubah status, jika ada presensi yang sudah check-in tapi
     * belum check-out, akan otomatis di-checkout (untuk konsistensi data presensi).
     * Email evaluasi juga dikirim ke PIC sebagai trigger pengisian form evaluasi.
     *
     * @param  Kunjungan $kunjungan Instance kunjungan yang akan diselesaikan
     * @param  int       $adminId   ID user admin yang melakukan aksi (untuk audit)
     * @return array{success: bool, message: string}
     */
    public function complete(Kunjungan $kunjungan, int $adminId): array
    {
        // Guard: pastikan status saat ini adalah 'approved' sebelum diubah ke 'completed'
        if ($kunjungan->status !== 'approved') {
            return [
                'success' => false,
                'message' => 'Hanya kunjungan berstatus Disetujui yang dapat ditandai selesai.',
            ];
        }

        // Guard: pastikan tanggal kunjungan sudah tiba (tidak bisa complete di masa depan)
        if ($kunjungan->tanggal_kunjungan->isFuture()) {
            return [
                'success' => false,
                'message' => 'Tanggal kunjungan belum tiba.',
            ];
        }

        // Catat perubahan status ke log audit
        $kunjungan->logStatus('completed', 'Ditandai selesai oleh admin.', $adminId);

        // Update status kunjungan
        $kunjungan->update(['status' => 'completed']);

        // Auto-checkout: jika presensi sudah check-in tapi belum check-out,
        // otomatis isi waktu keluar dengan waktu sekarang untuk konsistensi data
        $presensi = $kunjungan->presensi;
        if ($presensi && ! $presensi->waktu_keluar && $presensi->waktu_masuk) {
            $presensi->update([
                'waktu_keluar'      => now(), // Tandai waktu keluar otomatis
                'petugas_keluar_id' => $adminId, // Catat bahwa admin yang men-trigger checkout
            ]);
        }

        // Kirim email evaluasi — link form evaluasi akan disertakan dalam email
        $this->kirimEmailEvaluasi($kunjungan);

        return [
            'success' => true,
            'message' => "Kunjungan {$kunjungan->nomor_registrasi} selesai. Form evaluasi dikirim ke {$kunjungan->kontak->email}.",
        ];
    }

    // ─────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────

    /**
     * Mengirim email notifikasi perubahan status kepada PIC sekolah.
     *
     * Digunakan untuk notifikasi approve maupun reject.
     * Kegagalan pengiriman email dicatat sebagai warning (bukan error fatal)
     * karena perubahan status sudah tersimpan di database.
     *
     * @param  Kunjungan $kunjungan Instance kunjungan yang statusnya berubah
     * @return void
     */
    private function kirimEmailStatus(Kunjungan $kunjungan): void
    {
        try {
            // Kirim email ke alamat PIC yang tercatat pada kontak kunjungan
            Mail::to($kunjungan->kontak->email)->send(new StatusKunjunganMail($kunjungan));

            // Update timestamp untuk monitoring frekuensi pengiriman email
            $kunjungan->update(['email_notified_at' => now()]);
        } catch (\Exception $e) {
            // Catat ke log dengan konteks lengkap untuk memudahkan debugging
            Log::warning('[KunjunganStatusService] Gagal kirim email status', [
                'nomor_registrasi' => $kunjungan->nomor_registrasi,
                'status'           => $kunjungan->status,
                'email'            => $kunjungan->kontak->email ?? 'N/A',
                'error'            => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mengirim email evaluasi kepada PIC setelah kunjungan selesai.
     *
     * Email ini berisi link ke form evaluasi yang harus diisi dalam 7 hari
     * setelah kunjungan selesai.
     *
     * @param  Kunjungan $kunjungan Instance kunjungan yang sudah selesai
     * @return void
     */
    private function kirimEmailEvaluasi(Kunjungan $kunjungan): void
    {
        try {
            // Kirim email berisi link form evaluasi/survei kepuasan
            Mail::to($kunjungan->kontak->email)->send(new EvaluasiKunjunganMail($kunjungan));
        } catch (\Exception $e) {
            // Catat warning — kunjungan tetap selesai meski email evaluasi gagal terkirim
            Log::warning('[KunjunganStatusService] Gagal kirim email evaluasi', [
                'nomor_registrasi' => $kunjungan->nomor_registrasi,
                'email'            => $kunjungan->kontak->email ?? 'N/A',
                'error'            => $e->getMessage(),
            ]);
        }
    }
}
