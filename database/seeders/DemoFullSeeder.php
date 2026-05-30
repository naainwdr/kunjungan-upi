<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoFullSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────
        // 0. Ambil referensi tempat & sesi yang sudah ada
        // ─────────────────────────────────────────────────
        $tempat = DB::table('tempat')->pluck('id', 'nama');
        $sesi   = DB::table('sesi')->pluck('id', 'nama');
        $admin  = DB::table('users')->value('id'); // pakai admin pertama

        // ─────────────────────────────────────────────────
        // 1. Sekolah + Kontak
        // ─────────────────────────────────────────────────
        $sekolahData = [
            // [Nama, NPSN, Alamat, Email Sekolah, Telp Sekolah, PIC Nama, PIC Telp, PIC Email]
            ['SMAN 1 Bandung',        '20219121', 'Jl. Ir. H. Juanda No.93, Bandung',          'info@sman1bdg.sch.id',   '022-1111111', 'Drs. Ahmad Fauzi, M.Pd',   '081211110001', 'pic.sman1bdg@gmail.com'],
            ['SMAN 3 Cimahi',         '20228131', 'Jl. Pasantren No.161, Cimahi',              'info@sman3cmi.sch.id',   '022-1111112', 'Hj. Sri Wahyuni, S.Pd',    '081211110002', 'pic.sman3cmi@gmail.com'],
            ['SMK Negeri 2 Garut',    '20227181', 'Jl. Suherman No.90, Garut',                 'info@smkn2grt.sch.id',   '0262-1111113', 'Budi Santoso, M.T',        '081211110003', 'pic.smkn2grt@gmail.com'],
            ['SMAN 5 Bekasi',         '20222711', 'Jl. Jend. Ahmad Yani No.54, Bekasi',        'info@sman5bks.sch.id',   '021-1111114', 'Rina Kusumawati, S.Pd',    '081211110004', 'pic.sman5bks@gmail.com'],
            ['MAN 1 Sumedang',        '20208351', 'Jl. Mayor Abdurachman No.12, Sumedang',     'info@man1smd.sch.id',    '0261-1111115', 'Ustad Jamaludin, M.Ag',    '081211110005', 'pic.man1smd@gmail.com'],
            ['SMAN 7 Bogor',          '20220261', 'Jl. Palupuh No.1, Bogor',                   'info@sman7bgr.sch.id',   '0251-1111116', 'Dra. Nani Suryani',        '081211110006', 'pic.sman7bgr@gmail.com'],
            ['SMP Negeri 1 Subang',   '20233481', 'Jl. Sudirman No.32, Subang',                'info@smpn1sbg.sch.id',   '0260-1111117', 'Hendra Gunawan, S.Pd',     '081211110007', 'pic.smpn1sbg@gmail.com'],
            ['SMK Pasundan 1 Bandung','20219451', 'Jl. Balong Gede No.44, Bandung',            'info@smkpsd1.sch.id',    '022-1111118', 'Asep Hidayat, M.Pd',       '081211110008', 'pic.smkpsd1@gmail.com'],
            ['SMAN 2 Purwakarta',     '20216061', 'Jl. Veteran No.7, Purwakarta',              'info@sman2pwk.sch.id',   '0264-1111119', 'Dewi Anggraeni, S.Pd',     '081211110009', 'pic.sman2pwk@gmail.com'],
            ['SMA Muhammadiyah Tasik','20210211', 'Jl. Pasar Wetan No.28, Tasikmalaya',        'info@smamtsm.sch.id',    '0265-1111120', 'M. Rizki Firdaus, S.Pd',   '081211110010', 'pic.smamtsm@gmail.com'],
        ];

        $sekolahIds = [];
        $kontakIds  = [];

        foreach ($sekolahData as $idx => [$nama, $npsn, $alamat, $email, $telp, $picNama, $picTelp, $picEmail]) {
            $sid = DB::table('sekolah')->insertGetId([
                'nama'       => $nama,
                'npsn'       => $npsn,
                'alamat'     => $alamat,
                'email'      => 'ninawd27@gmail.com',
                'telepon'    => $telp,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $kid = DB::table('kontak_sekolah')->insertGetId([
                'sekolah_id' => $sid,
                'nama'       => $picNama,
                'telepon'    => $picTelp,
                'email'      => 'ninawd27@gmail.com',
                'jabatan'    => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sekolahIds[] = $sid;
            $kontakIds[]  = $kid;
        }

        // ─────────────────────────────────────────────────
        // 2. Kunjungan (beragam status & tanggal)
        // ─────────────────────────────────────────────────
        $tempatIds = array_values($tempat->toArray());
        $sesiIds   = array_values($sesi->toArray());

        $kunjunganDefs = [
            // [0-4] DATA MASA LALU (Untuk Grafik & Testimoni)
            [0, 0, 0, -30, 'completed', 55, 4],
            [1, 2, 1, -21, 'completed', 180, 8],
            [2, 1, 0, -14, 'completed', 270, 12],
            [3, 4, 1, -10, 'completed', 190, 9],
            [4, 3, 0, -7,  'completed', 280, 11],

            // [5] DEMO 1 (Hari Ini): Status Pending (Demo Admin Approval)
            [5, 2, 1, 0, 'pending', 120, 6], 

            // [6] DEMO 2 (Hari Ini): Status Approved, sudah check-in (Demo Check-Out Scanner)
            [6, 0, 0, 0, 'approved', 45, 3], 

            // [7] DEMO 3 (Besok): Status Approved, belum check-in
            [7, 1, 1, 1, 'approved', 250, 10], 

            // [8] DEMO 4 (Besok): Status Pending
            [8, 4, 0, 1, 'pending', 180, 7],

            // [9] Lusa (Offset 2 hari ke depan)
            [9, 2, 1, 2, 'pending', 200, 8],
            
            // Kemungkinan lain bebas
            [0, 3, 0, 14, 'pending', 290, 13],
            [1, 0, 1, -5, 'rejected', 60, 3],
            [2, 1, 0, -20, 'cancelled', 150, 7],
        ];

        $kunjunganIds = [];
        foreach ($kunjunganDefs as $i => [$si, $ti, $sei, $dayOffset, $status, $peserta, $guru]) {
            $tgl     = Carbon::today()->addDays($dayOffset)->format('Y-m-d');
            $prefix  = 'UPI-' . Carbon::parse($tgl)->format('Ymd') . '-';
            $noReg   = $prefix . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $id = DB::table('kunjungan')->insertGetId([
                'nomor_registrasi'  => $noReg,
                'sekolah_id'        => $sekolahIds[$si],
                'kontak_id'         => $kontakIds[$si],
                'tempat_id'         => $tempatIds[$ti],
                'sesi_id'           => $sesiIds[$sei],
                'tanggal_kunjungan' => $tgl,
                'jumlah_peserta'    => $peserta,
                'jumlah_kepsek'     => 1,
                'jumlah_guru'       => $guru,
                'jumlah_tendik'     => rand(1, 3),
                'file_surat'        => null,
                'status'            => $status,
                'catatan_admin'     => $status === 'rejected'
                    ? 'Kuota tempat sudah penuh pada tanggal tersebut.'
                    : ($status === 'approved' ? 'Permohonan disetujui. Harap tiba 30 menit sebelum sesi dimulai.' : null),
                'email_notified_at' => in_array($status, ['approved','rejected']) ? now()->subDays(abs($dayOffset) - 1) : null,
                'created_at'        => Carbon::parse($tgl)->subDays(10),
                'updated_at'        => Carbon::parse($tgl)->subDays(8),
            ]);

            $kunjunganIds[] = ['id' => $id, 'status' => $status, 'tgl' => $tgl, 'si' => $si, 'sei' => $sei];

            // Log status
            if ($status !== 'pending') {
                DB::table('kunjungan_log')->insert([
                    'kunjungan_id'   => $id,
                    'status_sebelum' => 'pending',
                    'status_sesudah' => $status,
                    'catatan'        => "Status diubah oleh admin",
                    'changed_by'     => $admin,
                    'created_at'     => Carbon::parse($tgl)->subDays(8),
                ]);
            }
        }

        // ─────────────────────────────────────────────────
        // 3. Presensi — untuk kunjungan completed & approved yg sudah lewat
        // ─────────────────────────────────────────────────
        $presensiData = [
            // Presensi untuk data Completed (Masa Lalu)
            [0, '08:30:00', '12:15:00'],
            [1, '12:40:00', '15:05:00'],
            [2, '08:45:00', '12:30:00'],
            [3, '12:35:00', '15:10:00'],
            [4, '08:55:00', '12:20:00'],

            // UNTUK DEMO CHECK-OUT: Hanya Check-In saja untuk index 6 (Hari Ini)
            // Ini yang akan di-scan untuk simulasi Checkout.
            [6, '08:40:00', null], 
        ];

        foreach ($presensiData as [$ki, $masuk, $keluar]) {
            $k   = $kunjunganIds[$ki];
            $tgl = $k['tgl'];
            DB::table('kunjungan_presensi')->insert([
                'kunjungan_id'      => $k['id'],
                'waktu_masuk'       => Carbon::parse($tgl . ' ' . $masuk),
                'waktu_keluar'      => $keluar ? Carbon::parse($tgl . ' ' . $keluar) : null,
                'petugas_masuk_id'  => $admin,
                'petugas_keluar_id' => $keluar ? $admin : null,
                'catatan'           => null,
                'created_at'        => Carbon::parse($tgl . ' ' . $masuk),
                'updated_at'        => $keluar ? Carbon::parse($tgl . ' ' . $keluar) : Carbon::parse($tgl . ' ' . $masuk),
            ]);
        }

        // ─────────────────────────────────────────────────
        // 4. Survei Kepuasan — untuk kunjungan completed (index 0–5)
        // ─────────────────────────────────────────────────
        $surveiData = [
            [0, 5, 5, 5, 'Luar biasa! Pelayanan sangat ramah dan informatif. Siswa kami sangat antusias dan terinspirasi setelah melihat fasilitas kampus UPI.', 'Semoga program ini terus berkembang.', true],
            [1, 4, 5, 4, 'Kunjungan sangat berkesan. Auditorium FPEB sangat megah dan kondisinya bersih. Petugas sangat membantu dan sabar menjawab pertanyaan siswa.', 'Mungkin bisa ada sesi tanya jawab dengan mahasiswa UPI.', true],
            [2, 5, 4, 5, 'Program kunjungan ini sangat bermanfaat. Anak-anak jadi lebih termotivasi untuk masuk UPI. Terima kasih KKIPP UPI!', 'Tambahkan info beasiswa juga saat sesi berlangsung.', true],
            [3, 4, 4, 4, 'Secara keseluruhan bagus. Jadwal berjalan tepat waktu dan koordinasi sangat baik.', 'Parkir kendaraan perlu diperluas untuk rombongan besar.', true],
            [4, 5, 5, 4, 'Kami dari Sumedang merasa sangat disambut. Ice breaking-nya seru sekali! Siswa tidak bosan sama sekali.', null, true],
            [5, 4, 3, 5, 'Pelayanan informasi sangat lengkap dan jelas. Fasilitas agak ramai hari itu tapi tetap kondusif.', 'Mohon disediakan air minum untuk peserta.', false],
        ];

        foreach ($surveiData as [$ki, $rp, $rf, $ri, $komentar, $saran, $publik]) {
            DB::table('survei_kepuasan')->insert([
                'kunjungan_id'     => $kunjunganIds[$ki]['id'],
                'rating_pelayanan' => $rp,
                'rating_fasilitas' => $rf,
                'rating_informasi' => $ri,
                'komentar'         => $komentar,
                'saran'            => $saran,
                'tampilkan_publik' => $publik,
                'created_at'       => Carbon::parse($kunjunganIds[$ki]['tgl'])->addHours(rand(2, 24)),
                'updated_at'       => Carbon::parse($kunjunganIds[$ki]['tgl'])->addHours(rand(2, 24)),
            ]);
        }

        // ─────────────────────────────────────────────────
        // 5. Pengaturan Kalender (Overrides)
        // ─────────────────────────────────────────────────
        DB::table('pengaturan_kalender')->insert([
            [
                'tanggal'       => Carbon::today()->addDays(14)->format('Y-m-d'),
                'is_libur'      => true,
                'sesi_tersedia' => json_encode([]),
                'catatan'       => 'Libur Ujian Akhir Semester',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'tanggal'       => Carbon::today()->addDays(16)->format('Y-m-d'),
                'is_libur'      => false,
                'sesi_tersedia' => json_encode([$sesiIds[0] ?? 1]), // Hanya Sesi 1
                'catatan'       => 'Hanya menerima kunjungan Sesi 1',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        ]);

        $this->command->info('✅ DemoFullSeeder selesai:');
        $this->command->info('   → 10 sekolah & kontak');
        $this->command->info('   → 13 kunjungan (completed/approved/pending/rejected/cancelled)');
        $this->command->info('   → 7 presensi (6 check-in+out, 1 check-in saja)');
        $this->command->info('   → 6 survei kepuasan (5 publik, 1 private)');
        $this->command->info('   → 2 pengaturan kalender (1 libur, 1 terbatas)');
    }
}
