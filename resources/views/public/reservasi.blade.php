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
        <p class="text-red-200 text-sm">Universitas Pendidikan Indonesia — Humas & Protokol</p>
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
                    <input type="tel" id="telepon" name="telepon"
                        class="w-full border @error('telepon') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('telepon') }}" placeholder="Contoh: 022-1234567" required>
                    @error('telepon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Penanggungjawab <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                        class="w-full border @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                        value="{{ old('email') }}" placeholder="email@sekolah.sch.id" required>
                    <p class="text-gray-400 text-xs mt-1">✉️ Notifikasi status akan dikirim ke email ini.</p>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                        <input type="hidden" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan', $tanggal) }}">
                    @else
                        <input type="date" id="tanggal_kunjungan" name="tanggal_kunjungan"
                            class="w-full border @error('tanggal_kunjungan') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40"
                            value="{{ old('tanggal_kunjungan') }}"
                            min="{{ now()->addDays(7)->toDateString() }}" required>
                        <p class="text-gray-400 text-xs mt-1">📅 Minimal 7 hari dari hari ini. Senin–Jumat.
                            <a href="{{ route('kalender') }}" class="text-upi-red hover:underline ml-1">Pilih dari kalender →</a>
                        </p>
                    @endif
                    @error('tanggal_kunjungan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jam Mulai & Selesai --}}
                <div>
                    <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <select id="jam_mulai" name="jam_mulai" onchange="updateJamSelesai()"
                        class="w-full border @error('jam_mulai') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40 bg-white" required>
                        <option value="">-- Pilih jam mulai --</option>
                        @foreach(['08:00','09:00','10:00','11:00','12:00','13:00','14:00'] as $jam)
                            <option value="{{ $jam }}" {{ old('jam_mulai') == $jam ? 'selected' : '' }}>{{ $jam }} WIB</option>
                        @endforeach
                    </select>
                    @error('jam_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Selesai <span class="text-red-500">*</span>
                    </label>
                    <select id="jam_selesai" name="jam_selesai"
                        class="w-full border @error('jam_selesai') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-upi-red/40 bg-white" required>
                        <option value="">-- Pilih jam mulai dulu --</option>
                    </select>
                    <p class="text-gray-400 text-xs mt-1">⏱ Durasi minimal 2 jam, maksimal 5 jam. Selesai paling lambat 16:00.</p>
                    @error('jam_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                    <p class="text-gray-400 text-xs mt-1">👥 Termasuk guru pendamping. Maks. 500 orang.</p>
                    @error('jumlah_peserta') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
// ── Time picker logic ──────────────────────────────────
function updateJamSelesai() {
    const mulaiEl   = document.getElementById('jam_mulai');
    const selesaiEl = document.getElementById('jam_selesai');
    const oldVal    = '{{ old("jam_selesai") }}';
    const startH    = parseInt(mulaiEl.value);

    selesaiEl.innerHTML = '<option value="">-- Pilih jam selesai --</option>';

    if (isNaN(startH)) return;

    // Min 2 jam, Max 5 jam, tapi selesai paling lambat 16:00
    for (let h = startH + 2; h <= Math.min(startH + 5, 16); h++) {
        const val  = `${String(h).padStart(2,'0')}:00`;
        const dur  = h - startH;
        const opt  = document.createElement('option');
        opt.value  = val;
        opt.textContent = `${val} WIB  (${dur} jam)`;
        if (val === oldVal) opt.selected = true;
        selesaiEl.appendChild(opt);
    }
}

// Restore old value on page load (after validation error)
window.addEventListener('DOMContentLoaded', () => {
    const mulai = document.getElementById('jam_mulai');
    if (mulai && mulai.value) updateJamSelesai();
});

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
</script>
@endpush
@endsection
