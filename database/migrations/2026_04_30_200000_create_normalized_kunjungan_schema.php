<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SEKOLAH
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('npsn', 20)->unique();
            $table->text('alamat');
            $table->string('email');
            $table->string('telepon', 20);
            $table->timestamps();
        });

        // 2. KONTAK SEKOLAH (PIC)
        Schema::create('kontak_sekolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolah')->cascadeOnDelete();
            $table->string('nama');
            $table->enum('jabatan', ['kepsek', 'guru', 'tendik'])->default('kepsek');
            $table->string('email');
            $table->string('telepon', 20);
            $table->timestamps();
        });

        // 3. TEMPAT (Venue)
        Schema::create('tempat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedInteger('kapasitas');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 4. SESI
        Schema::create('sesi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);       // "Sesi 1"
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 5. KUNJUNGAN (normalized)
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi', 25)->unique();
            $table->foreignId('sekolah_id')->constrained('sekolah');
            $table->foreignId('kontak_id')->constrained('kontak_sekolah');
            $table->foreignId('tempat_id')->constrained('tempat');
            $table->foreignId('sesi_id')->constrained('sesi');
            $table->date('tanggal_kunjungan');
            $table->unsignedInteger('jumlah_peserta');
            $table->unsignedSmallInteger('jumlah_kepsek')->default(0);
            $table->unsignedSmallInteger('jumlah_guru')->default(0);
            $table->unsignedSmallInteger('jumlah_tendik')->default(0);
            $table->string('file_surat')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('email_notified_at')->nullable();
            $table->timestamps();
        });

        // 6. KUNJUNGAN LOG (audit trail)
        Schema::create('kunjungan_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungan')->cascadeOnDelete();
            $table->string('status_sebelum', 20)->nullable();
            $table->string('status_sesudah', 20);
            $table->text('catatan')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_log');
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('sesi');
        Schema::dropIfExists('tempat');
        Schema::dropIfExists('kontak_sekolah');
        Schema::dropIfExists('sekolah');
    }
};
