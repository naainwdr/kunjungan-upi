<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveiKepuasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk manajemen survei kepuasan di panel admin.
 *
 * Admin dapat melihat semua data survei kepuasan yang masuk, melihat statistik
 * rata-rata rating, mencari survei berdasarkan nama sekolah, dan mengatur
 * visibilitas survei sebagai testimonial di halaman publik.
 *
 * Controller ini cukup sederhana (tidak memerlukan Service terpisah) karena
 * semua operasinya adalah query/presentasi data dan satu toggle field.
 */
class AdminSurveiController extends Controller
{
    /**
     * Menampilkan daftar semua survei kepuasan dengan statistik agregat.
     *
     * @param  Request $request HTTP request berisi parameter pencarian
     * @return View
     */
    public function index(Request $request): View
    {
        // Mulai query survei dengan relasi kunjungan untuk menampilkan detail sekolah
        $query = SurveiKepuasan::with(['kunjungan.sekolah', 'kunjungan.sesi', 'kunjungan.tempat'])
            ->orderByDesc('created_at'); // Survei terbaru ditampilkan lebih dulu

        // Filter pencarian berdasarkan nama sekolah (melalui relasi)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas(
                'kunjungan.sekolah',
                fn($q) => $q->where('nama', 'like', "%$s%") // Pencarian partial match
            );
        }

        // Paginate dengan 20 item per halaman
        $survei = $query->paginate(20)->withQueryString();

        // Statistik agregat semua survei untuk widget di bagian atas halaman
        $stats = [
            'total'         => SurveiKepuasan::count(),
            'avg_pelayanan' => round(SurveiKepuasan::avg('rating_pelayanan') ?? 0, 1), // Null-safe dengan ?? 0
            'avg_fasilitas' => round(SurveiKepuasan::avg('rating_fasilitas') ?? 0, 1),
            'avg_informasi' => round(SurveiKepuasan::avg('rating_informasi') ?? 0, 1),
        ];

        // Hitung rata-rata keseluruhan dari tiga dimensi penilaian
        $stats['avg_total'] = round(
            ($stats['avg_pelayanan'] + $stats['avg_fasilitas'] + $stats['avg_informasi']) / 3,
            1
        );

        return view('admin.survei', compact('survei', 'stats'));
    }

    /**
     * Mengubah visibilitas survei sebagai testimonial di halaman publik.
     *
     * Toggle: jika saat ini ditampilkan → sembunyikan, dan sebaliknya.
     * Fitur ini memungkinkan admin mengkurasi testimonial yang tampil di landing page.
     *
     * @param  SurveiKepuasan $survei Instance survei dari route model binding
     * @return RedirectResponse
     */
    public function togglePublik(SurveiKepuasan $survei): RedirectResponse
    {
        // Toggle nilai boolean: true → false, false → true
        $survei->update(['tampilkan_publik' => ! $survei->tampilkan_publik]);

        // Pesan yang spesifik berdasarkan hasil toggle
        $pesan = $survei->fresh()->tampilkan_publik
            ? 'Testimonial sekarang ditampilkan di halaman publik.'
            : 'Testimonial disembunyikan dari halaman publik.';

        return back()->with('success', $pesan);
    }
}
