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
                    <button type="submit" id="btn-cari" class="bg-upi-red text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:bg-red-800 transition-colors whitespace-nowrap">
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
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-semibold">{{ $item->nomor_registrasi }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $item->status_badge_class }}">
                                    {{ $item->status_label }}
                                </span>
                            </div>
                            <h2 class="font-bold text-gray-800 text-base">{{ $item->sekolah->nama }}</h2>
                            <p class="text-gray-500 text-sm mt-0.5">NPSN: {{ $item->sekolah->npsn }} &bull; {{ $item->sekolah->alamat }}</p>

                            <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-400 text-xs">Tanggal Kunjungan</span>
                                    <p class="font-medium">{{ $item->tanggal_format }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs">Sesi</span>
                                    <p class="font-medium">{{ $item->sesi?->label ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs">Tempat</span>
                                    <p class="font-medium">{{ $item->tempat?->nama ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs">Jumlah Peserta</span>
                                    <p class="font-medium">{{ number_format($item->jumlah_peserta) }} orang</p>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs">Penanggungjawab</span>
                                    <p class="font-medium">{{ $item->kontak?->nama ?? '-' }}</p>
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
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4 flex flex-col sm:flex-row gap-4 items-center sm:items-start">
                        <div class="flex-shrink-0 bg-white p-2 rounded-xl shadow-sm border border-green-100" id="qr-container-{{ $item->nomor_registrasi }}">
                            {{-- QR Code will be injected here via JS --}}
                        </div>
                        <div class="text-sm text-green-800 text-center sm:text-left">
                            <p class="mb-2">✅ <strong>Selamat!</strong> Kunjungan Anda telah disetujui. Silakan tunjukkan QR Code ini atau e-Ticket saat kedatangan untuk registrasi (scan check-in).</p>
                            <p class="mb-3">Hubungi kami: 📲 <a href="https://wa.me/6285133332559" target="_blank" class="font-semibold hover:underline">085133332559</a> atau ✉️ humas@upi.edu</p>
                            <a href="{{ route('reservasi.tiket', ['id' => $item->nomor_registrasi]) }}" target="_blank" class="inline-flex items-center gap-2 bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-green-800 transition-colors">
                                🎟️ Buka &amp; Unduh e-Ticket
                            </a>
                        </div>
                    </div>
                    @elseif($item->status === 'rejected')
                    <div class="mt-3 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-800">
                        ❌ Mohon maaf, pengajuan kunjungan Anda tidak dapat kami proses. Silakan buat pengajuan baru atau hubungi Humas UPI.
                    </div>
                    @elseif($item->status === 'cancelled')
                    <div class="mt-3 bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700">
                        🛑 Pengajuan kunjungan ini telah dibatalkan secara mandiri.
                    </div>
                    @elseif($item->status === 'completed')
                    <div class="mt-3 bg-purple-50 border border-purple-200 rounded-lg px-4 py-3 text-sm text-purple-800">
                        🏁 Kunjungan telah selesai. Terima kasih telah menggunakan layanan UPI.
                    </div>
                    @if($item->updated_at->diffInDays(now()) <= 7)
                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-gray-600">Form evaluasi tersedia selama 7 hari setelah kunjungan selesai.</p>
                        <a href="{{ route('evaluasi.form', ['id' => $item->nomor_registrasi]) }}" class="inline-flex items-center justify-center bg-upi-blue text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                            Isi Form Evaluasi
                        </a>
                    </div>
                    @else
                    <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 text-sm text-yellow-800">
                        ⚠️ Form evaluasi sudah tidak tersedia lagi karena sudah lewat 7 hari sejak kelengkapan kunjungan.
                    </div>
                    @endif
                    @endif

                    {{-- Action for Cancellation --}}
                    @if(in_array($item->status, ['pending', 'approved']))
                        @if(now()->startOfDay()->lte($item->tanggal_kunjungan->clone()->subDays(7)->startOfDay()))
                            <div class="mt-4 border-t border-gray-100 pt-4">
                                <button type="button" onclick="document.getElementById('modal-cancel-{{ $item->nomor_registrasi }}').classList.remove('hidden')" class="bg-red-50 border border-red-200 hover:bg-red-100 text-red-700 font-semibold px-4 py-2 rounded-lg text-sm transition-colors">
                                    🗑️ Batalkan Permohonan
                                </button>
                                <p class="text-xs text-gray-400 mt-1">Pembatalan hanya dapat dilakukan sampai <strong>H-7</strong> sebelum kunjungan.</p>
                            </div>

                            {{-- MODAL CANCEL --}}
                            <div id="modal-cancel-{{ $item->nomor_registrasi }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center transform transition-all">
                                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl">⚠️</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">Batalkan Permohonan?</h3>
                                    <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin membatalkan permohonan kunjungan <strong class="text-gray-800">{{ $item->nama_sekolah }}</strong>? Tindakan ini tidak dapat dikembalikan.</p>
                                    
                                    <form action="{{ route('reservasi.batal', $item->nomor_registrasi) }}" method="POST" class="flex gap-3">
                                        @csrf
                                        <button type="button" onclick="document.getElementById('modal-cancel-{{ $item->nomor_registrasi }}').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-2.5 rounded-xl hover:bg-gray-200 transition-colors">
                                            Kembali
                                        </button>
                                        <button type="submit" class="flex-1 bg-upi-red text-white font-semibold py-2.5 rounded-xl hover:bg-red-800 transition-colors">
                                            Ya, Batalkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs text-red-500 font-semibold">⚠️ Batas waktu pembatalan online telah lewat (H-7).</p>
                                <p class="text-xs text-gray-500">Jika batal hadir, segera hubungi Humas UPI:<br>
                                    📲 WhatsApp: <a href="https://wa.me/6285133332559" target="_blank" class="text-green-600 font-semibold hover:underline">085133332559</a> &bull;
                                    ✉️ <a href="mailto:humas@upi.edu" class="text-blue-600 hover:underline">humas@upi.edu</a>
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    @endisset
</div>
@endsection

@push('scripts')
@if(isset($kunjungan) && $kunjungan->isNotEmpty() && $kunjungan->contains('status', 'approved'))
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @foreach($kunjungan as $item)
            @if($item->status === 'approved')
            new QRCode(document.getElementById("qr-container-{{ $item->nomor_registrasi }}"), {
                text: "{{ $item->nomor_registrasi }}",
                width: 90,
                height: 90,
                colorDark: "#166534", // text-green-800
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
            @endif
        @endforeach
    });
</script>
@endif
@endpush
