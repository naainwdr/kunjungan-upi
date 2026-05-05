@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Sistem Permohonan Kunjungan Sekolah ke Universitas Pendidikan Indonesia. Ajukan kunjungan edukatif ke kampus UPI secara online dengan mudah dan cepat.')

@push('styles')
<style>
/* ── Gallery Filter Buttons ─────────────────── */
.filter-btn {
    background: rgba(255,255,255,0.07);
    color: rgba(255,255,255,0.6);
    border: 1px solid rgba(255,255,255,0.12);
}
.filter-btn:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
    border-color: rgba(255,255,255,0.3);
}
.active-filter {
    background: #800000 !important;
    color: #fff !important;
    border-color: #800000 !important;
    box-shadow: 0 0 16px rgba(128,0,0,0.5);
}

/* ── Gallery items ──────────────────────────── */
.gallery-item {
    opacity: 1;
    transform: scale(1);
    transition: opacity 0.35s ease, transform 0.35s ease;
}
.gallery-item.hidden-item {
    display: none;
}

/* ── Lightbox entrance animation ────────────── */
#lightbox-modal.show,
#video-modal.show {
    display: flex !important;
    animation: fadeInModal 0.25s ease;
}
@keyframes fadeInModal {
    from { opacity: 0; transform: scale(0.97); }
    to   { opacity: 1; transform: scale(1); }
}

/* ── Lightbox image transition ──────────────── */
#lightbox-img {
    transition: opacity 0.2s ease;
}

/* ── Smooth video hover preview ─────────────── */
.gallery-item.video video {
    transition: transform 0.7s ease;
}
.gallery-item.video:hover video {
    transform: scale(1.05);
}
</style>
@endpush

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
<section class="max-w-6xl mx-auto px-4 py-12" id="prosedur">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-bold text-upi-red mb-2">Prosedur Permohonan Kunjungan</h2>
        <p class="text-gray-500 text-sm">Ikuti langkah berikut untuk mengajukan kunjungan ke Universitas Pendidikan Indonesia</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 relative">
        {{-- Connector line (desktop) --}}
        <div class="hidden md:block absolute top-10 left-[12.5%] right-[12.5%] h-px bg-gradient-to-r from-transparent via-upi-red/30 to-transparent"></div>

        @foreach([
            ['1', 'Pilih Tanggal', 'Buka kalender kunjungan dan pilih tanggal yang tersedia. Kunjungan hanya dilayani Senin–Kamis.'],
            ['2', 'Isi Formulir',  'Lengkapi data sekolah, pilih sesi dan tempat, serta unggah surat permohonan resmi berkop sekolah.'],
            ['3', 'Verifikasi',    'Tim KKIPP UPI memverifikasi pengajuan dalam 3–5 hari kerja dan mengirimkan notifikasi ke email PIC.'],
            ['4', 'Konfirmasi',    'Jika disetujui, unduh tiket QR dan hadir 30 menit sebelum sesi dimulai untuk registrasi.'],
        ] as $step)
        <div class="relative bg-white border border-gray-200 rounded-2xl p-5 text-center shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-upi-red text-white rounded-full flex items-center justify-center font-bold text-sm mx-auto mb-3 shadow-md shadow-red-200 relative z-10">
                {{ $step[0] }}
            </div>
            <div class="font-bold text-gray-800 text-sm mb-1.5">{{ $step[1] }}</div>
            <p class="text-gray-500 text-xs leading-relaxed">{{ $step[2] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══════════════════════════════════════════════
     INFORMASI PENTING
     ══════════════════════════════════════════════ --}}
