@extends('layouts.app')

@section('title', 'Form Evaluasi Kunjungan - ' . $kunjungan->nomor_registrasi)

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="text-6xl mb-4">📝</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Form Evaluasi Kunjungan</h1>
        <p class="text-gray-600">Bantu kami meningkatkan kualitas layanan dengan mengisi form evaluasi berikut.</p>
    </div>

    {{-- Info Kunjungan --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
        <h2 class="font-bold text-blue-800 mb-3">📅 Detail Kunjungan</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-blue-600 text-xs">Nomor Registrasi</dt>
                <dd class="font-mono font-bold text-blue-800">{{ $kunjungan->nomor_registrasi }}</dd>
            </div>
            <div>
                <dt class="text-blue-600 text-xs">Tanggal Kunjungan</dt>
                <dd class="text-blue-700">{{ $kunjungan->tanggal_format }}</dd>
            </div>
            <div>
                <dt class="text-blue-600 text-xs">Sekolah</dt>
                <dd class="text-blue-700">{{ $kunjungan->nama_sekolah }}</dd>
            </div>
            <div>
                <dt class="text-blue-600 text-xs">PIC</dt>
                <dd class="text-blue-700">{{ $kunjungan->nama_pic }} ({{ $kunjungan->jenis_pic_format }})</dd>
            </div>
        </dl>
    </div>

    {{-- Form Evaluasi --}}
    <form method="POST" action="{{ route('evaluasi.simpan', $kunjungan->nomor_registrasi) }}" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        @csrf

        {{-- Rating Pelayanan --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                1. Bagaimana penilaian Anda terhadap pelayanan yang diberikan? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
                @for($i = 1; $i <= 5; $i++)
                <label class="flex items-center">
                    <input type="radio" name="rating_pelayanan" value="{{ $i }}" class="text-upi-blue focus:ring-upi-blue" required>
                    <span class="ml-2 text-sm">{{ $i }} <span class="text-gray-400">({{ ['Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'][$i-1] }})</span></span>
                </label>
                @endfor
            </div>
        </div>

        {{-- Rating Fasilitas --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                2. Bagaimana penilaian Anda terhadap fasilitas yang tersedia? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
                @for($i = 1; $i <= 5; $i++)
                <label class="flex items-center">
                    <input type="radio" name="rating_fasilitas" value="{{ $i }}" class="text-upi-blue focus:ring-upi-blue" required>
                    <span class="ml-2 text-sm">{{ $i }} <span class="text-gray-400">({{ ['Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'][$i-1] }})</span></span>
                </label>
                @endfor
            </div>
        </div>

        {{-- Rating Informasi --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                3. Bagaimana penilaian Anda terhadap informasi yang diberikan? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
                @for($i = 1; $i <= 5; $i++)
                <label class="flex items-center">
                    <input type="radio" name="rating_informasi" value="{{ $i }}" class="text-upi-blue focus:ring-upi-blue" required>
                    <span class="ml-2 text-sm">{{ $i }} <span class="text-gray-400">({{ ['Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'][$i-1] }})</span></span>
                </label>
                @endfor
            </div>
        </div>

        {{-- Komentar --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                4. Komentar/Kesan selama kunjungan (opsional)
            </label>
            <textarea name="komentar" rows="3" placeholder="Berikan komentar atau kesan Anda selama kunjungan..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-blue"></textarea>
        </div>

        {{-- Saran --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                5. Saran untuk perbaikan (opsional)
            </label>
            <textarea name="saran" rows="3" placeholder="Berikan saran untuk meningkatkan layanan kami..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-upi-blue"></textarea>
        </div>

        {{-- Submit --}}
        <div class="text-center">
            <button type="submit" class="bg-upi-red text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:bg-red-800 transition-colors whitespace-nowrap">
                Kirim Evaluasi
            </button>
        </div>
    </form>
</div>
@endsection