@component('mail::message')

# Form Evaluasi Kunjungan

Halo **{{ $kunjungan->kontak->nama }}**,

Terima kasih telah melaksanakan kunjungan ke Universitas Pendidikan Indonesia pada **{{ $kunjungan->tanggal_format }}**.

Untuk meningkatkan kualitas layanan kami, mohon mengisi form evaluasi berikut:

@component('mail::button', ['url' => route('survei.form', $kunjungan->nomor_registrasi), 'color' => 'primary'])
Isi Form Evaluasi
@endcomponent

Atau akses langsung melalui link berikut:  
{{ route('survei.form', $kunjungan->nomor_registrasi) }}

Form evaluasi dapat diisi dalam waktu 7 hari setelah email ini dikirim.

Terima kasih atas partisipasi Anda!

Salam,  
**KKIPP UPI**  
📧 humas@upi.edu  
📞 085133332559 (WhatsApp)

@endcomponent