<section class="py-12 bg-gray-900 text-white relative overflow-hidden" id="informasi">
    <div class="absolute inset-0 pointer-events-none opacity-[0.03]">
        <div class="absolute top-0 left-0 w-full h-full"
             style="background-image: radial-gradient(#FFCC00 1px, transparent 1px); background-size: 24px 24px;"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4">
        <div class="text-center mb-10">
            <span class="inline-block bg-white/5 border border-white/10 text-gray-300 text-xs font-semibold px-4 py-1.5 rounded-full mb-3 uppercase tracking-widest">
                Ketentuan Kunjungan
            </span>
            <h2 class="text-2xl font-bold text-white">Informasi Penting</h2>
            <p class="text-gray-400 text-sm mt-1">Pastikan memahami ketentuan berikut sebelum mengajukan permohonan</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Card 1: Jadwal Pelayanan --}}
            <div class="border border-white/10 bg-white/5 rounded-xl p-5 hover:bg-white/[0.08] transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-lg border border-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm mb-1">Jadwal Pelayanan</h3>
                        <p class="text-gray-400 text-xs leading-relaxed">Kunjungan dilayani setiap <strong class="text-gray-200">Senin hingga Kamis</strong>. Jumat, Sabtu, Minggu, dan hari libur nasional tidak melayani kunjungan.</p>
                    </div>
                </div>
            </div>

            {{-- Card 2: Sesi Kunjungan --}}
            <div class="border border-white/10 bg-white/5 rounded-xl p-5 hover:bg-white/[0.08] transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-lg border border-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm mb-1">Sesi Kunjungan</h3>
                        <p class="text-gray-400 text-xs leading-relaxed"><strong class="text-gray-200">Sesi 1:</strong> 09.00 &ndash; 12.00 WIB<br><strong class="text-gray-200">Sesi 2:</strong> 13.00 &ndash; 15.00 WIB<br>Harap tiba <strong class="text-gray-200">30 menit sebelum sesi</strong> untuk keperluan registrasi.</p>
                    </div>
                </div>
            </div>

            {{-- Card 3: Pilihan Tempat --}}
            <div class="border border-white/10 bg-white/5 rounded-xl p-5 hover:bg-white/[0.08] transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-lg border border-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm mb-1">Pilihan Tempat</h3>
                        <p class="text-gray-400 text-xs leading-relaxed">Gedung UC Lt.1 (60) &middot; Aud. FPMIPA (300) &middot; Aud. FPEB (200) &middot; Amphiteater (300) &middot; Aula PKM Lt.2 (200). Peserta tidak boleh melebihi kapasitas tempat.</p>
                    </div>
                </div>
            </div>

            {{-- Card 4: Pengajuan H-7 --}}
            <div class="border border-white/10 bg-white/5 rounded-xl p-5 hover:bg-white/[0.08] transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-lg border border-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm mb-1">Pengajuan Minimal H&#8209;7</h3>
                        <p class="text-gray-400 text-xs leading-relaxed">Permohonan harus dikirimkan paling lambat <strong class="text-gray-200">7 hari</strong> sebelum tanggal kunjungan yang dipilih.</p>
                    </div>
                </div>
            </div>

            {{-- Card 5: Dokumen Wajib --}}
            <div class="border border-white/10 bg-white/5 rounded-xl p-5 hover:bg-white/[0.08] transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-lg border border-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm mb-1">Dokumen Wajib</h3>
                        <p class="text-gray-400 text-xs leading-relaxed">Wajib melampirkan <strong class="text-gray-200">surat permohonan resmi</strong> berkop sekolah yang ditandatangani oleh Kepala Sekolah.</p>
                    </div>
                </div>
            </div>

            {{-- Card 6: Kontak --}}
            <div class="border border-white/10 bg-white/5 rounded-xl p-5 hover:bg-white/[0.08] transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-lg border border-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm mb-1">Kontak Kami</h3>
                        <p class="text-gray-400 text-xs leading-relaxed">WhatsApp: <strong class="text-gray-200">085133332559</strong><br>Email: <strong class="text-gray-200">humas@upi.edu</strong><br>Telepon: (022) 2013163</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     GALERI DOKUMENTASI
     ══════════════════════════════════════════════ --}}
