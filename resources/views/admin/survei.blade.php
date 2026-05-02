@extends('layouts.admin')
@section('title', 'Survei Kepuasan')
@section('page_title', 'Survei Kepuasan Kunjungan')

@section('content')
<div class="max-w-6xl">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-5">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm text-center md:col-span-1">
            <p class="text-3xl font-bold text-[#800000]">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Survei</p>
        </div>
        @foreach([
            ['Pelayanan', $stats['avg_pelayanan'], 'text-green-600', 'bg-green-50 border-green-200'],
            ['Fasilitas', $stats['avg_fasilitas'], 'text-blue-600', 'bg-blue-50 border-blue-200'],
            ['Informasi', $stats['avg_informasi'], 'text-purple-600', 'bg-purple-50 border-purple-200'],
            ['Rata-rata', $stats['avg_total'], 'text-[#800000]', 'bg-red-50 border-red-200'],
        ] as [$label, $val, $color, $bg])
        <div class="bg-white border {{ $bg }} rounded-xl p-4 shadow-sm text-center">
            <div class="flex items-center justify-center gap-1 mb-1">
                <span class="text-xl font-bold {{ $color }}">{{ $val }}</span>
                <span class="text-xs text-yellow-400">★</span>
            </div>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Search --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4 shadow-sm">
        <form method="GET" action="{{ route('admin.survei.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama sekolah..."
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#800000]/40">
            <button type="submit" class="bg-[#800000] text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-[#600000] transition-colors">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('admin.survei.index') }}" class="text-gray-400 text-sm hover:underline flex items-center">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @if($survei->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <div class="text-4xl mb-2">📝</div>
            <p>Belum ada survei masuk.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Sekolah</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Tgl Kunjungan</th>
                        <th class="text-center px-3 py-3 font-semibold text-gray-600 text-xs">Pelayanan</th>
                        <th class="text-center px-3 py-3 font-semibold text-gray-600 text-xs">Fasilitas</th>
                        <th class="text-center px-3 py-3 font-semibold text-gray-600 text-xs">Informasi</th>
                        <th class="text-center px-3 py-3 font-semibold text-gray-600 text-xs">Rata²</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs">Komentar</th>
                        <th class="text-center px-3 py-3 font-semibold text-gray-600 text-xs">Publik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($survei as $s)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-xs text-gray-800">{{ $s->kunjungan->sekolah->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $s->kunjungan->nomor_registrasi }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $s->kunjungan->tanggal_kunjungan->format('d M Y') }}</td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-bold text-sm text-green-600">{{ $s->rating_pelayanan }}</span>
                            <span class="text-yellow-400 text-xs">★</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-bold text-sm text-blue-600">{{ $s->rating_fasilitas }}</span>
                            <span class="text-yellow-400 text-xs">★</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-bold text-sm text-purple-600">{{ $s->rating_informasi }}</span>
                            <span class="text-yellow-400 text-xs">★</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-bold text-sm text-[#800000]">{{ $s->rating_rata }}</span>
                            <span class="text-yellow-400 text-xs">★</span>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="text-xs text-gray-600 line-clamp-2">{{ $s->komentar ?: '-' }}</p>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <form method="POST" action="{{ route('admin.survei.toggle', $s) }}">
                                @csrf
                                <button type="submit" class="text-lg" title="{{ $s->tampilkan_publik ? 'Klik untuk sembunyikan' : 'Klik untuk tampilkan' }}">
                                    {{ $s->tampilkan_publik ? '✅' : '⬜' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $survei->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
