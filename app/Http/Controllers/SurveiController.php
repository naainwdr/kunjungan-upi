<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveiRequest;
use App\Models\Kunjungan;
use App\Services\SurveiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller untuk form survei kepuasan kunjungan (publik).
 *
 * Survei kepuasan dapat diisi oleh PIC sekolah setelah kunjungan selesai
 * (sudah check-out) dalam rentang waktu 7 hari. Link survei dikirim otomatis
 * melalui email setelah petugas/admin melakukan check-out.
 *
 * Controller ini mendelegasikan semua validasi akses dan penyimpanan data
 * ke SurveiService.
 */
class SurveiController extends Controller
{
    /**
     * Inisialisasi controller dengan dependency injection SurveiService.
     *
     * @param  SurveiService $surveiService Service untuk logika survei kepuasan
     */
    public function __construct(
        private readonly SurveiService $surveiService,
    ) {}

    /**
     * Menampilkan form survei kepuasan kunjungan.
     *
     * Beberapa kondisi dapat menghasilkan tampilan berbeda:
     * - 'belum_checkout': kunjungan belum selesai check-out
     * - 'sudah_isi': pemohon sudah pernah mengisi survei ini
     * - 'kadaluarsa': sudah lewat 7 hari dari check-out
     * - 'ok': form bisa diakses dan diisi
     *
     * @param  string $nomor Nomor registrasi kunjungan
     * @return View
     */
    public function form(string $nomor): View
    {
        // Delegasikan validasi akses ke service — service mengembalikan status dan data kunjungan
        $validasi = $this->surveiService->validateFormAkses($nomor);

        $kunjungan = $validasi['kunjungan']; // Data kunjungan untuk ditampilkan di view
        $status    = $validasi['status'];    // Status akses: 'ok', 'belum_checkout', 'sudah_isi', 'kadaluarsa'

        // Kirim semua variabel ke view — view yang bertanggung jawab menampilkan pesan yang sesuai
        return view('public.survei', [
            'kunjungan'     => $kunjungan,
            'belumCheckout' => $status === 'belum_checkout', // Flag untuk menampilkan pesan "belum selesai"
            'sudahIsi'      => $status === 'sudah_isi',      // Flag untuk menampilkan pesan "sudah diisi"
            'kadaluarsa'    => $status === 'kadaluarsa',     // Flag untuk menampilkan pesan "link kadaluarsa"
        ]);
    }

    /**
     * Menyimpan data survei kepuasan yang diisi pemohon.
     *
     * Validasi format data ditangani oleh StoreSurveiRequest.
     * Validasi akses (sudah checkout, belum isi, tidak kadaluarsa) ditangani oleh SurveiService.
     *
     * @param  StoreSurveiRequest $request Request yang sudah tervalidasi
     * @param  string             $nomor   Nomor registrasi kunjungan
     * @return RedirectResponse
     */
    public function store(StoreSurveiRequest $request, string $nomor): RedirectResponse
    {
        // Delegasikan penyimpanan survei ke service (termasuk defense-in-depth validation)
        $result = $this->surveiService->simpanSurvei($nomor, $request->validated());

        if (! $result['success']) {
            // Jika service menolak (bypass form terdeteksi), tampilkan error
            return back()->with('error', $result['message']);
        }

        // Redirect ke halaman terima kasih setelah survei berhasil disimpan
        return redirect()->route('survei.terima-kasih', $nomor);
    }

    /**
     * Menampilkan halaman terima kasih setelah survei berhasil diisi.
     *
     * @param  string $nomor Nomor registrasi kunjungan
     * @return View
     */
    public function terimaKasih(string $nomor): View
    {
        // Ambil kunjungan untuk menampilkan informasi di halaman konfirmasi
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'survei'])
            ->where('nomor_registrasi', $nomor)
            ->firstOrFail();

        return view('public.survei-terima-kasih', compact('kunjungan'));
    }
}
