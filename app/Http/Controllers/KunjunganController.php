<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Sekolah;
use App\Models\KontakSekolah;
use App\Models\Tempat;
use App\Models\Sesi;
use App\Models\PengaturanKalender;
use App\Mail\StatusKunjunganMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    /** Landing Page */
    public function index()
    {
        return view('public.landing');
    }

    /** Kalender Kunjungan */
    public function kalender(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);
        $year  = max(now()->year - 1, min($year, now()->year + 2));
        $month = max(1, min($month, 12));

        $approvedVisitsList = Kunjungan::with(['sekolah', 'sesi', 'tempat'])
            ->where('status', 'approved')
            ->whereYear('tanggal_kunjungan', $year)
            ->whereMonth('tanggal_kunjungan', $month)
            ->orderBy('tanggal_kunjungan')
            ->get();

        $approvedVisits = $approvedVisitsList
            ->groupBy(fn($k) => $k->tanggal_kunjungan->format('Y-m-d'))
            ->map->count();

        $holidays    = $this->nationalHolidays($year);
        $overrides   = PengaturanKalender::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->keyBy(fn($i) => $i->tanggal->format('Y-m-d'));
            
        $servicedays = [1, 2, 3, 4]; // Senin–Kamis

        return view('public.kalender', compact(
            'year', 'month', 'approvedVisits', 'approvedVisitsList', 'holidays', 'servicedays', 'overrides'
        ));
    }

    /** Form Permohonan */
    public function create(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $tempat  = Tempat::where('aktif', true)->get();
        
        // Base Sesi Query
        $sesiQuery = Sesi::where('aktif', true)->orderBy('jam_mulai');
        
        if ($tanggal) {
            $override = PengaturanKalender::where('tanggal', $tanggal)->first();
            if ($override) {
                if ($override->is_libur) {
                    return redirect()->route('kalender')->with('error', 'Maaf, tanggal tersebut tidak melayani kunjungan.');
                }
                // Filter sessions by override
                $sesiQuery->whereIn('id', $override->sesi_tersedia ?? []);
            } else {
                // Check if it's a default off day (Fri-Sun)
                $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
                if (!in_array($dayOfWeek, [1, 2, 3, 4])) { // Not Mon-Thu
                    return redirect()->route('kalender')->with('error', 'Maaf, hari tersebut tidak melayani kunjungan.');
                }
            }
        }
        
        $sesi = $sesiQuery->get();
        
        if ($sesi->isEmpty() && $tanggal) {
            return redirect()->route('kalender')->with('error', 'Maaf, tidak ada sesi tersedia untuk tanggal tersebut.');
        }

        return view('public.reservasi', compact('tanggal', 'tempat', 'sesi'));
    }

    /** Simpan Permohonan */
    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah'      => 'required|string|max:255',
            'npsn'              => 'required|string|max:20',
            'alamat'            => 'required|string|max:500',
            'email_sekolah'     => 'required|email|max:255',
            'telepon_sekolah'   => 'required|string|max:20',
            'nama_pic'          => 'required|string|max:255',
            'jabatan_pic'       => 'required|in:kepsek,guru,tendik',
            'email_pic'         => 'required|email|max:255',
            'telepon_pic'       => 'required|string|max:20',
            'tanggal_kunjungan' => 'required|date|after_or_equal:' . now()->addDays(10)->format('Y-m-d'),
            'sesi_id'           => 'required|exists:sesi,id',
            'tempat_id'         => 'required|exists:tempat,id',
            'jumlah_peserta'    => 'required|integer|min:1',
            'jumlah_kepsek'     => 'nullable|integer|min:0',
            'jumlah_guru'       => 'nullable|integer|min:0',
            'jumlah_tendik'     => 'nullable|integer|min:0',
            'file_surat'        => 'required|file|mimes:pdf,jpg,jpeg|max:1024',
        ], [
            'nama_sekolah.required'      => 'Nama sekolah wajib diisi.',
            'npsn.required'              => 'NPSN wajib diisi.',
            'alamat.required'            => 'Alamat sekolah wajib diisi.',
            'email_sekolah.required'     => 'Email sekolah wajib diisi.',
            'telepon_sekolah.required'   => 'Nomor telepon sekolah wajib diisi.',
            'nama_pic.required'          => 'Nama penanggungjawab wajib diisi.',
            'jabatan_pic.required'       => 'Jabatan penanggungjawab wajib dipilih.',
            'email_pic.required'         => 'Email penanggungjawab wajib diisi.',
            'telepon_pic.required'       => 'Nomor telepon penanggungjawab wajib diisi.',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tanggal_kunjungan.after_or_equal' => 'Tanggal kunjungan minimal 10 hari dari sekarang.',
            'sesi_id.required'           => 'Sesi kunjungan wajib dipilih.',
            'sesi_id.exists'             => 'Sesi tidak valid.',
            'tempat_id.required'         => 'Tempat kunjungan wajib dipilih.',
            'tempat_id.exists'           => 'Tempat tidak valid.',
            'jumlah_peserta.required'    => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min'         => 'Jumlah peserta minimal 1 orang.',
            'file_surat.required'        => 'Surat permohonan wajib diunggah.',
            'file_surat.mimes'           => 'Format file harus PDF atau JPG.',
            'file_surat.max'             => 'Ukuran file maksimal 1 MB.',
        ]);

        // Validasi: hanya Senin–Kamis
        $tglObj = Carbon::parse($request->tanggal_kunjungan);
        if (!in_array($tglObj->dayOfWeek, [1, 2, 3, 4])) {
            return back()->withInput()->withErrors([
                'tanggal_kunjungan' => 'Kunjungan hanya dilayani pada hari Senin hingga Kamis.'
            ]);
        }

        // Validasi kapasitas tempat
        $tempatObj = Tempat::findOrFail($request->tempat_id);
        if ($request->jumlah_peserta > $tempatObj->kapasitas) {
            return back()->withInput()->withErrors([
                'jumlah_peserta' => "Kapasitas tempat yang dipilih maksimal {$tempatObj->kapasitas} orang."
            ]);
        }

        // Validasi sesi bentrok
        $bentrok = Kunjungan::where('tanggal_kunjungan', $request->tanggal_kunjungan)
            ->where('status', 'approved')
            ->where('sesi_id', $request->sesi_id)
            ->where('tempat_id', $request->tempat_id)
            ->exists();

        if ($bentrok) {
            return back()->withInput()->withErrors([
                'sesi_id' => 'Sesi dan tempat yang dipilih sudah penuh pada tanggal tersebut. Pilih sesi atau tempat lain.'
            ]);
        }

        // Upsert Sekolah by NPSN
        $sekolah = Sekolah::updateOrCreate(
            ['npsn' => $request->npsn],
            [
                'nama'    => $request->nama_sekolah,
                'alamat'  => $request->alamat,
                'email'   => $request->email_sekolah,
                'telepon' => $request->telepon_sekolah,
            ]
        );

        // Buat kontak baru untuk permohonan ini
        $kontak = KontakSekolah::create([
            'sekolah_id' => $sekolah->id,
            'nama'       => $request->nama_pic,
            'jabatan'    => $request->jabatan_pic,
            'email'      => $request->email_pic,
            'telepon'    => $request->telepon_pic,
        ]);

        $filePath = $this->uploadSurat($request);

        $kunjungan = Kunjungan::create([
            'nomor_registrasi'  => Kunjungan::generateNomorRegistrasi(),
            'sekolah_id'        => $sekolah->id,
            'kontak_id'         => $kontak->id,
            'tempat_id'         => $request->tempat_id,
            'sesi_id'           => $request->sesi_id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'jumlah_peserta'    => $request->jumlah_peserta,
            'jumlah_kepsek'     => $request->jumlah_kepsek ?? 0,
            'jumlah_guru'       => $request->jumlah_guru ?? 0,
            'jumlah_tendik'     => $request->jumlah_tendik ?? 0,
            'file_surat'        => $filePath,
            'status'            => 'pending',
        ]);

        // Log status awal
        $kunjungan->logStatus('pending', 'Permohonan baru diajukan.');

        try {
            Mail::to($kontak->email)->send(new StatusKunjunganMail($kunjungan));
            $kunjungan->update(['email_notified_at' => now()]);
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email: ' . $e->getMessage());
        }

        return redirect()->route('reservasi.sukses', ['id' => $kunjungan->nomor_registrasi]);
    }

    /** Upload file surat */
    private function uploadSurat(Request $request): string
    {
        // Menyimpan file secara lokal ke storage/app/public/surat
        return $request->file('file_surat')->store('surat', 'public');
    }

    /** Sukses */
    public function sukses(Request $request)
    {
        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat'])
            ->where('nomor_registrasi', $request->query('id'))
            ->firstOrFail();
        return view('public.sukses', compact('kunjungan'));
    }

    /** Cek Status (form) */
    public function cekStatus()
    {
        return view('public.cek-status');
    }

    /** Cari Status */
    public function cariStatus(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ], [
            'query.required' => 'Masukkan nomor registrasi atau email.',
            'query.min'      => 'Minimal 3 karakter.',
        ]);

        $q = trim($request->input('query'));

        $kunjungan = Kunjungan::with(['sekolah', 'kontak', 'sesi', 'tempat'])
            ->where('nomor_registrasi', $q)
            ->orWhereHas('kontak', fn($qb) => $qb->where('email', $q))
            ->orWhereHas('sekolah', fn($qb) => $qb->where('email', $q))
            ->latest()
            ->get();

        return view('public.cek-status', compact('kunjungan', 'q'));
    }

    /** Batalkan Kunjungan */
    public function batal(Request $request, $id)
    {
        $kunjungan = Kunjungan::where('nomor_registrasi', $id)->firstOrFail();

        if (now()->startOfDay()->gt($kunjungan->tanggal_kunjungan->clone()->subDays(5)->startOfDay())) {
            return back()->with('error', 'Pembatalan ditolak. Batas pembatalan adalah H-5 sebelum kunjungan.');
        }

        $kunjungan->logStatus('cancelled', 'Dibatalkan oleh pemohon.');
        $kunjungan->update(['status' => 'cancelled']);

        return back()->with('success', 'Permohonan kunjungan berhasil dibatalkan.');
    }

    /** API: sesi yang sudah penuh atau tidak tersedia di tanggal tertentu */
    public function bookedSesi(Request $request)
    {
        $tanggal  = $request->query('tanggal');
        $tempatId = $request->query('tempat_id');
        if (!$tanggal) return response()->json([]);

        $booked = [];

        // 1. Ambil dari pengaturan kalender (override)
        $override = PengaturanKalender::where('tanggal', $tanggal)->first();
        if ($override) {
            if ($override->is_libur) {
                // Jika libur, semua sesi dianggap tidak tersedia
                $booked = Sesi::pluck('id')->toArray();
            } else {
                // Sesi yang TIDAK ada di array sesi_tersedia dianggap tidak tersedia
                $semuaSesi = Sesi::pluck('id')->toArray();
                $tersedia = $override->sesi_tersedia ?? [];
                $booked = array_diff($semuaSesi, $tersedia);
            }
        } else {
            // Default: Jumat, Sabtu, Minggu libur (0, 5, 6)
            $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
            if (!in_array($dayOfWeek, [1, 2, 3, 4])) {
                $booked = Sesi::pluck('id')->toArray();
            }
        }

        // 2. Tambahkan sesi yang sudah disetujui (Approved) untuk tempat yang dipilih
        $query = Kunjungan::where('tanggal_kunjungan', $tanggal)->where('status', 'approved');
        if ($tempatId) {
            $query->where('tempat_id', $tempatId);
        }
        $alreadyApproved = $query->pluck('sesi_id')->toArray();
        
        $merged = array_merge($booked, $alreadyApproved);
        return response()->json(array_values(array_unique($merged)));
    }

    /** Evaluasi Form */
    public function evaluasiForm($id)
    {
        $kunjungan = Kunjungan::with(['sekolah', 'kontak'])->where('nomor_registrasi', $id)->firstOrFail();
        if ($kunjungan->status !== 'completed') abort(403, 'Hanya untuk kunjungan selesai.');
        if ($kunjungan->updated_at->diffInDays(now()) > 7) abort(403, 'Batas evaluasi sudah lewat.');
        return view('public.evaluasi', compact('kunjungan'));
    }

    /** Simpan Evaluasi */
    public function simpanEvaluasi(Request $request, $id)
    {
        $kunjungan = Kunjungan::where('nomor_registrasi', $id)->firstOrFail();
        if ($kunjungan->status !== 'completed') abort(403);
        if ($kunjungan->updated_at->diffInDays(now()) > 7) abort(403);

        $request->validate([
            'rating_pelayanan' => 'required|integer|min:1|max:5',
            'rating_fasilitas' => 'required|integer|min:1|max:5',
            'rating_informasi' => 'required|integer|min:1|max:5',
            'komentar'         => 'nullable|string|max:1000',
            'saran'            => 'nullable|string|max:1000',
        ]);

        $kunjungan->update(['catatan_admin' => json_encode([
            'rating_pelayanan' => $request->rating_pelayanan,
            'rating_fasilitas' => $request->rating_fasilitas,
            'rating_informasi' => $request->rating_informasi,
            'komentar'         => $request->komentar,
            'saran'            => $request->saran,
            'disimpan_pada'    => now()->toISOString(),
        ])]);

        return redirect()->route('evaluasi.terima-kasih', $kunjungan->nomor_registrasi);
    }

    /** Terima Kasih Evaluasi */
    public function terimaKasih($id)
    {
        $kunjungan = Kunjungan::where('nomor_registrasi', $id)->firstOrFail();
        return view('public.terima-kasih', compact('kunjungan'));
    }

    /** Daftar Hari Libur Nasional */
    private function nationalHolidays(int $year): array
    {
        $h = [
            "$year-01-01" => "Tahun Baru Masehi",
            "$year-05-01" => "Hari Buruh Internasional",
            "$year-06-01" => "Hari Lahir Pancasila",
            "$year-08-17" => "Hari Kemerdekaan RI",
            "$year-12-25" => "Hari Raya Natal",
            "$year-12-26" => "Cuti Bersama Natal",
        ];
        if ($year === 2026) {
            $h += [
                "2026-01-27" => "Isra Mi'raj Nabi Muhammad SAW",
                "2026-01-29" => "Tahun Baru Imlek 2577",
                "2026-03-20" => "Hari Raya Nyepi",
                "2026-03-31" => "Hari Raya Idul Fitri 1447H",
                "2026-04-01" => "Hari Raya Idul Fitri 1447H",
                "2026-04-02" => "Wafat Isa Al Masih",
                "2026-04-03" => "Cuti Bersama Idul Fitri",
                "2026-05-12" => "Hari Raya Waisak 2570",
                "2026-05-14" => "Kenaikan Isa Al Masih",
                "2026-06-06" => "Hari Raya Idul Adha 1447H",
                "2026-06-07" => "Cuti Bersama Idul Adha",
                "2026-06-27" => "Tahun Baru Islam 1448H",
                "2026-09-05" => "Maulid Nabi Muhammad SAW",
            ];
        }
        if ($year === 2027) {
            $h += [
                "2027-01-17" => "Isra Mi'raj Nabi Muhammad SAW",
                "2027-01-27" => "Tahun Baru Imlek 2578",
                "2027-03-09" => "Hari Raya Nyepi",
                "2027-03-20" => "Hari Raya Idul Fitri 1448H",
                "2027-03-21" => "Hari Raya Idul Fitri 1448H",
                "2027-04-26" => "Wafat Isa Al Masih",
                "2027-05-01" => "Hari Raya Waisak",
                "2027-05-27" => "Idul Adha 1448H",
                "2027-09-25" => "Maulid Nabi Muhammad SAW",
            ];
        }
        return $h;
    }
}
