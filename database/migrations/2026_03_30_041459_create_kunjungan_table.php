<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi', 20)->unique();
            $table->string('nama_sekolah');
            $table->string('npsn', 20);
            $table->text('alamat');
            $table->string('nama_pic'); // Nama penanggungjawab / kepala sekolah
            $table->string('email');
            $table->string('telepon', 20);
            $table->date('tanggal_kunjungan');
            $table->integer('jumlah_peserta');
            $table->string('file_surat')->nullable(); // path file surat permohonan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('email_notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
