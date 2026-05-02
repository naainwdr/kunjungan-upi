@extends('layouts.app')

@section('title', 'Terima Kasih - Evaluasi Kunjungan')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    {{-- Header --}}
    <div class="text-6xl mb-4">🙏</div>
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Terima Kasih!</h1>
    <p class="text-lg text-gray-600 mb-8">
        Evaluasi Anda telah berhasil disimpan. Terima kasih atas feedback yang diberikan.
    </p>

    {{-- Info --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
        <div class="text-4xl mb-3">✅</div>
        <h2 class="text-xl font-bold text-green-800 mb-2">Evaluasi Berhasil Dikirim</h2>
        <p class="text-green-700 text-sm">
            Feedback Anda akan membantu kami meningkatkan kualitas layanan kunjungan ke UPI.
        </p>
    </div>

    {{-- Detail --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-8 text-left">
        <h3 class="font-bold text-gray-800 mb-3">Detail Kunjungan</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-gray-400 text-xs">Nomor Registrasi</dt>
                <dd class="font-mono font-bold text-gray-800">{{ $kunjungan->nomor_registrasi }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">Tanggal Kunjungan</dt>
                <dd class="text-gray-700">{{ $kunjungan->tanggal_format }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">Sekolah</dt>
                <dd class="text-gray-700">{{ $kunjungan->nama_sekolah }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">PIC</dt>
                <dd class="text-gray-700">{{ $kunjungan->nama_pic }}</dd>
            </div>
        </dl>
    </div>

    {{-- Actions --}}
    <div class="flex gap-4 justify-center">
        <a href="{{ route('home') }}" class="bg-upi-red text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:bg-red-800 transition-colors whitespace-nowrap">
            Kembali ke Beranda
        </a>
        <a href="{{ route('cek-status') }}" class="border border-gray-300 text-gray-600 px-6 py-3 rounded-lg font-bold text-sm hover:bg-gray-50 transition-colors">
            Cek Status Lain
        </a>
    </div>

    <!-- footer -->
    <div class="text-center mt-8 text-sm text-gray-500"></div>
    
</div>
@endsection