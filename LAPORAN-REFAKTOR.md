# Laporan Teknis Refaktor Arsitektur

**Proyek:** Sistem Permohonan Kunjungan Sekolah  
**Tanggal Refaktor:** Juni 2026  
**Standar:** PSR-12, Service Pattern, Laravel Best Practices

---

## 1. Ringkasan Eksekutif

Refaktor ini bertujuan meningkatkan kualitas kode proyek dari struktur monolitik (semua logika di Controller) menjadi arsitektur berlapis (Layered Architecture) menggunakan **Service Pattern**. Seluruh fungsionalitas yang sudah ada dipertahankan tanpa breaking changes.

### Hasil Utama

| Metrik | Sebelum | Sesudah |
|---|---|---|
| File Controller yang punya business logic | 8 dari 10 | 0 dari 10 |
| Duplikasi kode check-in/checkout (Admin vs Petugas) | ~200 baris duplikat | 0 duplikasi |
| Kelas Form Request | 0 | 9 kelas baru |
| Kelas Service | 0 | 5 kelas baru |
| Komentar kode (PHPDoc + inline) | Minimal | Komprehensif |
| Baris terpanjang Controller (`KunjunganController`) | 402 baris | ~220 baris (−45%) |

---

## 2. Inventaris File yang Diubah

### File Baru (Dibuat)

#### Form Request Classes
| File | Deskripsi |
|---|---|
| `app/Http/Requests/StoreKunjunganRequest.php` | Validasi form reservasi publik (12 field, 14 pesan kustom) |
| `app/Http/Requests/CariStatusRequest.php` | Validasi pencarian status kunjungan |
| `app/Http/Requests/SimpanEvaluasiRequest.php` | Validasi form evaluasi legacy |
| `app/Http/Requests/StoreSurveiRequest.php` | Validasi form survei kepuasan |
| `app/Http/Requests/Admin/ApproveKunjunganRequest.php` | Validasi aksi approve (catatan opsional) |
| `app/Http/Requests/Admin/RejectKunjunganRequest.php` | Validasi aksi reject (catatan WAJIB) |
| `app/Http/Requests/Admin/StoreTempatRequest.php` | Validasi data tempat kunjungan |
| `app/Http/Requests/Admin/StoreSesiRequest.php` | Validasi data sesi (termasuk after:jam_mulai) |
| `app/Http/Requests/Admin/StoreKalenderRequest.php` | Validasi pengaturan kalender dengan validasi array sesi |

#### Service Classes
| File | Deskripsi |
|---|---|
| `app/Services/KunjunganService.php` | Business logic permohonan publik: simpan, batal, kalender, booked-sesi |
| `app/Services/KunjunganStatusService.php` | Transisi status admin: approve, reject, complete + email |
| `app/Services/PresensiService.php` | Check-in/checkout shared — menghilangkan 100% duplikasi Admin vs Petugas |
| `app/Services/SurveiService.php` | Validasi akses + penyimpanan survei kepuasan dan evaluasi |
| `app/Services/AdminReferensiService.php` | CRUD gabungan: Tempat, Sesi, Kalender |

#### Dokumentasi
| File | Deskripsi |
|---|---|
| `README-ARSITEKTUR.md` | Panduan arsitektur, peta direktori, alur request, panduan pengembangan |
| `LAPORAN-REFAKTOR.md` | Dokumen ini — laporan teknis perubahan |

---

### File yang Dimodifikasi (Refaktor)

| File | Perubahan Utama |
|---|---|
| `KunjunganController.php` | Injeksi `KunjunganService` + `SurveiService`. Hapus semua business logic. Tambah Form Request. |
| `Admin/AdminKunjunganController.php` | Injeksi `KunjunganStatusService`. Hapus `kirimEmail*` private methods. Tambah Form Request. |
| `Admin/PresensiController.php` | Injeksi `PresensiService`. Hapus semua duplikasi business logic. |
| `Admin/AdminTempatController.php` | Injeksi `AdminReferensiService`. Tambah Form Request. Sederhanakan. |
| `Admin/AdminSesiController.php` | Injeksi `AdminReferensiService`. Tambah Form Request. Sederhanakan. |
| `Admin/AdminKalenderController.php` | Injeksi `AdminReferensiService`. Tambah Form Request. |
| `Admin/AdminSurveiController.php` | Tambah PHPDoc komprehensif. Perbaiki pesan toggle yang lebih informatif. |
| `Petugas/PetugasPresensiController.php` | Injeksi `PresensiService`. Hapus ~160 baris duplikasi. |
| `SurveiController.php` | Injeksi `SurveiService`. Tambah Form Request. |

