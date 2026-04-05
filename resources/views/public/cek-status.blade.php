@extends('layouts.app')

@section('title', 'Cek Status Pengajuan')
@section('meta_description', 'Pantau status pengajuan reservasi kunjungan sekolah ke UPI menggunakan nomor registrasi atau email.')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('home') }}" class="text-upi-light text-sm hover:underline">← Kembali ke Beranda</a>
        <h1 class="text-2xl font-bold text-upi-blue mt-2">Cek Status Pengajuan</h1>
        <p class="text-gray-500 text-sm mt-1">Masukkan nomor registrasi atau email untuk mengetahui status pengajuan kunjungan Anda.</p>
    </div>

    {{-- Search Form --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-6">
        <form method="POST" action="{{ route('cek-status.cari') }}" id="form-cek-status">
            @csrf
            <div class="flex gap-3">
                <div class="flex-1">
                    <label for="query" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Registrasi atau Email Sekolah</label>
                    <input type="text" id="query" name="query"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-light @error('query') border-red-400 @enderror"
                        value="{{ old('query', request('query') ?? $query ?? '') }}"
                        placeholder="Contoh: UPI-20240101-0001 atau email@sekolah.sch.id"
                        required>
                    @error('query')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" id="btn-cari" class="bg-upi-blue text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:bg-upi-light transition-colors whitespace-nowrap">
                        🔍 Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Results --}}
    @isset($kunjungan)
        @if($kunjungan->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                <div class="text-3xl mb-2">🔍</div>
                <p class="font-semibold text-yellow-800">Tidak ada hasil ditemukan</p>
                <p class="text-yellow-700 text-sm mt-1">Pastikan nomor registrasi atau email yang Anda masukkan benar.</p>
            </div>
        @else
            <p class="text-sm text-gray-500 mb-3">Ditemukan <strong>{{ $kunjungan->count() }}</strong> pengajuan:</p>
            <div class="space-y-4">
                @foreach($kunjungan as $item)
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-semibold">{{ $item->nomor_registrasi }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $item->status_badge_class }}">
                                    {{ $item->status_label }}
                                </span>
                            </div>
                            <h2 class="font-bold text-gray-800 text-base">{{ $item->nama_sekolah }}</h2>
                            <p class="text-gray-500 text-sm mt-0.5">NPSN: {{ $item->npsn }} &bull; {{ $item->alamat }}</p>

                            <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-400 text-xs">Tanggal Kunjungan</span>
                                    <p class="font-medium">{{ $item->tanggal_format }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs">Jumlah Peserta</span>
                                    <p class="font-medium">{{ number_format($item->jumlah_peserta) }} orang</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs">Penanggungjawab</span>
                                    <p class="font-medium">{{ $item->nama_pic }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs">Diajukan</span>
                                    <p class="font-medium">{{ $item->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($item->status !== 'pending' && $item->catatan_admin)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Catatan dari Admin:</p>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2">{{ $item->catatan_admin }}</p>
                    </div>
                    @endif

                    @if($item->status === 'approved')
                    <div class="mt-3 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-800">
                        ✅ <strong>Selamat!</strong> Kunjungan Anda telah disetujui. Silakan datang sesuai jadwal yang telah ditentukan.
                        Hubungi Humas UPI di (022) 2013163 untuk informasi lebih lanjut.
                    </div>
                    @elseif($item->status === 'rejected')
                    <div class="mt-3 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-800">
                        ❌ Mohon maaf, pengajuan kunjungan Anda tidak dapat kami proses. Silakan buat pengajuan baru atau hubungi Humas UPI.
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    @endisset
</div>
@endsection
