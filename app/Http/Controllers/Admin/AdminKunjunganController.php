<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Mail\StatusKunjunganMail;
use App\Mail\EvaluasiKunjunganMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminKunjunganController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat']);

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'cancelled', 'completed'])) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tgl_dari'))   $query->whereDate('tanggal_kunjungan', '>=', $request->tgl_dari);
        if ($request->filled('tgl_sampai')) $query->whereDate('tanggal_kunjungan', '<=', $request->tgl_sampai);

        // Search (via relationship)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_registrasi', 'like', "%$s%")
                  ->orWhereHas('sekolah', fn($sq) => $sq->where('nama', 'like', "%$s%")->orWhere('npsn', 'like', "%$s%"));
            });
        }

        // Sort
        $sortBy  = in_array($request->sort, ['tanggal_kunjungan', 'created_at', 'jumlah_peserta'])
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
            'completed' => Kunjungan::where('status', 'completed')->count(),
        ];

        // Top 5 sekolah (via sekolah relation)
        // Catatan: PostgreSQL tidak mendukung alias column di HAVING clause,
        // gunakan whereHas() sebagai filter pengganti.
        $topSekolah = \App\Models\Sekolah::withCount(['kunjungan as total_kunjungan' => fn($q) => $q->where('status', 'approved')])
            ->withSum(['kunjungan as total_peserta' => fn($q) => $q->where('status', 'approved')], 'jumlah_peserta')
            ->whereHas('kunjungan', fn($q) => $q->where('status', 'approved'))
            ->orderByDesc('total_kunjungan')
            ->limit(5)
            ->get();

        // 5 kunjungan terdekat yang disetujui
        $recentVisits = Kunjungan::with(['sekolah', 'sesi', 'tempat'])
            ->where('status', 'approved')
            ->orderByDesc('tanggal_kunjungan')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('kunjungan', 'counts', 'topSekolah', 'recentVisits', 'sortBy', 'sortDir'));
    }

    public function show(Kunjungan $kunjungan)
    {
        $kunjungan->load(['sekolah', 'kontak', 'sesi', 'tempat', 'logs']);
        return view('admin.detail', compact('kunjungan'));
    }

    public function approve(Request $request, Kunjungan $kunjungan)
    {
        $request->validate(['catatan_admin' => 'nullable|string|max:500']);

        $kunjungan->logStatus('approved', $request->catatan_admin, auth()->id());
        $kunjungan->update(['status' => 'approved', 'catatan_admin' => $request->catatan_admin]);
        $this->kirimEmail($kunjungan);

        return redirect()->route('admin.dashboard')
            ->with('success', "Pengajuan {$kunjungan->nomor_registrasi} telah disetujui.");
    }

    public function reject(Request $request, Kunjungan $kunjungan)
    {
        $request->validate(['catatan_admin' => 'required|string|max:500'], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $kunjungan->logStatus('rejected', $request->catatan_admin, auth()->id());
        $kunjungan->update(['status' => 'rejected', 'catatan_admin' => $request->catatan_admin]);
        $this->kirimEmail($kunjungan);

        return redirect()->route('admin.dashboard')
            ->with('success', "Pengajuan {$kunjungan->nomor_registrasi} telah ditolak.");
    }

    public function complete(Request $request, Kunjungan $kunjungan)
    {
        if ($kunjungan->status !== 'approved')
            return back()->with('error', 'Hanya kunjungan yang disetujui yang dapat ditandai selesai.');
        if ($kunjungan->tanggal_kunjungan->isFuture())
            return back()->with('error', 'Tanggal kunjungan belum tiba.');

        $kunjungan->logStatus('completed', null, auth()->id());
        $kunjungan->update(['status' => 'completed']);
        $this->kirimEmailEvaluasi($kunjungan);

        return redirect()->route('admin.dashboard')
            ->with('success', "Kunjungan {$kunjungan->nomor_registrasi} selesai. Form evaluasi dikirim.");
    }

    private function kirimEmail(Kunjungan $kunjungan): void
    {
        try {
            Mail::to($kunjungan->kontak->email)->send(new StatusKunjunganMail($kunjungan));
            $kunjungan->update(['email_notified_at' => now()]);
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email: ' . $e->getMessage());
        }
    }

    private function kirimEmailEvaluasi(Kunjungan $kunjungan): void
    {
        try {
            Mail::to($kunjungan->kontak->email)->send(new EvaluasiKunjunganMail($kunjungan));
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email evaluasi: ' . $e->getMessage());
        }
    }
}
