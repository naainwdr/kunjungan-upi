@extends('layouts.admin')

@section('title', 'Detail Pengajuan - ' . $kunjungan->nomor_registrasi)
@section('page_title', 'Detail Pengajuan Kunjungan')

@section('content')
<div class="max-w-4xl">
    {{-- Breadcrumb --}}
    <div class="mb-5 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-upi-light hover:underline">Dashboard</a>
        <span class="text-gray-400 mx-2">→</span>
        <span class="text-gray-600">{{ $kunjungan->nomor_registrasi }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main Detail Card --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Info Sekolah --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-gray-800">🏫 Informasi Sekolah</h2>
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $kunjungan->status_badge_class }}">
                        {{ $kunjungan->status_label }}
                    </span>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Nama Sekolah</dt>
                        <dd class="font-semibold text-gray-800">{{ $kunjungan->nama_sekolah }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">NPSN</dt>
                        <dd class="font-mono font-semibold">{{ $kunjungan->npsn }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-400 text-xs mb-0.5">Alamat</dt>
                        <dd class="text-gray-700">{{ $kunjungan->alamat }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Penanggungjawab</dt>
                        <dd class="text-gray-700">{{ $kunjungan->nama_pic }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Telepon</dt>
                        <dd class="text-gray-700">{{ $kunjungan->telepon }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Email</dt>
                        <dd class="text-gray-700">{{ $kunjungan->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Email Notifikasi</dt>
                        <dd>
                            @if($kunjungan->email_notified_at)
                                <span class="text-green-600 text-xs">✓ Dikirim {{ $kunjungan->email_notified_at->diffForHumans() }}</span>
                            @else
                                <span class="text-gray-400 text-xs">Belum dikirim</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Detail Kunjungan --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-gray-800 mb-4">📅 Detail Kunjungan</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Nomor Registrasi</dt>
                        <dd class="font-mono font-bold text-upi-blue">{{ $kunjungan->nomor_registrasi }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Tanggal Diajukan</dt>
                        <dd class="text-gray-700">{{ $kunjungan->created_at->isoFormat('dddd, D MMMM Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Tanggal Kunjungan</dt>
                        <dd class="font-semibold text-gray-800">{{ $kunjungan->tanggal_format }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-xs mb-0.5">Jumlah Peserta</dt>
                        <dd class="font-semibold text-gray-800">{{ number_format($kunjungan->jumlah_peserta) }} orang</dd>
                    </div>
                </dl>
            </div>

            {{-- Surat Permohonan --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-gray-800 mb-3">📎 Surat Permohonan</h2>
                @if($kunjungan->file_surat)
                    @php
                        // Deteksi apakah URL Cloudinary (http/https) atau path lokal
                        $isCloudinary = str_starts_with($kunjungan->file_surat, 'http');
                        $fileUrl = $isCloudinary
                            ? $kunjungan->file_surat
                            : asset('storage/' . $kunjungan->file_surat);
                    @endphp
                    <a href="{{ $fileUrl }}" target="_blank"
                        class="inline-flex items-center gap-2 text-upi-light hover:underline text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Buka / Download Surat Permohonan
                        @if($isCloudinary)
                            <span class="text-xs text-gray-400">(Cloudinary)</span>
                        @endif
                    </a>
                @else
                    <p class="text-gray-400 text-sm">Tidak ada file surat.</p>
                @endif
            </div>

            @if($kunjungan->catatan_admin)
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                <h2 class="font-bold text-gray-800 mb-2">📝 Catatan Admin</h2>
                <p class="text-sm text-gray-700">{{ $kunjungan->catatan_admin }}</p>
            </div>
            @endif
        </div>

        {{-- Action Sidebar --}}
        <div class="space-y-5">
            @if($kunjungan->status === 'pending')
            {{-- Approve --}}
            <div class="bg-white border border-green-200 rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-green-700 mb-3">✅ Setujui Permohonan</h2>
                <form method="POST" action="{{ route('admin.kunjungan.approve', $kunjungan) }}" id="form-approve">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Catatan untuk Sekolah (opsional)</label>
                        <textarea name="catatan_admin" rows="3" placeholder="Contoh: Hadir pukul 09.00 WIB. Hubungi panitia di 022-2013163."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                    </div>

                    {{-- Konfirmasi sebelum submit --}}
                    <div id="confirm-approve" class="hidden mb-3 p-3 bg-green-50 border border-green-300 rounded-lg text-sm">
                        <p class="text-green-800 font-medium mb-2">Yakin setujui permohonan ini?</p>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-green-600 text-white py-1.5 rounded font-bold text-xs hover:bg-green-700">
                                ✓ Ya, Setujui
                            </button>
                            <button type="button" onclick="document.getElementById('confirm-approve').classList.add('hidden');"
                                class="flex-1 border border-gray-300 text-gray-600 py-1.5 rounded text-xs hover:bg-gray-50">
                                Batal
                            </button>
                        </div>
                    </div>

                    <button type="button"
                        onclick="document.getElementById('confirm-approve').classList.remove('hidden'); this.classList.add('hidden');"
                        class="w-full bg-green-600 text-white py-2.5 rounded-lg font-bold text-sm hover:bg-green-700 transition-colors">
                        ✓ Setujui Kunjungan
                    </button>
                </form>
            </div>

            {{-- Reject --}}
            <div class="bg-white border border-red-200 rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-red-700 mb-3">❌ Tolak Permohonan</h2>
                <form method="POST" action="{{ route('admin.kunjungan.reject', $kunjungan) }}" id="form-reject">
                    @csrf
                    @error('catatan_admin')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="catatan_admin" rows="3" placeholder="Jelaskan alasan penolakan secara singkat..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400" required>{{ old('catatan_admin') }}</textarea>
                    </div>

                    {{-- Konfirmasi sebelum submit --}}
                    <div id="confirm-reject" class="hidden mb-3 p-3 bg-red-50 border border-red-300 rounded-lg text-sm">
                        <p class="text-red-800 font-medium mb-2">Yakin tolak permohonan ini?</p>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-red-600 text-white py-1.5 rounded font-bold text-xs hover:bg-red-700">
                                ✓ Ya, Tolak
                            </button>
                            <button type="button" onclick="document.getElementById('confirm-reject').classList.add('hidden');"
                                class="flex-1 border border-gray-300 text-gray-600 py-1.5 rounded text-xs hover:bg-gray-50">
                                Batal
                            </button>
                        </div>
                    </div>

                    <button type="button"
                        onclick="document.getElementById('confirm-reject').classList.remove('hidden'); this.classList.add('hidden');"
                        class="w-full bg-red-600 text-white py-2.5 rounded-lg font-bold text-sm hover:bg-red-700 transition-colors">
                        ✗ Tolak Permohonan
                    </button>
                </form>
            </div>

            @elseif($kunjungan->status === 'approved')
            <div class="bg-green-50 border border-green-200 rounded-xl p-5 text-center">
                <div class="text-3xl mb-2">✅</div>
                <p class="font-bold text-green-700 text-sm">Pengajuan Telah Disetujui</p>
                <p class="text-green-600 text-xs mt-1">{{ $kunjungan->updated_at->diffForHumans() }}</p>
            </div>
            @elseif($kunjungan->status === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center">
                <div class="text-3xl mb-2">❌</div>
                <p class="font-bold text-red-700 text-sm">Pengajuan Telah Ditolak</p>
                <p class="text-red-600 text-xs mt-1">{{ $kunjungan->updated_at->diffForHumans() }}</p>
            </div>
            @endif

            <a href="{{ route('admin.dashboard') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 py-2">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
