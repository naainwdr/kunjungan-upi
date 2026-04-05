@extends('layouts.app')

@section('title', 'Formulir Reservasi Kunjungan')
@section('meta_description', 'Isi formulir reservasi kunjungan sekolah ke UPI secara online. Lengkapi data sekolah, tanggal, peserta, dan unggah surat permohonan.')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="mb-6">
        <a href="{{ route('home') }}" class="text-upi-light text-sm hover:underline">← Kembali ke Beranda</a>
        <h1 class="text-2xl font-bold text-upi-blue mt-2">Formulir Reservasi Kunjungan</h1>
        <p class="text-gray-500 text-sm mt-1">Lengkapi semua kolom yang bertanda <span class="text-red-500 font-semibold">*</span> dengan benar.</p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-5" role="alert">
        <p class="text-red-700 font-semibold text-sm mb-2">⚠️ Harap perbaiki kesalahan berikut:</p>
        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <form id="form-reservasi" method="POST" action="{{ route('reservasi.store') }}" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- Seksi: Identitas Sekolah --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-upi-blue uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">A. Identitas Sekolah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="nama_sekolah" class="form-label">Nama Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_sekolah" name="nama_sekolah" class="form-input @error('nama_sekolah') border-red-400 @enderror" value="{{ old('nama_sekolah') }}" placeholder="Contoh: SMA Negeri 1 Bandung" required>
                        @error('nama_sekolah')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="npsn" class="form-label">NPSN <span class="text-red-500">*</span></label>
                        <input type="text" id="npsn" name="npsn" class="form-input @error('npsn') border-red-400 @enderror" value="{{ old('npsn') }}" placeholder="Nomor Pokok Sekolah Nasional" maxlength="20" required>
                        @error('npsn')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="telepon" class="form-label">Nomor Telepon Sekolah <span class="text-red-500">*</span></label>
                        <input type="tel" id="telepon" name="telepon" class="form-input @error('telepon') border-red-400 @enderror" value="{{ old('telepon') }}" placeholder="Contoh: 022-1234567" required>
                        @error('telepon')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat" class="form-label">Alamat Sekolah <span class="text-red-500">*</span></label>
                        <textarea id="alamat" name="alamat" class="form-input @error('alamat') border-red-400 @enderror" rows="2" placeholder="Alamat lengkap sekolah" required>{{ old('alamat') }}</textarea>
                        @error('alamat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Seksi: Penanggungjawab --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-upi-blue uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">B. Penanggungjawab Kunjungan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_pic" class="form-label">Nama Kepala Sekolah / Penanggungjawab <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_pic" name="nama_pic" class="form-input @error('nama_pic') border-red-400 @enderror" value="{{ old('nama_pic') }}" placeholder="Nama lengkap beserta gelar" required>
                        @error('nama_pic')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="form-label">Email Sekolah / Penanggungjawab <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" class="form-input @error('email') border-red-400 @enderror" value="{{ old('email') }}" placeholder="email@sekolah.sch.id" required>
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-gray-400 text-xs mt-1">Email ini akan digunakan untuk menerima notifikasi status.</p>
                    </div>
                </div>
            </div>

            {{-- Seksi: Detail Kunjungan --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-upi-blue uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">C. Detail Kunjungan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_kunjungan" class="form-label">Tanggal Kunjungan yang Diinginkan <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_kunjungan" name="tanggal_kunjungan" class="form-input @error('tanggal_kunjungan') border-red-400 @enderror" value="{{ old('tanggal_kunjungan') }}" min="{{ now()->addDays(7)->toDateString() }}" required>
                        @error('tanggal_kunjungan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-gray-400 text-xs mt-1">Minimal 7 hari dari sekarang. Senin–Jumat.</p>
                    </div>
                    <div>
                        <label for="jumlah_peserta" class="form-label">Jumlah Peserta <span class="text-red-500">*</span></label>
                        <input type="number" id="jumlah_peserta" name="jumlah_peserta" class="form-input @error('jumlah_peserta') border-red-400 @enderror" value="{{ old('jumlah_peserta') }}" min="1" max="500" placeholder="Contoh: 50" required>
                        @error('jumlah_peserta')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-gray-400 text-xs mt-1">Termasuk guru pendamping. Maks. 500 orang.</p>
                    </div>
                </div>
            </div>

            {{-- Seksi: Surat Permohonan --}}
            <div class="mb-6">
                <h2 class="text-sm font-bold text-upi-blue uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">D. Surat Permohonan</h2>
                <div>
                    <label for="file_surat" class="form-label">Upload Surat Permohonan <span class="text-red-500">*</span></label>
                    <input type="file" id="file_surat" name="file_surat" class="w-full text-sm text-gray-500 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-upi-light px-3 py-2 @error('file_surat') border-red-400 @enderror" accept=".pdf,.jpg,.jpeg" required>
                    @error('file_surat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-gray-400 text-xs mt-1">Format: PDF atau JPG. Ukuran maksimal <strong>1 MB</strong>. Surat harus berkop sekolah.</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="border-t border-gray-100 pt-4 flex flex-col sm:flex-row items-center gap-3">
                <button type="submit" id="btn-submit" class="bg-upi-blue text-white px-8 py-3 rounded-lg font-bold hover:bg-upi-light transition-colors w-full sm:w-auto">
                    Kirim Pengajuan Reservasi →
                </button>
                <a href="{{ route('home') }}" class="text-gray-500 text-sm hover:text-gray-700 text-center">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Cegah submit ganda
    document.getElementById('form-reservasi').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.textContent = 'Mengupload...';
    });

    // Tampilkan nama file yang dipilih
    document.getElementById('file_surat').addEventListener('change', function() {
        if (this.files[0]) {
            const sizeKB = (this.files[0].size / 1024).toFixed(1);
            this.nextElementSibling?.remove();
            const info = document.createElement('p');
            info.className = 'text-green-600 text-xs mt-1';
            info.textContent = `✓ File dipilih: ${this.files[0].name} (${sizeKB} KB)`;
            this.parentNode.insertBefore(info, this.nextSibling);
        }
    });
</script>
@endpush
@endsection
