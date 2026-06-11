@extends('layouts.app')

@section('title', 'Formulir Permohonan Kunjungan')
@section('meta_description', 'Isi formulir permohonan kunjungan sekolah ke Universitas Pendidikan Indonesia (UPI).')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-5 text-sm flex items-center gap-1.5 text-gray-400">
        <a href="{{ route('home') }}" class="hover:text-upi-red transition-colors">Beranda</a>
        <span>›</span>
        <a href="{{ route('kalender') }}" class="hover:text-upi-red transition-colors">Kalender</a>
        <span>›</span>
        <span class="text-gray-600 font-medium">Formulir Permohonan</span>
    </div>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-upi-red to-red-800 rounded-2xl p-6 mb-6 text-white shadow-lg">
        <h1 class="text-2xl font-bold mb-1">📋 Formulir Permohonan Kunjungan</h1>
        <p class="text-red-200 text-sm">Universitas Pendidikan Indonesia —  KKIPP</p>
    </div>

    {{-- Errors Global --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
        <p class="font-semibold text-red-700 text-sm mb-1">⚠️ Harap perbaiki kesalahan berikut:</p>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('reservasi.store') }}" enctype="multipart/form-data"
          id="form-permohonan" onsubmit="return handleSubmit(this)">
        @csrf

        {{-- ===================== A. INFO SEKOLAH ===================== --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-5">
            <h2 class="flex items-center gap-2 font-bold text-gray-800 mb-4">
                <span class="w-7 h-7 bg-upi-red text-white rounded-full text-sm flex items-center justify-center font-bold">A</span>
                Informasi Sekolah
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="nama_sekolah" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_sekolah" name="nama_sekolah"
                        class="w-full border @error('nama_sekolah') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('nama_sekolah') }}" placeholder="Contoh: SMA Negeri 1 Bandung" required>
                    @error('nama_sekolah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="npsn" class="block text-sm font-medium text-gray-700 mb-1">
                        NPSN <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="npsn" name="npsn"
                        class="w-full border @error('npsn') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('npsn') }}" placeholder="Contoh: 20219706" required>
                    @error('npsn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Telepon Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="telepon" name="telepon_sekolah"
                        class="w-full border @error('telepon_sekolah') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('telepon_sekolah') }}" placeholder="Contoh: 022-1234567" required>
                    @error('telepon_sekolah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email_sekolah"
                        class="w-full border @error('email_sekolah') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('email_sekolah') }}" placeholder="email@sekolah.sch.id" required>
                    @error('email_sekolah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Lengkap Sekolah <span class="text-red-500">*</span>
                    </label>
                    <textarea id="alamat" name="alamat" rows="2"
                        class="w-full border @error('alamat') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40 resize-none"
                        placeholder="Jl. Nama Jalan No. XX, Kelurahan, Kecamatan, Kota" required>{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ===================== B. PENANGGUNGJAWAB ===================== --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-5">
            <h2 class="flex items-center gap-2 font-bold text-gray-800 mb-4">
                <span class="w-7 h-7 bg-upi-red text-white rounded-full text-sm flex items-center justify-center font-bold">B</span>
                Penanggungjawab Kunjungan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama_pic" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Kepala Sekolah / PIC <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_pic" name="nama_pic"
                        class="w-full border @error('nama_pic') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('nama_pic') }}" placeholder="Nama lengkap beserta gelar" required>
                    @error('nama_pic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jenis_pic" class="block text-sm font-medium text-gray-700 mb-1">
                        Jabatan Penanggungjawab <span class="text-red-500">*</span>
                    </label>
                    <select id="jenis_pic" name="jabatan_pic"
                        class="w-full border @error('jabatan_pic') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40 bg-white" required>
                        <option value="">-- Pilih jabatan --</option>
                        <option value="kepsek" {{ old('jabatan_pic') == 'kepsek' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="guru"   {{ old('jabatan_pic') == 'guru'   ? 'selected' : '' }}>Guru</option>
                        <option value="tendik" {{ old('jabatan_pic') == 'tendik' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                    </select>
                    @error('jabatan_pic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email_pic" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Penanggungjawab <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email_pic" name="email_pic"
                        class="w-full border @error('email_pic') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('email_pic') }}" placeholder="email@pic.sch.id" required>
                    <p class="text-gray-400 text-xs mt-1">✉️ Notifikasi status akan dikirim ke email ini.</p>
                    @error('email_pic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="telepon_pic" class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Telepon Penanggungjawab <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="telepon_pic" name="telepon_pic"
                        class="w-full border @error('telepon_pic') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('telepon_pic') }}" placeholder="Contoh: 081234567890" required>
                    @error('telepon_pic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ===================== C. DETAIL KUNJUNGAN ===================== --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-5">
            <h2 class="flex items-center gap-2 font-bold text-gray-800 mb-4">
                <span class="w-7 h-7 bg-upi-red text-white rounded-full text-sm flex items-center justify-center font-bold">C</span>
                Detail Kunjungan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Tanggal --}}
                <div class="sm:col-span-2">
                    <label for="tanggal_kunjungan" class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Kunjungan <span class="text-red-500">*</span>
                    </label>
                    @if($tanggal)
                        {{-- Pre-filled dari kalender --}}
                        <div class="flex items-center gap-3">
                            <div class="flex-1 bg-emerald-50 border border-emerald-300 rounded-xl px-4 py-2.5 text-sm text-emerald-800 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse(old('tanggal_kunjungan', $tanggal))->isoFormat('dddd, D MMMM Y') }}
                            </div>
                            <a href="{{ route('kalender') }}" class="text-xs text-upi-red hover:underline whitespace-nowrap">Ganti tanggal</a>
                        </div>
                        <input type="hidden" id="tanggal_kunjungan" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan', $tanggal) }}">
                    @else
                        <input type="date" id="tanggal_kunjungan" name="tanggal_kunjungan"
                            class="w-full border @error('tanggal_kunjungan') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                            value="{{ old('tanggal_kunjungan') }}"
                            min="{{ now()->addDays(10)->toDateString() }}" 
                            onchange="fetchBookedHours()" required>
                        <p class="text-gray-400 text-xs mt-1">📅 Minimal 10 hari dari hari ini. Hanya <strong>Senin–Kamis</strong>.
                            <a href="{{ route('kalender') }}" class="text-upi-red hover:underline ml-1">Pilih dari kalender →</a>
                        </p>
                    @endif
                    @error('tanggal_kunjungan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sesi Kunjungan --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sesi Kunjungan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($sesi as $s)
                        <label class="cursor-pointer">
                            <input type="radio" name="sesi_id" value="{{ $s->id }}" class="peer sr-only"
                                {{ old('sesi_id') == $s->id ? 'checked' : '' }} required>
                            <div class="border-2 border-gray-200 peer-checked:border-upi-red peer-checked:bg-red-50 rounded-xl p-4 hover:border-upi-red/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">{{ $loop->first ? '🌅' : '☀️' }}</span>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $s->nama }}</p>
                                        <p class="text-upi-red font-semibold text-sm">{{ substr($s->jam_mulai,0,5) }} – {{ substr($s->jam_selesai,0,5) }} WIB</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-xs mt-2 flex items-center gap-1.5">
                        ⚠️ <span>Harap datang <strong>selambatnya 30 menit sebelum sesi dimulai</strong> untuk proses registrasi.</span>
                    </p>
                    @error('sesi_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tempat Kunjungan --}}
                <div class="sm:col-span-2">
                    <label for="tempat" class="block text-sm font-medium text-gray-700 mb-1">
                        Tempat Kunjungan <span class="text-red-500">*</span>
                    </label>
                    <select id="tempat" name="tempat_id" onchange="updateKapasitas()"
                        class="w-full border @error('tempat_id') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40 bg-white" required>
                        <option value="">-- Pilih tempat kunjungan --</option>
                        @foreach($tempat as $t)
                        <option value="{{ $t->id }}" data-cap="{{ $t->kapasitas }}"
                            {{ old('tempat_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->nama }} (maks. {{ number_format($t->kapasitas) }} orang)
                        </option>
                        @endforeach
                    </select>
                    <p id="kapasitas-info" class="text-gray-400 text-xs mt-1 hidden">📍 Kapasitas maksimal: <strong id="kapasitas-val"></strong> orang.</p>
                    @error('tempat_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jumlah Peserta --}}
                <div>
                    <label for="jumlah_peserta" class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Peserta <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="jumlah_peserta" name="jumlah_peserta"
                        class="w-full border @error('jumlah_peserta') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('jumlah_peserta') }}" min="1" max="500"
                        placeholder="Contoh: 50" required>
                    <p class="text-gray-400 text-xs mt-1">👥 Termasuk guru pendamping. Kapasitas sesuai tempat yang dipilih.</p>
                    @error('jumlah_peserta') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jumlah Kepsek --}}
                <div>
                    <label for="jumlah_kepsek" class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Kepala Sekolah
                    </label>
                    <input type="number" id="jumlah_kepsek" name="jumlah_kepsek"
                        class="w-full border @error('jumlah_kepsek') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('jumlah_kepsek', 0) }}" min="0" max="10"
                        placeholder="0">
                    @error('jumlah_kepsek') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jumlah Guru --}}
                <div>
                    <label for="jumlah_guru" class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Guru
                    </label>
                    <input type="number" id="jumlah_guru" name="jumlah_guru"
                        class="w-full border @error('jumlah_guru') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('jumlah_guru', 0) }}" min="0" max="100"
                        placeholder="0">
                    @error('jumlah_guru') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jumlah Tendik --}}
                <div>
                    <label for="jumlah_tendik" class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Tenaga Kependidikan
                    </label>
                    <input type="number" id="jumlah_tendik" name="jumlah_tendik"
                        class="w-full border @error('jumlah_tendik') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('jumlah_tendik', 0) }}" min="0" max="50"
                        placeholder="0">
                    @error('jumlah_tendik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ===================== D. SURAT PERMOHONAN ===================== --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="flex items-center gap-2 font-bold text-gray-800 mb-4">
                <span class="w-7 h-7 bg-upi-red text-white rounded-full text-sm flex items-center justify-center font-bold">D</span>
                Surat Permohonan
            </h2>

            <label class="block text-sm font-medium text-gray-700 mb-2">
                Upload Surat Permohonan Resmi <span class="text-red-500">*</span>
            </label>

            {{-- Contoh surat --}}
            <div class="mb-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-xs font-semibold text-blue-700 mb-2">📄 Contoh / Template Surat Permohonan:</p>
                <a href="https://docs.google.com/document/d/12a4thOHk9uW5mdbqfMicy_CIElKfAdZ-DWYJFd8PYoE/edit?usp=sharing" target="_blank" rel="noopener"
                   class="flex items-center gap-3 hover:opacity-80 transition-opacity group">
                    <div class="flex-shrink-0 w-16 h-20 bg-white border-2 border-blue-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
                        <div class="bg-blue-600 h-3 w-full"></div>
                        <div class="p-1.5 flex-1 space-y-1">
                            <div class="h-1.5 bg-gray-300 rounded w-full"></div>
                            <div class="h-1.5 bg-gray-300 rounded w-3/4"></div>
                            <div class="h-1.5 bg-gray-200 rounded w-full mt-2"></div>
                            <div class="h-1.5 bg-gray-200 rounded w-full"></div>
                            <div class="h-1.5 bg-gray-200 rounded w-5/6"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-blue-700 group-hover:underline">Lihat Template Surat Permohonan</p>
                        <p class="text-xs text-blue-500 mt-0.5">Klik untuk membuka di Google Docs →</p>
                        <p class="text-xs text-gray-400 mt-1">Surat harus berkop sekolah & ditandatangani Kepala Sekolah</p>
                    </div>
                </a>
            </div>

            <div id="drop-zone"
                class="relative border-2 border-dashed @error('file_surat') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl p-8 text-center cursor-pointer hover:border-upi-red hover:bg-red-50/40 transition-colors"
                onclick="document.getElementById('file_surat').click()"
                ondragover="event.preventDefault(); this.classList.add('border-upi-red','bg-red-50/40')"
                ondragleave="this.classList.remove('border-upi-red','bg-red-50/40')"
                ondrop="handleDrop(event)">

                <input type="file" id="file_surat" name="file_surat" accept=".pdf,.jpg,.jpeg"
                    class="absolute inset-0 opacity-0 cursor-pointer" onchange="showFileName(this)" required>

                <div id="drop-placeholder">
                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-gray-600">
                        <span class="text-upi-red font-semibold">Klik untuk pilih file</span> atau drag & drop
                    </p>
                    <p class="text-xs text-gray-400 mt-1">PDF atau JPG • Maksimal 1 MB • Harus berkop sekolah</p>
                </div>
                <div id="file-selected" class="hidden">
                    <svg class="w-8 h-8 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm font-semibold text-green-700" id="file-name"></p>
                    <p class="text-xs text-green-600 mt-0.5">File siap diunggah</p>
                </div>
            </div>
            @error('file_surat') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between gap-4">
            <p class="text-xs text-gray-400 flex-1">Dengan mengklik tombol ini, Anda menyetujui bahwa data yang diisi adalah benar.</p>
            <div class="flex gap-3">
                <a href="{{ route('kalender') }}" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-xl font-medium text-sm hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" id="btn-submit"
                    class="px-8 py-2.5 bg-upi-red text-white rounded-xl font-bold text-sm hover:bg-red-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Kirim Permohonan →
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Kapasitas dari tempat
function updateKapasitas() {
    const sel = document.getElementById('tempat');
    const opt = sel.options[sel.selectedIndex];
    const cap = opt ? opt.getAttribute('data-cap') : null;
    const info = document.getElementById('kapasitas-info');
    const capVal = document.getElementById('kapasitas-val');
    const pesertaInput = document.getElementById('jumlah_peserta');
    if (cap) {
        capVal.textContent = parseInt(cap).toLocaleString('id-ID');
        info.classList.remove('hidden');
        pesertaInput.max = cap;
    } else {
        info.classList.add('hidden');
        pesertaInput.max = 500;
    }
    fetchBookedHours();
}

// ── File drop/select ───────────────────────────────────
function showFileName(input) {
    if (!input.files.length) return;
    const f = input.files[0];
    document.getElementById('drop-placeholder').classList.add('hidden');
    const fn = document.getElementById('file-name');
    fn.textContent = f.name + ' (' + (f.size / 1024).toFixed(1) + ' KB)';
    document.getElementById('file-selected').classList.remove('hidden');
}

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').classList.remove('border-upi-red','bg-red-50/40');
    const dt = e.dataTransfer;
    if (dt.files.length) {
        const inp = document.getElementById('file_surat');
        inp.files = dt.files;
        showFileName(inp);
    }
}

// ── Anti double-submit ─────────────────────────────────
function handleSubmit(form) {
    const btn = document.getElementById('btn-submit');
    if (btn.disabled) return false;
    btn.disabled = true;
    btn.textContent = 'Mengirim...';
    return true;
}

// ── Sesi Availability ─────────────────────────────────
async function fetchBookedHours() {
    const tanggal = document.getElementById('tanggal_kunjungan')?.value;
    const tempatId = document.getElementById('tempat')?.value;
    const btnSubmit = document.getElementById('btn-submit');
    
    if (!tanggal) return;

    try {
        const response = await fetch(`/api/booked-sesi?tanggal=${tanggal}&tempat_id=${tempatId || ''}`);
        const bookedSesi = await response.json();

        const radios = document.querySelectorAll('input[name="sesi_id"]');
        let availableCount = 0;

        radios.forEach(radio => {
            const container = radio.closest('label');
            const isBooked = bookedSesi.includes(parseInt(radio.value)) || bookedSesi.includes(radio.value);
            
            if (isBooked) {
                radio.disabled = true;
                radio.checked = false;
                container.classList.add('opacity-40', 'grayscale', 'cursor-not-allowed');
                container.classList.remove('cursor-pointer');
            } else {
                radio.disabled = false;
                container.classList.remove('opacity-40', 'grayscale', 'cursor-not-allowed');
                container.classList.add('cursor-pointer');
                availableCount++;
            }
        });

        if (availableCount === 0) {
            // Show alert or message
            console.log('Tidak ada sesi tersedia');
        }
    } catch (error) {
        console.error('Gagal mengambil data sesi:', error);
    }
}

window.addEventListener('DOMContentLoaded', () => { 
    updateKapasitas(); 
    fetchBookedHours();
});
</script>
@endpush
@endsection