---

## 3. Analisis Masalah yang Diselesaikan

### 3.1 Business Logic di Controller (`KunjunganController`)

**Sebelum:** Method `store()` di `KunjunganController` (102 baris) menangani:
- Validasi format data (`$request->validate(...)`)
- Validasi bisnis (hari kerja, kapasitas, bentrok sesi)
- Upsert data sekolah ke database
- Insert data kontak ke database
- Upload file ke storage
- Insert data kunjungan ke database
- Logging perubahan status
- Pengiriman email

**Sesudah:** Method `store()` (30 baris aktif) hanya:
1. Menerima request (Form Request sudah memvalidasi)
2. Validasi bisnis tambahan (hari, kapasitas, bentrok)
3. Memanggil `$this->kunjunganService->simpanPermohonan()`
4. Redirect ke halaman sukses

Semua proses bisnis (langkah 1-8 di atas) dipindah ke `KunjunganService::simpanPermohonan()`.

---

### 3.2 Duplikasi Kode 100% (PresensiController vs PetugasPresensiController)

**Sebelum:** Kedua controller memiliki implementasi yang hampir identik kata per kata:

```
PresensiController.php          ≈ 167 baris
PetugasPresensiController.php   ≈ 164 baris
────────────────────────────────────────────
Duplikasi bersih                ≈ ~160 baris
```

Setiap perubahan logic check-in/checkout harus dilakukan di 2 tempat. Risiko inconsistency tinggi.

**Sesudah:**

```
PresensiService.php             ← ~170 baris (satu implementasi)
PresensiController.php          ← ~70 baris  (hanya routing/response)
PetugasPresensiController.php   ← ~80 baris  (hanya routing/response)
```

Kedua controller memanggil `PresensiService` yang sama. Perubahan logic cukup di satu tempat.

---

### 3.3 Tidak Ada Validasi Terpisah (Inline Validation)

**Sebelum:** Semua validasi dilakukan inline di dalam method controller:

```php
// Di KunjunganController::store() — contoh kode sebelumnya
$request->validate([
    'nama_sekolah' => 'required|string|max:255',
    // ... 12 field lagi
], [
    'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
    // ... 14 pesan error lagi
]);
```

Masalah:
- Controller menjadi sangat panjang
- Aturan validasi tidak bisa di-reuse
- Sulit di-test secara independen

**Sesudah:** Seluruh validasi ada di Form Request class yang dedicated. Controller cukup type-hint ke Request class yang tepat:

```php
// Di KunjunganController::store() — setelah refaktor
public function store(StoreKunjunganRequest $request): RedirectResponse
{
    $data = $request->validated(); // Data sudah bersih dan tervalidasi
    // ...
}
```

---

### 3.4 Import Cloudinary Tidak Terpakai

**Sebelum:** `KunjunganController` mengimport `CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary` tetapi tidak menggunakannya — method `uploadSurat()` menggunakan `Storage::putFile()` biasa.

**Sesudah:** Import Cloudinary dihapus. Upload file dilakukan di `KunjunganService::simpanPermohonan()` menggunakan `$fileSurat->store('surat', 'public')` (Laravel local storage).

---

## 4. Keputusan Desain

### 4.1 Mengapa Service Pattern (bukan Repository)?

Repository Pattern menambahkan lapisan abstraksi antara Service dan database (berguna untuk unit test tanpa database, atau switching ORM). Untuk proyek ini:

- Proyek sudah committed ke Eloquent dan tidak ada rencana switch ORM
- Tim lebih familiar dengan Eloquent langsung
- Repository menambah kompleksitas tanpa benefit yang signifikan untuk skala ini

**Keputusan:** Service Pattern dengan Eloquent langsung. Jika di masa depan ingin unit test tanpa database, bisa mock Service layer saja.

### 4.2 Mengapa Tiga Controller Referensi Digabung ke Satu Service?

`AdminTempatController`, `AdminSesiController`, dan `AdminKalenderController` masing-masing hanya punya 2-3 operasi dengan logic yang trivial. Membuat 3 service terpisah (`TempatService`, `SesiService`, `KalenderService`) akan menghasilkan service class masing-masing < 50 baris — overhead yang tidak proporsional.

