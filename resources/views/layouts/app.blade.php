<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Sistem Permohonan Kunjungan Sekolah ke Universitas Pendidikan Indonesia (UPI). Ajukan kunjungan dengan mudah secara online.')">
    <title>@yield('title', 'Permohonan Kunjungan') | UPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        upi: {
                            red:   '#800000',
                            dark:  '#600000',
                            light: '#9a0000',
                            black: '#111111',
                            gold:  '#FFCC00',
                            cream: '#FFF5F5',
                        }
                    },
                    animation: {
                        'marquee': 'marquee 35s linear infinite',
                        'fade-in': 'fadeIn 0.3s ease',
                    },
                    keyframes: {
                        marquee: {
                            '0%':   { transform: 'translateX(0%)' },
                            '100%': { transform: 'translateX(-50%)' },
                        },
                        fadeIn: {
                            '0%':   { opacity: '0', transform: 'translateY(-4px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; }

        /* Marquee ticker */
        .marquee-wrapper { overflow: hidden; white-space: nowrap; }
        .marquee-track   { display: inline-flex; gap: 3rem; animation: marquee 35s linear infinite; }
        .marquee-track:hover { animation-play-state: paused; }
        @keyframes marquee {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        /* Logo - transparent PNG, tampil langsung di navbar maroon */
        .logo-nav { filter: drop-shadow(0 1px 2px rgba(0,0,0,0.3)); }

        /* Footer wave */
        .footer-wave {
            background: linear-gradient(180deg, transparent 0%, #0f0f0f 100%);
        }

        /* Glow on hover social icons */
        .social-btn { transition: transform 0.2s, box-shadow 0.2s; }
        .social-btn:hover { transform: translateY(-2px); }

        /* Mobile nav animation */
        #mobile-nav { transition: max-height 0.3s ease, opacity 0.25s ease; max-height: 0; opacity: 0; overflow: hidden; }
        #mobile-nav.open { max-height: 360px; opacity: 1; }

        /* About modal */
        #about-modal {
            display: none;
            position: fixed; inset: 0; z-index: 9999;
            align-items: center; justify-content: center;
            animation: fadeIn 0.2s ease;
        }
        #about-modal.open { display: flex; }
        #about-modal-backdrop {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
        }
        #about-modal-box {
            position: relative; z-index: 1;
            background: #fff;
            border-radius: 1.25rem;
            padding: 2rem 2rem 1.75rem;
            max-width: 400px; width: 90%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            animation: fadeIn 0.25s ease;
        }
    </style>
    @stack('head')
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

{{-- ══════════════════════════════════════════════
     TOP BAR — Scrolling ticker
     ══════════════════════════════════════════════ --}}
<div class="bg-upi-black text-gray-400 text-xs py-1.5 hidden md:block overflow-hidden relative border-b border-gray-800">
    <div class="marquee-track select-none">
        {{-- Content × 2 for seamless infinite loop --}}
        @foreach(range(0, 1) as $r)
        <span class="flex-shrink-0 flex items-center gap-6">
            <span class="flex items-center gap-1.5">
                <svg class="w-3 h-3 text-upi-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                laporan-ult.upi.edu/lapor
            </span>
            <span>|</span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3 h-3 text-upi-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Jam Operasional: Senin–Jumat 08.00–16.00 WIB
            </span>
            <span>|</span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3 h-3 text-upi-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                Jl. Dr. Setiabudhi No.229, Bandung 40154
            </span>
            <span>|</span>
            <span class="text-upi-gold font-semibold">🎓 KKIPP — Kantor Komunikasi, Informasi dan Pelayanan Publik UPI</span>
            <span class="px-6 text-gray-700">✦</span>
        </span>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════
     NAVBAR
     ══════════════════════════════════════════════ --}}
