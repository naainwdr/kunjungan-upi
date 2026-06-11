<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTempatRequest;
use App\Models\Tempat;
use App\Services\AdminReferensiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller untuk manajemen data Tempat Kunjungan di panel admin.
 *
 * Tempat kunjungan adalah lokasi fisik di dalam instansi yang dapat dipilih
 * oleh pemohon saat mengajukan permohonan kunjungan. Setiap tempat memiliki
 * kapasitas maksimum peserta yang menjadi batas validasi permohonan.
 *
 * Controller ini menggunakan AdminReferensiService untuk semua operasi CRUD.
 */
class AdminTempatController extends Controller
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
     * Menampilkan daftar semua tempat kunjungan.
     *
     * @return View
     */
    public function index(): View
    {
        // Ambil semua tempat, diurutkan alphabetically untuk kemudahan browsing
        $tempat = Tempat::orderBy('nama')->get();
        return view('admin.tempat.index', compact('tempat'));
    }

    /**
     * Menyimpan data tempat kunjungan baru.
     *
     * @param  StoreTempatRequest $request Request yang sudah tervalidasi
     * @return RedirectResponse
     */
    public function store(StoreTempatRequest $request): RedirectResponse
    {
        // Delegasikan pembuatan tempat ke service — null berarti mode create (bukan update)
        $this->referensiService->simpanTempat($request->validated(), null);

        return redirect()->route('admin.tempat.index')
            ->with('success', 'Tempat kunjungan berhasil ditambahkan.');
    }

    /**
     * Memperbarui data tempat kunjungan yang sudah ada.
     *
     * @param  StoreTempatRequest $request Request yang sudah tervalidasi
     * @param  Tempat             $tempat  Instance dari route model binding
     * @return RedirectResponse
     */
    public function update(StoreTempatRequest $request, Tempat $tempat): RedirectResponse
    {
        // Delegasikan ke service — pass instance $tempat untuk mode update
        $this->referensiService->simpanTempat($request->validated(), $tempat);

        return redirect()->route('admin.tempat.index')
            ->with('success', 'Data tempat kunjungan berhasil diperbarui.');
    }

    /**
     * Menghapus data tempat kunjungan.
     *
     * Penghapusan akan gagal jika tempat masih digunakan oleh kunjungan
     * yang sudah ada (foreign key constraint). Service menangani error ini.
     *
     * @param  Tempat $tempat Instance dari route model binding
     * @return RedirectResponse
     */
    public function destroy(Tempat $tempat): RedirectResponse
    {
        // Delegasikan penghapusan ke service — service menangani exception FK constraint
        $result = $this->referensiService->hapusTempat($tempat);

        return redirect()->route('admin.tempat.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
