<div align="center">

# 🎓 Sistem Permohonan Kunjungan Sekolah
### KKIPP — Kantor Komunikasi, Informasi dan Pelayanan Publik
### Universitas Pendidikan Indonesia

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/Lisensi-Instansi-800000?style=flat-square)](LICENSE)

</div>

---

## 📋 Daftar Isi

1. [Tentang Aplikasi](#tentang-aplikasi)
2. [Fitur Utama](#fitur-utama)
3. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
4. [Struktur Direktori Proyek](#struktur-direktori-proyek)
5. [Arsitektur Kode](#arsitektur-kode)
6. [Instalasi & Konfigurasi](#instalasi--konfigurasi)
7. [Panduan Penggunaan](#panduan-penggunaan)
8. [Tim Pengembang](#tim-pengembang)

---

## Tentang Aplikasi

Sistem Permohonan Kunjungan Sekolah adalah aplikasi web berbasis Laravel yang dikembangkan untuk **KKIPP (Kantor Komunikasi, Informasi dan Pelayanan Publik) Universitas Pendidikan Indonesia**. Aplikasi ini menyederhanakan proses administrasi permohonan kunjungan dari sekolah-sekolah ke UPI yang sebelumnya dilakukan secara manual melalui surat atau datang langsung.

### Tujuan

- Memudahkan sekolah mengajukan permohonan kunjungan secara online, kapan saja dan dari mana saja
- Memungkinkan admin KKIPP mengelola, menyetujui, atau menolak permohonan secara digital
- Mengurangi beban administrasi manual dan mempercepat proses persetujuan
- Menyediakan sistem presensi digital berbasis QR code pada hari kunjungan
- Mengumpulkan umpan balik kunjungan melalui survei kepuasan otomatis

---

## Fitur Utama

### 👥 Untuk Pemohon (Sekolah)
| Fitur | Deskripsi |
|---|---|
| **Form Reservasi Online** | Pengajuan permohonan kunjungan dengan upload surat resmi |
| **Kalender Kunjungan** | Melihat jadwal kunjungan yang sudah terisi dan hari layanan |
| **Cek Status** | Melacak status permohonan via nomor registrasi atau email |
| **Tiket Digital** | QR code unik untuk presensi pada hari kunjungan |
| **Pembatalan Online** | Pembatalan mandiri dengan batas waktu H-5 |
| **Survei Kepuasan** | Form evaluasi dikirim otomatis via email setelah kunjungan |

### 🔐 Untuk Admin (KKIPP)
| Fitur | Deskripsi |
|---|---|
| **Dashboard Manajemen** | Daftar semua permohonan dengan filter, pencarian, dan sorting |
| **Approve / Reject** | Menyetujui atau menolak permohonan dengan catatan resmi |
| **Complete** | Menandai kunjungan selesai dengan auto-checkout |
| **Manajemen Tempat** | CRUD lokasi kunjungan beserta kapasitas |
| **Manajemen Sesi** | CRUD slot waktu kunjungan |
| **Pengaturan Kalender** | Override hari libur khusus atau pembatasan sesi per tanggal |
| **Manajemen Survei** | Melihat data survei kepuasan dan mengatur tampilan testimonial |
| **Statistik** | Top sekolah pemohon, jumlah per status, kunjungan terdekat |

### 📲 Untuk Petugas Presensi
| Fitur | Deskripsi |
|---|---|
| **Scanner QR** | Scan tiket digital untuk check-in dan check-out kunjungan |
| **Rekap Presensi** | Melihat riwayat presensi harian |

---

## Teknologi yang Digunakan

| Layer | Teknologi |
|---|---|
| **Backend Framework** | Laravel 12.x (PHP 8.2+) |
| **Database** | SQLite (development) / PostgreSQL (production) |
| **Frontend Styling** | Tailwind CSS (via CDN) |
| **Email** | Laravel Mailer (SMTP / Mailtrap) |
| **File Storage** | Laravel Local Storage (public disk) |
| **Authentication** | Laravel built-in Auth dengan role-based access |
| **QR Code** | Dibuat dari nomor registrasi unik (client-side) |
| **Deployment** | Railway / Render (production) |

---

## Struktur Direktori Proyek

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── KunjunganController.php          ← Halaman publik (reservasi, cek status, evaluasi)
│   │   ├── SurveiController.php             ← Form survei kepuasan publik
│   │   ├── Admin/
│   │   │   ├── AuthController.php           ← Login/logout admin
│   │   │   ├── AdminKunjunganController.php ← Dashboard, approve, reject, complete
│   │   │   ├── PresensiController.php       ← Scanner QR & check-in/out (Admin)
│   │   │   ├── AdminTempatController.php    ← CRUD tempat kunjungan
│   │   │   ├── AdminSesiController.php      ← CRUD sesi kunjungan
│   │   │   ├── AdminKalenderController.php  ← Override kalender
│   │   │   └── AdminSurveiController.php    ← Manajemen survei & testimonial
│   │   └── Petugas/
│   │       ├── PetugasAuthController.php    ← Login/logout petugas
│   │       └── PetugasPresensiController.php← Scanner QR & check-in/out (Petugas)
│   │
│   ├── Requests/                            ← Validasi input (Form Request)
│   │   ├── StoreKunjunganRequest.php
│   │   ├── CariStatusRequest.php
│   │   ├── SimpanEvaluasiRequest.php
│   │   ├── StoreSurveiRequest.php
│   │   └── Admin/
│   │       ├── ApproveKunjunganRequest.php
│   │       ├── RejectKunjunganRequest.php
│   │       ├── StoreTempatRequest.php
│   │       ├── StoreSesiRequest.php
│   │       └── StoreKalenderRequest.php
│   │
│   └── Middleware/
│       ├── EnsureIsAdmin.php                ← Guard role admin
│       └── EnsureIsPetugas.php              ← Guard role petugas
│
├── Services/                                ← Business Logic Layer
│   ├── KunjunganService.php
│   ├── KunjunganStatusService.php
│   ├── PresensiService.php
│   ├── SurveiService.php
│   └── AdminReferensiService.php
│
├── Models/                                  ← Eloquent Models & Relasi
│   ├── Kunjungan.php
│   ├── Sekolah.php
│   ├── KontakSekolah.php
│   ├── KunjunganPresensi.php
│   ├── KunjunganLog.php
│   ├── SurveiKepuasan.php
│   ├── Sesi.php
│   ├── Tempat.php
│   ├── PengaturanKalender.php
│   └── User.php
│
└── Mail/
    ├── StatusKunjunganMail.php              ← Email konfirmasi/notifikasi status
    ├── EvaluasiKunjunganMail.php            ← Email link survei kepuasan
    └── SurveiKunjunganMail.php

database/
├── migrations/                              ← Skema database bertahap
├── seeders/                                 ← Data awal sistem
└── ERD.md                                   ← Diagram Entity Relationship

resources/views/
├── layouts/
│   ├── app.blade.php                        ← Layout utama halaman publik
│   ├── admin.blade.php                      ← Layout panel admin
│   └── petugas.blade.php                    ← Layout panel petugas
├── public/                                  ← View halaman publik
└── admin/                                   ← View panel admin
```

---

## Arsitektur Kode

Proyek ini menggunakan **Service Pattern** dengan prinsip **Separation of Concerns**:

```
HTTP Request
    │
    ▼
┌─────────────────────┐
│   Form Request      │  ← Validasi format input
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│   Controller        │  ← Terima request, delegasikan, kembalikan response
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│   Service           │  ← Business logic, email, storage
└──────────┬──────────┘
           ▼
┌─────────────────────┐
│   Model / Eloquent  │  ← Query database & relasi
└─────────────────────┘
```

> Untuk dokumentasi arsitektur lengkap, lihat [README-ARSITEKTUR.md](README-ARSITEKTUR.md).

---

## Instalasi & Konfigurasi

### Prasyarat
- PHP 8.2+
- Composer
- Node.js & npm
- Database (SQLite untuk dev, PostgreSQL untuk production)

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone <repo-url>
cd <nama-folder>

# 2. Install dependensi PHP
composer install

# 3. Install dependensi frontend
npm install

# 4. Salin file konfigurasi
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di .env
# DB_CONNECTION=sqlite (untuk development)
# Atau isi DB_HOST, DB_DATABASE, dll. untuk PostgreSQL

# 7. Jalankan migrasi database
php artisan migrate

# 8. (Opsional) Jalankan seeder untuk data awal
php artisan db:seed

# 9. Buat symlink storage
php artisan storage:link

# 10. Jalankan development server
php artisan serve
npm run dev
```

### Konfigurasi Environment Penting

```env
# Email (gunakan Mailtrap untuk testing)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@upi.edu
MAIL_FROM_NAME="KKIPP UPI"

# Storage
FILESYSTEM_DISK=public

# App URL (sesuaikan dengan domain production)
APP_URL=https://your-domain.com
```

### Membuat Akun Admin & Petugas

```bash
php artisan tinker

# Buat akun Admin
\App\Models\User::create([
    'name'     => 'Admin KKIPP',
    'email'    => 'admin@upi.edu',
    'password' => bcrypt('password_kuat'),
    'role'     => 'admin',
]);

# Buat akun Petugas Presensi
\App\Models\User::create([
    'name'     => 'Petugas Presensi',
    'email'    => 'petugas@upi.edu',
    'password' => bcrypt('password_kuat'),
    'role'     => 'petugas',
]);
```

---

## Panduan Penggunaan

### Alur Lengkap Permohonan Kunjungan

```
1. Sekolah membuka situs dan memilih tanggal di Kalender
         │
         ▼
2. Mengisi Form Reservasi (data sekolah, PIC, peserta, upload surat)
         │
         ▼
3. Sistem mengirim email konfirmasi dengan Nomor Registrasi
         │
         ▼
4. Admin KKIPP mereview di Dashboard → Approve / Reject
         │
         ▼ (jika Approve)
5. Sekolah menerima email persetujuan + Tiket Digital (QR Code)
         │
         ▼
6. Hari kunjungan: Petugas scan QR untuk Check-In
         │
         ▼
7. Akhir kunjungan: Petugas scan QR untuk Check-Out
         │
         ▼
8. Sistem otomatis kirim email Survei Kepuasan ke PIC sekolah
         │
         ▼
9. PIC mengisi survei → data tersimpan di sistem untuk analisis
```

### URL Penting

| Halaman | URL |
|---|---|
| Landing Page | `/` |
| Kalender Kunjungan | `/kalender` |
| Form Reservasi | `/reservasi` |
| Cek Status | `/cek-status` |
| Admin Panel | Domain terpisah (akses via `/admin/login`) |
| Petugas Panel | Domain terpisah (akses via `/petugas/login`) |

---

## Tim Pengembang

### Pengembang Aplikasi

| Nama | Peran |
|---|---|
| **Nina Wulandari** | Pengembang Aplikasi |

### Pengarah

| Nama | Jabatan |
|---|---|
| **Vidi Sukmayadi, S.S., M.Si., Ph.D.** | Pengarah |
| **Dr. Angga Hadipurwa, S.Pd., M.I.Kom.** | Pengarah |
| **Jaka Falah, S.S., M.Pd.** | Pengarah |

---

<div align="center">

**KKIPP — Kantor Komunikasi, Informasi dan Pelayanan Publik**  
**Universitas Pendidikan Indonesia**  
Jl. Dr. Setiabudhi No.229, Bandung 40154  
Tel: 085133332559 (WhatsApp) | humas.upi.edu

</div>
