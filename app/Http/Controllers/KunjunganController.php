<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Mail\StatusKunjunganMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class KunjunganController extends Controller
{
    /**
     * Halaman Landing Page
     */
    public function index()
    {
        return view('public.landing');
    }

    /**
     * Halaman Formulir Reservasi
     */
    public function create()
    {
        return view('public.reservasi');
    }

    /**
     * Proses submit formulir reservasi
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah'      => 'required|string|max:255',
            'npsn'              => 'required|string|max:20',
            'alamat'            => 'required|string|max:500',
            'nama_pic'          => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'telepon'           => 'required|string|max:20',
            'tanggal_kunjungan' => 'required|date|after:today',
            'jumlah_peserta'    => 'required|integer|min:1|max:500',
            'file_surat'        => 'required|file|mimes:pdf,jpg,jpeg|max:1024',
        ], [
            'nama_sekolah.required'      => 'Nama sekolah wajib diisi.',
            'npsn.required'              => 'NPSN wajib diisi.',
            'alamat.required'            => 'Alamat sekolah wajib diisi.',
            'nama_pic.required'          => 'Nama penanggungjawab wajib diisi.',
            'email.required'             => 'Email wajib diisi.',
            'email.email'                => 'Format email tidak valid.',
            'telepon.required'           => 'Nomor telepon wajib diisi.',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tanggal_kunjungan.after'    => 'Tanggal kunjungan harus setelah hari ini.',
            'jumlah_peserta.required'    => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min'         => 'Jumlah peserta minimal 1 orang.',
            'jumlah_peserta.max'         => 'Jumlah peserta maksimal 500 orang.',
            'file_surat.required'        => 'Surat permohonan wajib diunggah.',
            'file_surat.mimes'           => 'Format file harus PDF atau JPG.',
            'file_surat.max'             => 'Ukuran file maksimal 1 MB.',
        ]);

        // Upload file: Cloudinary (production) atau local (development)
        $filePath = $this->uploadSurat($request);

        $kunjungan = Kunjungan::create([
            'nomor_registrasi'  => Kunjungan::generateNomorRegistrasi(),
            'nama_sekolah'      => $request->nama_sekolah,
            'npsn'              => $request->npsn,
            'alamat'            => $request->alamat,
            'nama_pic'          => $request->nama_pic,
            'email'             => $request->email,
            'telepon'           => $request->telepon,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'jumlah_peserta'    => $request->jumlah_peserta,
            'file_surat'        => $filePath,
            'status'            => 'pending',
        ]);

        // Kirim email konfirmasi pendaftaran
        try {
            Mail::to($kunjungan->email)->send(new StatusKunjunganMail($kunjungan));
            $kunjungan->update(['email_notified_at' => now()]);
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email: ' . $e->getMessage());
        }

        return redirect()->route('reservasi.sukses', ['id' => $kunjungan->nomor_registrasi]);
    }

    /**
     * Upload file surat — otomatis pilih Cloudinary (production) atau local (development)
     */
    private function uploadSurat(Request $request): string
    {
        $disk = config('filesystems.default');

        if ($disk === 'cloudinary') {
            // Production: Upload ke Cloudinary
            $result = Cloudinary::upload($request->file('file_surat')->getRealPath(), [
                'folder'        => 'upi-reservasi/surat',
                'resource_type' => 'auto',
                'public_id'     => 'surat_' . time(),
            ]);
            return $result->getSecurePath(); // URL lengkap Cloudinary
        }

        // Development: Simpan ke local storage
        return $request->file('file_surat')->store('surat', 'public');
    }

    /**
     * Halaman sukses setelah submit
     */
    public function sukses(Request $request)
    {
        $kunjungan = Kunjungan::where('nomor_registrasi', $request->query('id'))->firstOrFail();
        return view('public.sukses', compact('kunjungan'));
    }

    /**
     * Halaman cek status
     */
    public function cekStatus()
    {
        return view('public.cek-status');
    }

    /**
     * Proses pencarian status
     */
    public function cariStatus(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ], [
            'query.required' => 'Masukkan nomor registrasi atau email.',
            'query.min'      => 'Minimal 3 karakter.',
        ]);

        $query = trim($request->query('query'));

        $kunjungan = Kunjungan::where('nomor_registrasi', $query)
            ->orWhere('email', $query)
            ->latest()
            ->get();

        return view('public.cek-status', compact('kunjungan', 'query'));
    }
}
