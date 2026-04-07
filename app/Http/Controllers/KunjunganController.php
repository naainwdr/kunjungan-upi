<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Mail\StatusKunjunganMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;

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
     * Kalender Kunjungan
     */
    public function kalender(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);

        // Batasi rentang navigasi: 1 tahun ke belakang, 2 tahun ke depan
        $year  = max(now()->year - 1, min($year, now()->year + 2));
        $month = max(1, min($month, 12));

        // Kunjungan yang sudah disetujui di bulan ini
        $approvedVisitsList = Kunjungan::where('status', 'approved')
            ->whereYear('tanggal_kunjungan', $year)
            ->whereMonth('tanggal_kunjungan', $month)
            ->orderBy('tanggal_kunjungan')
            ->get();

        // Grouped count per date: ['2026-04-15' => 2, ...]
        $approvedVisits = $approvedVisitsList
            ->groupBy(fn($k) => $k->tanggal_kunjungan->format('Y-m-d'))
            ->map->count();

        $holidays = $this->nationalHolidays($year);

        return view('public.kalender', compact(
            'year', 'month', 'approvedVisits', 'approvedVisitsList', 'holidays'
        ));
    }

    /**
     * Halaman Formulir Permohonan
     */
    public function create(Request $request)
    {
        $tanggal = $request->get('tanggal'); // pre-fill dari kalender
        return view('public.reservasi', compact('tanggal'));
    }

    /**
     * Proses submit formulir permohonan
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
            'tanggal_kunjungan' => 'required|date|after_or_equal:' . now()->addDays(7)->format('Y-m-d'),
            'jam_mulai'         => 'required|string',
            'jam_selesai'       => ['required', 'string', function ($attribute, $value, $fail) use ($request) {
                if ($request->jam_mulai && $value) {
                    $start    = (int) substr($request->jam_mulai, 0, 2);
                    $end      = (int) substr($value, 0, 2);
                    $duration = $end - $start;
                    if ($duration < 2) $fail('Durasi kunjungan minimal 2 jam.');
                    if ($duration > 5) $fail('Durasi kunjungan maksimal 5 jam.');
                    if ($end > 16)     $fail('Kunjungan harus selesai paling lambat pukul 16:00 WIB.');
                }
            }],
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
            'tanggal_kunjungan.after_or_equal' => 'Tanggal kunjungan minimal 7 hari dari sekarang.',
            'jam_mulai.required'         => 'Jam mulai kunjungan wajib dipilih.',
            'jam_selesai.required'       => 'Jam selesai kunjungan wajib dipilih.',
            'jumlah_peserta.required'    => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min'         => 'Jumlah peserta minimal 1 orang.',
            'jumlah_peserta.max'         => 'Jumlah peserta maksimal 500 orang.',
            'file_surat.required'        => 'Surat permohonan wajib diunggah.',
            'file_surat.mimes'           => 'Format file harus PDF atau JPG.',
            'file_surat.max'             => 'Ukuran file maksimal 1 MB.',
        ]);

        // Validasi overlap jam kunjungan
        $startReq = (int) substr($request->jam_mulai, 0, 2);
        $endReq   = (int) substr($request->jam_selesai, 0, 2);
        
        $overlapping = Kunjungan::where('tanggal_kunjungan', $request->tanggal_kunjungan)
            ->where('status', 'approved')
            ->get()
            ->contains(function ($visit) use ($startReq, $endReq) {
                $startB = (int) substr($visit->jam_mulai, 0, 2);
                $endB   = (int) substr($visit->jam_selesai, 0, 2);
                return ($startReq < $endB && $startB < $endReq);
            });

        if ($overlapping) {
            return back()->withInput()->withErrors([
                'jam_mulai' => 'Jadwal jam bentrok dengan kunjungan lain yang sudah disetujui. Silakan pilih hari atau jam lain.'
            ]);
        }

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
            'jam_mulai'         => $request->jam_mulai,
            'jam_selesai'       => $request->jam_selesai,
            'jumlah_peserta'    => $request->jumlah_peserta,
            'file_surat'        => $filePath,
            'status'            => 'pending',
        ]);

        try {
            Mail::to($kunjungan->email)->send(new StatusKunjunganMail($kunjungan));
            $kunjungan->update(['email_notified_at' => now()]);
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email: ' . $e->getMessage());
        }

        return redirect()->route('reservasi.sukses', ['id' => $kunjungan->nomor_registrasi]);
    }

    /**
     * Upload file surat
     */
    private function uploadSurat(Request $request): string
    {
        $disk = config('filesystems.default');

        if ($disk === 'cloudinary') {
            $result = Cloudinary::uploadApi()->upload($request->file('file_surat')->getRealPath(), [
                'folder'        => 'upi-reservasi/surat',
                'resource_type' => 'auto',
                'public_id'     => 'surat_' . time(),
            ]);
            return $result['secure_url'];
        }

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
     * API Ambil data jam yang terbooking di tanggal tertentu
     */
    public function bookedHours(Request $request)
    {
        $tanggal = $request->query('tanggal');
        if (!$tanggal) return response()->json([]);

        $booked = Kunjungan::where('tanggal_kunjungan', $tanggal)
            ->where('status', 'approved')
            ->get();

        $blockedHours = [];
        foreach ($booked as $b) {
            $start = (int) substr($b->jam_mulai, 0, 2);
            $end = (int) substr($b->jam_selesai, 0, 2);
            for ($i = $start; $i < $end; $i++) {
                $blockedHours[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            }
        }
        return response()->json(array_values(array_unique($blockedHours)));
    }

    /**
     * Batal Kunjungan
     */
    public function batal(Request $request, $id)
    {
        $kunjungan = Kunjungan::where('nomor_registrasi', $id)->firstOrFail();
        
        // Maksimal pembatalan H-2
        if (now()->startOfDay()->gt($kunjungan->tanggal_kunjungan->clone()->subDays(2)->startOfDay())) {
            return back()->with('error', 'Pembatalan ditolak. Pembatalan hanya dapat dilakukan maksimal H-2 dari tanggal kunjungan.');
        }

        $kunjungan->update(['status' => 'cancelled']);

        return back()->with('success', 'Permohonan kunjungan berhasil dibatalkan secara sistem.');
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

        $query = trim($request->input('query'));

        $kunjungan = Kunjungan::where('nomor_registrasi', $query)
            ->orWhere('email', $query)
            ->latest()
            ->get();

        return view('public.cek-status', compact('kunjungan', 'query'));
    }

    /**
     * Daftar hari libur nasional Indonesia
     */
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
                "2026-03-20" => "Hari Raya Nyepi (Tahun Baru Saka 1948)",
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
