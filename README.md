# Kunjungan UPI - Sistem Permohonan Kunjungan Sekolah

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

## 📌 Deskripsi Project
**Kunjungan UPI** adalah platform digital untuk memudahkan pendaftaran, penjadwalan, dan verifikasi permohonan kunjungan sekolah ke Universitas Pendidikan Indonesia (UPI). 

Sistem ini membantu sekolah melihat jadwal kosong secara *real-time*, mengajukan permohonan dengan mengunggah surat dinas (otomatis terunggah ke *Cloudinary*), hingga membatalkan permohonan secara mandiri (maksimal H-2). Di sisi lain, Humas UPI dapat mengelola seluruh pengajuan melalui panel admin yang dilengkapi statistik interaktif.

## ✨ Fitur Utama Tersedia

### 🏫 Portal Publik (Untuk Sekolah)
- **Desain Modern**: Antarmuka premium dengan warna identitas institusi.
- **Kalender Cerdas (Anti-Bentrok)**: Sistem otomatis memblokir jadwal kunjungan di hari libur, akhir pekan, H-7, atau pada jam yang sudah dipakai oleh sekolah lain di tanggal yang sama.
- **Integrasi Cloudinary**: Bukti surat permohonan langsung tersimpan dengan aman di penyimpanan awan Cloudinary.
- **Cek Status & Pembatalan Mandiri**: Pantau pengajuan dengan nomor registrasi. Terdapat fitur pembatalan mandiri yang hanya aktif hingga batas maksimal H-2 keberangkatan.

### 👨‍💻 Portal Admin (Untuk Humas UPI)
- **Dashboard Statistik**: Memantau jumlah permohonan (Menunggu, Disetujui, Ditolak, Dibatalkan Pemohon).
- **Detail Kunjungan Lengkap**: Memverifikasi dokumen surat, kontak penanggungjawab, jumlah rombongan, dan menentukan *Jam Kunjungan*.
- **Tolak / Setujui Terintegrasi Email**: Opsi memberikan catatan khusus saat menyetujui atau menolak permohonan. *(Mendukung notifikasi via Mailtrap / Gmail SMTP)*.

---

## 🛠️ Teknologi yang Digunakan
- **Backend**: Laravel 12 (PHP)
- **Frontend**: Blade Templating & CSS Tailwind (Via CDN) + AlpineJS Modal
- **Database Utama**: PostgreSQL (atau MySQL)
- **Cloud Storage**: Cloudinary (untuk Surat PDF/JPG)

---

## 🚀 Panduan Instalasi (Lokal)

Ikuti instruksi ini untuk menjalankan program di komputer lokal Anda:

### 1. Kebutuhan Aplikasi (Prerequisites)
Pastikan di laptop Anda sudah terinstal:
- **PHP** (minimal versi 8.2) & **Composer**
- **pgAdmin / PostgreSQL** (atau XAMPP jika pakai MySQL)

### 2. Konfigurasi
1. Buka folder *project* ini melalui Terminal / Command Prompt.
2. Gandakan file `.env.example` lalu ubah namanya menjadi `.env`
3. Sesuaikan isi file `.env` terutama pada bagian database:
   ```env
   # Contoh jika menggunakan PostgreSQL lokal
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=kunjungan_upi
   DB_USERNAME=postgres
   DB_PASSWORD=postgres
   ```
4. Jalankan perintah instalasi dependensi (wajib jika baru mengunduh):
   ```bash
   composer install
   ```
5. Buat kerangka database percobaan (`seed`). Perintah ini sekaligus akan membuat akun admin *default*:
   ```bash
   php artisan migrate:fresh --seed
   ```

### 3. Cara Menjalankan
Mulai jalankan server lokal aplikasi Laravel dengan mengetik perintah ini di Terminal:
```bash
php artisan serve
```

---

## 💡 Panduan Penggunaan Aplikasi (Cara Test)

### 1. Test Sebagai Sekolah (Pemohon)
1. Buka browser: **http://localhost:8000**
2. Klik tombol navigasi **"Permohonan"**.
3. Klik salah satu tanggal di Kalender yang berwarna putih (Hari aktif). *Catatan: Tanggal yang kurang dari 7 hari (H-7) ke depan akan otomatis berwarna kuning dan dikunci.*
4. Isi kelengkapan administrasi pada formulir. Pastikan koneksi internet aktif karena sistem akan mengunggah file surat ke *Cloudinary*.
5. Selesai! Catat **Nomor Registrasi** yang muncul di layar (Contoh: `UPI-20261011-0001`).

### 2. Test Pembatalan Mandiri
1. Buka menu navigasi **"Cek Status"** di *header*.
2. Masukkan **Nomor Registrasi** atau Alamat Email yang tadi dipakai.
3. Anda akan melihat tombol **"Batalkan Permohonan"** warna merah (Hanya muncul apabila jadwal kunjungan masih lebih dari 2 hari ke depan).

### 3. Test Sebagai Admin Humas
1. Buka URL: **http://localhost:8000/admin/login**
2. Masukkan akun *default* (hasil dari proses `--seed` tadi):
   - **Email:** `ninawd27@upi.edu`
   - **Password:** `admin123`
3. Anda akan masuk ke **Panel Admin**.
4. Klik **Detail** pada tabel data permohonan yang baru saja Anda buat.
5. Cobalah beri catatan dan tekan **Setujui**. Secara otomatis, pada hari tersebut jumlah jam yang terpotong tidak akan bisa diklaim oleh sekolah lain!

---

## 📦 Deployment
Untuk melakukan penempatan *online* (*hosting* luring), wajib merubah parameter di `.env`:
1. Ubah `APP_ENV=production` dan `APP_DEBUG=false`
2. Atur kredensial email `MAIL_MAILER` untuk Notifikasi SMTP.
3. Gunakan Cloudinary sepenuhnya.

*Dikembangkan untuk Program Magang - Universitas Pendidikan Indonesia.*
