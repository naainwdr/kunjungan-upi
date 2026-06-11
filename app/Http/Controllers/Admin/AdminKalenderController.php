<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKalenderRequest;
use App\Models\PengaturanKalender;
use App\Models\Sesi;
use App\Services\AdminReferensiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk manajemen pengaturan kalender kunjungan di panel admin.
 *
 * Pengaturan kalender memungkinkan admin untuk meng-override perilaku default
 * sistem (Senin-Kamis melayani, Jumat-Minggu libur) pada tanggal-tanggal tertentu.
 * Contoh penggunaan:
 * - Menandai tanggal tertentu sebagai hari libur khusus instansi
 * - Membatasi sesi yang tersedia pada hari tertentu (misal: hanya Sesi Pagi)
 *
 * Controller ini menggunakan AdminReferensiService untuk operasi penyimpanan.
 */
class AdminKalenderController extends Controller
{
    /**
     * Inisialisasi controller dengan dependency injection AdminReferensiService.
     *
     * @param  AdminReferensiService $referensiService Service untuk operasi data referensi
     */
    public function __construct(
        private readonly AdminReferensiService $referensiService,
    ) {}

    /**
     * Menampilkan halaman manajemen kalender dengan pengaturan yang sudah ada.
     *
     * @param  Request $request HTTP request berisi parameter year dan month untuk navigasi
     * @return View
     */
    public function index(Request $request): View
    {
        // Ambil parameter navigasi bulan/tahun dengan nilai default saat ini
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // Ambil semua pengaturan override untuk bulan yang ditampilkan
        $settings = PengaturanKalender::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->keyBy(fn($item) => $item->tanggal->format('Y-m-d')); // Index by tanggal untuk akses O(1) di view

        // Ambil semua sesi aktif untuk ditampilkan di form multi-select
        $sesi = Sesi::where('aktif', true)->get();

        return view('admin.kalender.index', compact('settings', 'sesi', 'year', 'month'));
    }

    /**
     * Menyimpan atau memperbarui pengaturan kalender untuk tanggal tertentu.
     *
     * Menggunakan updateOrCreate: jika pengaturan sudah ada untuk tanggal yang sama,
     * akan diperbarui; jika belum ada, akan dibuat baru.
     *
     * @param  StoreKalenderRequest $request Request yang sudah tervalidasi
     * @return RedirectResponse
     */
    public function store(StoreKalenderRequest $request): RedirectResponse
    {
        // Delegasikan penyimpanan/update ke service (menggunakan updateOrCreate internally)
        $this->referensiService->simpanKalender($request->validated());

        return redirect()->back()
            ->with('success', 'Pengaturan tanggal berhasil disimpan.');
    }

    /**
     * Menghapus pengaturan kalender (mengembalikan tanggal ke perilaku default).
     *
     * Setelah dihapus, tanggal tersebut kembali mengikuti aturan default:
     * Senin-Kamis = hari layanan, Jumat-Minggu = libur.
     *
     * @param  int $id ID pengaturan kalender yang akan dihapus
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        // Delegasikan penghapusan ke service (service akan throw 404 jika ID tidak ditemukan)
        $this->referensiService->hapusKalender($id);

        return redirect()->back()
            ->with('success', 'Pengaturan tanggal berhasil dihapus. Tanggal kembali ke aturan default.');
    }
}
