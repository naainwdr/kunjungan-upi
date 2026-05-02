<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveiKepuasan;
use Illuminate\Http\Request;

class AdminSurveiController extends Controller
{
    public function index(Request $request)
    {
        $query = SurveiKepuasan::with(['kunjungan.sekolah', 'kunjungan.sesi', 'kunjungan.tempat'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('kunjungan.sekolah', fn($q) => $q->where('nama', 'like', "%$s%"));
        }

        $survei = $query->paginate(20)->withQueryString();

        // Statistik
        $stats = [
            'total'          => SurveiKepuasan::count(),
            'avg_pelayanan'  => round(SurveiKepuasan::avg('rating_pelayanan'), 1),
            'avg_fasilitas'  => round(SurveiKepuasan::avg('rating_fasilitas'), 1),
            'avg_informasi'  => round(SurveiKepuasan::avg('rating_informasi'), 1),
        ];
        $stats['avg_total'] = round(($stats['avg_pelayanan'] + $stats['avg_fasilitas'] + $stats['avg_informasi']) / 3, 1);

        return view('admin.survei', compact('survei', 'stats'));
    }

    /** Toggle tampil di landing page */
    public function togglePublik(SurveiKepuasan $survei)
    {
        $survei->update(['tampilkan_publik' => !$survei->tampilkan_publik]);
        return back()->with('success', 'Visibilitas testimonial diperbarui.');
    }
}
