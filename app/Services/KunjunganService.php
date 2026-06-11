<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\KontakSekolah;
use App\Models\PengaturanKalender;
use App\Models\Sekolah;
use App\Models\Sesi;
use App\Mail\StatusKunjunganMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service untuk mengelola seluruh business logic permohonan kunjungan publik.
 *
 * Kelas ini memisahkan logika bisnis dari Controller sesuai prinsip
 * Separation of Concerns (SoC). Controller hanya bertanggung jawab
 * menerima input dan mengembalikan response; semua proses bisnis ada di sini.
 *
 * Tanggung jawab kelas ini:
 * - Menyimpan permohonan kunjungan baru (upsert sekolah, buat kontak, upload surat)
 * - Membatalkan kunjungan dengan validasi batas H-5
 * - Menghasilkan data sesi yang sudah terpesan (untuk frontend form)
 * - Menyiapkan data kalender bulanan untuk tampilan publik
 */
class KunjunganService
{
    // ─────────────────────────────────────────────────────────
    // Konstanta Bisnis — dikelompokkan di sini agar mudah diubah
    // tanpa harus mencari satu per satu dalam kode
    // ─────────────────────────────────────────────────────────

    /** Hari-hari layanan kunjungan: 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis */
    private const HARI_LAYANAN = [1, 2, 3, 4];

    /** Minimal hari sebelum tanggal kunjungan yang masih bisa dibatalkan */
    private const BATAS_BATAL_HARI = 5;

    // ─────────────────────────────────────────────────────────
    // Public Methods
    // ─────────────────────────────────────────────────────────

    /**
     * Menyimpan permohonan kunjungan baru ke database.
     *
     * Proses:
     * 1. Upsert data sekolah berdasarkan NPSN (update jika sudah ada, buat jika belum)
     * 2. Buat record kontak baru untuk permohonan ini
     * 3. Upload file surat ke local storage
     * 4. Buat record kunjungan dengan status 'pending'
     * 5. Log perubahan status awal
     * 6. Kirim email konfirmasi kepada PIC
     *
     * @param  array<string, mixed>  $data   Data tervalidasi dari StoreKunjunganRequest
     * @param  UploadedFile          $fileSurat File surat permohonan yang diunggah
     * @return Kunjungan             Instance kunjungan yang baru dibuat
     *
     * @throws \Exception Jika proses penyimpanan ke database gagal
     */
    public function simpanPermohonan(array $data, UploadedFile $fileSurat): Kunjungan
    {
        // Langkah 1: Upsert data sekolah — update jika NPSN sudah terdaftar,
        // buat baru jika belum, agar database tidak berisi duplikat sekolah
        $sekolah = Sekolah::updateOrCreate(
            ['npsn' => $data['npsn']], // Kunci pencarian unik
            [
                'nama'    => $data['nama_sekolah'],
                'alamat'  => $data['alamat'],
                'email'   => $data['email_sekolah'],
                'telepon' => $data['telepon_sekolah'],
            ]
        );

        // Langkah 2: Buat kontak baru untuk setiap permohonan
        // (tidak di-upsert karena satu sekolah bisa diwakili PIC berbeda tiap permohonan)
        $kontak = KontakSekolah::create([
            'sekolah_id' => $sekolah->id,
            'nama'       => $data['nama_pic'],
            'jabatan'    => $data['jabatan_pic'],
            'email'      => $data['email_pic'],
            'telepon'    => $data['telepon_pic'],
        ]);

        // Langkah 3: Upload file surat ke storage lokal
        // Menggunakan disk 'public' agar bisa diakses melalui URL
        $filePath = $fileSurat->store('surat', 'public');

        // Langkah 4: Buat record kunjungan dengan semua data yang diperlukan
        $kunjungan = Kunjungan::create([
            'nomor_registrasi'  => Kunjungan::generateNomorRegistrasi(), // Generate nomor unik otomatis
            'sekolah_id'        => $sekolah->id,
            'kontak_id'         => $kontak->id,
            'tempat_id'         => $data['tempat_id'],
            'sesi_id'           => $data['sesi_id'],
            'tanggal_kunjungan' => $data['tanggal_kunjungan'],
            'jumlah_peserta'    => $data['jumlah_peserta'],
            'jumlah_kepsek'     => $data['jumlah_kepsek'] ?? 0, // Default 0 jika tidak diisi
            'jumlah_guru'       => $data['jumlah_guru'] ?? 0,
            'jumlah_tendik'     => $data['jumlah_tendik'] ?? 0,
            'file_surat'        => $filePath,
            'status'            => 'pending', // Status awal selalu 'pending'
        ]);

        // Langkah 5: Catat status awal ke log untuk keperluan audit trail
        $kunjungan->logStatus('pending', 'Permohonan baru diajukan oleh pemohon.');

        // Langkah 6: Kirim email konfirmasi ke PIC
        // Dibungkus try-catch agar kegagalan email tidak membatalkan permohonan
        $this->kirimEmailKonfirmasi($kunjungan, $kontak->email);

        return $kunjungan; // Kembalikan instance untuk digunakan controller (redirect ke halaman sukses)
    }