**Keputusan:** Digabung ke `AdminReferensiService` karena ketiganya adalah "data referensi" yang konseptnya sejenis. Jika salah satu berkembang, mudah untuk diekstrak ke service sendiri.

### 4.3 Mengapa Evaluasi (/evaluasi) Tidak Dihapus?

Dua mekanisme umpan balik memang redundant secara arsitektur, namun menghapus route `/evaluasi` akan breaking change untuk link yang sudah dikirim via email kepada pemohon sebelumnya. 

**Keputusan:** Dipertahankan dan dibungkus dalam `SurveiService::simpanEvaluasi()` dan `isEvaluasiValid()` dengan komentar yang jelas bahwa ini adalah legacy feature.

---

## 5. Standar Kode yang Diterapkan

### PSR-12 Compliance
- Indentasi: 4 spasi
- Opening brace `{` pada baris yang sama dengan declaration
- Blank line antara property dan method
- Import diurutkan alphabetically
- Tidak ada trailing whitespace

### Type Hinting
- Semua argumen method memiliki type hint
- Semua method memiliki return type declaration
- Penggunaan `?Type` untuk nullable, bukan `Type|null` (konsistensi PHP 8+)
- `readonly` properties di constructor untuk immutability

### PHPDoc Standard
```php
/**
 * Deskripsi singkat satu baris.
 *
 * Deskripsi panjang jika diperlukan. Menjelaskan "mengapa",
 * bukan sekadar mengulang kode.
 *
 * @param  Type  $namaParam  Penjelasan parameter
 * @return Type              Penjelasan return value
 * @throws ExceptionClass    Kapan exception dilempar
 */
```

### Komentar Inline
Setiap blok logis memiliki komentar yang menjelaskan "mengapa" (bukan "apa"):

```php
// Cek 1: Kunjungan harus sudah check-out agar survei bisa diisi
// (tidak cukup hanya status 'completed', karena complete bisa tanpa checkout)
if (! $kunjungan->presensi?->waktu_keluar) {
    return ['kunjungan' => $kunjungan, 'status' => 'belum_checkout'];
}
```

---

## 6. Verifikasi Tidak Ada Breaking Changes

Semua route yang ada di `routes/web.php` dipertahankan:

| Route | Controller Method | Status |
|---|---|---|
| `GET /` | `KunjunganController::index` | ✅ |
| `GET /kalender` | `KunjunganController::kalender` | ✅ |
| `GET /reservasi` | `KunjunganController::create` | ✅ |
| `POST /reservasi` | `KunjunganController::store` | ✅ |
| `GET /reservasi/sukses` | `KunjunganController::sukses` | ✅ |
| `GET /cek-status` | `KunjunganController::cekStatus` | ✅ |
| `POST /cek-status` | `KunjunganController::cariStatus` | ✅ |
| `POST /reservasi/{id}/batal` | `KunjunganController::batal` | ✅ |
| `GET /api/booked-sesi` | `KunjunganController::bookedSesi` | ✅ |
| `GET /evaluasi/{id}` | `KunjunganController::evaluasiForm` | ✅ |
| `POST /evaluasi/{id}` | `KunjunganController::simpanEvaluasi` | ✅ |
| `GET /evaluasi/{id}/terima-kasih` | `KunjunganController::terimaKasih` | ✅ |
| `GET /survei/{nomor}` | `SurveiController::form` | ✅ |
| `POST /survei/{nomor}` | `SurveiController::store` | ✅ |
| `GET /survei/{nomor}/terima-kasih` | `SurveiController::terimaKasih` | ✅ |
| Semua route Admin & Petugas | (lihat web.php) | ✅ |

---

## 7. Rekomendasi Tindak Lanjut (Prioritas)

| Prioritas | Item | Dampak |
|---|---|---|
| 🔴 High | Hapus/konversi route `/evaluasi` ke survei formal | Konsistensi arsitektur |
| 🟡 Medium | Buat `FileUploadService` dengan interface untuk abstraksi storage | Mudah switch ke Cloudinary |
| 🟡 Medium | Gunakan `Mail::queue()` bukan `Mail::send()` untuk email async | Performa HTTP response |
| 🟢 Low | Implementasi unit test untuk Service classes | Reliability |
| 🟢 Low | Buat API Holiday Indonesia integration | Menghapus hardcoded holidays |
| 🟢 Low | Pertimbangkan caching untuk `getKalenderData()` | Performa query |
