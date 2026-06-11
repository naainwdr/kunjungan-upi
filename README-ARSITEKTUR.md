# Arsitektur Kode — Sistem Permohonan Kunjungan Sekolah

> Dokumentasi ini menjelaskan struktur arsitektur kode proyek setelah refaktor.
> Dibaca oleh: developer yang ingin memahami atau mengembangkan fitur baru.

---

## Daftar Isi

1. [Prinsip Arsitektur](#prinsip-arsitektur)
2. [Peta Direktori](#peta-direktori)
3. [Layer Arsitektur](#layer-arsitektur)
4. [Alur Request → Response](#alur-request--response)
5. [Panduan: Cara Menambah Fitur Baru](#panduan-cara-menambah-fitur-baru)
6. [Deskripsi Tiap Kelas](#deskripsi-tiap-kelas)
7. [Catatan Teknis & Rekomendasi](#catatan-teknis--rekomendasi)

---

## Prinsip Arsitektur

Proyek ini menggunakan **Service Pattern** dengan filosofi:

| Prinsip | Penerapan |
|---|---|
| **Separation of Concerns (SoC)** | Controller ≠ Business Logic. Controller hanya menerima request dan mengembalikan response. |
| **Single Responsibility** | Setiap kelas punya satu alasan untuk berubah. |
| **DRY (Don't Repeat Yourself)** | `PresensiService` digunakan bersama oleh Admin dan Petugas — tidak ada duplikasi kode. |
| **Thin Controller** | Rata-rata method Controller hanya 5-15 baris aktif. |
| **PSR-12** | Semua kode mengikuti standar coding PHP PSR-12. |

---

## Peta Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── KunjunganController.php          ← Halaman publik (reservasi, cek status, evaluasi)
│   │   ├── SurveiController.php             ← Form survei kepuasan publik
│   │   ├── Admin/
│   │   │   ├── AuthController.php           ← Login/logout admin
│   │   │   ├── AdminKunjunganController.php ← Dashboard, approve, reject, complete
│   │   │   ├── PresensiController.php       ← Scanner QR, check-in/out (Admin)
│   │   │   ├── AdminTempatController.php    ← CRUD tempat kunjungan
│   │   │   ├── AdminSesiController.php      ← CRUD sesi kunjungan
│   │   │   ├── AdminKalenderController.php  ← Override kalender
│   │   │   └── AdminSurveiController.php    ← Manajemen survei & testimonial
│   │   └── Petugas/
│   │       ├── PetugasAuthController.php    ← Login/logout petugas
│   │       └── PetugasPresensiController.php← Scanner QR, check-in/out (Petugas)
│   │
│   ├── Requests/                            ← LAYER VALIDASI
│   │   ├── StoreKunjunganRequest.php        ← Validasi form reservasi publik
│   │   ├── CariStatusRequest.php            ← Validasi pencarian status
│   │   ├── SimpanEvaluasiRequest.php        ← Validasi form evaluasi (legacy)
│   │   ├── StoreSurveiRequest.php           ← Validasi form survei kepuasan
│   │   └── Admin/
│   │       ├── ApproveKunjunganRequest.php  ← Validasi aksi approve
│   │       ├── RejectKunjunganRequest.php   ← Validasi aksi reject (catatan wajib)
│   │       ├── StoreTempatRequest.php       ← Validasi data tempat
│   │       ├── StoreSesiRequest.php         ← Validasi data sesi
│   │       └── StoreKalenderRequest.php     ← Validasi pengaturan kalender
│   │
│   └── Middleware/
│       ├── EnsureIsAdmin.php                ← Guard role admin
│       └── EnsureIsPetugas.php              ← Guard role petugas
│
├── Services/                                ← LAYER BUSINESS LOGIC
│   ├── KunjunganService.php                 ← Simpan permohonan, batalkan, kalender, booked sesi
│   ├── KunjunganStatusService.php           ← Approve, reject, complete + email notifikasi
│   ├── PresensiService.php                  ← Check-in, check-out (digunakan Admin & Petugas)
│   ├── SurveiService.php                    ← Validasi akses + simpan survei/evaluasi
│   └── AdminReferensiService.php            ← CRUD tempat, sesi, dan kalender
│
└── Models/                                  ← LAYER DATA
    ├── Kunjungan.php
    ├── Sekolah.php
    ├── KontakSekolah.php
    ├── KunjunganPresensi.php
    ├── KunjunganLog.php
    ├── SurveiKepuasan.php
    ├── Sesi.php
    ├── Tempat.php
    ├── PengaturanKalender.php
    └── User.php
```

---

## Layer Arsitektur

```
HTTP Request
    │
    ▼
┌─────────────────────┐
│   Form Request      │  ← Validasi format & aturan Laravel (required, email, exists, dll)
│   (Requests/)       │
└──────────┬──────────┘
           │ Data tervalidasi
           ▼
┌─────────────────────┐
│   Controller        │  ← Terima request, delegasikan ke service, kembalikan response
│   (Controllers/)    │
└──────────┬──────────┘
           │ Panggil service
           ▼
┌─────────────────────┐
│   Service           │  ← Business logic: validasi bisnis, manipulasi data, kirim email
│   (Services/)       │
└──────────┬──────────┘
           │ Operasi database
           ▼
┌─────────────────────┐
│   Model / Eloquent  │  ← Query database, relasi, accessor, scope
│   (Models/)         │
└──────────┬──────────┘
           │
           ▼
        Database
```

---

## Alur Request → Response

### Contoh: Pengajuan Permohonan Kunjungan Baru

```
POST /reservasi
    │
    ▼
StoreKunjunganRequest::rules()         ← Validasi: required, email, exists, max, dll.
    │ (jika gagal → 422 redirect back)
    ▼
KunjunganController::store()           ← Validasi bisnis: hari layanan, kapasitas, bentrok sesi
    │
    ▼
KunjunganService::simpanPermohonan()   ← Upsert sekolah, buat kontak, upload file, create kunjungan
    │                                     logStatus('pending'), kirimEmailKonfirmasi()
    ▼
redirect → reservasi.sukses
```

### Contoh: Admin Menyetujui Kunjungan

```
POST /admin/kunjungan/{id}/approve
    │
    ▼
ApproveKunjunganRequest::rules()       ← Validasi: catatan_admin opsional, max:500
    │
    ▼
AdminKunjunganController::approve()   ← Ambil ID admin dari auth()
    │
    ▼
KunjunganStatusService::approve()     ← logStatus('approved'), update DB, kirimEmailStatus()
    │
    ▼
redirect → admin.dashboard
```

### Contoh: Check-In via Scanner (Admin atau Petugas)

```
POST /admin/presensi/{id}/checkin   ATAU   POST /petugas/presensi/{id}/checkin
    │                                               │
    ▼                                               ▼
PresensiController::checkIn()          PetugasPresensiController::checkIn()
    │                                               │
    └─────────────────┬─────────────────────────────┘
                      │  (keduanya memanggil service yang sama)
                      ▼
              PresensiService::checkIn()  ← Validasi status, buat/update record presensi
                      │
                      ▼
              JSON response atau Redirect
```

---

## Panduan: Cara Menambah Fitur Baru

### Scenario: Menambahkan fitur "Reschedule Kunjungan"

**Langkah 1 — Buat Form Request:**
```bash
php artisan make:request RescheduleKunjunganRequest
```
Tambahkan rules: `tanggal_baru`, `sesi_id_baru`, validasi H+10, dll.

**Langkah 2 — Tambahkan Method di Service:**
```php
// Di KunjunganService.php (atau buat RescheduleService.php jika kompleks)
public function reschedule(Kunjungan $kunjungan, array $data): array
{
    // Validasi: hanya bisa reschedule jika status pending atau approved
    // Update tanggal dan sesi
    // Log perubahan
    // Kirim email notifikasi
}
```

**Langkah 3 — Tambahkan Method di Controller:**
```php
// Di KunjunganController.php
public function reschedule(RescheduleKunjunganRequest $request, string $id): RedirectResponse
{
    $kunjungan = Kunjungan::where('nomor_registrasi', $id)->firstOrFail();
    $result = $this->kunjunganService->reschedule($kunjungan, $request->validated());
    // ... return response
}
```

**Langkah 4 — Tambahkan Route:**
```php
// Di routes/web.php
Route::post('/reservasi/{id}/reschedule', [KunjunganController::class, 'reschedule'])
    ->name('reservasi.reschedule');
```

**Prinsip:** Controller tidak boleh berisi business logic. Jika method controller lebih dari 20-30 baris, pertimbangkan untuk memindahkan logika ke service.

---

## Deskripsi Tiap Kelas

### Services

| Kelas | Tanggung Jawab |
|---|---|
| `KunjunganService` | Menyimpan permohonan baru, membatalkan, menghasilkan data booked-sesi, data kalender bulanan |
| `KunjunganStatusService` | Transisi status: approve, reject, complete. Termasuk audit log dan email notifikasi. |
| `PresensiService` | Check-in dan check-out — **digunakan bersama** oleh Admin dan Petugas. Termasuk auto-complete dan email survei. |
| `SurveiService` | Validasi akses form survei/evaluasi, menyimpan data survei kepuasan. |
| `AdminReferensiService` | CRUD Tempat, Sesi, dan Kalender (digabung karena ketiganya adalah data referensi yang kecil) |

### Form Requests

| Kelas | Digunakan Oleh |
|---|---|
| `StoreKunjunganRequest` | `KunjunganController::store()` |
| `CariStatusRequest` | `KunjunganController::cariStatus()` |
| `SimpanEvaluasiRequest` | `KunjunganController::simpanEvaluasi()` |
| `StoreSurveiRequest` | `SurveiController::store()` |
| `Admin\ApproveKunjunganRequest` | `AdminKunjunganController::approve()` |
| `Admin\RejectKunjunganRequest` | `AdminKunjunganController::reject()` |
| `Admin\StoreTempatRequest` | `AdminTempatController::store()` dan `update()` |
| `Admin\StoreSesiRequest` | `AdminSesiController::store()` dan `update()` |
| `Admin\StoreKalenderRequest` | `AdminKalenderController::store()` |

---

## Catatan Teknis & Rekomendasi

### 🔴 Masalah Arsitektur yang Diketahui (Technical Debt)

1. **Dua Mekanisme Umpan Balik:**
   - `/evaluasi/{id}` — menyimpan ke kolom `catatan_admin` di tabel kunjungan (legacy, tidak ideal)
   - `/survei/{nomor}` — menyimpan ke tabel `survei_kepuasan` (recommended)
   
   **Rekomendasi:** Buat migrasi untuk tabel `evaluasi_kunjungan` dan pindahkan data dari `catatan_admin`. Nonaktifkan route `/evaluasi` setelah migrasi selesai.

2. **Cloudinary Import Tidak Terpakai:**
   Import `CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary` sudah dihapus dari `KunjunganController`. File surat saat ini disimpan ke local storage (`storage/app/public/surat`). Jika ingin mengaktifkan Cloudinary, buat `FileUploadService` dengan interface `FileUploadInterface` untuk abstraksi.

3. **Hari Libur Nasional Hardcoded:**
   Daftar libur nasional ada di `KunjunganService::getNationalHolidays()`. Setiap tahun perlu diperbarui manual. 
   **Rekomendasi:** Gunakan API Hari Libur Nasional (misal: `api-harilibur.vercel.app`) atau buat tabel `hari_libur_nasional` di database.

### 🟡 Saran Pengembangan

- **Unit Testing:** Dengan Service Pattern, business logic kini mudah diuji dengan `php artisan make:test` tanpa perlu hit database atau HTTP.
- **Service Interface:** Jika di masa depan ingin swap implementasi (misal: ganti provider email), buat interface `KunjunganServiceInterface` dan bind di `AppServiceProvider`.
- **Queue untuk Email:** Notifikasi email saat ini dikirim synchronous. Untuk performa lebih baik, gunakan `Mail::to()->queue()` dengan Laravel Queue.
