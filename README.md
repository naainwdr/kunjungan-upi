# Kunjungan UPI - Sistem Reservasi Kunjungan Sekolah

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

## 📌 Deskripsi Project
**Kunjungan UPI** adalah platform sistem reservasi kunjungan sekolah ke Universitas Pendidikan Indonesia (UPI) yang modern, efisien, dan transparan. Dibangun menggunakan teknologi terbaru, aplikasi ini mendigitalisasi proses pendaftaran kunjungan yang sebelumnya manual menjadi sistem pencatatan otomatis yang mudah diakses oleh pihak sekolah maupun admin internal UPI.

## ✨ Fitur Utama

### 🏫 Portal Publik
- **Landing Page Modern**: Halaman beranda yang informatif dan responsif.
- **Kalender Kunjungan**: Lihat ketersediaan kuota dan jadwal kunjungan secara real-time.
- **Reservasi Online**: Alur pendaftaran kunjungan dengan validasi data sekolah yang lengkap.
- **Cek Status Mandiri**: Sekolah dapat memantau progres pengajuan (Pending, Approved, Rejected) melalui fitur cek status.

### 👨‍💻 Portal Admin
- **Dashboard Management**: Dashboard ringkasan untuk memantau trafik kunjungan.
- **Verifikasi Kunjungan**: Sistem manajemen untuk menyetujui atau menolak permohonan kunjungan.
- **Detail Kunjungan**: Melihat dokumen dan informasi lengkap dari sekolah yang mendaftar.
- **Keamanan Data**: Dilengkapi sistem autentikasi untuk akses manajemen.

## 🛠️ Tech Stack
- **Backend**: [Laravel 12 (Stable)](https://laravel.com)
- **PHP**: PHP 8.2 or higher
- **Frontend**: [Tailwind CSS 4.0](https://tailwindcss.com) (Modern CSS Engine)
- **JavaScript Engine**: Vite 7
- **Database**: MySQL / PostgreSQL / SQLite
- **Media Storage**: [Cloudinary Labs](https://cloudinary-laravel.com) for image & document management

## 🚀 Instalasi Lokal

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Server

### Langkah Instalasi
1. **Clone Repositori**
   ```bash
   git clone [URL-REPOSI-INI]
   cd kunjungan-upi
   ```

2. **Setup Server-side**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database & Migration**
   Konfigurasi database di `.env`, lalu jalankan:
   ```bash
   php artisan migrate
   ```

4. **Setup Client-side**
   ```bash
   npm install
   ```

5. **Jalankan Aplikasi**
   Gunakan script custom yang tersedia di `composer.json` untuk menjalankan server dan vite sekaligus:
   ```bash
   npm run dev
   ```

## 📦 Deployment
Aplikasi ini sudah dipersiapkan untuk deployment menggunakan:
- **Docker**: Dilengkapi dengan `Dockerfile` dan konfigurasi Docker.
- **Platform Ready**: Konfigurasi `railway.json` dan `render.yaml` tersedia untuk deployment cepat di Railway atau Render.

## 📄 Lisensi
Project ini bersifat open-source dan berada di bawah lisensi [MIT](LICENSE).

---
*Dikembangkan untuk Program Magang - Universitas Pendidikan Indonesia.*
