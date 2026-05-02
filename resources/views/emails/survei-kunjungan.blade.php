@component('mail::message')

# Terima Kasih Atas Kunjungan Anda ke UPI!

Halo **{{ $kunjungan->kontak->nama }}**,
{{ $kunjungan->sekolah->nama }}

Kunjungan Anda ke Universitas Pendidikan Indonesia pada **{{ $kunjungan->tanggal_format }}** telah berhasil dilaksanakan. Kami sangat senang menyambut rombongan Anda!

Untuk membantu kami meningkatkan kualitas layanan, mohon luangkan waktu mengisi **Form Survei Kepuasan** berikut (sekitar 2–3 menit):

@component('mail::button', ['url' => $surveiUrl, 'color' => 'primary'])
Isi Form Survei Kepuasan
@endcomponent

> ⚠️ Form survei hanya dapat diisi dalam **7 hari** setelah kunjungan.

---

Terima kasih atas partisipasi dan kepercayaan Anda kepada UPI!

Salam hangat,
**Humas & Protokol UPI**
📧 humas@upi.edu · 📞 (022) 2013163 · 📲 085133332559

@endcomponent
