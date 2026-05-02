@component('mail::message')
# {{ $kunjungan->status === 'pending' ? 'Konfirmasi Penerimaan Pengajuan' : ($kunjungan->status === 'approved' ? '🎉 Pengajuan Kunjungan Disetujui!' : '📋 Informasi Status Pengajuan Kunjungan') }}

Yth. Bapak/Ibu **{{ $kunjungan->kontak->nama }}**,
{{ $kunjungan->sekolah->nama }}

---

@if($kunjungan->status === 'pending')
Kami telah menerima pengajuan reservasi kunjungan dari sekolah Anda. Pengajuan akan diverifikasi oleh tim Humas UPI dalam **3–5 hari kerja**.

@elseif($kunjungan->status === 'approved')
Dengan senang hati, kami informasikan bahwa pengajuan kunjungan sekolah Anda telah **disetujui**. Harap datang tepat waktu sesuai jadwal yang telah ditetapkan.

@else
Kami menyampaikan bahwa pengajuan kunjungan sekolah Anda **tidak dapat kami proses** saat ini. Untuk informasi lebih lanjut, silakan menghubungi Humas UPI.

@endif

## Detail Pengajuan

| Informasi | Keterangan |
|-----------|-----------|
| **Nomor Registrasi** | {{ $kunjungan->nomor_registrasi }} |
| **Nama Sekolah** | {{ $kunjungan->sekolah->nama }} |
| **NPSN** | {{ $kunjungan->sekolah->npsn }} |
| **Tanggal Kunjungan** | {{ $kunjungan->tanggal_format }} |
| **Sesi** | {{ $kunjungan->sesi->label ?? '-' }} |
| **Tempat** | {{ $kunjungan->tempat->nama ?? '-' }} |
| **Jumlah Peserta** | {{ number_format($kunjungan->jumlah_peserta) }} orang |
| **Status** | {{ strtoupper($kunjungan->status_label) }} |

@if($kunjungan->catatan_admin)
## Catatan dari Admin

> {{ $kunjungan->catatan_admin }}

@endif

@component('mail::button', ['url' => route('cek-status') . '?query=' . $kunjungan->nomor_registrasi, 'color' => 'primary'])
Pantau Status Pengajuan
@endcomponent

---

Untuk pertanyaan lebih lanjut, hubungi kami:
- 📞 **Telepon:** (022) 2013163
- 📲 **WhatsApp:** 085133332559
- ✉️ **Email:** humas@upi.edu
- 🕐 **Jam Operasional:** Senin–Kamis, 09.00–15.00 WIB

Salam hormat,
**Humas Universitas Pendidikan Indonesia**

@endcomponent
