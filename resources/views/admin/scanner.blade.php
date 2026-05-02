@extends('layouts.admin')
@section('title', 'Scanner QR Presensi')
@section('page_title', 'Scanner QR Presensi Kunjungan')

@push('styles')
<style>
#reader { width: 100%; border-radius: 12px; overflow: hidden; }
#reader video { border-radius: 12px; }
#reader img { display: none; }
.pulse-ring { animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>
@endpush

@section('content')
<div class="max-w-6xl">

    {{-- Main Scanner Card --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">

            {{-- LEFT: Camera --}}
            <div class="p-6 border-b lg:border-b-0 lg:border-r border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        📷 Kamera Scanner
                    </h2>
                    {{-- Mode toggle --}}
                    <div class="flex bg-gray-100 rounded-lg p-0.5 gap-0.5" id="mode-toggle">
                        <button onclick="setMode('checkin')" id="btn-mode-in"
                            class="px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-green-600 text-white">
                            ✅ Check-In
                        </button>
                        <button onclick="setMode('checkout')" id="btn-mode-out"
                            class="px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500">
                            🚪 Check-Out
                        </button>
                    </div>
                </div>

                {{-- Scanner --}}
                <div class="relative rounded-xl overflow-hidden bg-black aspect-square max-h-72">
                    <div id="reader" class="w-full h-full"></div>
                    {{-- Scan overlay --}}
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="w-48 h-48 border-2 border-white/60 rounded-lg relative">
                            <span class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-[#800000] rounded-tl-md"></span>
                            <span class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-[#800000] rounded-tr-md"></span>
                            <span class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-[#800000] rounded-bl-md"></span>
                            <span class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-[#800000] rounded-br-md"></span>
                            <span class="absolute inset-x-4 top-1/2 h-0.5 bg-[#800000]/70 pulse-ring"></span>
                        </div>
                    </div>
                </div>

                {{-- Manual input fallback --}}
                <div class="mt-4">
                    <p class="text-xs text-gray-400 mb-2">Atau masukkan nomor registrasi manual:</p>
                    <div class="flex gap-2">
                        <input id="manual-input" type="text" placeholder="UPI-YYYYMMDD-0001"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#800000]/40 uppercase">
                        <button onclick="processCode(document.getElementById('manual-input').value)"
                            class="bg-[#800000] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#600000] transition-colors">
                            Cari
                        </button>
                    </div>
                </div>

                {{-- Scan status --}}
                <div id="scan-status" class="mt-3 hidden text-xs font-semibold text-center py-2 px-3 rounded-lg"></div>
            </div>

            {{-- RIGHT: Result --}}
            <div class="p-6">
                <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    🎫 Informasi Kunjungan
                </h2>

                {{-- Empty state --}}
                <div id="result-empty" class="flex flex-col items-center justify-center h-64 text-center">
                    <div class="text-5xl mb-3">📱</div>
                    <p class="text-gray-400 text-sm">Arahkan kamera ke QR Code tiket<br>untuk menampilkan informasi kunjungan</p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 pulse-ring"></span>
                        <span class="text-xs text-gray-400" id="mode-label">Mode: Check-In</span>
                    </div>
                </div>

                {{-- Result card --}}
                <div id="result-card" class="hidden space-y-4">
                    {{-- School info --}}
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-gray-900 text-base" id="r-sekolah">-</p>
                                <p class="text-xs text-gray-400 font-mono mt-0.5" id="r-nomor">-</p>
                            </div>
                            <span id="r-status-badge" class="text-xs px-2 py-1 rounded-full font-bold flex-shrink-0"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            <div class="bg-white rounded-lg p-2 text-center border border-gray-100">
                                <p class="text-[10px] text-gray-400">Tanggal</p>
                                <p class="text-xs font-bold text-gray-800" id="r-tanggal">-</p>
                            </div>
                            <div class="bg-white rounded-lg p-2 text-center border border-gray-100">
                                <p class="text-[10px] text-gray-400">Sesi</p>
                                <p class="text-xs font-bold text-[#800000]" id="r-sesi">-</p>
                            </div>
                            <div class="bg-white rounded-lg p-2 text-center border border-gray-100">
                                <p class="text-[10px] text-gray-400">Peserta</p>
                                <p class="text-xs font-bold text-gray-800" id="r-peserta">-</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            📍 <span id="r-tempat">-</span>
                        </p>
                        <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                            👤 <span id="r-kontak">-</span> · <span id="r-telepon">-</span>
                        </p>
                    </div>

                    {{-- Presensi status --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-green-500 font-semibold uppercase">Check-In</p>
                            <p class="text-lg font-bold text-green-700" id="r-masuk">-</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-center">
                            <p class="text-[10px] text-blue-500 font-semibold uppercase">Check-Out</p>
                            <p class="text-lg font-bold text-blue-700" id="r-keluar">-</p>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div id="action-area" class="space-y-2"></div>

                    {{-- Reset --}}
                    <button onclick="resetResult()"
                        class="w-full text-center text-gray-400 hover:text-gray-600 text-xs py-2 underline">
                        Scan Tiket Lain
                    </button>
                </div>

                {{-- Error card --}}
                <div id="result-error" class="hidden bg-red-50 border border-red-200 rounded-xl p-5 text-center">
                    <div class="text-3xl mb-2">❌</div>
                    <p class="font-semibold text-red-700 text-sm" id="error-msg">-</p>
                    <button onclick="resetResult()" class="mt-3 text-xs text-red-500 underline">Coba Lagi</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-3 gap-4 mt-4">
        @php
            $todayCi = \App\Models\KunjunganPresensi::whereDate('waktu_masuk', today())->whereNotNull('waktu_masuk')->count();
            $todayCo = \App\Models\KunjunganPresensi::whereDate('waktu_keluar', today())->whereNotNull('waktu_keluar')->count();
            $todayPending = \App\Models\Kunjungan::where('status','approved')->whereDate('tanggal_kunjungan', today())->count();
        @endphp
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-[#800000]">{{ $todayPending }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Kunjungan Hari Ini</p>
        </div>
        <div class="bg-white border border-green-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-green-600">{{ $todayCi }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Sudah Check-In</p>
        </div>
        <div class="bg-white border border-blue-200 rounded-xl p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-blue-600">{{ $todayCo }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Sudah Check-Out</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let currentMode = 'checkin';
let currentKunjunganId = null;
let scanner = null;
let scanCooldown = false;

// Init scanner
window.addEventListener('DOMContentLoaded', () => {
    scanner = new Html5Qrcode("reader");
    Html5Qrcode.getCameras().then(devices => {
        if (!devices.length) return showStatus('Tidak ada kamera ditemukan.', 'error');
        const camId = devices[devices.length - 1].id; // prefer back camera
        scanner.start(camId, { fps: 10, qrbox: { width: 180, height: 180 } },
            (text) => { if (!scanCooldown) processCode(text); },
            () => {}
        ).catch(e => showStatus('Gagal akses kamera: ' + e, 'error'));
    }).catch(() => showStatus('Tidak dapat mengakses kamera.', 'error'));
});

function setMode(mode) {
    currentMode = mode;
    const inBtn  = document.getElementById('btn-mode-in');
    const outBtn = document.getElementById('btn-mode-out');
    document.getElementById('mode-label').textContent = 'Mode: ' + (mode === 'checkin' ? 'Check-In' : 'Check-Out');
    if (mode === 'checkin') {
        inBtn.className  = 'px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-green-600 text-white';
        outBtn.className = 'px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500';
    } else {
        outBtn.className = 'px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-blue-600 text-white';
        inBtn.className  = 'px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500';
    }
    resetResult();
}

async function processCode(code) {
    if (!code || scanCooldown) return;
    code = code.trim().toUpperCase();
    scanCooldown = true;
    setTimeout(() => { scanCooldown = false; }, 3000);

    showStatus('🔍 Mencari data kunjungan...', 'info');

    try {
        const res = await fetch(`{{ route('admin.scanner.lookup') }}?kode=${encodeURIComponent(code)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (!res.ok) {
            showError(data.error || 'Terjadi kesalahan.');
            return;
        }

        currentKunjunganId = data.id;
        renderResult(data);
        showStatus('✅ Data ditemukan!', 'success');

    } catch (e) {
        showError('Gagal menghubungi server. Periksa koneksi.');
    }
}

function renderResult(data) {
    document.getElementById('result-empty').classList.add('hidden');
    document.getElementById('result-error').classList.add('hidden');
    document.getElementById('result-card').classList.remove('hidden');

    document.getElementById('r-sekolah').textContent = data.nama_sekolah;
    document.getElementById('r-nomor').textContent   = data.nomor_registrasi;
    document.getElementById('r-tanggal').textContent = data.tanggal;
    document.getElementById('r-sesi').textContent    = data.sesi;
    document.getElementById('r-peserta').textContent = data.jumlah_peserta + ' org';
    document.getElementById('r-tempat').textContent  = data.tempat;
    document.getElementById('r-kontak').textContent  = data.kontak_nama;
    document.getElementById('r-telepon').textContent = data.kontak_telepon;
    document.getElementById('r-masuk').textContent   = data.waktu_masuk || '-';
    document.getElementById('r-keluar').textContent  = data.waktu_keluar || '-';

    const badge = document.getElementById('r-status-badge');
    const statusMap = {
        'belum':    ['Belum Presensi', 'bg-gray-200 text-gray-600'],
        'checkin':  ['Sudah Masuk', 'bg-green-100 text-green-700'],
        'checkout': ['Selesai', 'bg-blue-100 text-blue-700'],
    };
    const [label, cls] = statusMap[data.presensi_status] || ['Unknown', ''];
    badge.textContent = label;
    badge.className   = 'text-xs px-2 py-1 rounded-full font-bold flex-shrink-0 ' + cls;

    renderActions(data);
}

function renderActions(data) {
    const area = document.getElementById('action-area');
    area.innerHTML = '';

    const status = data.presensi_status;
    const canIn  = status === 'belum' && currentMode === 'checkin';
    const canOut = status === 'checkin' && currentMode === 'checkout';

    if (canIn) {
        area.innerHTML = `
        <form method="POST" action="/admin/presensi/${currentKunjunganId}/checkin">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                ✅ Konfirmasi Check-In Sekarang
            </button>
        </form>`;
    } else if (canOut) {
        area.innerHTML = `
        <form method="POST" action="/admin/presensi/${currentKunjunganId}/checkout">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                🚪 Konfirmasi Check-Out Sekarang
            </button>
        </form>`;
    } else if (status === 'checkout') {
        area.innerHTML = `<div class="bg-purple-50 border border-purple-200 rounded-xl p-3 text-center text-sm text-purple-700 font-semibold">🏁 Kunjungan selesai · Durasi: ${data.durasi || '-'}</div>`;
    } else if (currentMode === 'checkin' && status !== 'belum') {
        area.innerHTML = `<div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-center text-xs text-amber-700">Check-in sudah tercatat. Ganti ke mode Check-Out untuk keluar.</div>`;
    } else if (currentMode === 'checkout' && status === 'belum') {
        area.innerHTML = `<div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-center text-xs text-amber-700">Check-in belum dilakukan. Ganti ke mode Check-In terlebih dahulu.</div>`;
    }
}

function showStatus(msg, type) {
    const el = document.getElementById('scan-status');
    el.textContent = msg;
    el.className = {
        success: 'mt-3 text-xs font-semibold text-center py-2 px-3 rounded-lg bg-green-100 text-green-700',
        error:   'mt-3 text-xs font-semibold text-center py-2 px-3 rounded-lg bg-red-100 text-red-700',
        info:    'mt-3 text-xs font-semibold text-center py-2 px-3 rounded-lg bg-blue-100 text-blue-700',
    }[type] || '';
    el.classList.remove('hidden');
}

function showError(msg) {
    document.getElementById('result-empty').classList.add('hidden');
    document.getElementById('result-card').classList.add('hidden');
    document.getElementById('result-error').classList.remove('hidden');
    document.getElementById('error-msg').textContent = msg;
}

function resetResult() {
    currentKunjunganId = null;
    scanCooldown = false;
    document.getElementById('result-card').classList.add('hidden');
    document.getElementById('result-error').classList.add('hidden');
    document.getElementById('result-empty').classList.remove('hidden');
    document.getElementById('scan-status').classList.add('hidden');
    document.getElementById('manual-input').value = '';
}
</script>
@endpush
