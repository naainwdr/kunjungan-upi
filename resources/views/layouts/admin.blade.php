<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') | Humas UPI</title>
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
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex">

{{-- Sidebar --}}
<aside class="w-56 bg-upi-black text-white flex-shrink-0 min-h-screen flex flex-col border-r-4 border-upi-red">
    <div class="p-4 border-b border-gray-800">
        <div class="flex items-center gap-2 mb-1">
            <div class="w-7 h-7 bg-upi-red rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0">U</div>
            <div class="font-bold text-sm leading-tight">Panel Admin Humas</div>
        </div>
        <div class="text-upi-gold text-xs mt-0.5 pl-9">Universitas Pendidikan Indonesia</div>
    </div>
    <nav class="p-3 flex-1 space-y-1 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-upi-red transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-upi-red' : 'hover:bg-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.dashboard') }}?status=pending" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-upi-red transition-colors hover:bg-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Menunggu Ulasan
        </a>
        <a href="{{ route('admin.dashboard') }}?status=approved" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-upi-red transition-colors hover:bg-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Disetujui
        </a>
        <a href="{{ route('admin.dashboard') }}?status=rejected" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-upi-red transition-colors hover:bg-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ditolak
        </a>
    </nav>
    <div class="p-3 border-t border-gray-800">
        <div class="text-xs text-gray-400 mb-2">{{ auth()->user()->name ?? 'Admin' }}</div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full text-left text-xs text-red-300 hover:text-red-100 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main Content Area --}}
<div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
    {{-- Top bar --}}
    <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <h1 class="text-base font-semibold text-gray-700">@yield('page_title', 'Dashboard')</h1>
        <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
    </header>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-3 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
