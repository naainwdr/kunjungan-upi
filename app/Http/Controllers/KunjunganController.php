<?php

namespace App\Http\Controllers;

use App\Http\Requests\CariStatusRequest;
use App\Http\Requests\SimpanEvaluasiRequest;
use App\Http\Requests\StoreKunjunganRequest;
use App\Models\Kunjungan;
use App\Models\PengaturanKalender;
use App\Models\Sesi;
use App\Models\Tempat;
use App\Services\KunjunganService;
use App\Services\SurveiService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Controller untuk halaman publik sistem permohonan kunjungan.
 *
 * Tanggung jawab controller ini HANYA:
 * 1. Menerima HTTP request dari pengguna
 * 2. Mendelegasikan pemrosesan ke Service yang sesuai
 * 3. Mengembalikan response (view atau redirect)
 *
 * Semua business logic (validasi bisnis, manipulasi data, kirim email, dll.)
 * berada di KunjunganService dan SurveiService.
 */
class KunjunganController extends Controller
{
    /**
     * Inisialisasi controller dengan dependency injection.
     *
     * Laravel secara otomatis meng-inject instance service melalui constructor
     * berkat service container. Ini memudahkan unit testing (bisa mock service).
     *
     * @param  KunjunganService $kunjunganService Service untuk logika permohonan kunjungan
     * @param  SurveiService    $surveiService     Service untuk logika survei dan evaluasi
     */
    public function __construct(
        private readonly KunjunganService $kunjunganService,
        private readonly SurveiService $surveiService,
    ) {}

    /**
     * Menampilkan halaman landing page publik.
     *
     * @return View
     */
    public function index(): View
    {
        // Tidak ada logika bisnis — halaman statis yang menampilkan informasi umum
        return view('public.landing');
    }

    /**
     * Menampilkan halaman kalender kunjungan publik.
     *
     * Kalender menampilkan tanggal-tanggal yang sudah ada kunjungannya,
     * hari libur nasional, dan override pengaturan dari admin.
     *
     * @param  Request $request HTTP request (dapat berisi parameter year dan month)
     * @return View
     */
    public function kalender(Request $request): View
    {
        // Ambil parameter year dan month dengan nilai default tahun/bulan saat ini
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // Batasi rentang tahun yang bisa dilihat (tahun lalu hingga 2 tahun ke depan)
        $year  = max(now()->year - 1, min($year, now()->year + 2));
        $month = max(1, min($month, 12)); // Batasi bulan antara 1-12

        // Delegasikan ke service untuk mendapatkan data kalender yang dibutuhkan
        $kalenderData = $this->kunjunganService->getKalenderData($year, $month);

        // Merge data kalender dengan parameter navigasi untuk dipakai di view
        return view('public.kalender', array_merge($kalenderData, [
            'year'  => $year,
            'month' => $month,
        ]));
    }

