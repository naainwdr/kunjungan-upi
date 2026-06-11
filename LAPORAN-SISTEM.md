# LAPORAN PENGEMBANGAN SISTEM
## Sistem Permohonan Kunjungan Sekolah Berbasis Web
### KKIPP — Universitas Pendidikan Indonesia

---

> **Pengembang:** Nina Wulandari  
> **Pengarah:** Vidi Sukmayadi, S.S., M.Si., Ph.D. · Dr. Angga Hadipurwa, S.Pd., M.I.Kom. · Jaka Falah, S.S., M.Pd.  
> **Instansi:** Kantor Komunikasi, Informasi dan Pelayanan Publik (KKIPP) UPI  
> **Tahun:** 2026

---

## Daftar Isi

1. [Latar Belakang](#1-latar-belakang)
2. [Rumusan Masalah](#2-rumusan-masalah)
3. [Tujuan Pengembangan](#3-tujuan-pengembangan)
4. [Manfaat Sistem](#4-manfaat-sistem)
5. [Ruang Lingkup Sistem](#5-ruang-lingkup-sistem)
6. [Landasan Teori & Teknologi](#6-landasan-teori--teknologi)
7. [Analisis Sistem](#7-analisis-sistem)
8. [Perancangan Sistem](#8-perancangan-sistem)
9. [Implementasi](#9-implementasi)
10. [Pengujian](#10-pengujian)
11. [Hasil & Pembahasan](#11-hasil--pembahasan)
12. [Kendala & Solusi](#12-kendala--solusi)
13. [Kesimpulan](#13-kesimpulan)
14. [Saran Pengembangan](#14-saran-pengembangan)

---

## 1. Latar Belakang

Universitas Pendidikan Indonesia (UPI) sebagai perguruan tinggi negeri terkemuka di Indonesia senantiasa menerima kunjungan dari berbagai sekolah di seluruh Indonesia. Kunjungan-kunjungan ini merupakan bagian dari program sosialisasi dan orientasi akademik yang diselenggarakan untuk memperkenalkan UPI kepada calon mahasiswa, guru, maupun pemangku kepentingan pendidikan lainnya.

Kantor Komunikasi, Informasi dan Pelayanan Publik (KKIPP) UPI adalah unit yang bertanggung jawab dalam mengelola dan memfasilitasi permohonan kunjungan tersebut. Sebelum adanya sistem digital, seluruh proses permohonan kunjungan dilakukan secara **manual**, yaitu dengan mekanisme:

1. Sekolah mengirimkan surat permohonan fisik melalui pos atau diserahkan langsung ke KKIPP
2. Staf KKIPP memproses surat secara manual dan mencatat di buku agenda
3. Konfirmasi persetujuan atau penolakan disampaikan melalui telepon atau surat balasan
4. Pada hari kunjungan, presensi dilakukan dengan absensi kertas
5. Tidak ada mekanisme formal pengumpulan umpan balik pasca-kunjungan

Mekanisme manual ini menimbulkan berbagai permasalahan yang berdampak pada efisiensi operasional KKIPP dan kualitas layanan yang diterima oleh sekolah pemohon. Seiring dengan meningkatnya jumlah permohonan kunjungan dari tahun ke tahun, kebutuhan akan sebuah sistem informasi yang dapat mengotomasi dan mendigitalisasi proses ini menjadi semakin mendesak.

Perkembangan teknologi web, khususnya framework PHP modern seperti **Laravel**, memberikan solusi yang tepat untuk membangun sistem informasi yang handal, aman, dan mudah dikelola. Laravel menyediakan ekosistem lengkap mulai dari manajemen database (Eloquent ORM), autentikasi, pengiriman email, hingga manajemen file, sehingga pengembangan dapat dilakukan secara lebih cepat dan terstruktur.

Berdasarkan kondisi tersebut, dikembangkanlah **Sistem Permohonan Kunjungan Sekolah Berbasis Web** yang dapat menjawab kebutuhan digitalisasi proses administrasi kunjungan di KKIPP UPI.

---

## 2. Rumusan Masalah

Berdasarkan latar belakang yang telah diuraikan, dapat diidentifikasi beberapa permasalahan yang menjadi dasar pengembangan sistem ini:

1. **Inefisiensi proses manual:** Proses pengajuan, pencatatan, dan konfirmasi permohonan kunjungan yang dilakukan secara manual membutuhkan waktu yang lama dan rentan terhadap kesalahan pencatatan.

2. **Keterbatasan aksesibilitas:** Sekolah hanya dapat mengajukan permohonan pada jam kerja dengan cara datang langsung atau mengirim surat, yang membatasi kemudahan akses.

3. **Tidak adanya transparansi status:** Pemohon tidak dapat mengetahui perkembangan status permohonan mereka secara real-time tanpa menghubungi KKIPP secara langsung.

4. **Ketiadaan manajemen kapasitas:** Tidak ada mekanisme otomatis untuk mencegah konflik jadwal (double booking) untuk sesi dan tempat yang sama pada tanggal yang sama.

5. **Presensi tidak terdigitalisasi:** Absensi pada hari kunjungan masih menggunakan kertas, sehingga data tidak terintegrasi dengan sistem dan sulit dianalisis.

6. **Minimnya data umpan balik:** Tidak ada mekanisme formal untuk mengumpulkan umpan balik dari sekolah-sekolah yang telah berkunjung, sehingga sulit dilakukan evaluasi layanan secara sistematis.

---

## 3. Tujuan Pengembangan

### Tujuan Umum

Mengembangkan sistem informasi permohonan kunjungan sekolah berbasis web yang terintegrasi untuk mendukung digitalisasi layanan publik KKIPP UPI.

### Tujuan Khusus

1. Membangun modul pengajuan permohonan online yang dapat diakses oleh sekolah kapan saja dan dari mana saja.
2. Mengimplementasikan sistem manajemen permohonan bagi admin KKIPP untuk memproses, menyetujui, atau menolak permohonan secara digital.
3. Membangun sistem notifikasi email otomatis untuk setiap perubahan status permohonan.
4. Mengembangkan sistem presensi berbasis QR code yang terintegrasi dengan data permohonan.
5. Menyediakan fitur survei kepuasan digital yang terkirim otomatis pasca-kunjungan.
6. Menerapkan arsitektur kode yang bersih, modular, dan dapat dikembangkan lebih lanjut (maintainable).

---

## 4. Manfaat Sistem

### Bagi Sekolah Pemohon
- Kemudahan mengajukan permohonan kunjungan tanpa harus datang langsung atau mengirim surat fisik
- Transparansi proses: dapat memantau status permohonan secara real-time melalui fitur "Cek Status"
- Mendapatkan konfirmasi dan informasi kunjungan melalui email secara otomatis
- Tiket digital dengan QR code yang praktis untuk presensi pada hari kunjungan

### Bagi Admin KKIPP
- Dashboard terpusat untuk melihat, memfilter, dan mengelola semua permohonan
- Alur kerja yang terstandarisasi: approve/reject dengan catatan yang tersimpan permanen
- Statistik dan analitik otomatis (top sekolah pemohon, tren kunjungan)
- Audit trail lengkap: setiap perubahan status tercatat dengan waktu dan pelaku

### Bagi Petugas Presensi
- Scanner QR digital yang mudah digunakan tanpa pengetahuan teknis
- Rekap presensi harian yang tersusun otomatis

### Bagi Institusi UPI
- Data kunjungan yang terstruktur dan dapat dianalisis untuk perencanaan kapasitas
- Peningkatan citra institusi melalui layanan digital yang modern dan profesional
- Pengurangan penggunaan kertas (paperless) sesuai komitmen green campus

---

## 5. Ruang Lingkup Sistem

### Yang Termasuk dalam Sistem

| No | Modul | Deskripsi |
|---|---|---|
| 1 | Permohonan Publik | Form reservasi, cek status, pembatalan mandiri |
| 2 | Kalender Kunjungan | Tampilan visual jadwal kunjungan yang disetujui |
| 3 | Manajemen Admin | Dashboard, approve, reject, complete kunjungan |
| 4 | Presensi QR | Check-in dan check-out via scan kode QR |
| 5 | Survei Kepuasan | Form evaluasi digital pasca-kunjungan |
| 6 | Data Referensi | Manajemen Tempat, Sesi, dan Kalender override |
| 7 | Notifikasi Email | Email otomatis untuk setiap tahap alur kunjungan |
| 8 | Role Management | Admin dan Petugas Presensi dengan hak akses berbeda |

### Yang Tidak Termasuk dalam Sistem (di luar scope)

- Sistem pembayaran/biaya kunjungan
- Integrasi langsung dengan SIMPEG atau sistem internal UPI lainnya
- Aplikasi mobile native (iOS/Android) — hanya web yang responsive
- Live chat atau komunikasi real-time

---

## 6. Landasan Teori & Teknologi

### 6.1 Laravel Framework

Laravel adalah framework PHP open-source yang mengikuti pola arsitektur **MVC (Model-View-Controller)**. Laravel dipilih karena:
- Ekosistem yang matang dan dokumentasi yang komprehensif
- Fitur bawaan yang kaya: Eloquent ORM, Queue, Mail, Storage, Authentication
- Komunitas yang besar dan aktif
- Standar pengembangan yang mendorong kode yang bersih dan terstruktur

### 6.2 Service Pattern

Service Pattern adalah pola desain perangkat lunak yang memisahkan **business logic** dari layer Controller dan Model. Dalam pola ini:

- **Controller** bertugas menerima HTTP request dan mengembalikan response
- **Service** bertugas menjalankan logika bisnis, validasi domain, dan orkestrasi proses
- **Model** bertugas berinteraksi dengan database

Pemisahan ini menghasilkan kode yang lebih mudah diuji (testable), dikelola (maintainable), dan dikembangkan (extensible).

### 6.3 Form Request Validation

Laravel Form Request adalah kelas khusus yang memisahkan logika validasi input dari Controller. Setiap form memiliki kelas validasi tersendiri, sehingga Controller tetap ramping dan validasi dapat di-reuse serta diuji secara independen.

### 6.4 PSR-12 Coding Standard

PSR-12 adalah standar penulisan kode PHP yang ditetapkan oleh PHP-FIG (Framework Interoperability Group). Standar ini mencakup konvensi indentasi, penamaan, penempatan tanda kurung, dan format kode lainnya untuk menjamin konsistensi dan keterbacaan kode.

### 6.5 QR Code Technology

QR (Quick Response) Code adalah kode dua dimensi yang dapat menyimpan data dalam format yang dapat dipindai oleh kamera. Dalam sistem ini, QR code digunakan sebagai tiket digital yang berisi nomor registrasi unik, memungkinkan petugas melakukan verifikasi presensi secara cepat dan akurat.

---

## 7. Analisis Sistem

### 7.1 Analisis Sistem Lama (As-Is)

```
Proses Sebelumnya (Manual):

Sekolah                    KKIPP Admin                 Hari Kunjungan
   │                           │                              │
   │── Kirim Surat Fisik ──►   │                              │
   │                           │── Review Manual ─►           │
   │                           │── Catat di Buku ─►           │
   │◄── Konfirmasi Telepon ─── │                              │
   │                           │                              │
   │                           │              ──► Absensi Kertas
   │                           │              ──► Tidak Ada Evaluasi
```

**Kelemahan:**
- Waktu proses lama (bisa berhari-hari untuk konfirmasi)
- Tidak ada visibilitas status bagi pemohon
- Risiko double booking (tidak ada sistem pencegahan)
- Data tidak terintegrasi (tersebar di buku agenda, email, dan arsip fisik)
- Tidak ada mekanisme pengumpulan umpan balik

### 7.2 Analisis Sistem Baru (To-Be)

```
Proses Baru (Digital):

Sekolah          Sistem Web          Admin KKIPP         Petugas
   │                 │                   │                  │
   │── Isi Form ──►  │                   │                  │
   │◄── Email ──── Proses ───────────►   │                  │
   │               Otomatis             Approve/Reject       │
   │◄── Email Notif ─────────────────── │                  │
   │                 │                                       │
   │                 │── QR Tiket ────────────────────────►  │
   │                 │                             Check-in/out
   │◄── Email Survei ─── Auto-trigger ◄───────────────────── │
```

**Keunggulan:**
- Proses real-time dengan notifikasi email otomatis
- Pemohon dapat memantau status kapan saja
- Pencegahan double booking secara otomatis
- Semua data tersimpan terstruktur dan mudah dianalisis
- Mekanisme survei kepuasan terintegrasi

### 7.3 Analisis Pengguna (User Stories)

| Sebagai... | Saya ingin... | Agar... |
|---|---|---|
| Guru sekolah | mengajukan kunjungan online | tidak perlu datang langsung ke kampus |
| Kepala sekolah | memantau status pengajuan | bisa mempersiapkan acara kunjungan |
| Admin KKIPP | melihat semua permohonan dalam satu dashboard | dapat memproses lebih cepat |
| Admin KKIPP | mengatur hari libur di kalender | tidak ada permohonan di hari yang tidak layanan |
| Petugas | scan QR kunjungan | presensi tercatat akurat tanpa kertas |
| KKIPP | melihat data survei kepuasan | dapat mengevaluasi kualitas layanan kunjungan |

---

## 8. Perancangan Sistem

### 8.1 Diagram Alur Utama (Flowchart)

```
                    ┌─────────────────────┐
                    │   Buka Situs Web    │
                    └──────────┬──────────┘
                               │
              ┌────────────────┴─────────────────┐
              │           Pilih Tanggal          │
              │         di Kalender              │
              └────────────────┬─────────────────┘
                               │
              ┌────────────────┴─────────────────┐
              │        Isi Form Reservasi         │
              │  (Data Sekolah + PIC + Peserta)   │
              └────────────────┬─────────────────┘
                               │
              ┌────────────────┴─────────────────┐
              │     Upload Surat Permohonan       │
              └────────────────┬─────────────────┘
                               │
              ┌────────────────┴─────────────────┐
              │         Validasi Sistem           │◄─ Gagal ─► Pesan Error
              └────────────────┬─────────────────┘
                          (Berhasil)
                               │
              ┌────────────────┴─────────────────┐
              │   Nomor Registrasi Dibuat &       │
              │   Email Konfirmasi Terkirim       │
              └────────────────┬─────────────────┘
                               │
                         [Admin Review]
                               │
            ┌──────────────────┴──────────────────┐
            │ Approve                              │ Reject
            │                                     │
     [Email Persetujuan                   [Email Penolakan +
      + QR Tiket Digital]                  Alasan Penolakan]
            │
     [Hari Kunjungan]
            │
     [Petugas Scan QR]
            │
      ┌─────┴─────┐
   Check-In    Check-Out
                  │
         [Status → Completed]
         [Email Survei Terkirim]
                  │
          [Isi Survei Online]
```

### 8.2 Desain Database (Entity Relationship)

```
SEKOLAH (1) ──────────── (N) KUNJUNGAN (N) ──────────── (1) SESI
   │                              │                            
   └── (1) KONTAK_SEKOLAH (N)     │──── (1) TEMPAT
                                  │
                                  ├── (1) KUNJUNGAN_PRESENSI
                                  ├── (N) KUNJUNGAN_LOG
                                  └── (1) SURVEI_KEPUASAN

USERS ──────────────────── mengelola ─────────────────── KUNJUNGAN
(admin / petugas)           (via log)

PENGATURAN_KALENDER ──── memengaruhi ────────────── SESI (array)
```

**Tabel Utama:**

| Tabel | Kunci | Keterangan |
|---|---|---|
| `kunjungan` | `id`, `nomor_registrasi` (unique) | Tabel inti permohonan |
| `sekolah` | `id`, `npsn` (unique) | Data sekolah pemohon |
| `kontak_sekolah` | `id`, `sekolah_id` | PIC per permohonan |
| `sesi` | `id` | Slot waktu kunjungan |
| `tempat` | `id` | Lokasi kunjungan |
| `kunjungan_presensi` | `id`, `kunjungan_id` | Waktu check-in/out |
| `kunjungan_log` | `id`, `kunjungan_id` | Audit trail perubahan status |
| `survei_kepuasan` | `id`, `kunjungan_id` | Data survei setelah kunjungan |
| `pengaturan_kalender` | `id`, `tanggal` (unique) | Override hari layanan |
| `users` | `id` | Akun admin dan petugas |

### 8.3 Desain Arsitektur Layered

```
┌──────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                     │
│           (Blade Templates + Tailwind CSS)                │
└─────────────────────────┬────────────────────────────────┘
                          │ HTTP Request / Response
┌─────────────────────────▼────────────────────────────────┐
│                  FORM REQUEST LAYER                       │
│         (Validasi Format + Pesan Error Custom)            │
└─────────────────────────┬────────────────────────────────┘
                          │ Data Tervalidasi
┌─────────────────────────▼────────────────────────────────┐
│                   CONTROLLER LAYER                        │
│     (Terima request → delegasikan → return response)      │
└─────────────────────────┬────────────────────────────────┘
                          │ Memanggil Service
┌─────────────────────────▼────────────────────────────────┐
│                    SERVICE LAYER                          │
│   (Business Logic, Validasi Domain, Email, Storage)       │
│                                                           │
│  KunjunganService  │  KunjunganStatusService              │
│  PresensiService   │  SurveiService                       │
│  AdminReferensiService                                    │
└─────────────────────────┬────────────────────────────────┘
                          │ Query Database
┌─────────────────────────▼────────────────────────────────┐
│                     MODEL LAYER                           │
│       (Eloquent ORM, Relasi, Accessor, Scope)             │
└─────────────────────────┬────────────────────────────────┘
                          │
┌─────────────────────────▼────────────────────────────────┐
│                      DATABASE                             │
│            (SQLite / PostgreSQL)                          │
└──────────────────────────────────────────────────────────┘
```

---

## 9. Implementasi

### 9.1 Teknologi Stack

| Komponen | Teknologi | Versi |
|---|---|---|
| Backend Framework | Laravel | 12.x |
| Bahasa Pemrograman | PHP | 8.2+ |
| Database (Dev) | SQLite | 3.x |
| Database (Prod) | PostgreSQL | 15+ |
| Styling Frontend | Tailwind CSS | 3.x (via CDN) |
| Email | Laravel Mail + SMTP | - |
| File Storage | Laravel Storage (public disk) | - |
| Deployment | Railway / Render | - |
| Containerisasi | Docker | - |

### 9.2 Pola Arsitektur yang Diterapkan

**a) Service Pattern**

Seluruh business logic dipisahkan dari Controller ke kelas Service:

```php
// Sebelum (Controller melakukan segalanya):
public function store(Request $request) {
    $request->validate([...]); // validasi di controller
    $sekolah = Sekolah::updateOrCreate(...); // query di controller
    // ... 50 baris logika bisnis ...
}

// Sesudah (Controller hanya mendelegasikan):
public function store(StoreKunjunganRequest $request): RedirectResponse {
    $kunjungan = $this->kunjunganService->simpanPermohonan(
        $request->validated(),
        $request->file('file_surat')
    );
    return redirect()->route('reservasi.sukses', $kunjungan->nomor_registrasi);
}
```

**b) Form Request Validation**

Validasi dipisahkan ke kelas Form Request yang dedicated:

```php
// StoreKunjunganRequest.php
public function rules(): array {
    return [
        'nama_sekolah'      => 'required|string|max:255',
        'npsn'              => 'required|string|max:20',
        'tanggal_kunjungan' => 'required|date|after_or_equal:' . now()->addDays(10)->format('Y-m-d'),
        'sesi_id'           => 'required|exists:sesi,id',
        'file_surat'        => 'required|file|mimes:pdf,jpg,jpeg|max:1024',
        // ... 7 aturan lainnya
    ];
}
```

**c) Dependency Injection via Constructor**

Semua Service diinjeksikan melalui constructor Laravel Service Container:

```php
public function __construct(
    private readonly KunjunganService $kunjunganService,
    private readonly SurveiService $surveiService,
) {}
```

**d) Audit Trail**

Setiap perubahan status kunjungan dicatat ke tabel `kunjungan_log`:

```php
$kunjungan->logStatus('approved', $catatan, auth()->id());
// → Menyimpan: status_sebelum, status_sesudah, catatan, changed_by, created_at
```

### 9.3 Fitur Keamanan

| Mekanisme | Implementasi |
|---|---|
| **CSRF Protection** | Token CSRF pada semua form POST/PUT/DELETE |
| **Role-Based Access** | Middleware `role.admin` dan `role.petugas` pada semua route terlindungi |
| **SQL Injection Prevention** | Eloquent ORM dengan parameter binding |
| **XSS Prevention** | Blade template auto-escaping semua output |
| **Whitelist Validation** | Semua input divalidasi sebelum diproses |
| **Session Regeneration** | Session diperbarui setiap login untuk mencegah session fixation |
| **File Type Validation** | Upload surat dibatasi hanya PDF/JPG dengan maksimal 1 MB |

### 9.4 Alur Email Notifikasi

| Trigger | Email yang Terkirim | Penerima |
|---|---|---|
| Permohonan baru diajukan | Konfirmasi dengan nomor registrasi | PIC sekolah |
| Admin approve | Persetujuan + link tiket digital | PIC sekolah |
| Admin reject | Penolakan + alasan | PIC sekolah |
| Petugas check-out | Link form survei kepuasan | PIC sekolah |

---

## 10. Pengujian

### 10.1 Pengujian Fungsional

| No | Skenario Uji | Kondisi Awal | Langkah | Hasil yang Diharapkan | Status |
|---|---|---|---|---|---|
| 1 | Pengajuan Permohonan | Data sekolah tersedia | Isi form lengkap + upload surat | Nomor registrasi terbuat, email terkirim | ✅ |
| 2 | Validasi Tanggal | - | Pilih tanggal kurang dari H+10 | Error: "minimal 10 hari dari sekarang" | ✅ |
| 3 | Validasi Hari Kerja | - | Pilih hari Jumat | Error: "hanya Senin–Kamis" | ✅ |
| 4 | Cegah Double Booking | Sesi X sudah approved | Ajukan sesi yang sama | Error: "sesi sudah penuh" | ✅ |
| 5 | Cek Status | Permohonan ada | Input nomor registrasi | Data permohonan tampil | ✅ |
| 6 | Pembatalan H-5 | Kunjungan 3 hari lagi | Klik batal | Error: "sudah lewat batas pembatalan" | ✅ |
| 7 | Approve oleh Admin | Status pending | Admin klik approve | Status → approved, email terkirim | ✅ |
| 8 | Reject dengan Alasan | Status pending | Admin klik reject tanpa alasan | Error: "alasan wajib diisi" | ✅ |
| 9 | Check-In QR | Status approved | Scan QR kunjungan | Waktu masuk tercatat | ✅ |
| 10 | Check-Out QR | Sudah check-in | Scan QR kunjungan | Waktu keluar tercatat, email survei terkirim | ✅ |
| 11 | Isi Survei | Sudah check-out | Buka link survei, isi form | Data survei tersimpan | ✅ |
| 12 | Survei Kadaluarsa | Check-out > 7 hari | Buka link survei | Pesan "link sudah kadaluarsa" | ✅ |

### 10.2 Pengujian Keamanan

| No | Skenario | Hasil |
|---|---|---|
| 1 | Akses halaman admin tanpa login | Redirect ke halaman login | ✅ |
| 2 | Petugas mencoba akses dashboard admin | Redirect/403 Forbidden | ✅ |
| 3 | Upload file selain PDF/JPG | Validasi gagal, file ditolak | ✅ |
| 4 | Input script XSS di form | Escaped, tidak dieksekusi | ✅ |
| 5 | Submit form tanpa CSRF token | 419 Page Expired | ✅ |

### 10.3 Pengujian Kompatibilitas

| Browser | Versi | Status |
|---|---|---|
| Google Chrome | 120+ | ✅ Kompatibel |
| Mozilla Firefox | 121+ | ✅ Kompatibel |
| Microsoft Edge | 120+ | ✅ Kompatibel |
| Safari | 17+ | ✅ Kompatibel |
| Mobile Chrome (Android) | 120+ | ✅ Responsive |
| Mobile Safari (iOS) | 17+ | ✅ Responsive |

---

## 11. Hasil & Pembahasan

### 11.1 Halaman Publik yang Dihasilkan

**Landing Page** — Menampilkan informasi KKIPP, prosedur kunjungan, galeri foto momen kunjungan, lokasi/denah, dan testimonial dari sekolah yang pernah berkunjung. Dilengkapi ticker informasi kontak dan navigasi yang responsif.

**Kalender Kunjungan** — Tampilan kalender bulanan yang menampilkan:
- Tanggal yang sudah memiliki kunjungan (dengan indikator visual)
- Hari libur nasional (berwarna merah)
- Override admin (libur khusus/pembatasan sesi)
- Hari Jumat-Minggu dinonaktifkan secara default

**Form Reservasi** — Formulir multi-bagian yang mencakup:
- Informasi sekolah (nama, NPSN, alamat, kontak)
- Informasi PIC (nama, jabatan, email, telepon)
- Jadwal kunjungan (tanggal dari kalender, sesi, tempat)
- Data peserta (total + rincian per jabatan)
- Upload surat permohonan (PDF/JPG, max 1 MB)

**Tiket Digital** — Halaman dengan QR code yang dapat dicetak atau disimpan, berisi nomor registrasi dan detail kunjungan.

### 11.2 Panel Admin

**Dashboard** — Menampilkan:
- Daftar permohonan dengan filter status, rentang tanggal, dan pencarian
- Statistik jumlah per status (pending, approved, rejected, dll.)
- Widget Top 5 Sekolah Pemohon
- Widget Kunjungan Terdekat yang Disetujui

**Halaman Detail Kunjungan** — Menampilkan seluruh informasi permohonan, dokumen surat, riwayat status (audit log), dan data presensi.

### 11.3 Perbandingan Sebelum dan Sesudah

| Aspek | Sebelum (Manual) | Sesudah (Sistem) |
|---|---|---|
| Waktu proses permohonan | 2–5 hari kerja | Maks. 1 hari kerja (admin langsung dapat notifikasi) |
| Aksesibilitas pengajuan | Jam kerja, fisik/telepon | 24/7 via web |
| Konfirmasi ke pemohon | Telepon/surat (1-3 hari) | Email otomatis (< 1 menit) |
| Pencegahan double booking | Tidak ada | Otomatis oleh sistem |
| Data presensi | Kertas | Digital, terintegrasi |
| Umpan balik | Tidak ada | Survei kepuasan digital otomatis |
| Audit trail | Buku agenda (tidak lengkap) | Log digital lengkap per aksi |

---

## 12. Kendala & Solusi

### 12.1 Kendala Teknis

| No | Kendala | Solusi yang Diterapkan |
|---|---|---|
| 1 | **Kompatibilitas PostgreSQL vs SQLite** pada beberapa query aggregation | Menggunakan pendekatan `withCount()` dan `withSum()` dengan `whereHas()` sebagai filter, menghindari `HAVING` clause yang berbeda sintaksnya |
| 2 | **Upload file di lingkungan berbeda** (local vs cloud storage) | Menggunakan Laravel Storage abstraction dengan disk `public`, memudahkan switch ke cloud storage (Cloudinary/S3) di masa depan |
| 3 | **Pengiriman email gagal tidak boleh membatalkan proses utama** | Membungkus semua panggilan `Mail::send()` dalam `try-catch`, mencatat warning ke Log, dan melanjutkan proses |
| 4 | **Duplikasi kode check-in/checkout** antara Admin dan Petugas | Diekstrak ke `PresensiService` yang digunakan oleh kedua Controller |
| 5 | **Hari libur nasional bersifat dinamis** setiap tahunnya | Dikelola dalam method terpisah `getNationalHolidays()` di Service, dikombinasikan dengan override manual dari admin melalui `PengaturanKalender` |

### 12.2 Kendala Non-Teknis

| No | Kendala | Solusi |
|---|---|---|
| 1 | Kebutuhan sistem yang berkembang selama pengembangan | Menggunakan pendekatan incremental dengan migrasi database bertahap |
| 2 | Perbedaan kebiasaan pengguna admin dan petugas | Memisahkan interface admin dan petugas dengan desain yang disesuaikan kebutuhan masing-masing |

---

## 13. Kesimpulan

Pengembangan Sistem Permohonan Kunjungan Sekolah Berbasis Web untuk KKIPP UPI telah berhasil diselesaikan dengan menghasilkan sebuah platform digital yang komprehensif dan terintegrasi. Sistem ini menjawab seluruh permasalahan yang diidentifikasi pada kondisi sistem lama (manual), yaitu:

1. **Efisiensi Proses** — Proses yang sebelumnya membutuhkan beberapa hari kini dapat diselesaikan dalam hitungan jam berkat alur digital dan notifikasi email otomatis.

2. **Aksesibilitas** — Sekolah dari seluruh Indonesia dapat mengajukan permohonan kapan saja dan dari mana saja, tidak terbatas jam kerja atau jarak geografis.

3. **Transparansi** — Pemohon dapat memantau status permohonannya secara real-time melalui fitur Cek Status, mengurangi kebutuhan untuk menghubungi KKIPP secara langsung.

4. **Integritas Data** — Sistem mencegah double booking secara otomatis dan menyimpan seluruh riwayat perubahan dalam audit log yang terstruktur.

5. **Kualitas Layanan Terukur** — Mekanisme survei kepuasan digital yang terkirim otomatis memungkinkan KKIPP mengumpulkan data umpan balik secara sistematis untuk perbaikan layanan.

Dari sisi teknis, sistem berhasil dibangun dengan menerapkan standar arsitektur yang baik — **Service Pattern**, **Form Request Validation**, dan **PSR-12** — menghasilkan kode yang bersih, termodularisasi, dan mudah dikembangkan oleh pengembang berikutnya. Pemisahan concerns yang jelas antara Controller, Service, dan Model memastikan setiap komponen memiliki tanggung jawab yang tunggal dan terdefinisi.

Sistem ini merupakan langkah nyata dalam transformasi digital layanan publik UPI, sejalan dengan komitmen institusi untuk terus meningkatkan kualitas dan kemudahan layanan kepada masyarakat.

---

## 14. Saran Pengembangan

Berdasarkan pengalaman pengembangan dan kebutuhan yang teridentifikasi, berikut adalah saran untuk pengembangan sistem di masa mendatang:

### Jangka Pendek (1–3 Bulan)

1. **Migrasi Evaluasi ke Tabel Terpisah** — Fitur evaluasi saat ini menyimpan data ke kolom `catatan_admin` yang tidak semantik. Direkomendasikan membuat tabel `evaluasi_kunjungan` terpisah.

2. **Notifikasi Email Asinkron (Queue)** — Mengubah `Mail::send()` menjadi `Mail::queue()` agar pengiriman email tidak memblokir HTTP response dan meningkatkan performa aplikasi.

3. **Optimasi Caching** — Menerapkan Laravel Cache pada query kalender dan statistik dashboard yang tidak berubah sering, untuk mengurangi beban database.

### Jangka Menengah (3–6 Bulan)

4. **Integrasi API Hari Libur Nasional** — Menggantikan daftar hari libur yang hardcoded dengan integrasi API publik hari libur nasional, sehingga tidak perlu update manual setiap tahun.

5. **Laporan & Ekspor Data** — Fitur ekspor data kunjungan ke Excel/PDF untuk keperluan pelaporan internal KKIPP.

6. **Manajemen Multi-Admin** — Saat ini hanya ada satu level admin. Dapat dikembangkan dengan level: Superadmin, Admin, dan Operator dengan hak akses yang berbeda.

### Jangka Panjang (6+ Bulan)

7. **Unit Testing Komprehensif** — Dengan arsitektur Service Pattern yang sudah ada, sangat mudah untuk menambahkan unit test pada setiap Service class tanpa memerlukan database.

8. **Progressive Web App (PWA)** — Mengubah halaman publik dan petugas menjadi PWA agar dapat diinstal di smartphone dan bekerja offline (terutama untuk scanner QR petugas).

9. **Notifikasi WhatsApp** — Menambahkan notifikasi via WhatsApp Business API sebagai komplemen email, mengingat tingginya penetrasi WhatsApp di kalangan pengguna Indonesia.

---

<div align="center">

---

*Laporan ini disusun sebagai dokumentasi teknis dan akademis pengembangan sistem.*

**KKIPP — Universitas Pendidikan Indonesia**  
Jl. Dr. Setiabudhi No.229, Bandung 40154  
© 2026

</div>
