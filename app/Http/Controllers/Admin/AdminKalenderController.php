<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanKalender;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminKalenderController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $settings = PengaturanKalender::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->keyBy(fn($item) => $item->tanggal->format('Y-m-d'));

        $sesi = Sesi::where('aktif', true)->get();

        return view('admin.kalender.index', compact('settings', 'sesi', 'year', 'month'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'is_libur' => 'boolean',
            'sesi_tersedia' => 'nullable|array',
            'catatan' => 'nullable|string|max:255',
        ]);

        PengaturanKalender::updateOrCreate(
            ['tanggal' => $request->tanggal],
            [
                'is_libur' => $request->has('is_libur'),
                'sesi_tersedia' => $request->sesi_tersedia ?? [],
                'catatan' => $request->catatan,
            ]
        );

        return redirect()->back()->with('success', 'Pengaturan tanggal berhasil disimpan.');
    }

    public function destroy($id)
    {
        PengaturanKalender::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Pengaturan tanggal berhasil dihapus (kembali ke default).');
    }
}