    /**
     * Membatalkan permohonan kunjungan oleh pemohon.
     *
     * Pembatalan hanya diperbolehkan jika sisa waktu ke tanggal kunjungan
     * masih lebih dari atau sama dengan BATAS_BATAL_HARI (default: 5 hari).
     * Batasan ini ada untuk memastikan operasional instansi tidak terganggu
     * oleh pembatalan mendadak.
     *
     * @param  string $nomorRegistrasi Nomor registrasi kunjungan yang akan dibatalkan
     * @return array{success: bool, message: string, kunjungan: Kunjungan|null}
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika nomor registrasi tidak ditemukan
     */
    public function batalkanKunjungan(string $nomorRegistrasi): array
    {
        // Ambil data kunjungan, lempar 404 jika tidak ditemukan
        $kunjungan = Kunjungan::where('nomor_registrasi', $nomorRegistrasi)->firstOrFail();

        // Hitung apakah masih dalam batas waktu pembatalan yang diizinkan
        // Cek: apakah hari ini sudah melewati (tanggal kunjungan - BATAS_BATAL_HARI hari)?
        $batasPembatalan = $kunjungan->tanggal_kunjungan->clone()->subDays(self::BATAS_BATAL_HARI)->startOfDay();

        if (now()->startOfDay()->gt($batasPembatalan)) {
            // Tolak pembatalan karena sudah melewati batas waktu
            return [
                'success'   => false,
                'message'   => 'Pembatalan ditolak. Batas pembatalan adalah H-' . self::BATAS_BATAL_HARI . ' sebelum kunjungan.',
                'kunjungan' => null,
            ];
        }

        // Catat perubahan status ke log audit sebelum mengubah status model
        $kunjungan->logStatus('cancelled', 'Dibatalkan oleh pemohon.');

        // Update status di database
        $kunjungan->update(['status' => 'cancelled']);

        return [
            'success'   => true,
            'message'   => 'Permohonan kunjungan berhasil dibatalkan.',
            'kunjungan' => $kunjungan,
        ];
    }