    /**
     * Menampilkan form permohonan kunjungan baru.
     *
     * Jika parameter tanggal diberikan dari kalender, akan dicek apakah
     * tanggal tersebut valid untuk menerima kunjungan.
     *
     * @param  Request $request HTTP request (dapat berisi parameter tanggal)
     * @return View|RedirectResponse
     */
    public function create(Request $request): View|RedirectResponse
    {
        $tanggal = $request->get('tanggal'); // Tanggal yang dipilih dari kalender (opsional)

        // Ambil semua tempat yang aktif untuk ditampilkan di dropdown form
        $tempat = Tempat::where('aktif', true)->get();

        // Mulai query sesi aktif, diurutkan berdasarkan jam untuk UX yang baik
        $sesiQuery = Sesi::where('aktif', true)->orderBy('jam_mulai');

        if ($tanggal) {
            // Cek apakah ada override kalender untuk tanggal yang dipilih
            $override = PengaturanKalender::where('tanggal', $tanggal)->first();

            if ($override) {
                if ($override->is_libur) {
                    // Jika override menandai hari libur, redirect ke kalender dengan pesan error
                    return redirect()->route('kalender')->with('error', 'Maaf, tanggal tersebut tidak melayani kunjungan.');
                }
                // Jika ada pembatasan sesi, filter hanya sesi yang tersedia pada tanggal itu
                $sesiQuery->whereIn('id', $override->sesi_tersedia ?? []);
            } else {
                // Tanpa override: cek apakah hari tersebut adalah hari layanan (Senin-Kamis)
                $hariMinggu = Carbon::parse($tanggal)->dayOfWeek; // 0=Minggu, 1=Senin, ..., 6=Sabtu
                if (! in_array($hariMinggu, [1, 2, 3, 4])) {
                    // Bukan hari layanan — redirect ke kalender
                    return redirect()->route('kalender')->with('error', 'Maaf, hari tersebut tidak melayani kunjungan.');
                }
            }
        }

        $sesi = $sesiQuery->get(); // Eksekusi query setelah semua filter diterapkan

        // Jika tanggal dipilih tapi tidak ada sesi tersedia, redirect ke kalender
        if ($sesi->isEmpty() && $tanggal) {
            return redirect()->route('kalender')->with('error', 'Maaf, tidak ada sesi tersedia untuk tanggal tersebut.');
        }

        return view('public.reservasi', compact('tanggal', 'tempat', 'sesi'));
    }

    /**
     * Menyimpan permohonan kunjungan baru.
     *
     * Validasi dasar (format data) ditangani oleh StoreKunjunganRequest.
     * Validasi bisnis (hari layanan, kapasitas, bentrok sesi) ditangani oleh KunjunganService.
     *
     * @param  StoreKunjunganRequest $request Request yang sudah tervalidasi otomatis
     * @return RedirectResponse
     */
    public function store(StoreKunjunganRequest $request): RedirectResponse
    {
        // Ambil data yang sudah tervalidasi dari Form Request
        $data = $request->validated();

        // ── Validasi Bisnis Tambahan (tidak bisa dilakukan di Form Request) ──

        // Cek apakah tanggal yang dipilih adalah hari layanan (Senin-Kamis)
        $tglObj = Carbon::parse($data['tanggal_kunjungan']);
        if (! in_array($tglObj->dayOfWeek, [1, 2, 3, 4])) {
            return back()->withInput()->withErrors([
                'tanggal_kunjungan' => 'Kunjungan hanya dilayani pada hari Senin hingga Kamis.',
            ]);
        }

        // Cek apakah jumlah peserta melebihi kapasitas tempat yang dipilih
        $tempatObj = Tempat::findOrFail($data['tempat_id']);
        if ($data['jumlah_peserta'] > $tempatObj->kapasitas) {
            return back()->withInput()->withErrors([
                'jumlah_peserta' => "Kapasitas tempat yang dipilih maksimal {$tempatObj->kapasitas} orang.",
            ]);
        }

        // Cek apakah sesi pada tanggal dan tempat yang sama sudah di-approve orang lain
        $bentrok = Kunjungan::where('tanggal_kunjungan', $data['tanggal_kunjungan'])
            ->where('status', 'approved')
            ->where('sesi_id', $data['sesi_id'])
            ->where('tempat_id', $data['tempat_id'])
            ->exists();

        if ($bentrok) {
            return back()->withInput()->withErrors([
                'sesi_id' => 'Sesi dan tempat yang dipilih sudah penuh pada tanggal tersebut. Pilih sesi atau tempat lain.',
            ]);
        }

        // ── Delegasikan Penyimpanan ke Service ──

        // Semua logika bisnis (upsert sekolah, buat kontak, upload file, kirim email)
        // dieksekusi di dalam service, bukan di controller
        $kunjungan = $this->kunjunganService->simpanPermohonan(
            $data,
            $request->file('file_surat') // Pass UploadedFile ke service
        );

        // Redirect ke halaman sukses dengan nomor registrasi sebagai referensi
        return redirect()->route('reservasi.sukses', ['id' => $kunjungan->nomor_registrasi]);
    }

