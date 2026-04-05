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
        $query = Kunjungan::latest();

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
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

        $kunjungan = $query->paginate(15)->withQueryString();
        $counts = [
            'all'      => Kunjungan::count(),
            'pending'  => Kunjungan::where('status', 'pending')->count(),
            'approved' => Kunjungan::where('status', 'approved')->count(),
            'rejected' => Kunjungan::where('status', 'rejected')->count(),
        ];

        return view('admin.dashboard', compact('kunjungan', 'counts'));
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
