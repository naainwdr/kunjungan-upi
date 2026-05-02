@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Pengajuan Kunjungan')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    @foreach([
        ['Total Pengajuan', $counts['all'],       'text-blue-700',   'bg-blue-50 border-blue-200',    '📋'],
        ['Menunggu',        $counts['pending'],   'text-yellow-700', 'bg-yellow-50 border-yellow-200','⏳'],
        ['Disetujui',       $counts['approved'],  'text-green-700',  'bg-green-50 border-green-200',  '✅'],
        ['Ditolak',         $counts['rejected'],  'text-red-700',    'bg-red-50 border-red-200',      '❌'],
        ['Dibatalkan Pemohon', $counts['cancelled'], 'text-gray-700',   'bg-gray-50 border-gray-200',    '🗑️'],
    ] as $stat)
    <div class="bg-white rounded-xl border {{ $stat[3] }} p-4 shadow-sm">
        <div class="text-xl mb-1">{{ $stat[4] }}</div>
        <div class="text-3xl font-bold {{ $stat[2] }}">{{ $stat[1] }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $stat[0] }}</div>
    </div>
    @endforeach
</div>

{{-- Filter & Sort --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('admin.dashboard') }}" id="form-filter">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 mb-1">🔍 Cari</label>
                <input type="text" name="search" placeholder="Nama sekolah, nomor registrasi..."
                    value="{{ request('search') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40">
            </div>

            {{-- Filter Tanggal Dari --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">📅 Tgl. Kunjungan Dari</label>
                <input type="date" name="tgl_dari" value="{{ request('tgl_dari') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40">
            </div>

            {{-- Filter Tanggal Sampai --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">📅 Sampai</label>
                <input type="date" name="tgl_sampai" value="{{ request('tgl_sampai') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40">
            </div>

            {{-- Sort --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">↕ Urutkan</label>
                <div class="flex gap-2">
                    <select name="sort" class="flex-1 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40 bg-white">
                        <option value="created_at"         {{ $sortBy === 'created_at' ? 'selected' : '' }}>Waktu Submit</option>
                        <option value="tanggal_kunjungan"  {{ $sortBy === 'tanggal_kunjungan' ? 'selected' : '' }}>Tgl. Kunjungan</option>
                        <option value="jumlah_peserta"     {{ $sortBy === 'jumlah_peserta' ? 'selected' : '' }}>Jml. Peserta</option>
                        <option value="nama_sekolah"       {{ $sortBy === 'nama_sekolah' ? 'selected' : '' }}>Nama Sekolah</option>
                    </select>
                    <select name="dir" class="border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40 bg-white">
                        <option value="desc" {{ $sortDir === 'desc' ? 'selected' : '' }}>↓ Terbaru</option>
                        <option value="asc"  {{ $sortDir === 'asc'  ? 'selected' : '' }}>↑ Terlama</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-3">
            {{-- Preserve status from sidebar filter --}}
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <button type="submit" class="bg-upi-red text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-upi-dark transition-colors">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['search','tgl_dari','tgl_sampai','sort','dir']))
            <a href="{{ route('admin.dashboard', request()->only('status')) }}"
               class="text-gray-500 text-sm hover:text-gray-700 underline underline-offset-2">
                Reset Filter
            </a>
            @endif
        </div>
    </form>
</div>

{{-- 2 columns: Table + Widgets --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    {{-- ============ TABLE ============ --}}
    <div class="xl:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 text-sm">
                Daftar Pengajuan
                @if(request('status'))
                    — <span class="capitalize">{{ request('status') === 'pending' ? 'Menunggu' : (request('status') === 'approved' ? 'Disetujui' : (request('status') === 'cancelled' ? 'Dibatalkan' : 'Ditolak')) }}</span>
                @endif
            </h3>
            <span class="text-xs text-gray-400">{{ $kunjungan->total() }} data</span>
        </div>

        @if($kunjungan->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <div class="text-4xl mb-2">📭</div>
            <p>Belum ada pengajuan ditemukan.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-3 py-3 font-semibold text-gray-600 text-xs whitespace-nowrap">No. Registrasi</th>
                        <th class="text-left px-3 py-3 font-semibold text-gray-600 text-xs">Sekolah</th>
                        <th class="text-left px-3 py-3 font-semibold text-gray-600 text-xs whitespace-nowrap">Tgl. Kunjungan</th>
                        <th class="text-left px-3 py-3 font-semibold text-gray-600 text-xs">Status</th>
                        <th class="text-left px-3 py-3 font-semibold text-gray-600 text-xs">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($kunjungan as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-3 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">{{ $item->nomor_registrasi }}</td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-gray-800 text-xs">{{ $item->sekolah->nama }}</div>
                            <div class="text-xs text-gray-400">{{ number_format($item->jumlah_peserta) }} orang</div>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-600 whitespace-nowrap">
                            {{ $item->tanggal_format }}
                            @if($item->sesi)
                                <div class="text-gray-400">{{ $item->sesi->nama }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $item->status_badge_class }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            <a href="{{ route('admin.kunjungan.show', $item) }}"
                               class="text-upi-red font-semibold hover:underline text-xs">Detail →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($kunjungan->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $kunjungan->links() }}
        </div>
        @endif
        @endif
    </div>

    {{-- ============ WIDGETS ============ --}}
    <div class="space-y-4">

        {{-- Top 5 Sekolah --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-white">
                <h3 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                    🏆 Top 5 Sekolah Sering Berkunjung
                </h3>
            </div>
            @if($topSekolah->isEmpty())
            <p class="text-gray-400 text-xs text-center py-6">Belum ada data kunjungan disetujui.</p>
            @else
            <div class="divide-y divide-gray-100">
                @foreach($topSekolah as $i => $sekolah)
                <div class="px-4 py-3 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                        {{ $i === 0 ? 'bg-yellow-400 text-yellow-900' : ($i === 1 ? 'bg-gray-300 text-gray-700' : ($i === 2 ? 'bg-amber-600 text-white' : 'bg-gray-100 text-gray-600')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-800 truncate">{{ $sekolah->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $sekolah->total_kunjungan }}× · {{ number_format($sekolah->total_peserta ?? 0) }} peserta total</p>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full flex-shrink-0">
                        {{ $sekolah->total_kunjungan }}×
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Kunjungan Terbaru Disetujui --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
                <h3 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                    🕐 Kunjungan Terakhir Disetujui
                </h3>
            </div>
            @if($recentVisits->isEmpty())
            <p class="text-gray-400 text-xs text-center py-6">Belum ada kunjungan disetujui.</p>
            @else
            <div class="divide-y divide-gray-100">
                @foreach($recentVisits as $visit)
                <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-xs text-gray-800 truncate">{{ $visit->sekolah->nama }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $visit->tanggal_format }}
                                @if($visit->sesi) · {{ $visit->sesi->nama }} @endif
                            </p>
                        </div>
                        <a href="{{ route('admin.kunjungan.show', $visit) }}"
                           class="text-upi-red text-xs hover:underline flex-shrink-0">→</a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