    /**
     * Menampilkan halaman konfirmasi sukses setelah permohonan berhasil diajukan.
     *
     * @param  Request $request HTTP request (berisi query parameter 'id' = nomor registrasi)
     * @return View
     */
    public function sukses(Request $request): View
    {
        // Ambil kunjungan berdasarkan nomor registrasi di query string
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat'])
            ->where('nomor_registrasi', $request->query('id'))
            ->firstOrFail(); // Lempar 404 jika nomor registrasi tidak valid

        return view('public.sukses', compact('kunjungan'));
    }

    /**
     * Menampilkan form pencarian status kunjungan.
     *
     * @return View
     */
    public function cekStatus(): View
    {
        // Halaman awal form pencarian — tidak ada data yang perlu diload
        return view('public.cek-status');
    }

    /**
     * Memproses pencarian status kunjungan berdasarkan nomor registrasi atau email.
     *
     * @param  CariStatusRequest $request Request yang sudah tervalidasi
     * @return View
     */
    public function cariStatus(CariStatusRequest $request): View
    {
        // Bersihkan whitespace dari input pengguna untuk akurasi pencarian
        $q = trim($request->input('query'));

        // Cari kunjungan yang cocok dengan nomor registrasi atau email
        // orWhereHas digunakan karena email disimpan di relasi (kontak/sekolah)
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat'])
            ->where('nomor_registrasi', $q) // Cari by nomor registrasi
            ->orWhereHas('kontak', fn($qb) => $qb->where('email', $q)) // Cari by email PIC
            ->orWhereHas('sekolah', fn($qb) => $qb->where('email', $q)) // Cari by email sekolah
            ->latest() // Urutkan terbaru dulu
            ->get();

        return view('public.cek-status', compact('kunjungan', 'q'));
    }

    /**
     * Membatalkan permohonan kunjungan oleh pemohon.
     *
     * Validasi batas waktu pembatalan (H-5) dilakukan di KunjunganService.
     *
     * @param  Request $request HTTP request
     * @param  string  $id      Nomor registrasi kunjungan yang akan dibatalkan
     * @return RedirectResponse
     */
    public function batal(Request $request, string $id): RedirectResponse
    {
        // Delegasikan logika pembatalan (termasuk validasi H-5) ke service
        $result = $this->kunjunganService->batalkanKunjungan($id);

        if (! $result['success']) {
            // Pembatalan ditolak — tampilkan pesan error dari service
            return back()->with('error', $result['message']);
        }

        // Pembatalan berhasil — tampilkan pesan sukses
        return back()->with('success', $result['message']);
    }

    /**
     * API endpoint: mendapatkan daftar ID sesi yang tidak tersedia pada tanggal tertentu.
     *
     * Digunakan oleh JavaScript di form reservasi untuk menonaktifkan opsi sesi
     * yang sudah tidak tersedia secara real-time.
     *
     * @param  Request $request HTTP request (berisi query 'tanggal' dan 'tempat_id')
     * @return JsonResponse     Array ID sesi yang tidak tersedia
     */
    public function bookedSesi(Request $request): JsonResponse
    {
        $tanggal  = $request->query('tanggal'); // Format Y-m-d
        $tempatId = $request->query('tempat_id') ? (int) $request->query('tempat_id') : null;

        // Jika tanggal tidak diberikan, kembalikan array kosong (tidak ada yang diblokir)
        if (! $tanggal) {
            return response()->json([]);
        }

        // Delegasikan logika kalender dan pengecekan booking ke service
        $bookedIds = $this->kunjunganService->getBookedSesi($tanggal, $tempatId);

        return response()->json($bookedIds);
    }

}