    /**
     * Mendapatkan daftar ID sesi yang tidak tersedia pada tanggal dan tempat tertentu.
     *
     * Logika penentuan sesi tidak tersedia:
     * 1. Jika ada override kalender yang menandai hari libur → semua sesi tidak tersedia
     * 2. Jika ada override kalender dengan sesi terbatas → sesi di luar daftar tersedia
     * 3. Jika tanggal jatuh di hari Jumat-Minggu (default) → semua sesi tidak tersedia
     * 4. Tambahkan sesi yang sudah di-approve pada tempat & tanggal yang sama
     *
     * @param  string      $tanggal  Tanggal dalam format Y-m-d
     * @param  int|null    $tempatId ID tempat yang dipilih (opsional)
     * @return array<int>            Array ID sesi yang tidak tersedia
     */
    public function getBookedSesi(string $tanggal, ?int $tempatId = null): array
    {
        // Inisialisasi array sesi yang terblokir
        $terblokir = [];

        // Cek apakah ada pengaturan override untuk tanggal ini
        $override = PengaturanKalender::where('tanggal', $tanggal)->first();

        if ($override) {
            if ($override->is_libur) {
                // Jika tanggal ditandai libur, semua sesi diblokir
                $terblokir = Sesi::pluck('id')->toArray();
            } else {
                // Jika bukan libur tapi ada pembatasan sesi, blokir sesi yang tidak ada di daftar tersedia
                $semuaSesi   = Sesi::pluck('id')->toArray();
                $sesiTersedia = $override->sesi_tersedia ?? [];
                $terblokir   = array_values(array_diff($semuaSesi, $sesiTersedia)); // Sesi yang TIDAK tersedia
            }
        } else {
            // Default: blokir semua sesi jika hari tersebut bukan hari layanan (Senin-Kamis)
            $hariMinggu = Carbon::parse($tanggal)->dayOfWeek; // 0=Minggu, 1=Senin, ..., 6=Sabtu
            if (! in_array($hariMinggu, self::HARI_LAYANAN)) {
                $terblokir = Sesi::pluck('id')->toArray(); // Semua sesi diblokir
            }
        }

        // Query sesi yang sudah ter-approve pada kombinasi tanggal+tempat yang sama
        $queryApproved = Kunjungan::where('tanggal_kunjungan', $tanggal)
            ->where('status', 'approved'); // Hanya yang sudah disetujui yang dianggap "sudah terisi"

        if ($tempatId) {
            // Filter per tempat jika tempat dipilih (sesi bisa sama di tempat berbeda)
            $queryApproved->where('tempat_id', $tempatId);
        }

        $sesiSudahTerisi = $queryApproved->pluck('sesi_id')->toArray();

        // Gabungkan dan deduplikasi semua sesi yang tidak tersedia
        return array_values(array_unique(array_merge($terblokir, $sesiSudahTerisi)));
    }

    /**
     * Mengambil data yang diperlukan untuk menampilkan halaman kalender publik.
     *
     * @param  int  $year   Tahun yang ditampilkan
     * @param  int  $month  Bulan yang ditampilkan (1-12)
     * @return array{
     *     approvedVisits: \Illuminate\Support\Collection,
     *     approvedVisitsList: \Illuminate\Support\Collection,
     *     holidays: array<string, string>,
     *     overrides: \Illuminate\Support\Collection,
     *     servicedays: int[]
     * }
     */
    public function getKalenderData(int $year, int $month): array
    {
        // Ambil semua kunjungan yang disetujui pada bulan tersebut untuk ditampilkan di kalender
        $approvedVisitsList = Kunjungan::with(['sekolah', 'sesi', 'tempat'])
            ->where('status', 'approved')
            ->whereYear('tanggal_kunjungan', $year)
            ->whereMonth('tanggal_kunjungan', $month)
            ->orderBy('tanggal_kunjungan')
            ->get();

        // Group kunjungan per tanggal dan hitung jumlahnya untuk menampilkan dot/badge
        $approvedVisits = $approvedVisitsList
            ->groupBy(fn($k) => $k->tanggal_kunjungan->format('Y-m-d'))
            ->map->count(); // Jumlah kunjungan per tanggal

        // Ambil override kalender untuk bulan ini (libur khusus atau batasan sesi)
        $overrides = PengaturanKalender::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->keyBy(fn($i) => $i->tanggal->format('Y-m-d')); // Index berdasarkan tanggal untuk akses O(1)

        return [
            'approvedVisits'     => $approvedVisits,
            'approvedVisitsList' => $approvedVisitsList,
            'holidays'           => $this->getNationalHolidays($year), // Daftar hari libur nasional
            'overrides'          => $overrides,
            'servicedays'        => self::HARI_LAYANAN, // Hari-hari layanan untuk highlight kalender
        ];
    }

    // ─────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────