<nav class="bg-white shadow-md sticky top-0 z-50 border-b-4 border-upi-red">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center py-2">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0 flex-shrink">
                <img src="/images/kkipp-logo.png" alt="KKIPP UPI"
                     class="logo-nav h-12 sm:h-14 w-auto object-contain flex-shrink-0">
                <div class="min-w-0 hidden sm:block">
                    <div class="text-upi-red font-bold text-sm leading-tight">Universitas Pendidikan Indonesia</div>
                    <div class="text-gray-500 text-xs font-medium">Permohonan Kunjungan Sekolah</div>
                </div>
            </a>

            <div class="flex items-center gap-2">
                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="text-gray-600 text-sm px-3 py-1.5 rounded-lg hover:bg-upi-red hover:text-white transition-colors {{ request()->routeIs('home') ? 'bg-upi-red text-white font-semibold' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('home') }}#prosedur"
                       class="text-gray-600 text-sm px-3 py-1.5 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                        Prosedur
                    </a>
                    <a href="{{ route('home') }}#informasi"
                       class="text-gray-600 text-sm px-3 py-1.5 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                        Informasi
                    </a>
                    <a href="{{ route('home') }}#galeri"
                       class="text-gray-600 text-sm px-3 py-1.5 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                        Momen
                    </a>
                    <a href="{{ route('home') }}#denah"
                       class="text-gray-600 text-sm px-3 py-1.5 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                        Denah
                    </a>
                    <a href="{{ route('cek-status') }}"
                       class="text-gray-600 text-sm px-3 py-1.5 rounded-lg hover:bg-upi-red hover:text-white transition-colors {{ request()->routeIs('cek-status*') ? 'bg-upi-red text-white font-semibold' : '' }}">
                        Cek Status
                    </a>

                    {{-- Tombol About / Info Aplikasi --}}
                    <button id="btn-about"
                            onclick="openAboutModal()"
                            title="Tentang Aplikasi"
                            aria-label="Tentang Aplikasi"
                            class="ml-1 w-8 h-8 flex items-center justify-center rounded-full border-2 border-upi-red text-upi-red hover:bg-upi-red hover:text-white transition-all duration-200 hover:scale-110 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </div>

                {{-- Mobile Hamburger --}}
                <button id="mobile-nav-btn"
                        class="md:hidden text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        aria-label="Toggle menu" aria-expanded="false">
                    <svg id="icon-hamburger" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="icon-close-nav" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Nav Dropdown --}}
        <div id="mobile-nav" class="md:hidden border-t border-gray-100">
            <div class="py-2 flex flex-col gap-0.5">
                <a href="{{ route('home') }}"
                   class="text-gray-700 text-sm py-2.5 px-3 rounded-lg hover:bg-upi-red hover:text-white transition-colors {{ request()->routeIs('home') ? 'bg-upi-red text-white font-semibold' : '' }}">
                    🏠 Beranda
                </a>
                <a href="{{ route('home') }}#prosedur"
                   class="text-gray-700 text-sm py-2.5 px-3 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                    📝 Prosedur
                </a>
                <a href="{{ route('home') }}#informasi"
                   class="text-gray-700 text-sm py-2.5 px-3 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                    ℹ️ Informasi
                </a>
                <a href="{{ route('home') }}#galeri"
                   class="text-gray-700 text-sm py-2.5 px-3 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                    📸 Momen
                </a>
                <a href="{{ route('home') }}#denah"
                   class="text-gray-700 text-sm py-2.5 px-3 rounded-lg hover:bg-upi-red hover:text-white transition-colors">
                    🗺️ Denah
                </a>
                <a href="{{ route('cek-status') }}"
                   class="text-gray-700 text-sm py-2.5 px-3 rounded-lg hover:bg-upi-red hover:text-white transition-colors {{ request()->routeIs('cek-status*') ? 'bg-upi-red text-white font-semibold' : '' }}">
                    🔍 Cek Status Pengajuan
                </a>
                {{-- Tombol About di mobile nav --}}
                <button onclick="openAboutModal()"
                        class="text-left text-gray-700 text-sm py-2.5 px-3 rounded-lg hover:bg-upi-red hover:text-white transition-colors w-full flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tentang Aplikasi
                </button>
            </div>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 text-sm" role="alert">
    <div class="max-w-6xl mx-auto flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif

{{-- Main Content --}}
<main class="flex-1">
    @yield('content')
</main>

{{-- ══════════════════════════════════════════════
     FOOTER — Modern Premium Style
     ══════════════════════════════════════════════ --}}
