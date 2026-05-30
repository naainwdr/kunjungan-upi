<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TempathSesiSeeder extends Seeder
{
    public function run(): void
    {
        // Tempat (Venue)
        DB::table('tempat')->insert([
            ['nama' => 'Gedung UC Lt. 1',   'kapasitas' => 100, 'deskripsi' => 'University Center Lantai 1', 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Auditorium FPMIPA', 'kapasitas' => 400, 'deskripsi' => 'Auditorium Fakultas FPMIPA',  'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Auditorium FPEB',   'kapasitas' => 300, 'deskripsi' => 'Auditorium Fakultas FPEB',    'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Amphiteater UPI',   'kapasitas' => 300, 'deskripsi' => 'Area Amphiteater Outdoor UPI','aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Aula PKM Lt. 2',    'kapasitas' => 200, 'deskripsi' => 'Aula Pusat Kegiatan Mahasiswa Lantai 2', 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Sesi
        DB::table('sesi')->insert([
            ['nama' => 'Sesi 1', 'jam_mulai' => '09:00:00', 'jam_selesai' => '11:00:00', 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Sesi 2', 'jam_mulai' => '13:00:00', 'jam_selesai' => '15:00:00', 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
