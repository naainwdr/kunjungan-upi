<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveKunjunganRequest;
use App\Http\Requests\Admin\RejectKunjunganRequest;
use App\Models\Kunjungan;
use App\Models\Sekolah;
use App\Services\KunjunganStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk halaman manajemen kunjungan di panel admin.
 *
 * Tanggung jawab controller ini HANYA:
 * 1. Menerima request admin (dashboard, detail, approve, reject, complete)
 * 2. Mendelegasikan business logic ke KunjunganStatusService
 * 3. Mengembalikan response (view atau redirect dengan flash message)
 *
 * Controller ini tidak mengandung logika bisnis sama sekali.
 */
class AdminKunjunganController extends Controller
{
    /**
     * Inisialisasi controller dengan dependency injection KunjunganStatusService.
     *
     * @param  KunjunganStatusService $statusService Service untuk transisi status kunjungan
     */
    public function __construct(
        private readonly KunjunganStatusService $statusService,
    ) {}

    /**
     * Menampilkan halaman dashboard admin dengan daftar dan filter kunjungan.
     *
     * Mendukung filter: status, rentang tanggal, dan pencarian teks.
     * Mendukung pengurutan: berdasarkan tanggal kunjungan, waktu dibuat, atau jumlah peserta.
     *
     * @param  Request $request HTTP request berisi parameter filter dan sort
     * @return View
     */
    public function dashboard(Request $request): View
    {
        // Mulai query dengan eager load relasi yang dibutuhkan di tabel dashboard
        $query = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat']);

        // Filter berdasarkan status — validasi whitelist mencegah SQL injection
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'cancelled', 'completed'])) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan rentang tanggal kunjungan
        if ($request->filled('tgl_dari')) {
            $query->whereDate('tanggal_kunjungan', '>=', $request->tgl_dari);
        }
        if ($request->filled('tgl_sampai')) {
            $query->whereDate('tanggal_kunjungan', '<=', $request->tgl_sampai);
        }

        // Pencarian teks: cocokkan nomor registrasi, nama sekolah, atau NPSN
        if ($request->filled('search')) {
            $s = $request->search; // Ambil nilai pencarian
            $query->where(function ($q) use ($s) {
                $q->where('nomor_registrasi', 'like', "%$s%") // Cari di nomor registrasi
                  ->orWhereHas('sekolah', fn($sq) => $sq->where('nama', 'like', "%$s%")->orWhere('npsn', 'like', "%$s%")); // Cari di nama/NPSN sekolah
            });
        }

        // Pengurutan — whitelist kolom yang valid untuk keamanan
        $sortBy  = in_array($request->sort, ['tanggal_kunjungan', 'created_at', 'jumlah_peserta'])
                   ? $request->sort : 'created_at'; // Default sort by waktu dibuat (terbaru dulu)
        $sortDir = $request->dir === 'asc' ? 'asc' : 'desc'; // Default descending
        $query->orderBy($sortBy, $sortDir);

        // Paginate dengan 15 item per halaman, pertahankan parameter filter di URL
        $kunjungan = $query->paginate(15)->withQueryString();

        // Hitung jumlah kunjungan per status untuk ditampilkan di badge filter
        $counts = [
            'all'       => Kunjungan::count(),
            'pending'   => Kunjungan::where('status', 'pending')->count(),
            'approved'  => Kunjungan::where('status', 'approved')->count(),
            'rejected'  => Kunjungan::where('status', 'rejected')->count(),
            'cancelled' => Kunjungan::where('status', 'cancelled')->count(),
            'completed' => Kunjungan::where('status', 'completed')->count(),
        ];

        // Top 5 sekolah berdasarkan jumlah kunjungan yang disetujui (untuk widget statistik)
        $topSekolah = Sekolah::withCount(['kunjungan as total_kunjungan' => fn($q) => $q->where('status', 'approved')])
            ->withSum(['kunjungan as total_peserta' => fn($q) => $q->where('status', 'approved')], 'jumlah_peserta')
            ->whereHas('kunjungan', fn($q) => $q->where('status', 'approved')) // Hanya sekolah yang punya kunjungan approved
            ->orderByDesc('total_kunjungan')
            ->limit(5)
            ->get();

        // 5 kunjungan yang sudah disetujui (paling baru) untuk widget kunjungan terdekat
        $recentVisits = Kunjungan::with(['sekolah', 'sesi', 'tempat'])
            ->where('status', 'approved')
            ->orderByDesc('tanggal_kunjungan')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('kunjungan', 'counts', 'topSekolah', 'recentVisits', 'sortBy', 'sortDir'));
    }

    /**
     * Menampilkan halaman detail satu kunjungan.
     *
     * Menggunakan Route Model Binding — Laravel otomatis query berdasarkan ID di URL.
     *
     * @param  Kunjungan $kunjungan Instance kunjungan dari route model binding
     * @return View
     */
    public function show(Kunjungan $kunjungan): View
    {
        // Load relasi yang dibutuhkan di halaman detail termasuk riwayat status
        $kunjungan->load(['sekolah', 'kontak', 'sesi', 'tempat', 'logs']);
        return view('admin.detail', compact('kunjungan'));
    }

    /**
     * Menyetujui permohonan kunjungan.
     *
     * @param  ApproveKunjunganRequest $request   Request yang sudah tervalidasi
     * @param  Kunjungan               $kunjungan Instance dari route model binding
     * @return RedirectResponse
     */
    public function approve(ApproveKunjunganRequest $request, Kunjungan $kunjungan): RedirectResponse
    {
        // Delegasikan seluruh proses approve ke service (update status, log, kirim email)
        $this->statusService->approve(
            $kunjungan,
            $request->validated()['catatan_admin'] ?? null, // Catatan opsional
            auth()->id() // ID admin yang melakukan aksi (untuk audit log)
        );

        return redirect()->route('admin.dashboard')
            ->with('success', "Pengajuan {$kunjungan->nomor_registrasi} telah disetujui.");
    }

    /**
     * Menolak permohonan kunjungan.
     *
     * @param  RejectKunjunganRequest $request   Request yang sudah tervalidasi (catatan wajib)
     * @param  Kunjungan              $kunjungan Instance dari route model binding
     * @return RedirectResponse
     */
    public function reject(RejectKunjunganRequest $request, Kunjungan $kunjungan): RedirectResponse
    {
        // Delegasikan proses reject ke service — catatan_admin sudah pasti ada (wajib di request)
        $this->statusService->reject(
            $kunjungan,
            $request->validated()['catatan_admin'],
            auth()->id()
        );

        return redirect()->route('admin.dashboard')
            ->with('success', "Pengajuan {$kunjungan->nomor_registrasi} telah ditolak.");
    }

    /**
     * Menandai kunjungan sebagai selesai.
     *
     * Validasi (status harus approved, tanggal sudah tiba) dilakukan di service.
     *
     * @param  Request   $request   HTTP request (tidak ada input tambahan yang diperlukan)
     * @param  Kunjungan $kunjungan Instance dari route model binding
     * @return RedirectResponse
     */
    public function complete(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        // Delegasikan validasi dan pemrosesan complete ke service
        $result = $this->statusService->complete($kunjungan, auth()->id());

        if (! $result['success']) {
            // Jika service menolak (kondisi tidak terpenuhi), tampilkan error
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', $result['message']);
    }
}
