@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Sistem Permohonan Kunjungan Sekolah ke Universitas Pendidikan Indonesia. Ajukan kunjungan edukatif ke kampus UPI secara online dengan mudah dan cepat.')

@section('content')

{{-- ══════════════════════════════════════════════
     HERO SECTION
     ══════════════════════════════════════════════ --}}
<section class="relative bg-upi-red text-white overflow-hidden">
    {{-- Decorative BG pattern --}}
    <div class="absolute inset-0 pointer-events-none opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-upi-gold rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full translate-y-1/3 -translate-x-1/4"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4 py-14 md:py-20 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-semibold px-4 py-1.5 rounded-full mb-5">
            🎓 Humas Universitas Pendidikan Indonesia
        </div>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold leading-tight mb-4">
            Permohonan Kunjungan Sekolah
        </h1>
        <p class="text-red-200 text-base md:text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
            Rencanakan kunjungan edukatif sekolah Anda ke kampus UPI. Proses pengajuan mudah, cepat, dan dapat dipantau secara <em>online</em>.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('kalender') }}" id="btn-ajukan-reservasi"
               class="bg-upi-gold text-upi-black px-7 py-3.5 rounded-xl font-bold hover:opacity-90 transition-all hover:scale-105 shadow-lg shadow-black/30 inline-flex items-center justify-center gap-2">
                📅 Pilih Tanggal Kunjungan
            </a>
            <a href="{{ route('cek-status') }}" id="btn-cek-status"
               class="border-2 border-white/70 text-white px-7 py-3.5 rounded-xl font-semibold hover:bg-white hover:text-upi-red transition-all inline-flex items-center justify-center gap-2">
                🔍 Cek Status Pengajuan
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     PROSEDUR (Step Cards)
     ══════════════════════════════════════════════ --}}
<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-bold text-upi-red mb-2">Prosedur Permohonan Kunjungan</h2>
        <p class="text-gray-400 text-sm">Ikuti 4 langkah mudah berikut untuk mengajukan kunjungan ke UPI</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 relative">
        {{-- Connector line (desktop) --}}
        <div class="hidden md:block absolute top-10 left-[12.5%] right-[12.5%] h-px bg-gradient-to-r from-transparent via-upi-red/30 to-transparent"></div>

        @foreach([
            ['1', '📅', 'Pilih Tanggal', 'Buka kalender dan pilih tanggal kunjungan yang tersedia. Hari libur ditandai merah.', 'bg-red-50 border-red-200'],
            ['2', '📝', 'Isi Formulir',  'Lengkapi data sekolah, pilih jam kunjungan (min. 2 jam), dan upload surat resmi.',   'bg-orange-50 border-orange-200'],
            ['3', '⏳', 'Verifikasi',    'Tim KKIPP UPI akan memverifikasi pengajuan Anda dalam 3–5 hari kerja.',              'bg-yellow-50 border-yellow-200'],
            ['4', '✅', 'Konfirmasi',    'Notifikasi status persetujuan dikirim otomatis ke email penanggungjawab.',            'bg-green-50 border-green-200'],
        ] as $step)
        <div class="relative bg-white border {{ $step[4] }} rounded-2xl p-5 text-center shadow-sm hover:shadow-md transition-shadow">
            <div class="w-11 h-11 bg-upi-red text-white rounded-full flex items-center justify-center font-bold text-base mx-auto mb-3 shadow-md shadow-red-200 relative z-10">
                {{ $step[0] }}
            </div>
            <div class="text-3xl mb-2">{{ $step[1] }}</div>
            <div class="font-bold text-gray-800 text-sm mb-1.5">{{ $step[2] }}</div>
            <p class="text-gray-500 text-xs leading-relaxed">{{ $step[3] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══════════════════════════════════════════════
     INFORMASI PENTING — Modern Card Grid
     ══════════════════════════════════════════════ --}}
<section class="py-12 bg-gradient-to-br from-gray-900 via-upi-black to-gray-950 text-white relative overflow-hidden">
    {{-- BG decor --}}
    <div class="absolute inset-0 pointer-events-none opacity-5">
        <div class="absolute top-0 left-0 w-full h-full"
             style="background-image: radial-gradient(#FFCC00 1px, transparent 1px); background-size: 24px 24px;"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4">
        <div class="text-center mb-10">
            <span class="inline-block bg-upi-gold/10 border border-upi-gold/30 text-upi-gold text-xs font-bold px-4 py-1.5 rounded-full mb-3">
                ℹ️ KETENTUAN KUNJUNGAN
            </span>
            <h2 class="text-2xl font-bold text-white">Informasi Penting</h2>
            <p class="text-gray-400 text-sm mt-1">Pastikan Anda memahami ketentuan berikut sebelum mengajukan permohonan</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['🗓️', 'Pengajuan Minimal 7 Hari',    'Permohonan harus diajukan minmal 7 hari sebelum tanggal kunjungan.',  'border-blue-500/30  bg-blue-500/5',   'text-blue-400'],
                ['⏰', 'Jam Kerja Senin–Jumat',        'Kunjungan hanya diterima pada hari kerja, pukul 08.00–16.00 WIB.',    'border-amber-500/30 bg-amber-500/5',  'text-amber-400'],
                ['👥', 'Kapasitas 500 Peserta',        'Maksimal 500 orang termasuk guru pendamping setiap kunjungan.',       'border-green-500/30 bg-green-500/5',  'text-green-400'],
                ['📄', 'Surat Resmi Berkop',           'Wajib melampirkan surat permohonan berkop sekolah dan tanda tangan Kepala Sekolah.', 'border-purple-500/30 bg-purple-500/5','text-purple-400'],
                ['🔖', 'Simpan Nomor Registrasi',      'Catat nomor registrasi Anda untuk memantau status pengajuan kapan saja.', 'border-pink-500/30 bg-pink-500/5',  'text-pink-400'],
                ['📞', 'Butuh Bantuan?',               'Hubungi Humas UPI di (022) 2013163 atau email humas@upi.edu.', 'border-teal-500/30 bg-teal-500/5', 'text-teal-400'],
            ] as [$icon, $title, $desc, $card, $iconCls])
            <div class="border {{ $card }} rounded-2xl p-5 backdrop-blur-sm hover:scale-[1.02] transition-transform">
                <div class="flex items-start gap-3">
                    <span class="text-2xl flex-shrink-0 mt-0.5">{{ $icon }}</span>
                    <div>
                        <h3 class="font-bold text-sm {{ $iconCls }} mb-1">{{ $title }}</h3>
                        <p class="text-gray-400 text-xs leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     CTA
     ══════════════════════════════════════════════ --}}
<section class="py-12 text-center bg-white">
    <div class="max-w-lg mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Siap Mengajukan Kunjungan?</h2>
        <p class="text-gray-400 text-sm mb-6">Proses pengajuan hanya membutuhkan beberapa menit. Mulai dari pilih tanggal!</p>
        <a href="{{ route('kalender') }}"
           class="bg-upi-red text-white px-10 py-3.5 rounded-xl font-bold hover:bg-upi-dark transition-all hover:scale-105 inline-flex items-center gap-2 shadow-lg shadow-red-900/30">
            📅 Lihat Kalender &amp; Ajukan →
        </a>
    </div>
</section>

@endsection
