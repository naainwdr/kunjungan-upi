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
                            red:   '#800000',
                            dark:  '#600000',
                            light: '#9a0000',
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
        #sidebar { transition: transform 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex">

{{-- Mobile Sidebar Backdrop --}}
<div id="sidebar-backdrop"
     class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden"
     onclick="closeSidebar()"></div>

{{-- Sidebar --}}
<aside id="sidebar"
       class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col border-r-2 border-upi-red
              -translate-x-full lg:translate-x-0 min-h-screen">

    <div class="p-4 border-b border-gray-700 bg-upi-red flex-shrink-0">
        <div class="flex items-center gap-2.5">
            <img src="/images/kkipp-logo.png" alt="KKIPP" class="w-9 h-9 rounded-sm object-contain bg-white p-0.5 flex-shrink-0">
            <div class="min-w-0">
                <div class="font-bold text-sm leading-tight text-white truncate">Panel Admin KKIPP</div>
                <div class="text-upi-gold text-xs truncate">Universitas Pendidikan Indonesia</div>
            </div>
            {{-- Close button on mobile --}}
            <button onclick="closeSidebar()" class="lg:hidden ml-auto text-white/80 hover:text-white p-1 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="p-3 flex-1 space-y-1 text-sm overflow-y-auto">
        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-3 py-2">Menu</p>
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium transition-colors
                  {{ request()->routeIs('admin.dashboard') && !request()->has('status') ? 'bg-upi-red text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"
           onclick="closeSidebar()">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-3 pt-3 pb-1">Filter Status</p>

        <a href="{{ route('admin.dashboard', ['status' => 'pending']) }}"
           class="flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors
                  {{ request('status') === 'pending' ? 'bg-yellow-600 text-white' : 'text-yellow-300 bg-yellow-900/40 hover:bg-yellow-800/60 hover:text-yellow-100' }}"
           onclick="closeSidebar()">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Menunggu
            </div>
            @php $pendingCount = App\Models\Kunjungan::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
            <span class="bg-yellow-500 text-white text-xs rounded-full px-1.5 py-0.5 font-bold min-w-[20px] text-center">
                {{ $pendingCount }}
            </span>
            @endif
        </a>

        <a href="{{ route('admin.dashboard', ['status' => 'approved']) }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium transition-colors
                  {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'text-green-300 bg-green-900/40 hover:bg-green-800/60 hover:text-green-100' }}"
           onclick="closeSidebar()">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Disetujui
        </a>

        <a href="{{ route('admin.dashboard', ['status' => 'rejected']) }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium transition-colors
                  {{ request('status') === 'rejected' ? 'bg-red-700 text-white' : 'text-red-300 bg-red-900/40 hover:bg-red-800/60 hover:text-red-100' }}"
           onclick="closeSidebar()">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Ditolak
        </a>
    </nav>

    <div class="p-3 border-t border-gray-700 bg-gray-800 flex-shrink-0">
        <div class="text-xs text-gray-400 mb-2 truncate">{{ auth()->user()->name ?? auth()->user()->email ?? 'Admin' }}</div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full text-left text-xs text-red-300 hover:text-red-100 flex items-center gap-1.5 py-1 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main Content Area --}}
<div class="flex-1 flex flex-col min-h-screen overflow-x-hidden w-full">

    {{-- Top Header --}}
    <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-3">
            {{-- Mobile sidebar toggle --}}
            <button id="sidebar-toggle" onclick="openSidebar()"
                    class="lg:hidden p-1.5 rounded text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-sm sm:text-base font-semibold text-gray-700 truncate">@yield('page_title', 'Dashboard')</h1>
        </div>
        <span class="text-xs text-gray-400 hidden sm:block">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        <span class="text-xs text-gray-400 sm:hidden">{{ now()->format('d/m/Y') }}</span>
    </header>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 sm:px-6 py-3 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 sm:px-6 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <main class="flex-1 p-4 sm:p-6">
        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-backdrop').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-backdrop').classList.add('hidden');
    document.body.style.overflow = '';
}
// Close on resize to lg
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) closeSidebar();
});
</script>
</body>
</html>
