# 📚 Dokumentasi Lengkap: Kunjungan UPI

## 📋 Daftar Isi
1. [Ringkasan Project](#ringkasan-project)
2. [Petunjuk Instalasi & Penggunaan](#petunjuk-instalasi--penggunaan)
3. [Arsitektur Aplikasi](#arsitektur-aplikasi)
4. [Database Schema](#database-schema)
5. [Fitur-Fitur Aplikasi](#fitur-fitur-aplikasi)
6. [Panduan Pengguna](#panduan-pengguna)

---

## 🎯 Ringkasan Project

**Kunjungan UPI** adalah platform sistem permohonan kunjungan sekolah ke Universitas Pendidikan Indonesia (UPI) yang modern, efisien, dan transparan. Aplikasi ini memudahkan sekolah untuk mendaftar kunjungan dan UPI untuk mengelola permohonan tersebut dengan sistem yang terintegrasi.

### Informasi Repository
- **Repository**: `naainwdr/kunjungan-upi`
- **Bahasa Utama**: Blade (Laravel View Engine)
- **Status**: Public, Active
- **Lisensi**: MIT

### Teknologi yang Digunakan
- **Backend**: Laravel 12 (Stable)
- **PHP**: 8.2 or higher
- **Frontend**: Tailwind CSS 4.0
- **JavaScript Engine**: Vite 7
- **Database**: MySQL / PostgreSQL / SQLite
- **Media Storage**: Cloudinary Labs (untuk image & document management)
- **Dependencies**: 
  - `laravel/framework: ^12.0`
  - `cloudinary-labs/cloudinary-laravel: ^3.0`
  - `laravel/tinker: ^2.10.1`

---

## 🚀 Petunjuk Instalasi & Penggunaan

### Prerequisites
Sebelum instalasi, pastikan sistem Anda memiliki:
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Server (atau PostgreSQL/SQLite)

### Langkah-Langkah Instalasi

#### 1. Clone Repository
```bash
git clone https://github.com/naainwdr/kunjungan-upi.git
cd kunjungan-upi
```

#### 2. Setup Backend (Server-side)
```bash
composer install
cp .env.example .env
php artisan key:generate
```

#### 3. Konfigurasi Database
Edit file `.env` dan sesuaikan konfigurasi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kunjungan_upi
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration:
```bash
php artisan migrate
```

#### 4. Setup Frontend (Client-side)
```bash
npm install
```

#### 5. Jalankan Aplikasi
Gunakan script custom dari `composer.json`:
```bash
npm run dev
```

Script ini akan menjalankan secara bersamaan:
- PHP Artisan Server (port 8000)
- PHP Artisan Queue Listener
- Laravel Pail (logging)
- Vite Dev Server (untuk HMR)

### Deployment

Aplikasi ini sudah dipersiapkan untuk deployment:

#### Docker
Gunakan `Dockerfile` yang tersedia:
```bash
docker build -t kunjungan-upi .
docker run -p 8000:8000 kunjungan-upi
```

#### Railway
Konfigurasi `railway.json` sudah tersedia untuk deployment cepat di Railway.

#### Render
Konfigurasi `render.yaml` tersedia untuk deployment di Render.

---

## 🏗️ Arsitektur Aplikasi

### Struktur Folder

```
kunjungan-upi/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── KunjunganController.php      # Controller untuk public area
│   │       └── Admin/
│   │           ├── AuthController.php       # Controller admin authentication
│   │           └── AdminKunjunganController.php  # Controller admin management
│   └── Models/
│       ├── Kunjungan.php                   # Model untuk data kunjungan
│       └── User.php                        # Model untuk user admin
├── routes/
│   └── web.php                             # Routing aplikasi
├── database/
│   └── migrations/                         # Database migrations
├── resources/views/
│   ├── public/                             # Template untuk publik
│   ├── admin/                              # Template untuk admin
│   ├── layouts/                            # Layout templates
│   └── emails/                             # Email templates
├── config/                                 # Konfigurasi aplikasi
├── storage/                                # File storage
└── bootstrap/                              # Bootstrap application
```

### MVC Flow

```
Route (web.php)
    ↓
Controller (KunjunganController atau AdminKunjunganController)
    ↓
Model (Kunjungan atau User)
    ↓
View (Blade templates)
```

---

## 💾 Database Schema

### Tabel: `kunjungan`

Tabel utama untuk menyimpan data permohonan kunjungan sekolah.

```sql
CREATE TABLE kunjungan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nomor_registrasi VARCHAR(20) UNIQUE,        -- Format: UPI-YYYYMMDD-XXXX
    nama_sekolah VARCHAR(255),                   -- Nama sekolah pengunjung
    npsn VARCHAR(20),                            -- NPSN (Nomor Pokok Sekolah Nasional)
    alamat TEXT,                                 -- Alamat sekolah
    nama_pic VARCHAR(255),                       -- Nama Pic (Person In Charge)
    email VARCHAR(255),                          -- Email sekolah
    telepon VARCHAR(20),                         -- Nomor telepon
    tanggal_kunjungan DATE,                      -- Tanggal rencana kunjungan
    jam_mulai VARCHAR(5),                        -- Jam mulai (format HH:MM)
    jam_selesai VARCHAR(5),                      -- Jam selesai (format HH:MM)
    jumlah_peserta INT,                          -- Jumlah peserta yang berkunjung
    file_surat VARCHAR(255),                     -- Path/URL file surat permohonan
    status ENUM('pending','approved','rejected','cancelled'),  -- Status pengajuan
    catatan_admin TEXT,                          -- Catatan dari admin
    email_notified_at TIMESTAMP,                 -- Waktu notifikasi email terakhir
    created_at TIMESTAMP,                        -- Waktu record dibuat
    updated_at TIMESTAMP                         -- Waktu record diupdate
);
```

### Tabel: `users`
Tabel untuk menyimpan data user admin dengan sistem autentikasi Laravel default.

### Model: Kunjungan.php

```php
class Kunjungan extends Model {
    protected $table = 'kunjungan';
    
    protected $fillable = [
        'nomor_registrasi', 'nama_sekolah', 'npsn', 'alamat',
        'nama_pic', 'email', 'telepon', 'tanggal_kunjungan',
        'jam_mulai', 'jam_selesai', 'jumlah_peserta', 'file_surat',
        'status', 'catatan_admin', 'email_notified_at'
    ];
    
    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'email_notified_at' => 'datetime'
    ];
}
```

---

## ✨ Fitur-Fitur Aplikasi

### 1️⃣ PORTAL PUBLIK

#### A. Landing Page (Halaman Beranda)
**Route**: `GET /`  
**Controller**: `KunjunganController@index`  
**View**: `public.landing`

**Fungsi**:
- Menampilkan halaman beranda informatif dan responsif
- Memberikan overview tentang program kunjungan UPI
- Navigasi ke fitur-fitur lainnya

---

#### B. Kalender Kunjungan
**Route**: `GET /kalender`  
**Controller**: `KunjunganController@kalender`  
**View**: `public.kalender`

**Parameter Query**:
- `year`: Tahun (default: tahun sekarang)
- `month`: Bulan (default: bulan sekarang)

**Batasan Navigasi**:
- 1 tahun ke belakang dari sekarang
- 2 tahun ke depan dari sekarang

**Fitur**:
- Menampilkan kalender interaktif
- Menampilkan tanggal yang tersedia untuk kunjungan
- Menampilkan jumlah kunjungan per tanggal
- Menampilkan hari libur nasional Indonesia
- Dapat pre-fill tanggal ke formulir reservasi

**Data yang Ditampilkan**:
```
- Hanya kunjungan dengan status 'approved' yang ditampilkan
- Hari libur nasional ditandai berbeda
- Navigasi bulan dengan batasan yang sudah ditentukan
```

---

#### C. Permohonan Online / Formulir Reservasi
**Route**: 
- `GET /reservasi` → Tampil form
- `POST /reservasi` → Submit form

**Controller**: `KunjunganController@create` & `KunjunganController@store`  
**View**: `public.reservasi`

**Input Fields**:
```
1. nama_sekolah (required, string, max:255)
2. npsn (required, string, max:20) - Nomor Pokok Sekolah Nasional
3. alamat (required, string, max:500)
4. nama_pic (required, string, max:255) - Nama penanggungjawab/kepala sekolah
5. email (required, email, max:255)
6. telepon (required, string, max:20)
7. tanggal_kunjungan (required, date, min: 7 hari dari sekarang)
8. jam_mulai (required, string format HH:MM)
9. jam_selesai (required, string format HH:MM)
   - Validasi durasi minimal 2 jam
   - Validasi durasi maksimal 5 jam
   - Validasi berakhir paling lambat pukul 16:00 WIB
   - Validasi tidak bentrok dengan jam kunjungan lain yang approved
10. jumlah_peserta (required, integer, 1-500)
11. file_surat (required, file, PDF/JPG, max:1MB)
```

**Proses Penyimpanan**:

1. **Validasi Input** - Validasi semua field sesuai rules
2. **Check Overlap Jam** - Pastikan tidak ada bentrok dengan kunjungan approved lain
3. **Upload File** - Upload ke Cloudinary atau local storage
4. **Generate Nomor Registrasi** - Format: `UPI-YYYYMMDD-XXXX`
5. **Simpan Data** - Create record Kunjungan dengan status 'pending'
6. **Kirim Email** - Kirim email notifikasi ke sekolah
7. **Redirect** - Redirect ke halaman sukses dengan nomor registrasi

**Contoh Nomor Registrasi**:
```
UPI-20260407-0001  (untuk pengajuan pertama tgl 7 April 2026)
UPI-20260407-0002  (untuk pengajuan kedua tgl 7 April 2026)
```

---

#### D. Halaman Sukses Permohonan
**Route**: `GET /reservasi/sukses?id=UPI-YYYYMMDD-XXXX`  
**Controller**: `KunjunganController@sukses`  
**View**: `public.sukses`

**Fungsi**:
- Menampilkan pesan sukses pengajuan
- Menampilkan nomor registrasi untuk tracking
- Menampilkan informasi permohonan yang telah dikirim

---

#### E. Cek Status Permohonan
**Route**: 
- `GET /cek-status` → Tampil form pencarian
- `POST /cek-status` → Submit pencarian

**Controller**: `KunjunganController@cekStatus` & `KunjunganController@cariStatus`  
**View**: `public.cek-status`

**Cara Pencarian**:
- Berdasarkan **Nomor Registrasi** (contoh: UPI-20260407-0001)
- Berdasarkan **Email** sekolah

**Informasi yang Ditampilkan**:
```
- Nomor registrasi
- Nama sekolah
- Status (Menunggu, Disetujui, Ditolak, Dibatalkan)
- Tanggal kunjungan
- Jam mulai - jam selesai
- Jumlah peserta
- Catatan admin (jika ada)
- Tombol batal (jika status pending/approved dan masih bisa dibatalkan)
```

**Status Mapping**:
```
pending   → "Menunggu" (warna kuning)
approved  → "Disetujui" (warna hijau)
rejected  → "Ditolak" (warna merah)
cancelled → "Dibatalkan" (warna abu-abu)
```

---

#### F. Pembatalan Permohonan
**Route**: `POST /reservasi/{nomor_registrasi}/batal`  
**Controller**: `KunjunganController@batal`

**Aturan Pembatalan**:
- Hanya dapat dibatalkan **maksimal H-2** (2 hari sebelum) tanggal kunjungan
- Permohonan yang sudah lewat H-2 tidak dapat dibatalkan lagi
- Status akan berubah menjadi 'cancelled'

**Contoh**:
- Kunjungan tanggal 10 April → Pembatalan harus sebelum 8 April
- Kunjungan tanggal 7 April → Pembatalan harus sebelum 5 April

---

#### G. API: Ambil Data Jam yang Terbooking
**Route**: `GET /api/booked-hours?tanggal=YYYY-MM-DD`  
**Controller**: `KunjunganController@bookedHours`  
**Response**: JSON array jam yang sudah terbooking

**Contoh Response**:
```json
["09:00", "10:00", "11:00", "14:00", "15:00"]
```

**Fungsi**:
- Menampilkan jam-jam mana saja yang sudah diambil pada tanggal tertentu
- Digunakan untuk validasi real-time saat input formulir
- Hanya menampilkan jam kunjungan dengan status 'approved'

---

### 2️⃣ PORTAL ADMIN

#### A. Admin Login
**Route**: 
- `GET /admin/login` → Tampil form login
- `POST /admin/login` → Submit login

**Controller**: `Admin/AuthController@showLogin` & `Admin/AuthController@login`  
**View**: `admin.login`

**Proses Login**:
1. Input email dan password
2. Validasi credentials menggunakan `Auth::attempt()`
3. Jika valid → Redirect ke dashboard admin
4. Jika invalid → Tampilkan error message
5. Support "Remember me" option

**Keamanan**:
- Session regeneration setelah login
- CSRF token protection
- Password hashing dengan bcrypt

---

#### B. Admin Dashboard
**Route**: `GET /admin/dashboard`  
**Controller**: `Admin/AdminKunjunganController@dashboard`  
**View**: `admin.dashboard`  
**Middleware**: `auth` (harus login)

**Fitur Dashboard**:

1. **Ringkasan Statistik**:
   ```
   - Total semua permohonan
   - Permohonan pending (menunggu)
   - Permohonan approved (disetujui)
   - Permohonan rejected (ditolak)
   - Permohonan cancelled (dibatalkan)
   ```

2. **Top 5 Sekolah Paling Sering Berkunjung** (yang approved):
   ```
   Data: Nama sekolah, Total kunjungan, Total peserta
   ```

3. **5 Kunjungan Terbaru yang Disetujui**:
   ```
   Data: Tanggal kunjungan, Nama sekolah, Jumlah peserta
   ```

4. **List Permohonan dengan Filter dan Sorting**:

   **Filter**:
   - Status: `pending`, `approved`, `rejected`, `cancelled`
   - Tanggal dari: `tgl_dari` (date picker)
   - Tanggal sampai: `tgl_sampai` (date picker)
   - Search: `search` (cari by nama sekolah, nomor registrasi, NPSN)

   **Sorting**:
   - Kolom: tanggal_kunjungan, created_at, jumlah_peserta, nama_sekolah
   - Arah: ascending / descending
   - Default: created_at descending

   **Pagination**:
   - 15 item per halaman
   - Query string preserved untuk filter/sort

---

#### C. Detail Permohonan
**Route**: `GET /admin/kunjungan/{id}`  
**Controller**: `Admin/AdminKunjunganController@show`  
**View**: `admin.detail`  
**Middleware**: `auth`

**Informasi yang Ditampilkan**:
```
- Nomor registrasi
- Nama sekolah
- NPSN
- Alamat
- Nama PIC
- Email
- Telepon
- Tanggal kunjungan (format: dd Bulan yyyy)
- Jam mulai - jam selesai
- Jumlah peserta
- File surat (download link)
- Status saat ini
- Catatan admin
- Tanggal pengajuan
- Waktu update terakhir
```

**Action Buttons**:
- Tombol "Setujui" (jika status pending)
- Tombol "Tolak" (jika status pending)

---

#### D. Setujui Permohonan
**Route**: `POST /admin/kunjungan/{id}/approve`  
**Controller**: `Admin/AdminKunjunganController@approve`  
**Middleware**: `auth`

**Form Input**:
- `catatan_admin` (optional, string, max:500)

**Proses**:
1. Update status menjadi 'approved'
2. Simpan catatan admin (jika ada)
3. Kirim email notifikasi ke sekolah
4. Update timestamp email_notified_at
5. Redirect ke dashboard dengan success message

**Email Notifikasi**:
- Menggunakan `StatusKunjunganMail` mailable
- Mengirim ke email sekolah
- Informasi approval dan detail kunjungan

---

#### E. Tolak Permohonan
**Route**: `POST /admin/kunjungan/{id}/reject`  
**Controller**: `Admin/AdminKunjunganController@reject`  
**Middleware**: `auth`

**Form Input**:
- `catatan_admin` (required, string, max:500) - Alasan penolakan

**Proses**:
1. Validasi catatan admin (wajib diisi)
2. Update status menjadi 'rejected'
3. Simpan catatan admin (alasan penolakan)
4. Kirim email notifikasi ke sekolah
5. Update timestamp email_notified_at
6. Redirect ke dashboard dengan success message

**Validasi**:
- Jika catatan kosong → tampilkan error "Alasan penolakan wajib diisi."

---

#### F. Admin Logout
**Route**: `POST /admin/logout`  
**Controller**: `Admin/AuthController@logout`  
**Middleware**: `auth`

**Proses**:
1. Logout user
2. Invalidate session
3. Regenerate session token
4. Redirect ke login page

---

## 📖 Panduan Pengguna

### Untuk Sekolah (Public User)

**Alur Penggunaan**:

1. **Buka Landing Page**
   - Akses `http://localhost:8000/`
   - Lihat informasi tentang program kunjungan

2. **Lihat Kalender Kunjungan**
   - Klik menu "Kalender Kunjungan"
   - Pilih bulan dan tahun yang ingin
   - Lihat tanggal yang tersedia (jumlah kunjungan ditampilkan)
   - Klik tanggal untuk pre-fill form reservasi

3. **Isi Formulir Permohonan**
   - Klik "Buat Permohonan" atau dari kalender
   - Isi semua data sekolah dengan lengkap:
     * Nama sekolah
     * NPSN
     * Alamat
     * Nama PIC
     * Email
     * Telepon
   - Pilih tanggal kunjungan (min. 7 hari dari sekarang)
   - Pilih jam mulai dan selesai:
     * Durasi minimal 2 jam
     * Durasi maksimal 5 jam
     * Harus selesai sebelum pukul 16:00
     * Jam tidak boleh bentrok dengan kunjungan lain
   - Input jumlah peserta (1-500 orang)
   - Upload surat permohonan (PDF/JPG, max 1MB)
   - Klik "Kirim Permohonan"

4. **Lihat Konfirmasi Sukses**
   - Catat nomor registrasi
   - Email notifikasi dikirim ke email sekolah

5. **Cek Status Permohonan**
   - Klik "Cek Status"
   - Cari dengan nomor registrasi atau email
   - Lihat status permohonan:
     * Menunggu: Admin masih memproses
     * Disetujui: Permohonan diterima
     * Ditolak: Lihat alasan penolakan
     * Dibatalkan: Permohonan telah dibatalkan

6. **Batalkan Permohonan (jika perlu)**
   - Klik tombol "Batalkan" pada detail permohonan
   - Hanya bisa dibatalkan maksimal H-2 sebelum kunjungan
   - Konfirmasi pembatalan

---

### Untuk Admin UPI

**Alur Penggunaan**:

1. **Login Admin**
   - Akses `http://localhost:8000/admin/login`
   - Input email dan password admin
   - Klik "Masuk"
   - (Optional) Centang "Ingat saya" untuk remember login

2. **Lihat Dashboard**
   - Melihat ringkasan statistik semua permohonan
   - Melihat top 5 sekolah paling sering berkunjung
   - Melihat 5 kunjungan terbaru yang approved

3. **Filter & Search Permohonan**
   - Filter berdasarkan status (pending, approved, rejected, cancelled)
   - Filter berdasarkan rentang tanggal kunjungan
   - Search berdasarkan nama sekolah / NPSN / nomor registrasi
   - Sort berdasarkan kolom (tanggal, jumlah peserta, nama, dll)

4. **Lihat Detail Permohonan**
   - Klik nomor registrasi atau nama sekolah
   - Lihat semua informasi lengkap:
     * Data sekolah
     * Data PIC
     * Tanggal dan jam kunjungan
     * Jumlah peserta
     * File surat permohonan (bisa didownload/dilihat)
     * Tanggal pengajuan

5. **Setujui Permohonan**
   - Klik tombol "Setujui"
   - Input catatan admin (opsional)
   - Klik "Setujui"
   - Email notifikasi approval dikirim ke sekolah
   - Status berubah menjadi "Disetujui"

6. **Tolak Permohonan**
   - Klik tombol "Tolak"
   - Input alasan penolakan (wajib)
   - Klik "Tolak"
   - Email notifikasi penolakan dikirim ke sekolah
   - Status berubah menjadi "Ditolak"

7. **Logout**
   - Klik menu logout
   - Redirect ke login page

---

## 🔧 Konfigurasi Khusus

### Environment Variables (.env)

```env
# Application
APP_NAME="Kunjungan UPI"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kunjungan_upi
DB_USERNAME=root
DB_PASSWORD=

# Email (untuk notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@kunjungan-upi.com

# Cloudinary (untuk upload file)
CLOUDINARY_URL=cloudinary://your_key:your_secret@your_cloud_name

# File Storage
FILESYSTEM_DISK=public  # atau 'cloudinary'
```

---

## 📧 Notifikasi Email

Aplikasi menggunakan Laravel Mail untuk mengirim notifikasi:

### Trigger Pengiriman Email:

1. **Setelah Permohonan Baru Disubmit**
   - Notifikasi ke sekolah bahwa permohonan diterima
   - Menyertakan nomor registrasi

2. **Admin Menyetujui Permohonan**
   - Notifikasi ke sekolah bahwa permohonan disetujui
   - Menyertakan informasi kunjungan

3. **Admin Menolak Permohonan**
   - Notifikasi ke sekolah bahwa permohonan ditolak
   - Menyertakan alasan penolakan

### Email Template:
- File: `resources/views/emails/status-kunjungan.blade.php`
- Menggunakan `StatusKunjunganMail` mailable

---

## 🔒 Keamanan

1. **Authentication**: Menggunakan Laravel's built-in authentication
2. **Authorization**: Middleware `auth` untuk protect admin routes
3. **CSRF Protection**: Token validation di semua form
4. **File Upload**: Validasi tipe file (PDF, JPG), ukuran max 1MB
5. **Input Validation**: Validasi lengkap di semua form
6. **Session Security**: Session regeneration setelah login/logout

---

## 📝 Catatan Penting

- **Hari Libur Nasional**: Sudah dikonfigurasi untuk tahun 2026 dan 2027
- **Batasan Tanggal Kunjungan**: Minimal 7 hari dari sekarang
- **Durasi Kunjungan**: 2-5 jam, harus selesai sebelum 16:00 WIB
- **File Surat**: Harus dalam format PDF atau JPG, max 1MB
- **Pembatalan**: Hanya bisa dilakukan maksimal H-2

---

**Dikembangkan untuk Program Magang - Universitas Pendidikan Indonesia.**
