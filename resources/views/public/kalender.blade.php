@extends('layouts.app')

@section('title', 'Kalender Kunjungan — Pilih Tanggal')
@section('meta_description', 'Pilih tanggal kunjungan sekolah ke UPI melalui kalender interaktif. Lihat jadwal yang sudah disetujui.')

@section('content')
@php
use Carbon\Carbon;
$today      = Carbon::today();
$minDate    = $today->copy()->addDays(7);
$monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$firstDay   = Carbon::create($year, $month, 1);
$startOffset = ($firstDay->dayOfWeek + 6) % 7; // 0=Mon, 6=Sun
$daysInMonth = $firstDay->daysInMonth;
$prevMonth   = $month == 1 ? 12 : $month - 1;
$prevYear    = $month == 1 ? $year - 1 : $year;
$nextMonth   = $month == 12 ? 1 : $month + 1;
$nextYear    = $month == 12 ? $year + 1 : $year;
@endphp

<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Breadcrumb & Title --}}
    <div class="mb-6">
        <a href="{{ route('home') }}" class="text-upi-light text-sm hover:underline">← Kembali ke Beranda</a>
        <h1 class="text-2xl font-bold text-upi-red mt-2">📅 Kalender Kunjungan</h1>
        <p class="text-gray-500 text-sm mt-1">Pilih tanggal kunjungan yang tersedia. Klik tanggal untuk mengajukan permohonan.</p>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-x-3 gap-y-2 mb-5 text-xs text-gray-600">
        <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-white border border-gray-300 inline-block"></span> Tersedia</div>
        <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-red-100 inline-block"></span> Libur</div>
        <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-amber-50 border border-amber-200 inline-block"></span> &lt;7 hari</div>
        <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-gray-100 inline-block"></span> Lewat</div>
        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-green-500 inline-flex items-center justify-center text-white text-xs font-bold">2</span> Disetujui</div>
    </div>

    {{-- Calendar Card --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-md overflow-hidden">

        {{-- Month Header --}}
        <div class="bg-gradient-to-r from-upi-red to-red-700 text-white px-6 py-4 flex items-center justify-between">
            <a href="{{ route('kalender', ['year' => $prevYear, 'month' => $prevMonth]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors text-white font-bold text-lg">‹</a>
            <div class="text-center">
                <h2 class="text-xl font-bold tracking-wide">{{ $monthNames[$month-1] }} {{ $year }}</h2>
                @if($month == now()->month && $year == now()->year)
                    <span class="text-xs text-red-200">Bulan ini</span>
                @endif
            </div>
            <a href="{{ route('kalender', ['year' => $nextYear, 'month' => $nextMonth]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors text-white font-bold text-lg">›</a>
        </div>

        {{-- Day Name Header --}}
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            @foreach([
                ['Sen','S'],['Sel','S'],['Rab','R'],['Kam','K'],['Jum','J'],['Sab','S'],['Min','M']
            ] as [$full,$short])
            <div class="text-center text-xs font-bold py-2 {{ in_array($full, ['Sab','Min']) ? 'text-red-400' : 'text-gray-500' }}">
                <span class="hidden sm:inline">{{ $full }}</span>
                <span class="sm:hidden">{{ $short }}</span>
            </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7">
            {{-- Leading empty --}}
            @for ($i = 0; $i < $startOffset; $i++)
                <div class="min-h-[52px] sm:min-h-[70px] bg-gray-50/40 border-b border-r border-gray-100"></div>
            @endfor

            {{-- Day Cells --}}
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date       = Carbon::create($year, $month, $day);
                    $dateStr    = $date->format('Y-m-d');
                    $dow        = $date->dayOfWeek; // 0=Sun,6=Sat
                    $isWeekend  = in_array($dow, [0, 6]);
                    $isHoliday  = isset($holidays[$dateStr]);
                    $holName    = $isHoliday ? $holidays[$dateStr] : '';
                    $isPast     = $date->lt($today);
                    $isTooSoon  = !$isPast && $date->lt($minDate);
                    $isToday    = $date->isToday();
                    $isBlocked  = $isWeekend || $isHoliday;
                    $count      = $approvedVisits->get($dateStr, 0);

                    if ($isPast) {
                        $bg     = 'bg-gray-50 cursor-not-allowed';
                        $action = 'past';
                        $numCls = 'text-gray-300';
                    } elseif ($isBlocked) {
                        $bg     = 'bg-red-50 hover:bg-red-100 cursor-pointer transition-colors';
                        $action = $isHoliday ? 'holiday' : 'weekend';
                        $numCls = 'text-red-500 font-semibold';
                    } elseif ($isTooSoon) {
                        $bg     = 'bg-amber-50 cursor-not-allowed';
                        $action = 'toosoon';
                        $numCls = 'text-amber-400';
                    } else {
                        $bg     = 'bg-white hover:bg-emerald-50 cursor-pointer transition-colors';
                        $action = 'ok';
                        $numCls = $isToday ? 'text-upi-red font-bold' : 'text-gray-800 font-medium';
                    }
                    $holNameJs = addslashes($holName);
                @endphp

                <div class="min-h-[52px] sm:min-h-[70px] border-b border-r border-gray-100 p-1 sm:p-1.5 relative select-none {{ $bg }} {{ $isToday ? 'ring-2 ring-inset ring-upi-red' : '' }}"
                    onclick="handleDayClick('{{ $dateStr }}', '{{ $action }}', '{{ $holNameJs }}', {{ $count }})">

                    {{-- Day number --}}
                    <span class="text-sm {{ $numCls }}">{{ $day }}</span>

                    {{-- Today badge --}}
                    @if($isToday)
                        <span class="absolute top-0.5 right-0.5 text-[9px] sm:text-[10px] bg-upi-red text-white rounded px-1 py-0.5 leading-none">Hari ini</span>
                    @endif

                    {{-- Holiday label --}}
                    @if($isHoliday && !$isPast)
                        <div class="mt-0.5 text-[9px] sm:text-[10px] text-red-500 leading-tight truncate hidden sm:block" title="{{ $holName }}">
                            🔴 {{ Str::limit($holName, 12) }}
                        </div>
                        <div class="mt-0.5 text-[9px] text-red-500 sm:hidden">🔴</div>
                    @elseif($isWeekend && !$isPast)
                        <div class="mt-0.5 text-[9px] sm:text-[10px] text-red-400"><span class="hidden sm:inline">Libur</span><span class="sm:hidden">🔴</span></div>
                    @endif

                    {{-- Approved visit count badge --}}
                    @if($count > 0 && !$isPast)
                        <span class="absolute bottom-0.5 right-0.5 sm:bottom-1 sm:right-1 bg-green-500 text-white text-[9px] sm:text-[10px] rounded-full w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center font-bold">
                            {{ $count }}
                        </span>
                    @endif
                </div>
            @endfor

            {{-- Trailing empty cells --}}
            @php
                $totalCells = $startOffset + $daysInMonth;
                $remaining  = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for ($i = 0; $i < $remaining; $i++)
                <div class="min-h-[52px] sm:min-h-[70px] bg-gray-50/40 border-b border-r border-gray-100"></div>
            @endfor
        </div>
    </div>

    {{-- Approved visits list this month --}}
    @if($approvedVisitsList->isNotEmpty())
    <div class="mt-8">
        <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
            Kunjungan Disetujui — {{ $monthNames[$month-1] }} {{ $year }}
        </h3>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            @foreach($approvedVisitsList as $v)
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                <div>
                    <p class="font-semibold text-sm text-gray-800">{{ $v->nama_sekolah }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        📅 {{ $v->tanggal_format }}
                        @if($v->jam_mulai) · ⏰ {{ $v->jam_mulai }}–{{ $v->jam_selesai }} WIB @endif
                        · 👥 {{ number_format($v->jumlah_peserta) }} orang
                    </p>
                </div>
                <span class="text-xs bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-semibold">Disetujui</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ============================================================
     MODAL: Konfirmasi Pengajuan
     ============================================================ --}}
<div id="modal-confirm" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="text-center mb-5">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-upi-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Ajukan Permohonan Kunjungan</h3>
            <p class="text-gray-500 text-sm mt-1" id="modal-date-label">—</p>
        </div>

        <div id="modal-visit-info" class="hidden mb-4 bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm text-amber-800">
            <p>ℹ️ Sudah ada <strong id="modal-visit-count">0</strong> kunjungan disetujui pada tanggal ini.</p>
            <p class="text-xs mt-0.5 text-amber-600">Anda tetap dapat mengajukan permohonan.</p>
        </div>

        <p class="text-center text-sm text-gray-600 mb-5">
            Lanjutkan ke formulir dan isi detail permohonan kunjungan sekolah Anda ke UPI?
        </p>

        <div class="flex gap-3">
            <button onclick="closeModal('confirm')"
                class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-xl font-medium hover:bg-gray-50 transition-colors text-sm">
                Batal
            </button>
            <a href="#" id="modal-btn-ajukan"
                class="flex-1 bg-upi-red text-white py-2.5 rounded-xl font-bold text-center hover:bg-red-800 transition-colors text-sm">
                Ya, Isi Formulir →
            </a>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: Hari Libur / Weekend
     ============================================================ --}}
