@extends('layouts.admin')
@section('title', 'Rekap Presensi')
@section('page_title', 'Rekap Presensi Kunjungan')

@section('content')
<div class="max-w-6xl">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        @foreach([
            ['Total Presensi', $counts['all'],   'text-blue-700',  'bg-blue-50 border-blue-200',   '📋'],
            ['Sedang di Dalam', $counts['masuk'], 'text-green-700', 'bg-green-50 border-green-200', '✅'],
            ['Sudah Keluar',   $counts['keluar'],'text-blue-700',  'bg-blue-50 border-blue-200',   '🚪'],
        ] as $s)
        <div class="bg-white rounded-xl border {{ $s[3] }} p-4 shadow-sm">
            <div class="text-xl mb-1">{{ $s[4] }}</div>
            <div class="text-3xl font-bold {{ $s[2] }}">{{ $s[1] }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $s[0] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4 shadow-sm">
        <form method="GET" action="{{ route('admin.presensi.index') }}">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Status filter --}}
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                    @foreach(['all' => 'Semua', 'masuk' => 'Sedang Dalam', 'keluar' => 'Sudah Keluar'] as $val => $lbl)
                    <a href="{{ route('admin.presensi.index', array_merge(request()->query(), ['filter' => $val])) }}"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all
                        {{ $filter === $val ? 'bg-[#800000] text-white' : 'text-gray-600 hover:bg-gray-200' }}">
                        {{ $lbl }}
                    </a>
                    @endforeach
                </div>

                {{-- Tanggal --}}
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-500 font-semibold">📅 Tanggal:</label>
                    <input type="date" name="tgl" value="{{ request('tgl') }}"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#800000]/40">
                    <button type="submit" class="bg-[#800000] text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-[#600000] transition-colors">
                        Filter
                    </button>
                    @if(request('tgl'))
                    <a href="{{ route('admin.presensi.index', ['filter' => $filter]) }}" class="text-gray-400 text-xs hover:underline">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if($presensi->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <div class="text-4xl mb-2">📭</div>
            <p>Belum ada data presensi.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">No. Registrasi</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Sekolah</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Tgl Kunjungan</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Sesi / Tempat</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Check-In</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Check-Out</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Durasi</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($presensi as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.kunjungan.show', $p->kunjungan) }}" class="font-mono text-xs text-[#800000] hover:underline font-semibold">
                                {{ $p->kunjungan->nomor_registrasi }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-xs text-gray-800">{{ $p->kunjungan->sekolah->nama }}</p>
                            <p class="text-[10px] text-gray-400">{{ number_format($p->kunjungan->jumlah_peserta) }} orang</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            {{ $p->kunjungan->tanggal_kunjungan->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs font-semibold text-[#800000]">{{ $p->kunjungan->sesi->nama ?? '-' }}</p>
                            <p class="text-[10px] text-gray-400">{{ $p->kunjungan->tempat->nama ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($p->waktu_masuk)
                                <span class="text-xs font-bold text-green-700">{{ $p->waktu_masuk->format('H:i:s') }}</span>
                                <p class="text-[10px] text-gray-400">{{ $p->petugasMasuk?->name ?? 'Sistem' }}</p>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($p->waktu_keluar)
                                <span class="text-xs font-bold text-blue-700">{{ $p->waktu_keluar->format('H:i:s') }}</span>
                                <p class="text-[10px] text-gray-400">{{ $p->petugasKeluar?->name ?? 'Sistem' }}</p>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs font-semibold text-purple-700">
                            {{ $p->durasi ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @php $st = $p->status; @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $st === 'checkout' ? 'bg-blue-100 text-blue-700' : ($st === 'checkin' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500') }}">
                                {{ $st === 'checkout' ? 'Selesai' : ($st === 'checkin' ? 'Di Dalam' : 'Belum') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $presensi->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
