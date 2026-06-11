<?php

namespace App\Services;

use App\Models\PengaturanKalender;
use App\Models\Sesi;
use App\Models\Tempat;

/**
 * Service untuk mengelola data referensi sistem: Tempat, Sesi, dan Kalender.
 *
 * Berdasarkan diskusi arsitektur, ketiga resource referensi ini digabungkan
 * dalam satu service karena:
 * 1. Ukuran logic bisnis masing-masing sangat kecil (< 5 operasi)
 * 2. Ketiganya bersifat "referensi data" yang saling terkait (sesi_tersedia di kalender
 *    merujuk ke ID sesi, kapasitas tempat digunakan di validasi kunjungan)
 * 3. Menghindari proliferasi service class untuk logic yang trivial
 *
 * Jika di masa depan salah satu resource membutuhkan logic yang kompleks,
 * dapat dengan mudah diekstrak ke service tersendiri.
 */
class AdminReferensiService
{
    // ─────────────────────────────────────────────────────────
    // TEMPAT — Lokasi pelaksanaan kunjungan
    // ─────────────────────────────────────────────────────────

    /**
     * Membuat atau memperbarui data tempat kunjungan.
     *
     * @param  array<string, mixed> $data     Data tempat yang sudah divalidasi oleh StoreTempatRequest
     * @param  Tempat|null          $tempat   Instance yang ada (null = buat baru, ada = update)
     * @return Tempat                         Instance tempat yang baru dibuat atau diperbarui
     */
    public function simpanTempat(array $data, ?Tempat $tempat = null): Tempat
    {
        // Tentukan payload yang akan disimpan — sama antara create dan update
        $payload = [
            'nama'      => $data['nama'],
            'kapasitas' => $data['kapasitas'],
            'deskripsi' => $data['deskripsi'] ?? null,
            // Checkbox 'aktif' hanya terkirim jika dicentang, tidak ada jika tidak dicentang
            'aktif'     => isset($data['aktif']) && $data['aktif'],
        ];

        if ($tempat) {
            // Mode update: perbarui data tempat yang sudah ada
            $tempat->update($payload);
            return $tempat->fresh(); // Kembalikan instance yang sudah ter-refresh dari database
        }

        // Mode create: buat record baru
        return Tempat::create($payload);
    }

    /**
     * Menghapus data tempat kunjungan.
     *
     * Jika tempat masih digunakan oleh kunjungan, database akan melempar exception
     * karena constraint foreign key. Exception ini ditangkap dan dikembalikan
     * sebagai pesan error yang ramah pengguna.
     *
     * @param  Tempat $tempat Instance tempat yang akan dihapus
     * @return array{success: bool, message: string}
     */
    public function hapusTempat(Tempat $tempat): array
    {
        try {
            $tempat->delete(); // Foreign key constraint akan mencegah penghapusan jika masih digunakan
            return ['success' => true, 'message' => 'Tempat berhasil dihapus.'];
        } catch (\Exception $e) {
            // Tangkap exception dari constraint violation — kembalikan pesan yang ramah
            return ['success' => false, 'message' => 'Tempat tidak dapat dihapus karena masih digunakan oleh data kunjungan.'];
        }
    }

    // ─────────────────────────────────────────────────────────
    // SESI — Slot waktu kunjungan
    // ─────────────────────────────────────────────────────────

    /**
     * Membuat atau memperbarui data sesi kunjungan.
     *
     * @param  array<string, mixed> $data  Data sesi yang sudah divalidasi oleh StoreSesiRequest
     * @param  Sesi|null            $sesi  Instance yang ada (null = buat baru, ada = update)
     * @return Sesi                        Instance sesi yang baru dibuat atau diperbarui
     */
    public function simpanSesi(array $data, ?Sesi $sesi = null): Sesi
    {
        // Payload sama untuk create maupun update
        $payload = [
            'nama'        => $data['nama'],
            'jam_mulai'   => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai'],
            // Checkbox 'aktif' hanya terkirim jika dicentang
            'aktif'       => isset($data['aktif']) && $data['aktif'],
        ];

        if ($sesi) {
            // Mode update: perbarui sesi yang ada
            $sesi->update($payload);
            return $sesi->fresh(); // Refresh dari database untuk data terkini
        }

        // Mode create: tambah sesi baru
        return Sesi::create($payload);
    }

    /**
     * Menghapus data sesi kunjungan.
     *
     * Penghapusan akan gagal jika sesi masih direferensikan oleh kunjungan
     * atau pengaturan kalender (foreign key constraint).
     *
     * @param  Sesi $sesi Instance sesi yang akan dihapus
     * @return array{success: bool, message: string}
     */
    public function hapusSesi(Sesi $sesi): array
    {
        try {
            $sesi->delete();
            return ['success' => true, 'message' => 'Sesi kunjungan berhasil dihapus.'];
        } catch (\Exception $e) {
            // Sesi kemungkinan masih digunakan oleh data kunjungan atau pengaturan kalender
            return ['success' => false, 'message' => 'Sesi tidak dapat dihapus karena masih digunakan oleh data kunjungan.'];
        }
    }

    // ─────────────────────────────────────────────────────────
    // KALENDER — Override pengaturan hari layanan
    // ─────────────────────────────────────────────────────────

    /**
     * Menyimpan pengaturan tanggal kalender (buat baru atau update yang sudah ada).
     *
     * Menggunakan updateOrCreate karena setiap tanggal hanya boleh punya
     * satu baris pengaturan. Jika belum ada, buat baru; jika sudah ada, perbarui.
     *
     * @param  array<string, mixed> $data Data pengaturan yang sudah divalidasi oleh StoreKalenderRequest
     * @return PengaturanKalender         Instance pengaturan kalender
     */
    public function simpanKalender(array $data): PengaturanKalender
    {
        // updateOrCreate: kunci pencarian adalah 'tanggal' (unik per tanggal)
        return PengaturanKalender::updateOrCreate(
            ['tanggal' => $data['tanggal']], // Kunci unik
            [
                // Checkbox is_libur: ada di array = true (dicentang), tidak ada = false
                'is_libur'      => isset($data['is_libur']) && $data['is_libur'],
                // Array sesi_tersedia dari multi-select, default empty array jika tidak dipilih
                'sesi_tersedia' => $data['sesi_tersedia'] ?? [],
                'catatan'       => $data['catatan'] ?? null,
            ]
        );
    }

    /**
     * Menghapus pengaturan tanggal kalender (mengembalikan ke perilaku default Senin-Kamis).
     *
     * Penghapusan record override berarti tanggal tersebut kembali mengikuti
     * aturan default sistem (hari Senin-Kamis = layanan, Jumat-Minggu = libur).
     *
     * @param  int $id ID record pengaturan kalender yang akan dihapus
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika ID tidak ditemukan
     */
    public function hapusKalender(int $id): void
    {
        // findOrFail: lempar 404 jika ID tidak ditemukan, cegah penghapusan data yang tidak ada
        PengaturanKalender::findOrFail($id)->delete();
    }
}
