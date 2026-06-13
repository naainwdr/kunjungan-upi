<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoFullSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────
        // 0. Ambil referensi tempat & sesi yang sudah ada
        // ─────────────────────────────────────────────────
        $tempat = DB::table('tempat')->pluck('id', 'nama');
        $sesi   = DB::table('sesi')->pluck('id', 'nama');
        $admin  = DB::table('users')->where('role', 'admin')->value('id');

        $tempatIds = array_values($tempat->toArray());
        $sesiIds   = array_values($sesi->toArray());

        if (empty($tempatIds) || empty($sesiIds)) {
            $this->command->error('Tabel tempat atau sesi kosong. Harap run TempathSesiSeeder terlebih dahulu.');
            return;
        }

        // ─────────────────────────────────────────────────
        // 1. Sekolah + Kontak (Buat 20 Sekolah Dummy)
        // ─────────────────────────────────────────────────
        $sekolahData = [
            ['SMAN 1 Bandung', 'Drs. Ahmad Fauzi, M.Pd'],
            ['SMAN 3 Cimahi', 'Hj. Sri Wahyuni, S.Pd'],
            ['SMK Negeri 2 Garut', 'Budi Santoso, M.T'],
            ['SMAN 5 Bekasi', 'Rina Kusumawati, S.Pd'],
            ['MAN 1 Sumedang', 'Ustad Jamaludin, M.Ag'],
            ['SMAN 7 Bogor', 'Dra. Nani Suryani'],
            ['SMP Negeri 1 Subang', 'Hendra Gunawan, S.Pd'],
            ['SMK Pasundan 1 Bandung', 'Asep Hidayat, M.Pd'],
            ['SMAN 2 Purwakarta', 'Dewi Anggraeni, S.Pd'],
            ['SMA Muhammadiyah Tasik', 'M. Rizki Firdaus, S.Pd'],
            ['SMAN 1 Depok', 'Dr. Agus Suherman'],
            ['SMKN 1 Sukabumi', 'Lilis Karlina, M.Si'],
            ['SMPN 5 Cirebon', 'Dedi Mulyadi, S.Pd'],
            ['SMAN 1 Indramayu', 'Tuti Herawati, M.Pd'],
            ['SMAN 3 Karawang', 'Dr. Eko Prasetyo'],
            ['MAN 2 Majalengka', 'H. Zainal Abidin'],
            ['SMK BPK Penabur', 'Fransisca Wahyu'],
            ['SMA Taruna Nusantara', 'Kolonel Inf. Surya'],
            ['SMKN 4 Bandung', 'Tatang Suratang'],
            ['SMAN 20 Bandung', 'Nining Ningsih, M.Pd'],
        ];

        $sekolahIds = [];
        $kontakIds  = [];

        foreach ($sekolahData as $idx => [$nama, $picNama]) {
            // Nomor telepon acak yang realistis
            $telpSekolah = '022-' . rand(1000000, 9999999);
            $telpPic = '0812' . rand(10000000, 99999999);
            // NPSN acak 8 digit unik
            $npsn = '202' . str_pad($idx, 5, '0', STR_PAD_LEFT);

            $sid = DB::table('sekolah')->insertGetId([
                'nama'       => $nama,
                'npsn'       => $npsn,
                'alamat'     => 'Jl. Raya Pendidikan No. ' . rand(1, 100) . ', Jawa Barat',
                'email'      => 'ninawd27@gmail.com', // SEMUA EMAIL MENGGUNAKAN NINAWD27 SESUAI REQUEST
                'telepon'    => $telpSekolah,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $kid = DB::table('kontak_sekolah')->insertGetId([
                'sekolah_id' => $sid,
                'nama'       => $picNama,
                'telepon'    => $telpPic,
                'email'      => 'ninawd27@gmail.com', // SEMUA EMAIL PIC MENGGUNAKAN NINAWD27 SESUAI REQUEST
                'jabatan'    => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sekolahIds[] = $sid;
            $kontakIds[]  = $kid;
        }

        // ─────────────────────────────────────────────────
        // Helper: Cari Tanggal Valid (Senin-Kamis)
        // ─────────────────────────────────────────────────
        // Menghindari hari libur (Jumat, Sabtu, Minggu)
        $getValidWeekday = function($minDaysOffset, $maxDaysOffset) {
            do {
                $offset = rand($minDaysOffset, $maxDaysOffset);
                $date = Carbon::today()->addDays($offset);
            } while (!in_array($date->dayOfWeek, [1, 2, 3, 4])); // 1=Senin ... 4=Kamis
            return $date->format('Y-m-d');
        };

        // ─────────────────────────────────────────────────
        // 2. Kunjungan Massal (20 tiap status = 100 Kunjungan)
        // ─────────────────────────────────────────────────
        $kunjunganIds = [];
        $counter = 1;

        // Definisi Status dan Rentang Waktu
        // 'completed' dan 'cancelled' -> Masa Lalu (Offset negatif)
        // 'pending' dan 'approved' dan 'rejected' -> Masa Depan / Hari ini (Offset positif / 0)
        
        $statusConfigs = [
            'completed' => ['count' => 20, 'min_day' => -60, 'max_day' => -1],
            'approved'  => ['count' => 20, 'min_day' => 1,   'max_day' => 45],
            'pending'   => ['count' => 20, 'min_day' => 5,   'max_day' => 60],
            'rejected'  => ['count' => 20, 'min_day' => -10, 'max_day' => 20],
            'cancelled' => ['count' => 20, 'min_day' => -30, 'max_day' => 10],
        ];

        foreach ($statusConfigs as $status => $config) {
            for ($i = 0; $i < $config['count']; $i++) {
                $tgl = $getValidWeekday($config['min_day'], $config['max_day']);
                
                $si = array_rand($sekolahIds); // Pilih sekolah acak
                $ti = array_rand($tempatIds);  // Pilih tempat acak
                $sei = array_rand($sesiIds);   // Pilih sesi acak
                
                $prefix  = 'UPI-' . Carbon::parse($tgl)->format('Ymd') . '-';
                $noReg   = $prefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
                $counter++;

                $id = DB::table('kunjungan')->insertGetId([
                    'nomor_registrasi'  => $noReg,
                    'sekolah_id'        => $sekolahIds[$si],
                    'kontak_id'         => $kontakIds[$si],
                    'tempat_id'         => $tempatIds[$ti],
                    'sesi_id'           => $sesiIds[$sei],
                    'tanggal_kunjungan' => $tgl,
                    'jumlah_peserta'    => rand(20, 250),
                    'jumlah_kepsek'     => rand(0, 1),
                    'jumlah_guru'       => rand(2, 15),
                    'jumlah_tendik'     => rand(0, 5),
                    'file_surat'        => null,
                    'status'            => $status,
                    'catatan_admin'     => $status === 'rejected'
                        ? 'Kuota tempat sudah penuh pada tanggal tersebut. Silakan pilih jadwal lain.'
                        : ($status === 'approved' ? 'Permohonan disetujui. Harap tiba 30 menit sebelum sesi dimulai.' : null),
                    'email_notified_at' => in_array($status, ['approved','rejected']) ? now()->subDays(abs(rand(1, 5))) : null,
                    'created_at'        => Carbon::parse($tgl)->subDays(rand(15, 30)),
                    'updated_at'        => Carbon::parse($tgl)->subDays(rand(5, 14)),
                ]);

                $kunjunganIds[] = ['id' => $id, 'status' => $status, 'tgl' => $tgl, 'si' => $si, 'sei' => $sei];

                // Log status history
                if ($status !== 'pending') {
                    DB::table('kunjungan_log')->insert([
                        'kunjungan_id'   => $id,
                        'status_sebelum' => 'pending',
                        'status_sesudah' => $status,
                        'catatan'        => "Status diubah oleh admin (Simulasi Massal)",
                        'changed_by'     => $admin,
                        'created_at'     => Carbon::parse($tgl)->subDays(rand(5, 14)),
                    ]);
                }
            }
        }

        // ─────────────────────────────────────────────────
        // 3. Presensi — HANYA untuk kunjungan yang 'completed'
        // ─────────────────────────────────────────────────
        foreach ($kunjunganIds as $k) {
            if ($k['status'] === 'completed') {
                $tgl = $k['tgl'];
                
                // Waktu masuk acak antara 08:00 - 09:30
                $masuk = sprintf('%02d:%02d:00', rand(8, 9), rand(0, 59));
                // Waktu keluar acak antara 11:30 - 15:00
                $keluar = sprintf('%02d:%02d:00', rand(11, 14), rand(0, 59));

                DB::table('kunjungan_presensi')->insert([
                    'kunjungan_id'      => $k['id'],
                    'waktu_masuk'       => Carbon::parse($tgl . ' ' . $masuk),
                    'waktu_keluar'      => Carbon::parse($tgl . ' ' . $keluar),
                    'petugas_masuk_id'  => $admin,
                    'petugas_keluar_id' => $admin,
                    'catatan'           => 'Presensi massal',
                    'created_at'        => Carbon::parse($tgl . ' ' . $masuk),
                    'updated_at'        => Carbon::parse($tgl . ' ' . $keluar),
                ]);
            }
        }

        // ─────────────────────────────────────────────────
        // 4. Survei Kepuasan — HANYA untuk kunjungan 'completed'
        // ─────────────────────────────────────────────────
        $komentarPool = [
            'Sangat memuaskan, luar biasa!',
            'Anak-anak senang sekali bisa keliling UPI.',
            'Fasilitas kampus sangat megah dan lengkap.',
            'Pelayanan petugas informatif namun parkiran sempit.',
            'Toilet di beberapa gedung kotor, harap diperbaiki.',
            'Materi sosialisasi SNBP sangat bermanfaat untuk kelas 12.',
            'Terima kasih KKIPP, kami akan jadwalkan lagi tahun depan.',
            'Kami disambut dengan sangat ramah oleh kakak mahasiswa.',
        ];

        foreach ($kunjunganIds as $k) {
            if ($k['status'] === 'completed') {
                // 80% kemungkinan mengisi survei
                if (rand(1, 100) <= 80) {
                    $rp = rand(3, 5); // Rating Pelayanan
                    $rf = rand(2, 5); // Rating Fasilitas
                    $ri = rand(4, 5); // Rating Informasi

                    DB::table('survei_kepuasan')->insert([
                        'kunjungan_id'     => $k['id'],
                        'rating_pelayanan' => $rp,
                        'rating_fasilitas' => $rf,
                        'rating_informasi' => $ri,
                        'komentar'         => $komentarPool[array_rand($komentarPool)],
                        'saran'            => rand(1, 10) > 5 ? 'Perbanyak waktu keliling kampus.' : null,
                        'tampilkan_publik' => rand(1, 10) > 2, // 80% ditampilkan
                        'created_at'       => Carbon::parse($k['tgl'])->addHours(rand(2, 48)),
                        'updated_at'       => Carbon::parse($k['tgl'])->addHours(rand(2, 48)),
                    ]);
                }
            }
        }

        // ─────────────────────────────────────────────────
        // 5. Tambahkan 1 Data Demo Khusus HARI INI (Approved)
        //    (Supaya ada yang bisa di-scan check-in/out saat presentasi)
        // ─────────────────────────────────────────────────
        // Pastikan hari ini Senin-Kamis
        $hariIni = Carbon::today();
        if (in_array($hariIni->dayOfWeek, [1, 2, 3, 4])) {
            $si = array_rand($sekolahIds);
            $idHariIni = DB::table('kunjungan')->insertGetId([
                'nomor_registrasi'  => 'UPI-' . $hariIni->format('Ymd') . '-9999',
                'sekolah_id'        => $sekolahIds[$si],
                'kontak_id'         => $kontakIds[$si],
                'tempat_id'         => $tempatIds[0],
                'sesi_id'           => $sesiIds[0],
                'tanggal_kunjungan' => $hariIni->format('Y-m-d'),
                'jumlah_peserta'    => 50,
                'status'            => 'approved',
                'email_notified_at' => now(),
                'created_at'        => now()->subDays(15),
                'updated_at'        => now()->subDays(2),
            ]);
            $this->command->info('✅ Kunjungan spesial untuk Scanner Demo dibuat! No. Reg: UPI-' . $hariIni->format('Ymd') . '-9999');
        } else {
             $this->command->warn('⚠️ Hari ini adalah hari Libur/Jumat-Minggu. Data demo scanner tidak dibuat karena menyalahi aturan kalender.');
        }

        $this->command->info('✅ DemoFullSeeder MASIF selesai:');
        $this->command->info('   → 20 sekolah dummy dibuat.');
        $this->command->info('   → 100 kunjungan dibuat (20 per status).');
        $this->command->info('   → SEMUA email sekolah & PIC menggunakan ninawd27@gmail.com.');
        $this->command->info('   → SEMUA tanggal dijamin jatuh pada hari Senin-Kamis (Tidak ada hari libur).');
        $this->command->info('   → Presensi dan Survei otomatis di-generate untuk data Completed.');
    }
}
