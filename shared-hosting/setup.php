<?php

/**
 * ============================================================
 * SETUP.PHP — Web-based Migration Runner untuk Shared Hosting
 * ============================================================
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke htdocs/setup.php
 * 2. Buka browser: https://domain-anda.com/setup.php
 * 3. Ikuti instruksi yang muncul
 * 4. !! WAJIB HAPUS FILE INI SETELAH SELESAI !!
 * 
 * ============================================================
 */

// ─── Paksa tampilan error untuk debugging ───────────────────
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ─── Keamanan minimal: wajib konfirmasi ─────────────────────
$confirmed = isset($_GET['run']) && $_GET['run'] === 'yes';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Reservasi Kunjungan UPI</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: monospace; background: #0d1117; color: #e6edf3; padding: 2rem; }
        .card { max-width: 800px; margin: 0 auto; background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 2rem; }
        h1 { color: #C8102E; margin-bottom: 0.5rem; }
        p { color: #8b949e; margin-bottom: 1rem; }
        .warning { background: #3d1c1c; border: 1px solid #C8102E; border-radius: 6px; padding: 1rem; margin: 1rem 0; color: #ff7b72; }
        .btn { display: inline-block; background: #C8102E; color: white; padding: 0.75rem 2rem; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; margin-top: 1rem; }
        .btn:hover { background: #a50e26; }
        pre { background: #0d1117; border: 1px solid #30363d; border-radius: 6px; padding: 1rem; overflow-x: auto; white-space: pre-wrap; font-size: 0.85rem; margin-top: 1rem; line-height: 1.6; }
        .success { color: #3fb950; }
        .error { color: #ff7b72; }
        .info { color: #58a6ff; }
        hr { border-color: #30363d; margin: 1.5rem 0; }
    </style>
</head>
<body>
<div class="card">
    <h1>⚙️ Setup Reservasi Kunjungan UPI</h1>
    <p>Script ini akan membuat tabel database dan akun admin pertama.</p>

    <div class="warning">
        ⚠️ <strong>PERINGATAN KEAMANAN:</strong> Hapus file <code>setup.php</code> 
        segera setelah setup selesai!
    </div>

    <hr>

<?php if (!$confirmed): ?>

    <p>Klik tombol di bawah untuk menjalankan migrasi database:</p>
    <a href="?run=yes" class="btn">▶ Jalankan Setup Database</a>

<?php else: ?>

    <pre><?php
    // ─── Boot Laravel ────────────────────────────────────────
    echo "<span class='info'>Memuat Laravel...</span>\n";

    try {
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->bind('path.public', fn() => __DIR__);

        // Bootstrap console kernel
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        echo "<span class='success'>✓ Laravel berhasil dimuat</span>\n\n";

        // ─── Migrasi ──────────────────────────────────────────
        echo "<span class='info'>=== MENJALANKAN MIGRASI ===</span>\n";
        $kernel->call('migrate', ['--force' => true]);
        echo htmlspecialchars($kernel->output());
        echo "<span class='success'>✓ Migrasi selesai</span>\n\n";

        // ─── Seed Admin ───────────────────────────────────────
        echo "<span class='info'>=== MEMBUAT AKUN ADMIN ===</span>\n";
        $kernel->call('db:seed', ['--class' => 'AdminUserSeeder', '--force' => true]);
        echo htmlspecialchars($kernel->output());
        echo "<span class='success'>✓ Akun admin berhasil dibuat</span>\n\n";

        // ─── Config cache ─────────────────────────────────────
        echo "<span class='info'>=== CACHING KONFIGURASI ===</span>\n";
        $kernel->call('config:cache');
        echo "<span class='success'>✓ Config cache selesai</span>\n\n";

        echo "<span class='success' style='font-size:1.1em;'>
╔══════════════════════════════════════╗
║  ✅ SETUP SELESAI!                   ║
║                                      ║
║  Login admin:                        ║
║  Email   : ninawd27@upi.edu          ║
║  Password: (sesuai .env)             ║
║                                      ║
║  ⚠️  HAPUS FILE setup.php SEKARANG!  ║
╚══════════════════════════════════════╝
</span>";

    } catch (\Exception $e) {
        echo "<span class='error'>✗ ERROR: " . htmlspecialchars($e->getMessage()) . "</span>\n\n";
        echo "<span class='error'>Pastikan file .env sudah dikonfigurasi dengan benar!</span>\n";
        echo "\nStack trace:\n" . htmlspecialchars($e->getTraceAsString());
    }
    ?></pre>

<?php endif; ?>

</div>
</body>
</html>
