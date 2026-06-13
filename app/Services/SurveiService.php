<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\SurveiKepuasan;

/**
 * Service untuk mengelola form survei kepuasan kunjungan.
 *
 * Survei kepuasan adalah mekanisme formal pengumpulan umpan balik pemohon
 * setelah kunjungan selesai. Hasil survei disimpan di tabel `survei_kepuasan`
 * dan dapat ditampilkan sebagai testimonial di halaman publik.
 *
 * Catatan arsitektur:
 * Terdapat dua mekanisme umpan balik yang berbeda di sistem ini:
 * 1. Survei Kepuasan (/survei/{nomor}) — tabel survei_kepuasan — [RECOMMENDED]
 * 2. Evaluasi (/evaluasi/{id}) — disimpan ke kolom catatan_admin di tabel kunjungan
 *
 * Fitur (2) adalah implementasi lama yang kurang tepat secara arsitektur
 * karena mencampur data admin dengan data pemohon di kolom yang sama.
 * Disarankan untuk bermigrasi sepenuhnya ke (1) di masa mendatang.
 */
class SurveiService
{
    /** Batas waktu maksimal (dalam hari) setelah check-out untuk mengisi survei */
    private const BATAS_HARI_SURVEI = 7;

    /** Batas waktu maksimal (dalam hari) setelah updated_at untuk mengisi evaluasi lama */
    private const BATAS_HARI_EVALUASI = 7;

    /**
     * Memvalidasi apakah form survei dapat diakses oleh pemohon.
     *
     * Kondisi yang membuat survei TIDAK dapat diakses:
     * - Kunjungan belum check-out (waktu_keluar masih null)
     * - Pemohon sudah pernah mengisi survei sebelumnya (satu kali saja)
     * - Sudah melewati batas waktu BATAS_HARI_SURVEI hari setelah check-out
     *
     * @param  string $nomorRegistrasi Nomor registrasi kunjungan
     * @return array{kunjungan: Kunjungan, status: string}
     *         Status: 'ok' | 'belum_checkout' | 'sudah_isi' | 'kadaluarsa'
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika nomor registrasi tidak ditemukan
     */
    public function validateFormAkses(string $nomorRegistrasi): array
    {
        // Ambil kunjungan dengan relasi yang diperlukan untuk validasi dan tampilan form
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'presensi', 'survei'])
            ->where('nomor_registrasi', $nomorRegistrasi)
            ->firstOrFail(); // Lempar 404 jika tidak ada

        // Cek 1: Kunjungan harus sudah berstatus 'completed' (Selesai)
        if ($kunjungan->status !== 'completed') {
            return ['kunjungan' => $kunjungan, 'status' => 'belum_checkout'];
        }

        // Cek 2: Pastikan pemohon belum pernah mengisi survei sebelumnya (one-time only)
        if ($kunjungan->survei) {
            return ['kunjungan' => $kunjungan, 'status' => 'sudah_isi'];
        }

        // Cek 3: Survei hanya bisa diisi dalam BATAS_HARI_SURVEI hari setelah kunjungan selesai
        $waktuSelesai = $kunjungan->presensi?->waktu_keluar ?? $kunjungan->updated_at;
        $selisihHari = $waktuSelesai->diffInDays(now());
        if ($selisihHari > self::BATAS_HARI_SURVEI) {
            return ['kunjungan' => $kunjungan, 'status' => 'kadaluarsa'];
        }

        // Form dapat diakses — kembalikan kunjungan untuk ditampilkan di view
        return ['kunjungan' => $kunjungan, 'status' => 'ok'];
    }

    /**
     * Menyimpan data survei kepuasan yang diisi oleh pemohon.
     *
     * Validasi ulang dilakukan di sini (defense in depth) sebelum menyimpan,
     * untuk mencegah pengisian ulang melalui direct POST tanpa melalui form.
     *
     * @param  string               $nomorRegistrasi Nomor registrasi kunjungan
     * @param  array<string, mixed> $data            Data survei yang sudah divalidasi oleh Form Request
     * @return array{success: bool, kunjungan: Kunjungan|null, message: string|null}
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika nomor registrasi tidak ditemukan
     */
    public function simpanSurvei(string $nomorRegistrasi, array $data): array
    {
        // Ambil kunjungan dengan relasi presensi dan survei untuk validasi
        $kunjungan = Kunjungan::with(['presensi', 'survei'])
            ->where('nomor_registrasi', $nomorRegistrasi)
            ->firstOrFail();

        // Defense in depth: validasi ulang kondisi pengisian survei
        // Mencegah bypass form melalui direct HTTP POST
        if ($kunjungan->status !== 'completed' || $kunjungan->survei) {
            return [
                'success'   => false,
                'kunjungan' => null,
                'message'   => 'Survei tidak dapat diisi saat ini.',
            ];
        }

        // Simpan data survei ke tabel survei_kepuasan
        SurveiKepuasan::create([
            'kunjungan_id'     => $kunjungan->id,
            'rating_pelayanan' => $data['rating_pelayanan'],
            'rating_fasilitas' => $data['rating_fasilitas'],
            'rating_informasi' => $data['rating_informasi'],
            'komentar'         => $data['komentar'] ?? null, // Opsional
            'saran'            => $data['saran'] ?? null, // Opsional
            'tampilkan_publik' => true, // Default ditampilkan; admin bisa toggle via AdminSurveiController
        ]);

        return [
            'success'   => true,
            'kunjungan' => $kunjungan,
            'message'   => null,
        ];
    }

}