    /**
     * Mengirim email konfirmasi penerimaan permohonan kepada PIC sekolah.
     *
     * Dibungkus try-catch karena kegagalan pengiriman email tidak boleh
     * membatalkan proses penyimpanan permohonan yang sudah berhasil.
     * Error dicatat ke log untuk investigasi oleh admin.
     *
     * @param  Kunjungan $kunjungan Instance kunjungan yang baru dibuat
     * @param  string    $email     Alamat email tujuan
     * @return void
     */
    private function kirimEmailKonfirmasi(Kunjungan $kunjungan, string $email): void
    {
        try {
            // Kirim email notifikasi status kunjungan ke PIC sekolah
            Mail::to($email)->send(new StatusKunjunganMail($kunjungan));

            // Catat timestamp pengiriman email untuk monitoring dan audit
            $kunjungan->update(['email_notified_at' => now()]);
        } catch (\Exception $e) {
            // Log warning (bukan error kritis) karena permohonan sudah tersimpan
            Log::warning('[KunjunganService] Gagal kirim email konfirmasi', [
                'nomor_registrasi' => $kunjungan->nomor_registrasi,
                'email'            => $email,
                'error'            => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mendapatkan daftar hari libur nasional Indonesia untuk tahun tertentu.
     *
     * Libur nasional ini digunakan untuk menandai tanggal merah pada kalender publik.
     * Daftar libur keagamaan (Idul Fitri, Nyepi, dll.) bersifat dinamis tiap tahun
     * sehingga perlu diperbarui secara manual untuk tahun-tahun mendatang.
     *
     * @param  int                   $year Tahun yang diminta
     * @return array<string, string>       Array dengan key Y-m-d dan value nama hari libur
     */
    public function getNationalHolidays(int $year): array
    {
        // Hari libur nasional yang bersifat tetap (tidak bergerak tiap tahun)
        $libur = [
            "$year-01-01" => "Tahun Baru Masehi",
            "$year-05-01" => "Hari Buruh Internasional",
            "$year-06-01" => "Hari Lahir Pancasila",
            "$year-08-17" => "Hari Kemerdekaan RI",
            "$year-12-25" => "Hari Raya Natal",
            "$year-12-26" => "Cuti Bersama Natal",
        ];

        // Hari libur tahun 2026 — berdasarkan Perpres/Keputusan Pemerintah
        if ($year === 2026) {
            $libur += [
                "2026-01-27" => "Isra Mi'raj Nabi Muhammad SAW",
                "2026-01-29" => "Tahun Baru Imlek 2577",
                "2026-03-20" => "Hari Raya Nyepi",
                "2026-03-31" => "Hari Raya Idul Fitri 1447H",
                "2026-04-01" => "Hari Raya Idul Fitri 1447H",
                "2026-04-02" => "Wafat Isa Al Masih",
                "2026-04-03" => "Cuti Bersama Idul Fitri",
                "2026-05-12" => "Hari Raya Waisak 2570",
                "2026-05-14" => "Kenaikan Isa Al Masih",
                "2026-06-06" => "Hari Raya Idul Adha 1447H",
                "2026-06-07" => "Cuti Bersama Idul Adha",
                "2026-06-27" => "Tahun Baru Islam 1448H",
                "2026-09-05" => "Maulid Nabi Muhammad SAW",
            ];
        }

        // Hari libur tahun 2027 — berdasarkan Perpres/Keputusan Pemerintah
        if ($year === 2027) {
            $libur += [
                "2027-01-17" => "Isra Mi'raj Nabi Muhammad SAW",
                "2027-01-27" => "Tahun Baru Imlek 2578",
                "2027-03-09" => "Hari Raya Nyepi",
                "2027-03-20" => "Hari Raya Idul Fitri 1448H",
                "2027-03-21" => "Hari Raya Idul Fitri 1448H",
                "2027-04-26" => "Wafat Isa Al Masih",
                "2027-05-01" => "Hari Raya Waisak",
                "2027-05-27" => "Idul Adha 1448H",
                "2027-09-25" => "Maulid Nabi Muhammad SAW",
            ];
        }

        return $libur;
    }
}
