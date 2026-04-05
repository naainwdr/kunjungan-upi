@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Sistem Reservasi Kunjungan Sekolah ke Universitas Pendidikan Indonesia. Ajukan kunjungan edukatif ke kampus UPI secara online dengan mudah dan cepat.')

@section('content')
{{-- Hero Section --}}
<section class="bg-upi-red text-white">
    <div class="max-w-6xl mx-auto px-4 py-12 md:py-16 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-semibold px-4 py-1.5 rounded-full mb-4">
            🎓 Humas Universitas Pendidikan Indonesia
        </div>
        <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-3">
            Reservasi Kunjungan Sekolah
        </h1>
        <p class="text-red-100 text-base md:text-lg max-w-2xl mx-auto mb-8">
            Rencanakan kunjungan edukatif sekolah Anda ke UPI. Proses pengajuan mudah, cepat, dan dapat dipantau secara <em>online</em>.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('reservasi.create') }}" id="btn-ajukan-reservasi" class="bg-upi-gold text-upi-black px-6 py-3 rounded-lg font-bold hover:opacity-90 transition-opacity">
                📋 Ajukan Reservasi Sekarang
            </a>
            <a href="{{ route('cek-status') }}" id="btn-cek-status" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-upi-red transition-colors">
                🔍 Cek Status Pengajuan
            </a>
        </div>
    </div>
</section>

{{-- Prosedur Kunjungan --}}
<section class="max-w-6xl mx-auto px-4 py-10">
    <h2 class="text-xl font-bold text-upi-red text-center mb-2">Prosedur Kunjungan</h2>
    <p class="text-gray-500 text-sm text-center mb-8">Ikuti langkah berikut untuk mengajukan kunjungan ke UPI</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
        @foreach([
            ['1', '📝', 'Isi Formulir', 'Lengkapi data sekolah, tanggal kunjungan, dan jumlah peserta melalui formulir online.'],
            ['2', '📄', 'Unggah Surat', 'Sertakan surat permohonan resmi dari kepala sekolah (PDF/JPG, maks. 1 MB).'],
            ['3', '⏳', 'Menunggu Verifikasi', 'Tim Humas UPI akan memverifikasi pengajuan dalam 3–5 hari kerja.'],
            ['4', '✅', 'Terima Konfirmasi', 'Notifikasi persetujuan atau penolakan dikirim melalui email sekolah.'],
        ] as $step)
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center shadow-sm">
            <div class="w-10 h-10 bg-upi-red text-white rounded-full flex items-center justify-center font-bold text-base mx-auto mb-3">
                {{ $step[0] }}
            </div>
            <div class="text-2xl mb-2">{{ $step[1] }}</div>
            <div class="font-semibold text-upi-red text-sm mb-1">{{ $step[2] }}</div>
            <p class="text-gray-500 text-xs leading-relaxed">{{ $step[3] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- Informasi Penting --}}
<section class="bg-upi-cream border-t-4 border-upi-gold">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h2 class="text-lg font-bold text-upi-red mb-4">ℹ️ Informasi Penting</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <ul class="space-y-2">
                <li class="flex gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Pengajuan dilakukan minimal <strong>7 hari kerja</strong> sebelum tanggal kunjungan.</li>
                <li class="flex gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Kunjungan hanya pada hari <strong>Senin–Jumat</strong>, pukul 08.00–15.00 WIB.</li>
                <li class="flex gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Kapasitas maksimal <strong>500 peserta</strong> per kunjungan.</li>
            </ul>
            <ul class="space-y-2">
                <li class="flex gap-2"><span class="text-blue-600 font-bold mt-0.5">→</span> Surat permohonan harus <strong>berkop surat</strong> dan ditandatangani Kepala Sekolah.</li>
                <li class="flex gap-2"><span class="text-blue-600 font-bold mt-0.5">→</span> Simpan <strong>Nomor Registrasi</strong> yang diberikan untuk memantau status.</li>
                <li class="flex gap-2"><span class="text-blue-600 font-bold mt-0.5">→</span> Untuk informasi lebih lanjut, hubungi Humas UPI di <strong>(022) 2013163</strong>.</li>
            </ul>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="max-w-6xl mx-auto px-4 py-10 text-center">
    <h2 class="text-lg font-bold text-gray-700 mb-2">Siap Mengajukan Kunjungan?</h2>
    <p class="text-gray-500 text-sm mb-5">Proses pengajuan hanya membutuhkan waktu beberapa menit.</p>
    <a href="{{ route('reservasi.create') }}" class="bg-upi-red text-white px-8 py-3 rounded-lg font-bold hover:bg-upi-dark transition-colors inline-block">
        Mulai Pengajuan →
    </a>
</section>
@endsection
