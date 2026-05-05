@extends('layouts.app')
@section('title', 'Survei Kepuasan Kunjungan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    @if(isset($belumCheckout))
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center shadow-sm">
        <div class="text-4xl mb-3">⏳</div>
        <h1 class="text-xl font-bold text-amber-800 mb-2">Belum Check-Out</h1>
        <p class="text-amber-700 text-sm">Survei hanya dapat diisi setelah kunjungan selesai (check-out).</p>
    </div>
    @elseif(isset($sudahIsi))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center shadow-sm">
        <div class="text-4xl mb-3">✅</div>
        <h1 class="text-xl font-bold text-green-800 mb-2">Survei Sudah Diisi</h1>
        <p class="text-green-700 text-sm">Terima kasih! Anda sudah mengisi survei kepuasan untuk kunjungan ini.</p>
        <p class="text-green-600 text-xs mt-3">Rating rata-rata Anda: <strong>{{ $kunjungan->survei->rating_rata }}★</strong></p>
    </div>
    @elseif(isset($kadaluarsa))
    <div class="bg-red-50 border border-red-200 rounded-2xl p-8 text-center shadow-sm">
        <div class="text-4xl mb-3">⌛</div>
        <h1 class="text-xl font-bold text-red-800 mb-2">Link Survei Kadaluarsa</h1>
        <p class="text-red-700 text-sm">Maaf, survei hanya dapat diisi dalam 7 hari setelah kunjungan.</p>
    </div>
    @else

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-[#800000]/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">📝</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Survei Kepuasan Kunjungan</h1>
        <p class="text-gray-500 text-sm mt-1">
            <strong>{{ $kunjungan->sekolah->nama ?? 'Instansi/Sekolah' }}</strong><br>
            PIC: {{ $kunjungan->kontak->nama ?? 'PIC' }} &middot; Kunjungan: {{ $kunjungan->tanggal_format }}
        </p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('survei.store', $kunjungan->nomor_registrasi) }}" class="space-y-6">
        @csrf

        {{-- Rating Cards --}}
        @foreach([
            ['rating_pelayanan', 'Pelayanan Petugas', 'Keramahan, responsivitas, dan profesionalisme petugas.', '👨‍💼'],
            ['rating_fasilitas', 'Kualitas Fasilitas', 'Kondisi venue, kebersihan, kenyamanan tempat.', '🏛️'],
            ['rating_informasi', 'Kelengkapan Informasi', 'Kejelasan informasi dan materi yang disampaikan.', '📚'],
        ] as [$name, $label, $desc, $icon])
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <span class="text-2xl">{{ $icon }}</span>
                <div>
                    <p class="font-bold text-gray-800 text-sm">{{ $label }}</p>
                    <p class="text-xs text-gray-400">{{ $desc }}</p>
                </div>
            </div>
            <div class="flex gap-2 justify-center" id="stars-{{ $name }}">
                @for($i = 1; $i <= 5; $i++)
                <label class="cursor-pointer group">
                    <input type="radio" name="{{ $name }}" value="{{ $i }}" class="sr-only"
                        {{ old($name) == $i ? 'checked' : '' }} required>
                    <span class="text-3xl transition-all duration-150 group-hover:scale-110
                        star-{{ $name }} {{ old($name) >= $i ? 'text-yellow-400' : 'text-gray-200' }}"
                        data-val="{{ $i }}" data-group="{{ $name }}">★</span>
                </label>
                @endfor
            </div>
            <div class="text-center mt-2 text-xs text-gray-400" id="label-{{ $name }}">
                {{ old($name) ? ['', 'Sangat Kurang', 'Kurang', 'Cukup', 'Baik', 'Sangat Baik'][old($name)] : 'Pilih rating' }}
            </div>
            @error($name)<p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p>@enderror
        </div>
        @endforeach

        {{-- Komentar --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <label class="block font-bold text-gray-800 text-sm mb-2">💬 Komentar (Opsional)</label>
            <textarea name="komentar" rows="3" placeholder="Bagaimana kesan Anda selama kunjungan ke UPI?"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#800000]/40 resize-none">{{ old('komentar') }}</textarea>
        </div>

        {{-- Saran --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <label class="block font-bold text-gray-800 text-sm mb-2">💡 Saran & Masukan (Opsional)</label>
            <textarea name="saran" rows="3" placeholder="Ada saran untuk perbaikan layanan kami?"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#800000]/40 resize-none">{{ old('saran') }}</textarea>
        </div>

        <button type="submit"
            class="w-full bg-[#800000] text-white py-3.5 rounded-xl font-bold text-base hover:bg-[#600000] transition-colors shadow-md">
            Kirim Survei Kepuasan →
        </button>
    </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
const labels = ['', 'Sangat Kurang 😞', 'Kurang 😐', 'Cukup 🙂', 'Baik 😊', 'Sangat Baik 🤩'];
document.querySelectorAll('input[type=radio]').forEach(radio => {
    radio.addEventListener('change', function() {
        const group = this.name;
        const val   = parseInt(this.value);
        document.querySelectorAll(`.star-${group}`).forEach(star => {
            const sv = parseInt(star.dataset.val);
            star.classList.toggle('text-yellow-400', sv <= val);
            star.classList.toggle('text-gray-200',   sv > val);
        });
        document.getElementById(`label-${group}`).textContent = labels[val] || '';
    });
});
document.querySelectorAll('.star-${g}').forEach(star => {
    star.addEventListener('mouseenter', function() {
        const g = this.dataset.group;
        const v = parseInt(this.dataset.val);
        document.querySelectorAll(`.star-${g}`).forEach(s => {
            s.classList.toggle('text-yellow-300', parseInt(s.dataset.val) <= v);
        });
    });
});
</script>
@endpush
