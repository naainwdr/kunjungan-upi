<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoKunjunganEvaluasiSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Kunjungan::updateOrCreate([
            'nomor_registrasi' => 'UPI-DEMO-0001',
        ], [
            'nama_sekolah'      => 'SD Demo UPI',
            'npsn'              => '99999999',
            'alamat'            => 'Jl. Demo No. 1, Bandung',
            'nama_pic'          => 'Ibu Sari',
            'jenis_pic'         => 'guru',
            'email_pic'         => 'pic-demo@example.com',
            'telepon_pic'       => '081234567890',
            'email'             => 'admin-demo@example.com',
            'telepon'           => '0221234567',
            'tanggal_kunjungan' => Carbon::now()->subDay()->toDateString(),
            'jam_mulai'         => '09:00',
            'jam_selesai'       => '12:00',
            'jumlah_peserta'    => 15,
            'jumlah_kepsek'     => 1,
            'jumlah_guru'       => 10,
            'jumlah_tendik'     => 4,
            'file_surat'        => null,
            'status'            => 'approved',
            'catatan_admin'     => 'Demo: kunjungan sudah disetujui.',
            'email_notified_at' => Carbon::now(),
            'created_at'        => Carbon::now()->subDays(5),
            'updated_at'        => Carbon::now()->subDay(),
        ]);

        Kunjungan::updateOrCreate([
            'nomor_registrasi' => 'UPI-DEMO-0002',
        ], [
            'nama_sekolah'      => 'SMP Demo UPI',
            'npsn'              => '88888888',
            'alamat'            => 'Jl. Demo No. 2, Bandung',
            'nama_pic'          => 'Bapak Joko',
            'jenis_pic'         => 'kepsek',
            'email_pic'         => 'pic-demo-2@example.com',
            'telepon_pic'       => '081298765432',
            'email'             => 'admin-demo-2@example.com',
            'telepon'           => '0227654321',
            'tanggal_kunjungan' => Carbon::now()->subDays(2)->toDateString(),
            'jam_mulai'         => '10:00',
            'jam_selesai'       => '13:00',
            'jumlah_peserta'    => 20,
            'jumlah_kepsek'     => 1,
            'jumlah_guru'       => 12,
            'jumlah_tendik'     => 7,
            'file_surat'        => null,
            'status'            => 'completed',
            'catatan_admin'     => 'Demo: kunjungan sudah selesai dan evaluasi dikirim.',
            'email_notified_at' => Carbon::now(),
            'created_at'        => Carbon::now()->subDays(6),
            'updated_at'        => Carbon::now(),
        ]);
    }
}
