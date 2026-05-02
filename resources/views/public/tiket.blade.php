@extends('layouts.app')
@section('title', 'Tiket Kunjungan — ' . $kunjungan->nomor_registrasi)

@push('styles')
<style>
@media print {
    nav, footer, .no-print { display: none !important; }
    body { background: white !important; }
    .ticket-card { box-shadow: none !important; border: 2px solid #333 !important; }
}
.star-gold { color: #f59e0b; }
</style>
@endpush

@section('content')
<div class="max-w-lg mx-auto px-4 py-10">

    {{-- Actions --}}
    <div class="flex gap-3 mb-6 no-print">
        <button onclick="window.print()" class="flex items-center gap-2 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-900 transition-colors">
            🖨️ Print Tiket
        </button>
        <a href="{{ route('cek-status') }}" class="text-gray-500 text-sm hover:underline flex items-center">← Cek Status</a>
    </div>

    {{-- Ticket Card --}}
    <div class="ticket-card bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

        {{-- Header --}}
        <div class="bg-[#800000] px-6 py-5 text-white text-center">
            <div class="flex items-center justify-center gap-3 mb-1">
                @php $logoPath = public_path('images/logo-kkipp.png'); @endphp
                @if(file_exists($logoPath))
                <img src="{{ asset('images/logo-kkipp.png') }}" alt="KKIPP UPI" class="h-10 object-contain brightness-0 invert">
                @endif
                <div class="text-left">
                    <p class="font-bold text-sm leading-tight">UNIVERSITAS PENDIDIKAN INDONESIA</p>
                    <p class="text-xs text-red-200">Sistem Kunjungan Sekolah</p>
                </div>
            </div>
            <div class="mt-3 inline-block bg-white/20 rounded-full px-4 py-1">
                <p class="text-xs font-semibold tracking-widest uppercase">Tiket Kunjungan</p>
            </div>
        </div>

        {{-- Perforated line --}}
        <div class="relative">
            <div class="border-t-2 border-dashed border-gray-200 mx-4"></div>
            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-gray-100 rounded-full border border-gray-200"></span>
            <span class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-gray-100 rounded-full border border-gray-200"></span>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5">
            <div class="flex items-start gap-5">

                {{-- QR Code --}}
                <div class="flex-shrink-0 flex flex-col items-center">
                    <div id="qrcode" class="border-2 border-gray-200 rounded-xl p-2 bg-white shadow-sm"></div>
                    <p class="text-[9px] text-gray-400 mt-1.5 text-center font-mono">{{ $kunjungan->nomor_registrasi }}</p>
                </div>

                {{-- Info --}}
                <div class="flex-1 space-y-2.5">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Sekolah</p>
                        <p class="font-bold text-gray-900 text-sm leading-tight">{{ $kunjungan->sekolah->nama }}</p>
                        <p class="text-xs text-gray-400">NPSN: {{ $kunjungan->sekolah->npsn }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Tanggal</p>
                            <p class="text-xs font-semibold text-gray-800">{{ $kunjungan->tanggal_kunjungan->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Sesi</p>
                            <p class="text-xs font-semibold text-[#800000]">{{ $kunjungan->sesi->nama }}</p>
                            <p class="text-[10px] text-gray-500">{{ $kunjungan->sesi->label }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Tempat</p>
                            <p class="text-xs font-semibold text-gray-800">{{ $kunjungan->tempat->nama }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Peserta</p>
                            <p class="text-xs font-semibold text-gray-800">{{ number_format($kunjungan->jumlah_peserta) }} orang</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status presensi --}}
            @if($kunjungan->presensi)
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="bg-green-50 border border-green-200 rounded-xl px-3 py-2 text-center">
                    <p class="text-[10px] text-green-500 font-semibold uppercase">Check-In</p>
                    <p class="text-sm font-bold text-green-700">{{ $kunjungan->presensi->waktu_masuk?->format('H:i') ?? '-' }}</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl px-3 py-2 text-center">
                    <p class="text-[10px] text-blue-500 font-semibold uppercase">Check-Out</p>
                    <p class="text-sm font-bold text-blue-700">{{ $kunjungan->presensi->waktu_keluar?->format('H:i') ?? '-' }}</p>
                </div>
            </div>
            @endif

            {{-- Imbauan --}}
            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 flex items-start gap-2">
                <span class="text-base">⚠️</span>
                <p class="text-[10px] text-amber-700 leading-relaxed">
                    Harap datang <strong>selambatnya 30 menit sebelum sesi dimulai</strong> untuk registrasi ulang dan scan tiket.
                </p>
            </div>
        </div>

        {{-- Footer ticket --}}
        <div class="bg-gray-50 border-t border-dashed border-gray-200 px-6 py-3 text-center">
            <p class="text-[10px] text-gray-400">Tiket ini berlaku sebagai bukti permohonan kunjungan yang telah disetujui.</p>
            <p class="text-[10px] text-gray-400">Info: humas@upi.edu · (022) 2013163 · 085133332559</p>
        </div>
    </div>

    {{-- Status badge --}}
    <div class="mt-4 text-center no-print">
        <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $kunjungan->status_badge_class }}">
            {{ $kunjungan->status_label }}
        </span>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById("qrcode"), {
    text: "{{ $kunjungan->nomor_registrasi }}",
    width: 110,
    height: 110,
    colorDark: "#800000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.M
});
</script>
@endpush
