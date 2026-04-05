@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Pengajuan Kunjungan')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Total', $counts['all'], 'bg-blue-50 text-blue-700', '📋'],
        ['Menunggu', $counts['pending'], 'bg-yellow-50 text-yellow-700', '⏳'],
        ['Disetujui', $counts['approved'], 'bg-green-50 text-green-700', '✅'],
        ['Ditolak', $counts['rejected'], 'bg-red-50 text-red-700', '❌'],
    ] as $stat)
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <div class="text-xl mb-1">{{ $stat[3] }}</div>
        <div class="text-2xl font-bold {{ explode(' ', $stat[2])[1] }}">{{ $stat[1] }}</div>
        <div class="text-xs text-gray-500">{{ $stat[0] }}</div>
    </div>
    @endforeach
</div>

{{-- Filter & Search --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('admin.dashboard') }}" id="form-filter" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" placeholder="Cari nama sekolah, nomor registrasi..." value="{{ request('search') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-light">
        </div>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-light">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="bg-upi-blue text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-upi-light transition-colors">Filter</button>
        @if(request('search') || request('status'))
        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 text-sm px-3 py-2 hover:text-gray-700">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($kunjungan->isEmpty())
    <div class="text-center py-12 text-gray-400">
        <div class="text-4xl mb-2">📭</div>
        <p>Belum ada pengajuan masuk.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">No. Registrasi</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Nama Sekolah</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Tgl. Kunjungan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Peserta</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Diajukan</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($kunjungan as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 whitespace-nowrap">{{ $item->nomor_registrasi }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $item->nama_sekolah }}</div>
                        <div class="text-xs text-gray-400">NPSN: {{ $item->npsn }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $item->tanggal_format }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ number_format($item->jumlah_peserta) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $item->status_badge_class }}">
                            {{ $item->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">{{ $item->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.kunjungan.show', $item) }}" class="text-upi-light font-medium hover:underline text-xs">Lihat Detail →</a>
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
@endsection