<footer class="relative bg-gray-950 text-white overflow-hidden">
    {{-- Background decorative elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-upi-red/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-upi-gold/5 rounded-full blur-2xl"></div>
    </div>

    {{-- Top accent --}}
    <div class="h-1 bg-gradient-to-r from-upi-gold via-upi-red to-upi-gold"></div>

    <div class="relative max-w-6xl mx-auto px-4 pt-10 pb-6">

        {{-- Main footer grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <img src="/images/kkipp-logo.png" alt="KKIPP UPI"
                         class="h-14 w-auto object-contain">
                </div>
                <p class="text-gray-500 text-xs leading-relaxed">
                    Kantor Komunikasi, Informasi dan Pelayanan Publik<br>
                    Universitas Pendidikan Indonesia
                </p>
                <p class="text-gray-600 text-xs mt-2">
                    Jl. Dr. Setiabudhi No.229, Bandung 40154
                </p>
            </div>

            {{-- Kontak --}}
            <div>
                <h4 class="text-upi-gold font-bold text-sm mb-4 flex items-center gap-2">
                    <div class="w-4 h-0.5 bg-upi-gold"></div>
                    Kontak
                </h4>
                <div class="space-y-2.5">
                    <a href="https://wa.me/6285133332559" target="_blank" class="flex items-center gap-2.5 text-gray-400 hover:text-white transition-colors text-xs group">
                        <span class="w-7 h-7 bg-gray-800 group-hover:bg-green-600 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">📲</span>
                        085133332559 (WA)
                    </a>
                    <a href="https://laporan-ult.upi.edu/lapor" target="_blank" class="flex items-center gap-2.5 text-gray-400 hover:text-white transition-colors text-xs group">
                        <span class="w-7 h-7 bg-gray-800 group-hover:bg-upi-red rounded-lg flex items-center justify-center transition-colors flex-shrink-0">🔗</span>
                        Situs ULT UPI
                    </a>
                    <a href="https://www.upi.edu" target="_blank" class="flex items-center gap-2.5 text-gray-400 hover:text-white transition-colors text-xs group">
                        <span class="w-7 h-7 bg-gray-800 group-hover:bg-upi-red rounded-lg flex items-center justify-center transition-colors flex-shrink-0">🌐</span>
                        www.upi.edu
                    </a>
                </div>
                <div class="mt-4 bg-gray-900 rounded-xl p-3 border border-gray-800">
                    <p class="text-xs text-gray-500 font-semibold mb-1">⏰ JAM OPERASIONAL</p>
                    <p class="text-xs text-gray-300">Sen – Kam: 09.00 – 15.00 WIB</p>
                    <p class="text-xs text-gray-500 mt-0.5">Jumat, Sabtu, Minggu & Libur: Tutup</p>
                </div>
            </div>

            {{-- Navigasi --}}
            <div>
                <h4 class="text-upi-gold font-bold text-sm mb-4 flex items-center gap-2">
                    <div class="w-4 h-0.5 bg-upi-gold"></div>
                    Navigasi
                </h4>
                <div class="space-y-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-400 hover:text-upi-gold transition-colors text-xs">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Beranda
                    </a>
                    <a href="{{ route('kalender') }}" class="flex items-center gap-2 text-gray-400 hover:text-upi-gold transition-colors text-xs">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Kalender Kunjungan
                    </a>
                    <a href="{{ route('kalender') }}" class="flex items-center gap-2 text-gray-400 hover:text-upi-gold transition-colors text-xs">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Ajukan Permohonan
                    </a>
                    <a href="{{ route('cek-status') }}" class="flex items-center gap-2 text-gray-400 hover:text-upi-gold transition-colors text-xs">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Cek Status Pengajuan
                    </a>
                </div>
            </div>

            {{-- Social Media --}}
            <div>
                <h4 class="text-upi-gold font-bold text-sm mb-4 flex items-center gap-2">
                    <div class="w-4 h-0.5 bg-upi-gold"></div>
                    Ikuti Kami
                </h4>
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="https://www.instagram.com/upi.edu" target="_blank" rel="noopener"
                       class="social-btn flex flex-col items-center gap-1.5 bg-gray-800 hover:bg-gradient-to-br hover:from-purple-600 hover:to-pink-500 p-3 rounded-xl text-gray-400 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        <span class="text-[10px] font-medium">Instagram</span>
                    </a>
                    <a href="https://twitter.com/UPIHumas" target="_blank" rel="noopener"
                       class="social-btn flex flex-col items-center gap-1.5 bg-gray-800 hover:bg-gray-700 p-3 rounded-xl text-gray-400 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.74l7.73-8.835L1.254 2.25H8.08l4.713 5.858 5.45-5.858zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        <span class="text-[10px] font-medium">X / Twitter</span>
                    </a>
                    <a href="https://www.youtube.com/@upieduofficial" target="_blank" rel="noopener"
                       class="social-btn flex flex-col items-center gap-1.5 bg-gray-800 hover:bg-red-700 p-3 rounded-xl text-gray-400 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        <span class="text-[10px] font-medium">YouTube</span>
                    </a>
                    <a href="https://www.facebook.com/upiedu" target="_blank" rel="noopener"
                       class="social-btn flex flex-col items-center gap-1.5 bg-gray-800 hover:bg-blue-600 p-3 rounded-xl text-gray-400 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        <span class="text-[10px] font-medium">Facebook</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="pt-5 border-t border-gray-800/60 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-gray-600 text-xs text-center sm:text-left">
                &copy; {{ date('Y') }} <span class="text-gray-400 font-medium">KKIPP — Universitas Pendidikan Indonesia.</span> All rights reserved.
            </p>
        </div>
    </div>
</footer>

{{-- ══════════════════════════════════════════════
     ABOUT MODAL — Info Pengembang & Pengarah
     ══════════════════════════════════════════════ --}}
<div id="about-modal" role="dialog" aria-modal="true" aria-labelledby="about-modal-title">
    <div id="about-modal-backdrop" onclick="closeAboutModal()"></div>
    <div id="about-modal-box">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-upi-red/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-upi-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 id="about-modal-title" class="text-gray-900 font-bold text-base leading-tight">Tentang Aplikasi</h2>
                    <p class="text-gray-400 text-xs mt-0.5">Sistem Permohonan Kunjungan Sekolah</p>
                </div>
            </div>
            <button onclick="closeAboutModal()"
                    aria-label="Tutup"
                    class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg p-1.5 transition-colors ml-2 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Divider --}}
        <div class="h-px bg-gradient-to-r from-upi-red/30 via-upi-gold/40 to-transparent mb-5"></div>

        {{-- Pengembang --}}
        <div class="mb-5">
            <p class="text-[10px] font-bold text-upi-red uppercase tracking-widest mb-2">Pengembang Aplikasi</p>
            <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                <div class="w-9 h-9 bg-upi-red rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">N</div>
                <div>
                    <p class="text-gray-800 font-semibold text-sm leading-tight">Nina Wulandari</p>
                    <p class="text-gray-400 text-xs mt-0.5">Pengembang Sistem</p>
                </div>
            </div>
        </div>

        {{-- Pengarah --}}
        <div class="mb-6">
            <p class="text-[10px] font-bold text-upi-red uppercase tracking-widest mb-2">Pengarah</p>
            <div class="space-y-2">
                <div class="flex items-start gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-7 h-7 bg-upi-red/10 rounded-full flex items-center justify-center text-upi-red font-bold text-xs flex-shrink-0 mt-0.5">V</div>
                    <div>
                        <p class="text-gray-800 font-medium text-sm leading-tight">Vidi Sukmayadi, S.S., M.Si., Ph.D.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-7 h-7 bg-upi-red/10 rounded-full flex items-center justify-center text-upi-red font-bold text-xs flex-shrink-0 mt-0.5">A</div>
                    <div>
                        <p class="text-gray-800 font-medium text-sm leading-tight">Dr. Angga Hadipurwa, S.Pd., M.I.Kom.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-7 h-7 bg-upi-red/10 rounded-full flex items-center justify-center text-upi-red font-bold text-xs flex-shrink-0 mt-0.5">J</div>
                    <div>
                        <p class="text-gray-800 font-medium text-sm leading-tight">Jaka Falah, S.S., M.Pd.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer modal --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <img src="/images/kkipp-logo.png" alt="KKIPP UPI" class="h-7 w-auto object-contain opacity-70">
                <span class="text-gray-400 text-xs">KKIPP UPI &copy; {{ date('Y') }}</span>
            </div>
            <button onclick="closeAboutModal()"
                    class="text-xs text-white bg-upi-red hover:bg-upi-dark px-4 py-1.5 rounded-lg transition-colors font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

@stack('scripts')
<script>
// ── Mobile navbar toggle ────────────────────────────────
const mobileNavBtn  = document.getElementById('mobile-nav-btn');
const mobileNav     = document.getElementById('mobile-nav');
const iconHamburger = document.getElementById('icon-hamburger');
const iconCloseNav  = document.getElementById('icon-close-nav');
let   navOpen       = false;

mobileNavBtn.addEventListener('click', () => {
    navOpen = !navOpen;
    mobileNav.classList.toggle('open', navOpen);
    iconHamburger.classList.toggle('hidden', navOpen);
    iconCloseNav.classList.toggle('hidden', !navOpen);
    mobileNavBtn.setAttribute('aria-expanded', String(navOpen));
});

// ── About Modal ──────────────────────────────────────────
function openAboutModal() {
    const modal = document.getElementById('about-modal');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden'; // Cegah scroll body saat modal terbuka
}

function closeAboutModal() {
    const modal = document.getElementById('about-modal');
    modal.classList.remove('open');
    document.body.style.overflow = ''; // Kembalikan scroll
}

// Tutup modal dengan tombol Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAboutModal();
});
</script>
</body>
</html>