<div id="modal-holiday" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="text-3xl">🚫</span>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Tidak Menerima Kunjungan</h3>
        <p class="text-gray-600 text-sm mb-1" id="holiday-reason">—</p>
        <p class="text-red-500 text-xs font-semibold mt-1" id="holiday-name">—</p>
        <p class="text-gray-400 text-xs mt-3">Silakan pilih hari kerja lain (Senin–Jumat) untuk kunjungan Anda.</p>
        <button onclick="closeModal('holiday')"
            class="mt-5 bg-upi-red text-white px-8 py-2.5 rounded-xl font-bold hover:bg-red-800 transition-colors text-sm">
            Mengerti
        </button>
    </div>
</div>

{{-- ============================================================
     MODAL: Terlalu Dekat
     ============================================================ --}}
<div id="modal-toosoon" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
        <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="text-3xl">⏰</span>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Terlalu Dekat</h3>
        <p class="text-gray-600 text-sm">Permohonan kunjungan harus diajukan minimal <strong>7 hari sebelum</strong> tanggal kunjungan.</p>
        <p class="text-gray-400 text-xs mt-3">Pilih tanggal yang lebih jauh ke depan.</p>
        <button onclick="closeModal('toosoon')"
            class="mt-5 bg-amber-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-amber-600 transition-colors text-sm">
            Mengerti
        </button>
    </div>
