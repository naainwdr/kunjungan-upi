<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSesiRequest;
use App\Models\Sesi;
use App\Services\AdminReferensiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller untuk manajemen data Sesi Kunjungan di panel admin.
 *
 * Sesi kunjungan mendefinisikan slot waktu yang tersedia untuk kunjungan
 * (contoh: Sesi Pagi 08:00-10:00, Sesi Siang 10:30-12:00). Pemohon
 * memilih sesi saat mengajukan permohonan untuk menghindari konflik jadwal.
 *
 * Controller ini menggunakan AdminReferensiService untuk operasi CRUD.
 */
class AdminSesiController extends Controller
{
    /**
     * Inisialisasi controller dengan dependency injection AdminReferensiService.
     *
     * @param  AdminReferensiService $referensiService Service untuk operasi CRUD data referensi
     */
    public function __construct(
        private readonly AdminReferensiService $referensiService,
    ) {}

    /**
     * Menampilkan daftar semua sesi kunjungan.
     *
     * @return View
     */
    public function index(): View
    {
        // Urutkan berdasarkan jam_mulai agar tampil kronologis di UI
        $sesi = Sesi::orderBy('jam_mulai')->get();
        return view('admin.sesi.index', compact('sesi'));
    }

    /**
     * Menyimpan sesi kunjungan baru.
     *
     * @param  StoreSesiRequest $request Request yang sudah tervalidasi (termasuk validasi after:jam_mulai)
     * @return RedirectResponse
     */
    public function store(StoreSesiRequest $request): RedirectResponse
    {
        // Delegasikan pembuatan sesi ke service — null berarti mode create
        $this->referensiService->simpanSesi($request->validated(), null);

        return redirect()->route('admin.sesi.index')
            ->with('success', 'Sesi kunjungan berhasil ditambahkan.');
    }

    /**
     * Memperbarui data sesi kunjungan yang sudah ada.
     *
     * @param  StoreSesiRequest $request Request yang sudah tervalidasi
     * @param  Sesi             $sesi    Instance dari route model binding
     * @return RedirectResponse
     */
    public function update(StoreSesiRequest $request, Sesi $sesi): RedirectResponse
    {
        // Delegasikan ke service — pass instance $sesi untuk mode update
        $this->referensiService->simpanSesi($request->validated(), $sesi);

        return redirect()->route('admin.sesi.index')
            ->with('success', 'Data sesi kunjungan berhasil diperbarui.');
    }

    /**
     * Menghapus data sesi kunjungan.
     *
     * Penghapusan akan gagal jika sesi masih direferensikan oleh kunjungan
     * atau pengaturan kalender.
     *
     * @param  Sesi $sesi Instance dari route model binding
     * @return RedirectResponse
     */
    public function destroy(Sesi $sesi): RedirectResponse
    {
        // Delegasikan penghapusan ke service yang menangani exception FK constraint
        $result = $this->referensiService->hapusSesi($sesi);

        return redirect()->route('admin.sesi.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
