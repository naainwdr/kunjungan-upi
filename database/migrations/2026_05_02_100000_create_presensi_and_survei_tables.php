<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Presensi (check-in / check-out)
        Schema::create('kunjungan_presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->unique()->constrained('kunjungan')->cascadeOnDelete();
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamp('waktu_keluar')->nullable();
            $table->foreignId('petugas_masuk_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('petugas_keluar_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Survei Kepuasan
        Schema::create('survei_kepuasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->unique()->constrained('kunjungan')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating_pelayanan'); // 1-5
            $table->unsignedTinyInteger('rating_fasilitas'); // 1-5
            $table->unsignedTinyInteger('rating_informasi'); // 1-5
            $table->text('komentar')->nullable();
            $table->text('saran')->nullable();
            $table->boolean('tampilkan_publik')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survei_kepuasan');
        Schema::dropIfExists('kunjungan_presensi');
    }
};
