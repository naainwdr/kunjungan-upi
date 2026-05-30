<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_kalender', function (Blueprint $label) {
            $label->id();
            $label->date('tanggal')->unique();
            $label->boolean('is_libur')->default(false);
            $label->json('sesi_tersedia')->nullable(); // Array of Sesi IDs: [1, 2]
            $label->text('catatan')->nullable();
            $label->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_kalender');
    }
};
