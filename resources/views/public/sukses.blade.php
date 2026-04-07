@extends('layouts.app')

@section('title', 'Permohonan Berhasil Dikirim')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 text-center">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-8">

        {{-- Icon Success --}}
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Permohonan Berhasil Dikirim!</h1>
        <p class="text-gray-500 text-sm mb-6">
            Terima kasih, <strong>{{ $kunjungan->nama_sekolah }}</strong>.<br>
            Permohonan kunjungan Anda telah kami terima dan sedang dalam proses verifikasi oleh tim Humas UPI.
        </p>

        {{-- Nomor Registrasi --}}
        <div class="bg-upi-red/5 border-2 border-upi-red rounded-xl p-5 mb-6">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Nomor Permohonan Anda</p>
            <p class="text-3xl font-mono font-bold text-upi-red tracking-widest">{{ $kunjungan->nomor_registrasi }}</p>
            <p class="text-xs text-gray-400 mt-2">⚠️ Simpan nomor ini untuk memantau status permohonan Anda.</p>
        </div>

        {{-- Detail Ringkas --}}
        <table class="w-full text-sm text-left border-collapse mb-6">
            <tbody>
                <tr class="border-b border-gray-100">
                    <td class="text-gray-500 py-2 pr-4 w-1/2">Sekolah</td>
                    <td class="font-medium py-2">{{ $kunjungan->nama_sekolah }}</td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="text-gray-500 py-2 pr-4">Tanggal Kunjungan</td>
                    <td class="font-medium py-2">{{ $kunjungan->tanggal_format }}</td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="text-gray-500 py-2 pr-4">Jumlah Peserta</td>
                    <td class="font-medium py-2">{{ number_format($kunjungan->jumlah_peserta) }} orang</td>
                </tr>
                <tr>
                    <td class="text-gray-500 py-2 pr-4">Status</td>
                    <td class="py-2">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-1 rounded-full">⏳ Menunggu Verifikasi</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="text-gray-400 text-xs mb-6">
            Notifikasi akan dikirim ke <strong>{{ $kunjungan->email }}</strong> setelah verifikasi selesai (3–5 hari kerja).
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('cek-status') }}?query={{ $kunjungan->nomor_registrasi }}" id="btn-cek-ulang"
                class="bg-upi-red text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-upi-dark transition-colors">
                🔍 Cek Status Permohonan
            </a>
            <a href="{{ route('home') }}"
                class="border border-gray-300 text-gray-600 px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
