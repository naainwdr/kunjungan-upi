@extends('layouts.app')
@section('title', 'Terima Kasih — Survei Kepuasan')

@section('content')
<div class="max-w-lg mx-auto px-4 py-12 text-center">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">🎉</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Terima Kasih!</h1>
        <p class="text-gray-500 text-sm mb-6">
            Survei kepuasan dari <strong>{{ $kunjungan->sekolah->nama ?? 'Instansi/Sekolah' }}</strong><br>
            (PIC: {{ $kunjungan->kontak->nama ?? 'PIC' }})<br>
            telah berhasil dikirimkan. Masukan Anda sangat berarti bagi kami.
        </p>
        @if($kunjungan->survei)
        <div class="bg-[#800000]/5 border border-[#800000]/20 rounded-xl p-4 mb-6">
            <p class="text-xs text-gray-500 mb-1">Rating Anda</p>
            <p class="text-2xl text-yellow-400 font-bold mb-1">{{ $kunjungan->survei->bintang }}</p>
            <p class="text-lg font-bold text-[#800000]">{{ $kunjungan->survei->rating_rata }} / 5.0</p>
        </div>
        @endif
        <a href="{{ route('home') }}" class="inline-block bg-[#800000] text-white px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-[#600000] transition-colors">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