<section class="py-16 bg-gray-950 relative overflow-hidden" id="galeri">

    {{-- Background decorative --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-upi-red/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-upi-gold/5 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative">

        {{-- Header --}}
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="inline-block bg-upi-red/20 text-upi-gold text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4 border border-upi-red/30">
                📸 Dokumentasi
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-3">
                Momen Kunjungan Sekolah ke UPI
            </h2>
            <p class="text-gray-400 text-sm max-w-xl mx-auto">
                Ribuan siswa dari seluruh Indonesia telah merasakan pengalaman belajar langsung di Universitas Pendidikan Indonesia
            </p>
        </div>

        {{-- Category Filter --}}
        <div class="flex flex-wrap justify-center gap-2 mb-8" id="gallery-filter">
            <button onclick="filterGallery('all')" data-filter="all"
                class="filter-btn active-filter px-4 py-1.5 rounded-full text-xs font-semibold transition-all">
                Semua
            </button>
            <button onclick="filterGallery('foto')" data-filter="foto"
                class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition-all">
                📷 Foto
            </button>
            <button onclick="filterGallery('video')" data-filter="video"
                class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition-all">
                🎥 Video
            </button>
        </div>

        {{-- Grid Gallery --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="gallery-grid">

            {{-- FOTO 1: Ultrawide Isola (tall → spans 2) --}}
            <div class="gallery-item foto break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openLightbox(0)" data-index="0">
                <img src="{{ asset('storage/dokumentasi/Foto Ultrawide Gedung Isola dengan anak sekolah.jpeg') }}"
                     alt="Gedung Isola UPI dengan Rombongan Sekolah"
                     class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                    <div>
                        <p class="text-white font-bold text-sm">Gedung Isola UPI</p>
                        <p class="text-gray-300 text-xs">Ikon arsitektur bersejarah kampus UPI</p>
                    </div>
                </div>
                <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>

            {{-- VIDEO 1: Auditorium FPEB --}}
            <div class="gallery-item video break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openVideoModal('{{ asset('storage/dokumentasi/Video Auditorium FPEB Kunjungan.mp4') }}', 'Kunjungan di Auditorium FPEB')" >
                <video class="w-full aspect-[4/3] object-cover" muted preload="metadata"
                       poster="{{ asset('storage/dokumentasi/Foto Auditorium FPEB.jpeg') }}">
                    <source src="{{ asset('storage/dokumentasi/Video Auditorium FPEB Kunjungan.mp4') }}" type="video/mp4">
                </video>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-center justify-center">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm border-2 border-white/60 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 group-hover:bg-upi-red/80">
                        <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/90 to-transparent">
                    <span class="inline-block bg-upi-red text-white text-[10px] font-bold px-2 py-0.5 rounded-full mb-1">VIDEO</span>
                    <p class="text-white font-bold text-sm">Kunjungan di Auditorium FPEB</p>
                </div>
            </div>

            {{-- FOTO 2: Foto Bersama Tagline --}}
            <div class="gallery-item foto break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openLightbox(1)" data-index="1">
                <img src="{{ asset('storage/dokumentasi/Foto Bersama Sekolah Gaya Tagline UPI Melesat.jpeg') }}"
                     alt="Foto bersama dengan tagline UPI Melesat"
                     class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                    <p class="text-white font-bold text-sm">UPI Melesat — Semangat Bersama</p>
                </div>
                <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>

            {{-- FOTO 3: Auditorium FPEB --}}
            <div class="gallery-item foto break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openLightbox(2)" data-index="2">
                <img src="{{ asset('storage/dokumentasi/Foto Auditorium FPEB.jpeg') }}"
                     alt="Auditorium FPEB UPI"
                     class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                    <p class="text-white font-bold text-sm">Auditorium FPEB UPI</p>
                </div>
                <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>

            {{-- VIDEO 2: Ice breaking --}}
            <div class="gallery-item video break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openVideoModal('{{ asset('storage/dokumentasi/Video Ice breaking dalam sesi kunjungan.mp4') }}', 'Ice Breaking dalam Sesi Kunjungan')">
                <video class="w-full aspect-[4/3] object-cover" muted preload="metadata">
                    <source src="{{ asset('storage/dokumentasi/Video Ice breaking dalam sesi kunjungan.mp4') }}" type="video/mp4">
                </video>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-center justify-center">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm border-2 border-white/60 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 group-hover:bg-upi-red/80">
                        <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/90 to-transparent">
                    <span class="inline-block bg-upi-red text-white text-[10px] font-bold px-2 py-0.5 rounded-full mb-1">VIDEO</span>
                    <p class="text-white font-bold text-sm">Ice Breaking Sesi Kunjungan</p>
                    <p class="text-gray-300 text-xs">Kegiatan interaktif bersama mahasiswa UPI</p>
                </div>
            </div>

            {{-- FOTO 4: Gaya Sinergi --}}
            <div class="gallery-item foto break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openLightbox(3)" data-index="3">
                <img src="{{ asset('storage/dokumentasi/Gaya sinergi KKIPP UPI dengan Sekolah.jpeg') }}"
                     alt="Sinergi KKIPP UPI dengan Sekolah"
                     class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                    <p class="text-white font-bold text-sm">Sinergi KKIPP UPI & Sekolah</p>
                </div>
                <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>

            {{-- FOTO 5: Foto Bersama Auditorium FPEB --}}
            <div class="gallery-item foto break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openLightbox(4)" data-index="4">
                <img src="{{ asset('storage/dokumentasi/Foto Bersama Sekolah di Auditorium FPEB.jpeg') }}"
                     alt="Foto bersama di Auditorium FPEB"
                     class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                    <p class="text-white font-bold text-sm">Foto Bersama di Auditorium FPEB</p>
                </div>
                <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>

            {{-- FOTO 6: Suasana tampak belakang --}}
            <div class="gallery-item foto break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openLightbox(5)" data-index="5">
                <img src="{{ asset('storage/dokumentasi/Foto suasana kunjungan tampak belakang siswa sekolah.jpeg') }}"
                     alt="Suasana kunjungan siswa dari belakang"
                     class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                    <p class="text-white font-bold text-sm">Suasana Sesi Kunjungan</p>
                </div>
                <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>

            {{-- FOTO 7: Penyerahan plakat --}}
            <div class="gallery-item foto break-inside-avoid relative group cursor-pointer rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500"
                 onclick="openLightbox(6)" data-index="6">
                <img src="{{ asset('storage/dokumentasi/Penyerahan plakat KKIPP UPI dengan sekolah.jpeg') }}"
                     alt="Penyerahan plakat KKIPP UPI"
                     class="w-full aspect-[4/3] object-cover transition-transform duration-700 group-hover:scale-110"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                    <p class="text-white font-bold text-sm">Penyerahan Plakat KKIPP UPI</p>
                    <p class="text-gray-300 text-xs">Kenang-kenangan untuk sekolah tamu</p>
                </div>
                <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>

        </div>{{-- end gallery-grid --}}

    </div>
</section>

{{-- ── LIGHTBOX MODAL ──────────────────────────────────────── --}}
<div id="lightbox-modal" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/95 backdrop-blur-md"
     onclick="if(event.target===this) closeLightbox()">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <button onclick="changeLightbox(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button onclick="changeLightbox(1)" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all z-10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div class="max-w-5xl max-h-[85vh] px-14 w-full flex flex-col items-center">
        <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[75vh] rounded-xl object-contain shadow-2xl">
        <p id="lightbox-caption" class="text-white/80 text-sm mt-4 text-center"></p>
        <p id="lightbox-counter" class="text-white/40 text-xs mt-1"></p>
    </div>
</div>

{{-- ── VIDEO MODAL ─────────────────────────────────────────── --}}
<div id="video-modal" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/95 backdrop-blur-md"
     onclick="if(event.target===this) closeVideoModal()">
    <div class="max-w-4xl w-full px-4">
        <div class="flex items-center justify-between mb-3">
            <p id="video-modal-title" class="text-white font-bold text-sm"></p>
            <button onclick="closeVideoModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <video id="video-modal-player" controls class="w-full rounded-xl shadow-2xl bg-black max-h-[70vh]">
            <source id="video-modal-src" src="" type="video/mp4">
        </video>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     DENAH KUNJUNGAN
     ══════════════════════════════════════════════ --}}
<section class="py-12 bg-white" id="denah">
    <div class="max-w-5xl mx-auto px-6">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Denah Akses Kunjungan UPI</h2>
            <p class="mt-2 text-gray-600">Klik gambar peta di bawah ini untuk melihat lebih jelas dan memperbesar (Zoom).</p>
        </div>

        {{-- Single Column Container (Bukan Grid) --}}
        <div class="relative w-full rounded-2xl overflow-hidden shadow-lg border-4 border-gray-100 group cursor-pointer bg-blue-50">
            
            {{-- Wrapper Fancybox --}}
            <a href="{{ asset('images/Map Kunjungan.png') }}" data-fancybox="peta-denah" data-caption="Denah Akses Kunjungan Kampus UPI">
                
                {{-- Gambar Peta --}}
                <img src="{{ asset('images/Map Kunjungan.png') }}" 
                     alt="Peta Kampus UPI" 
                     class="w-full h-auto object-contain transition-transform duration-700 group-hover:scale-105 block mx-auto"
                     loading="lazy">
                
                {{-- Overlay Hover Kaca Pembesar (Akan muncul saat cursor diarahkan ke gambar) --}}
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <div class="bg-white/20 backdrop-blur-md rounded-full p-4 border border-white/50 shadow-2xl flex flex-col items-center gap-2 transform scale-75 group-hover:scale-100 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                        <span class="text-white text-xs font-bold tracking-wider">KLIK UNTUK ZOOM</span>
                    </div>
                </div>

            </a>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════
     TESTIMONI KEPUASAN
     ══════════════════════════════════════════════ --}}
