<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Sistem Reservasi Kunjungan Sekolah ke Universitas Pendidikan Indonesia (UPI). Ajukan kunjungan dengan mudah secara online.')">
    <title>@yield('title', 'Reservasi Kunjungan') | UPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        upi: {
                            red:   '#C8102E',
                            dark:  '#A50E26',
                            black: '#111111',
                            gold:  '#FFCC00',
                            cream: '#FFF9F0',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; }
        .btn-upi { @apply bg-upi-red text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-upi-dark transition-colors duration-200 inline-flex items-center gap-2; }
        .form-input { @apply w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red focus:border-transparent; }
        .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

{{-- Top Bar --}}
<div class="bg-upi-black text-gray-300 text-xs py-1.5 hidden md:block">
    <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
        <span>📞 (022) 2013163 &nbsp;|&nbsp; ✉️ humas@upi.edu</span>
        <span>Jam Operasional: Senin–Jumat 08.00–16.00 WIB</span>
    </div>
</div>

{{-- Navbar --}}
<nav class="bg-upi-red shadow-md sticky top-0 z-50 border-b-4 border-upi-gold">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center font-bold text-upi-red text-lg shadow-sm">U</div>
            <div>
                <div class="text-white font-bold text-sm leading-tight">Universitas Pendidikan Indonesia</div>
                <div class="text-upi-gold text-xs font-medium">Reservasi Kunjungan Sekolah</div>
            </div>
        </a>
        <div class="flex items-center gap-1 md:gap-4">
            <a href="{{ route('home') }}" class="text-white text-sm px-3 py-1.5 rounded hover:bg-upi-dark transition-colors {{ request()->routeIs('home') ? 'bg-upi-dark' : '' }}">Beranda</a>
            <a href="{{ route('reservasi.create') }}" class="text-white text-sm px-3 py-1.5 rounded hover:bg-upi-dark transition-colors {{ request()->routeIs('reservasi.*') ? 'bg-upi-dark' : '' }}">Reservasi</a>
            <a href="{{ route('cek-status') }}" class="text-white text-sm px-3 py-1.5 rounded hover:bg-upi-dark transition-colors {{ request()->routeIs('cek-status*') ? 'bg-upi-dark' : '' }}">Cek Status</a>
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

{{-- Footer --}}
<footer class="bg-upi-black text-white mt-auto">
    <div class="border-t-4 border-upi-red">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div>
                    <div class="font-bold text-upi-gold mb-2">Universitas Pendidikan Indonesia</div>
                    <p class="text-gray-400 text-xs leading-relaxed">Jl. Dr. Setiabudhi No.229, Isola, Sukasari, Kota Bandung, Jawa Barat 40154</p>
                </div>
                <div>
                    <div class="font-bold text-upi-gold mb-2">Kontak Humas</div>
                    <p class="text-gray-400 text-xs">📞 (022) 2013163</p>
                    <p class="text-gray-400 text-xs">✉️ humas@upi.edu</p>
                </div>
                <div>
                    <div class="font-bold text-upi-gold mb-2">Jam Operasional</div>
                    <p class="text-gray-400 text-xs">Senin – Jumat: 08.00 – 16.00 WIB</p>
                    <p class="text-gray-400 text-xs">Sabtu – Minggu: Tutup</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-4 pt-4 text-center text-gray-500 text-xs">
                &copy; {{ date('Y') }} Humas Universitas Pendidikan Indonesia. All rights reserved.
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
