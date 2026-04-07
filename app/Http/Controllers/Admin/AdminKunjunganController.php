<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Mail\StatusKunjunganMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminKunjunganController extends Controller
{
    /**
     * Dashboard Admin - list semua pengajuan
     */
    public function dashboard(Request $request)
    {
        $query = Kunjungan::query();

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // Filter tanggal kunjungan
        if ($request->filled('tgl_dari')) {
            $query->whereDate('tanggal_kunjungan', '>=', $request->tgl_dari);
        }
        if ($request->filled('tgl_sampai')) {
            $query->whereDate('tanggal_kunjungan', '<=', $request->tgl_sampai);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_sekolah', 'like', "%$s%")
                  ->orWhere('nomor_registrasi', 'like', "%$s%")
                  ->orWhere('npsn', 'like', "%$s%");
            });
        }

        // Sort
        $sortBy  = in_array($request->sort, ['tanggal_kunjungan', 'created_at', 'jumlah_peserta', 'nama_sekolah'])
                   ? $request->sort : 'created_at';
        $sortDir = $request->dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $kunjungan = $query->paginate(15)->withQueryString();

        $counts = [
            'all'       => Kunjungan::count(),
            'pending'   => Kunjungan::where('status', 'pending')->count(),
            'approved'  => Kunjungan::where('status', 'approved')->count(),
            'rejected'  => Kunjungan::where('status', 'rejected')->count(),
            'cancelled' => Kunjungan::where('status', 'cancelled')->count(),
        ];

        // Top 5 sekolah paling sering berkunjung (approved)
        $topSekolah = Kunjungan::where('status', 'approved')
            ->selectRaw('nama_sekolah, COUNT(*) as total_kunjungan, SUM(jumlah_peserta) as total_peserta')
            ->groupBy('nama_sekolah')
            ->orderByDesc('total_kunjungan')
            ->limit(5)
            ->get();

        // 5 kunjungan terbaru yang disetujui
        $recentVisits = Kunjungan::where('status', 'approved')
            ->orderByDesc('tanggal_kunjungan')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'kunjungan', 'counts', 'topSekolah', 'recentVisits',
            'sortBy', 'sortDir'
        ));
    }

    /**
     * Detail pengajuan
     */
    public function show(Kunjungan $kunjungan)
    {
        return view('admin.detail', compact('kunjungan'));
    }

    /**
     * Setujui pengajuan
     */
    public function approve(Request $request, Kunjungan $kunjungan)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $kunjungan->update([
            'status'        => 'approved',
            'catatan_admin' => $request->catatan_admin,
        ]);

        $this->kirimEmail($kunjungan);

        return redirect()->route('admin.dashboard')
            ->with('success', "Pengajuan {$kunjungan->nomor_registrasi} telah disetujui.");
    }

    /**
     * Tolak pengajuan
     */
    public function reject(Request $request, Kunjungan $kunjungan)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $kunjungan->update([
            'status'        => 'rejected',
            'catatan_admin' => $request->catatan_admin,
        ]);

        $this->kirimEmail($kunjungan);

        return redirect()->route('admin.dashboard')
            ->with('success', "Pengajuan {$kunjungan->nomor_registrasi} telah ditolak.");
    }

    private function kirimEmail(Kunjungan $kunjungan): void
    {
        try {
            Mail::to($kunjungan->email)->send(new StatusKunjunganMail($kunjungan));
            $kunjungan->update(['email_notified_at' => now()]);
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email notifikasi: ' . $e->getMessage());
        }
    }
}
