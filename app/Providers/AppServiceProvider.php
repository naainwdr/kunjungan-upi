<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Proteksi Anti-Tamper untuk Copyright Pengembang
        // Mengecek apakah string nama pengembang dan pengarah masih ada di file layout
        $layoutPath = resource_path('views/layouts/app.blade.php');
        if (file_exists($layoutPath)) {
            $content = file_get_contents($layoutPath);
            
            // Menggunakan base64 agar nama tidak terdeteksi oleh pencarian (Ctrl+Shift+F) yang dilakukan pemalsu
            // TmluYSBXdWxhbmRhcmk= (Nina Wulandari)
            // VmlkaSBTdWttYXlhZGk= (Vidi Sukmayadi)
            $dev = base64_decode('TmluYSBXdWxhbmRhcmk=');
            $dir = base64_decode('VmlkaSBTdWttYXlhZGk=');
            
            if (strpos($content, $dev) === false || strpos($content, $dir) === false) {
                // Jika nama dihapus, aplikasi akan mogok dan menampilkan error ini:
                abort(500, 'Application Error: Integritas sistem rusak. Informasi Pengembang/Hak Cipta tidak boleh dimodifikasi atau dihapus.');
            }
        }
    }
}
