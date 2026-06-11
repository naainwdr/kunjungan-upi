<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\KunjunganPresensi;
use App\Services\PresensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk manajemen presensi kunjungan di panel Petugas Presensi.
 *
 * Petugas Presensi adalah role operasional yang bertugas melakukan scan QR
 * dan mencatat check-in/check-out kunjungan. Controller ini IDENTIK dalam
 * fungsionalitas dengan Admin\PresensiController, namun menggunakan:
 * - View berbeda (petugas.* bukan admin.*)
 * - Route redirect berbeda (petugas.scanner bukan admin.kunjungan.show)
 *
 * Dengan menggunakan PresensiService yang shared, tidak ada duplikasi
 * business logic antara admin dan petugas.
 */
class PetugasPresensiController extends Controller
{
    /**
     * Inisialisasi controller dengan dependency injection PresensiService.
     *
     * Menggunakan service yang sama dengan Admin\PresensiController
     * untuk menjamin konsistensi behavior.
     *
     * @param  PresensiService $presensiService Service shared untuk logika presensi
     */
    public function __construct(
        private readonly PresensiService $presensiService,
    ) {}

    /**
     * Menampilkan halaman scanner QR untuk Petugas Presensi.
     *
     * @return View
     */
    public function scanner(): View
    {
        // Petugas menggunakan view khusus petugas (desain lebih sederhana, fokus scan)
        return view('petugas.scanner');
    }

    /**
     * API endpoint: mencari data kunjungan berdasarkan kode QR yang di-scan.
     *
     * Dipanggil via AJAX dari JavaScript pada halaman scanner petugas.
     * Menggunakan service yang sama dengan admin — data yang dikembalikan identik.
     *
     * @param  Request $request HTTP request berisi parameter 'kode' (nomor registrasi)
     * @return JsonResponse
     */
    public function lookup(Request $request): JsonResponse
    {
        // Bersihkan input untuk menghindari whitespace yang mungkin tertangkap scanner
        $kode = trim($request->input('kode', ''));

        // Delegasikan ke PresensiService yang sama dengan admin — behavior konsisten
        $result = $this->presensiService->lookup($kode);

        if (! $result['success']) {
            return response()->json(['error' => $result['error']], $result['httpCode']);
        }

        return response()->json($result['data']);
    }

    /**
     * Melakukan check-in kunjungan (oleh Petugas).
     *
     * @param  Request   $request   HTTP request
     * @param  Kunjungan $kunjungan Instance kunjungan dari route model binding
     * @return JsonResponse|RedirectResponse
     */
    public function checkIn(Request $request, Kunjungan $kunjungan): JsonResponse|RedirectResponse
    {
        // Delegasikan ke shared service — logika bisnis identik dengan admin
        $result = $this->presensiService->checkIn($kunjungan, auth()->id());

        if (! $result['success']) {
            return $this->errorResponse($request, $result['message']);
        }

        return $this->successResponse($request, $result['message']);
    }

    /**
     * Melakukan check-out kunjungan (oleh Petugas).
     *
     * Setelah check-out: status kunjungan → completed, email survei terkirim.
     * Semua proses ini ditangani oleh PresensiService.
     *
     * @param  Request   $request   HTTP request
     * @param  Kunjungan $kunjungan Instance kunjungan dari route model binding
     * @return JsonResponse|RedirectResponse
     */
    public function checkOut(Request $request, Kunjungan $kunjungan): JsonResponse|RedirectResponse
    {
        // Delegasikan ke shared service — behavior sama persis dengan admin checkout
        $result = $this->presensiService->checkOut($kunjungan, auth()->id());

        if (! $result['success']) {
            return $this->errorResponse($request, $result['message']);
        }

        return $this->successResponse($request, $result['message']);
    }

    /**
     * Menampilkan rekap presensi hari ini dan historis (untuk Petugas).
     *
     * @param  Request $request HTTP request berisi parameter filter
     * @return View
     */
    public function index(Request $request): View
    {
        // Query presensi dengan semua relasi yang diperlukan untuk tampilan rekap
        $query = KunjunganPresensi::with(['kunjungan.sekolah', 'kunjungan.sesi', 'kunjungan.tempat', 'petugasMasuk', 'petugasKeluar'])
            ->orderByDesc('updated_at');

        // Filter berdasarkan status presensi (sama dengan admin, kode identik)
        $filter = $request->input('filter', 'all');
        if ($filter === 'masuk') {
            $query->whereNotNull('waktu_masuk')->whereNull('waktu_keluar');
        }
        if ($filter === 'keluar') {
            $query->whereNotNull('waktu_keluar');
        }
        if ($filter === 'belum') {
            $query->whereNull('waktu_masuk');
        }

        // Filter berdasarkan tanggal check-in
        if ($request->filled('tgl')) {
            $query->whereDate('waktu_masuk', $request->tgl);
        }

        $presensi = $query->paginate(20)->withQueryString();

        // Hitung jumlah per status untuk badge filter
        $counts = [
            'all'    => KunjunganPresensi::count(),
            'masuk'  => KunjunganPresensi::whereNotNull('waktu_masuk')->whereNull('waktu_keluar')->count(),
            'keluar' => KunjunganPresensi::whereNotNull('waktu_keluar')->count(),
        ];

        // Gunakan view khusus petugas
        return view('petugas.presensi', compact('presensi', 'filter', 'counts'));
    }

    // ─────────────────────────────────────────────────────────
    // Private Helpers — Response Format
    // ─────────────────────────────────────────────────────────

    /**
     * Mengembalikan response sukses sesuai jenis request (JSON atau redirect).
     *
     * Perbedaan dengan Admin: redirect ke petugas.scanner (bukan admin.kunjungan.show)
     * karena petugas tidak bisa mengakses halaman detail kunjungan.
     *
     * @param  Request $request HTTP request
     * @param  string  $msg     Pesan sukses
     * @return JsonResponse|RedirectResponse
     */
    private function successResponse(Request $request, string $msg): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $msg]);
        }
        // Redirect ke halaman scanner (bukan detail kunjungan — petugas tidak punya akses)
        return redirect()->route('petugas.scanner')->with('success', $msg);
    }

    /**
     * Mengembalikan response error sesuai jenis request.
     *
     * @param  Request $request HTTP request
     * @param  string  $msg     Pesan error
     * @return JsonResponse|RedirectResponse
     */
    private function errorResponse(Request $request, string $msg): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $msg], 422);
        }
        return back()->with('error', $msg);
    }
}