</div>

@push('scripts')
<script>
const MONTH_NAMES = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const DAY_NAMES   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

function formatTanggal(dateStr) {
    const [y, m, d] = dateStr.split('-').map(Number);
    const date = new Date(y, m - 1, d);
    return `${DAY_NAMES[date.getDay()]}, ${d} ${MONTH_NAMES[m - 1]} ${y}`;
}

function handleDayClick(dateStr, action, holidayName, approvedCount) {
    if (action === 'past')    return; // grey, do nothing
    if (action === 'toosoon') { openModal('toosoon'); return; }
    if (action === 'holiday') {
        document.getElementById('holiday-reason').textContent = 'Mohon maaf, UPI tidak menerima kunjungan pada hari libur nasional.';
        document.getElementById('holiday-name').textContent   = holidayName;
        openModal('holiday'); return;
    }
    if (action === 'weekend') {
        document.getElementById('holiday-reason').textContent = 'Mohon maaf, UPI tidak menerima kunjungan pada hari Sabtu dan Minggu.';
        document.getElementById('holiday-name').textContent   = 'Akhir Pekan (Sabtu / Minggu)';
        openModal('holiday'); return;
    }
    // action === 'ok'
    document.getElementById('modal-date-label').textContent = formatTanggal(dateStr);
    const visitInfo = document.getElementById('modal-visit-info');
    if (approvedCount > 0) {
        document.getElementById('modal-visit-count').textContent = approvedCount;
        visitInfo.classList.remove('hidden');
    } else {
        visitInfo.classList.add('hidden');
    }
    document.getElementById('modal-btn-ajukan').href = `/reservasi?tanggal=${dateStr}`;
    openModal('confirm');
}

function openModal(id) {
    document.getElementById('modal-' + id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById('modal-' + id).classList.add('hidden');
    document.body.style.overflow = '';
}

// Backdrop click closes
['confirm', 'holiday', 'toosoon'].forEach(id => {
    document.getElementById('modal-' + id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

// ESC key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['confirm','holiday','toosoon'].forEach(closeModal);
    }
});
</script>
@endpush
@endsection
