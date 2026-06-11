<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\KunjunganPresensi;
use App\Services\PresensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk manajemen presensi kunjungan di panel admin.
 *
 * Controller ini menggunakan PresensiService yang sama dengan PetugasPresensiController,
 * menghilangkan duplikasi kode yang sebelumnya ada antara kedua controller tersebut.
 *
 * Tanggung jawab:
 * - Menampilkan halaman scanner QR
 * - Meneruskan request check-in/check-out ke PresensiService
 * - Menampilkan rekap presensi
 */
class PresensiController extends Controller
{
    /**
     * Inisialisasi controller dengan dependency injection PresensiService.
     *
     * @param  PresensiService $presensiService Service shared untuk logika presensi
     */
    public function __construct(
        private readonly PresensiService $presensiService,
    ) {}

    /**
     * Menampilkan halaman scanner QR untuk presensi (Admin).
     *
     * @return View
     */
    public function scanner(): View
    {
        // Halaman scanner tidak membutuhkan data awal — QR di-scan via JavaScript
        return view('admin.scanner');
    }

    /**
     * API endpoint: mencari data kunjungan berdasarkan kode QR yang di-scan.
     *
     * Dipanggil via AJAX dari JavaScript pada halaman scanner.
     * Mengembalikan data kunjungan dalam format JSON untuk ditampilkan di UI.
     *
     * @param  Request $request HTTP request berisi parameter 'kode' (nomor registrasi)
     * @return JsonResponse
     */
    public function lookup(Request $request): JsonResponse
    {
        // Ambil kode dari request, bersihkan whitespace
        $kode = trim($request->input('kode', ''));

        // Delegasikan pencarian dan validasi ke service — service mengembalikan struktur data terstandardisasi
        $result = $this->presensiService->lookup($kode);

        if (! $result['success']) {
            // Kembalikan response error dengan HTTP code yang sesuai dari service
            return response()->json(['error' => $result['error']], $result['httpCode']);
        }

        // Kembalikan data kunjungan dalam format JSON untuk ditampilkan di UI scanner
        return response()->json($result['data']);
    }

    /**
     * Melakukan check-in untuk kunjungan melalui scanner atau tombol manual.
     *
     * Mendukung dua mode response:
     * - JSON: jika request dari JavaScript (Ajax/scanner)
     * - Redirect: jika request dari form HTML biasa
     *
     * @param  Request   $request   HTTP request
     * @param  Kunjungan $kunjungan Instance kunjungan dari route model binding
     * @return JsonResponse|RedirectResponse
     */
    public function checkIn(Request $request, Kunjungan $kunjungan): JsonResponse|RedirectResponse
    {
        // Delegasikan logika check-in ke shared service
        $result = $this->presensiService->checkIn($kunjungan, auth()->id());

        if (! $result['success']) {
            // Kembalikan error sesuai jenis request
            return $this->errorResponse($request, $result['message']);
        }

        return $this->successResponse($request, $result['message'], $kunjungan);
    }

    /**
     * Melakukan check-out untuk kunjungan melalui scanner atau tombol manual.
     *
     * Setelah check-out, status kunjungan otomatis berubah ke 'completed'
     * dan email survei dikirim ke PIC (dilakukan di PresensiService).
     *
     * @param  Request   $request   HTTP request
     * @param  Kunjungan $kunjungan Instance kunjungan dari route model binding
     * @return JsonResponse|RedirectResponse
     */
    public function checkOut(Request $request, Kunjungan $kunjungan): JsonResponse|RedirectResponse
    {
        // Delegasikan logika check-out (termasuk auto-complete dan kirim email survei) ke service
        $result = $this->presensiService->checkOut($kunjungan, auth()->id());

        if (! $result['success']) {
            return $this->errorResponse($request, $result['message']);
        }

        return $this->successResponse($request, $result['message'], $kunjungan);
    }

    /**
     * Menampilkan rekap daftar presensi dengan filter.
     *
     * @param  Request $request HTTP request berisi parameter filter
     * @return View
     */
    public function index(Request $request): View
    {
        // Query presensi dengan relasi yang diperlukan untuk tampilan rekap
        $query = KunjunganPresensi::with(['kunjungan.sekolah', 'kunjungan.sesi', 'kunjungan.tempat', 'petugasMasuk', 'petugasKeluar'])
            ->orderByDesc('updated_at'); // Tampilkan yang paling baru diperbarui di atas

        // Filter berdasarkan status presensi
        $filter = $request->input('filter', 'all');
        if ($filter === 'masuk') {
            $query->whereNotNull('waktu_masuk')->whereNull('waktu_keluar'); // Sudah check-in, belum check-out
        }
        if ($filter === 'keluar') {
            $query->whereNotNull('waktu_keluar'); // Sudah check-out (lengkap)
        }
        if ($filter === 'belum') {
            $query->whereNull('waktu_masuk'); // Belum check-in sama sekali
        }

        // Filter berdasarkan tanggal check-in
        if ($request->filled('tgl')) {
            $query->whereDate('waktu_masuk', $request->tgl);
        }

        // Paginate dengan 20 item per halaman
        $presensi = $query->paginate(20)->withQueryString();

        // Hitung jumlah per status untuk badge filter
        $counts = [
            'all'    => KunjunganPresensi::count(),
            'masuk'  => KunjunganPresensi::whereNotNull('waktu_masuk')->whereNull('waktu_keluar')->count(),
            'keluar' => KunjunganPresensi::whereNotNull('waktu_keluar')->count(),
        ];

        return view('admin.presensi', compact('presensi', 'filter', 'counts'));
    }

    // ─────────────────────────────────────────────────────────
    // Private Helpers — Response Format
    // ─────────────────────────────────────────────────────────

    /**
     * Mengembalikan response sukses sesuai jenis request (JSON atau redirect).
     *
     * @param  Request   $request   HTTP request untuk menentukan jenis response
     * @param  string    $msg       Pesan sukses
     * @param  Kunjungan $kunjungan Instance kunjungan untuk URL redirect
     * @return JsonResponse|RedirectResponse
     */
    private function successResponse(Request $request, string $msg, Kunjungan $kunjungan): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            // Response untuk Ajax/JavaScript request di halaman scanner
            return response()->json(['success' => $msg]);
        }
        // Response untuk form HTML biasa — redirect ke detail kunjungan
        return redirect()->route('admin.kunjungan.show', $kunjungan)->with('success', $msg);
    }

    /**
     * Mengembalikan response error sesuai jenis request (JSON atau redirect).
     *
     * @param  Request $request HTTP request untuk menentukan jenis response
     * @param  string  $msg     Pesan error
     * @return JsonResponse|RedirectResponse
     */
    private function errorResponse(Request $request, string $msg): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            // HTTP 422: Unprocessable Entity — request valid tapi business rule gagal
            return response()->json(['error' => $msg], 422);
        }
        // Response untuk form HTML — kembali ke halaman sebelumnya dengan pesan error
        return back()->with('error', $msg);
    }
}