@php
    $testimoni = \App\Models\SurveiKepuasan::with('kunjungan.sekolah')
        ->where('tampilkan_publik', true)
        ->whereNotNull('komentar')
        ->where('komentar', '!=', '')
        ->orderByDesc('created_at')
        ->limit(6)
        ->get();
@endphp

@if($testimoni->isNotEmpty())
<section class="bg-gray-50 border-t border-gray-100 py-14">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-6">
            <span class="text-3xl">⭐</span>
            <h2 class="text-2xl font-bold text-gray-800 mt-2 mb-1">Apa Kata Sekolah yang Telah Berkunjung</h2>
            <p class="text-gray-400 text-sm">Testimoni nyata dari sekolah yang telah melakukan kunjungan ke UPI</p>
        </div>

        <div class="flex overflow-x-auto gap-5 pb-8 pt-4 px-2 -mx-2 snap-x snap-mandatory [&::-webkit-scrollbar]:hidden" style="-ms-overflow-style: none; scrollbar-width: none;">
            @foreach($testimoni as $t)
            <div class="flex-none w-[85vw] md:w-[calc(50%-1.25rem)] lg:w-[calc(33.333%-1.33rem)] snap-center bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:-translate-y-2 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-default">
                {{-- Stars --}}
                <div class="flex items-center gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= round($t->rating_rata) ? 'text-yellow-400' : 'text-gray-200' }} text-lg">★</span>
                    @endfor
                    <span class="text-xs font-bold text-gray-600 ml-1">{{ $t->rating_rata }}</span>
                </div>

                {{-- Komentar --}}
                <blockquote class="text-gray-700 text-sm leading-relaxed mb-4 italic">
                    "{{ Str::limit($t->komentar, 180) }}"
                </blockquote>

                {{-- Source --}}
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                    <div class="w-8 h-8 bg-upi-red/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-upi-red">{{ strtoupper(substr($t->kunjungan->sekolah->nama, 0, 2)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 text-xs truncate">{{ $t->kunjungan->sekolah->nama }}</p>
                        <p class="text-gray-400 text-[10px]">{{ $t->kunjungan->tanggal_kunjungan->format('M Y') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex justify-center items-center gap-2 text-gray-400 text-xs font-medium animate-pulse">
                <span>Geser untuk melihat lebih banyak</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
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

@push('scripts')
<script>
// ─────────────────────────────────────────────────────
// Lightbox Data
// ─────────────────────────────────────────────────────
const lightboxImages = [
    {
        src: '{{ asset("storage/dokumentasi/Foto Ultrawide Gedung Isola dengan anak sekolah.jpeg") }}',
        caption: 'Gedung Isola UPI — Ikon arsitektur bersejarah kampus UPI',
    },
    {
        src: '{{ asset("storage/dokumentasi/Foto Bersama Sekolah Gaya Tagline UPI Melesat.jpeg") }}',
        caption: 'UPI Melesat — Semangat bersama rombongan sekolah',
    },
    {
        src: '{{ asset("storage/dokumentasi/Foto Auditorium FPEB.jpeg") }}',
        caption: 'Auditorium FPEB UPI — Venue utama kunjungan',
    },
    {
        src: '{{ asset("storage/dokumentasi/Gaya sinergi KKIPP UPI dengan Sekolah.jpeg") }}',
        caption: 'Sinergi KKIPP UPI & Sekolah — Kolaborasi pendidikan',
    },
    {
        src: '{{ asset("storage/dokumentasi/Foto Bersama Sekolah di Auditorium FPEB.jpeg") }}',
        caption: 'Foto bersama rombongan sekolah di Auditorium FPEB',
    },
    {
        src: '{{ asset("storage/dokumentasi/Foto suasana kunjungan tampak belakang siswa sekolah.jpeg") }}',
        caption: 'Suasana sesi kunjungan — Antusias para siswa',
    },
    {
        src: '{{ asset("storage/dokumentasi/Penyerahan plakat KKIPP UPI dengan sekolah.jpeg") }}',
        caption: 'Penyerahan Plakat KKIPP UPI — Kenang-kenangan untuk sekolah tamu',
    },
];

let currentLightboxIndex = 0;

// ─────────────────────────────────────────────────────
// Lightbox Functions
// ─────────────────────────────────────────────────────
function openLightbox(index) {
    currentLightboxIndex = index;
    renderLightbox();
    const modal = document.getElementById('lightbox-modal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function renderLightbox() {
    const item = lightboxImages[currentLightboxIndex];
    const img  = document.getElementById('lightbox-img');
    img.style.opacity = '0';
    setTimeout(() => {
        img.src = item.src;
        img.alt = item.caption;
        img.onload = () => { img.style.opacity = '1'; };
    }, 150);
    document.getElementById('lightbox-caption').textContent = item.caption;
    document.getElementById('lightbox-counter').textContent =
        (currentLightboxIndex + 1) + ' / ' + lightboxImages.length;
}

function changeLightbox(dir) {
    currentLightboxIndex = (currentLightboxIndex + dir + lightboxImages.length) % lightboxImages.length;
    renderLightbox();
}

function closeLightbox() {
    const modal = document.getElementById('lightbox-modal');
    modal.classList.add('hidden');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// ─────────────────────────────────────────────────────
// Video Modal Functions
// ─────────────────────────────────────────────────────
function openVideoModal(src, title) {
    document.getElementById('video-modal-src').src  = src;
    document.getElementById('video-modal-title').textContent = title;
    const player = document.getElementById('video-modal-player');
    player.load();
    const modal = document.getElementById('video-modal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(() => player.play().catch(() => {}), 300);
}

function closeVideoModal() {
    const player = document.getElementById('video-modal-player');
    player.pause();
    player.currentTime = 0;
    const modal = document.getElementById('video-modal');
    modal.classList.add('hidden');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// ─────────────────────────────────────────────────────
// Gallery Filter
// ─────────────────────────────────────────────────────
function filterGallery(type) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.filter === type) {
            btn.classList.add('active-filter');
        } else {
            btn.classList.remove('active-filter');
        }
    });

    // Show/hide items
    document.querySelectorAll('.gallery-item').forEach(item => {
        if (type === 'all' || item.classList.contains(type)) {
            item.classList.remove('hidden-item');
        } else {
            item.classList.add('hidden-item');
        }
    });
}

// ─────────────────────────────────────────────────────
// Keyboard Navigation
// ─────────────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
    const lightboxOpen = !document.getElementById('lightbox-modal').classList.contains('hidden');
    const videoOpen    = !document.getElementById('video-modal').classList.contains('hidden');

    if (lightboxOpen) {
        if (e.key === 'ArrowRight') changeLightbox(1);
        if (e.key === 'ArrowLeft')  changeLightbox(-1);
        if (e.key === 'Escape')     closeLightbox();
    }
    if (videoOpen && e.key === 'Escape') closeVideoModal();
});

// ─────────────────────────────────────────────────────
// Hover video preview on desktop
// ─────────────────────────────────────────────────────
document.querySelectorAll('.gallery-item.video').forEach(item => {
    const vid = item.querySelector('video');
    if (!vid) return;
    item.addEventListener('mouseenter', () => {
        vid.currentTime = 0;
        vid.play().catch(() => {});
    });
    item.addEventListener('mouseleave', () => {
        vid.pause();
        vid.currentTime = 0;
    });
});

// ─────────────────────────────────────────────────────
// Intersection Observer — fade-in on scroll
// ─────────────────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity  = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.gallery-item').forEach((el, i) => {
    el.style.opacity   = '0';
    el.style.transform = 'translateY(24px)';
    el.style.transition = `opacity 0.5s ease ${i * 0.07}s, transform 0.5s ease ${i * 0.07}s`;
    observer.observe(el);
});
</script>
@endpush
