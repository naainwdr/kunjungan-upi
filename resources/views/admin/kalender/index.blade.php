@extends('layouts.admin')
@section('page_title', 'Pengaturan Kalender Operasional')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kalender Operasional</h2>
            <p class="text-sm text-gray-500">Atur hari libur dan ketersediaan sesi per tanggal.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
            <a href="{{ route('admin.kalender.index', ['year' => $month == 1 ? $year - 1 : $year, 'month' => $month == 1 ? 12 : $month - 1]) }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
               <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <span class="font-bold text-gray-700 min-w-[140px] text-center">
                {{ Carbon\Carbon::create($year, $month, 1)->isoFormat('MMMM Y') }}
            </span>
            <a href="{{ route('admin.kalender.index', ['year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1]) }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
               <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
            Terdapat kesalahan:
            <ul class="list-disc list-inside mt-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Calendar Grid --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            @php
                $firstDayOfMonth = Carbon\Carbon::create($year, $month, 1);
                $daysInMonth = $firstDayOfMonth->daysInMonth;
                $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 (Sun) - 6 (Sat)
                // Adjust for Monday start: 0 (Mon) - 6 (Sun)
                $startDayOfWeek = ($startDayOfWeek + 6) % 7; 
            @endphp

            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <div class="text-center py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1">
                {{-- Empty days --}}
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="aspect-square bg-gray-50/50 rounded-lg"></div>
                @endfor

                {{-- Days of month --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDate = Carbon\Carbon::create($year, $month, $day);
                        $dateStr = $currentDate->format('Y-m-d');
                        $isWeekend = in_array($currentDate->dayOfWeek, [0, 5, 6]); // Fri, Sat, Sun
                        $setting = $settings->get($dateStr);
                        
                        $isLibur = $setting ? $setting->is_libur : $isWeekend;
                        $sesiCount = $setting ? count($setting->sesi_tersedia ?? []) : ($isWeekend ? 0 : $sesi->count());
                    @endphp
                    
                    <button type="button" 
                            onclick="openSetting('{{ $dateStr }}', {{ $isLibur ? 'true' : 'false' }}, {{ json_encode($setting ? $setting->sesi_tersedia : ($isWeekend ? [] : $sesi->pluck('id'))) }}, '{{ $setting ? addslashes($setting->catatan) : '' }}', {{ $setting ? $setting->id : 'null' }})"
                            class="aspect-square p-1 rounded-lg border transition-all flex flex-col items-center justify-center gap-1
                            {{ $isLibur ? 'bg-red-50 border-red-100 text-red-600' : 'bg-white border-gray-100 hover:border-upi-red hover:shadow-md' }}
                            {{ $currentDate->isToday() ? 'ring-2 ring-upi-red ring-offset-2' : '' }}">
                        <span class="text-sm font-bold">{{ $day }}</span>
                        @if($isLibur)
                            <span class="text-[8px] font-bold uppercase">Libur</span>
                        @else
                            <span class="text-[8px] font-medium text-gray-400">{{ $sesiCount }} Sesi</span>
                        @endif
                    </button>
                @endfor
            </div>
            
            <div class="mt-6 flex items-center gap-4 text-xs">
                <div class="flex items-center gap-1.5"><div class="w-3 h-3 bg-white border border-gray-200 rounded"></div> <span>Default (Sen–Kam)</span></div>
                <div class="flex items-center gap-1.5"><div class="w-3 h-3 bg-red-50 border border-red-100 rounded"></div> <span>Libur / Tutup</span></div>
                <div class="flex items-center gap-1.5"><div class="w-3 h-3 ring-2 ring-upi-red rounded"></div> <span>Hari Ini</span></div>
            </div>
        </div>

        {{-- Setting Form --}}
        <div id="setting-pane" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div id="no-selection" class="h-full flex flex-col items-center justify-center text-center py-12">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-gray-500 text-sm">Pilih tanggal di kalender untuk mengatur ketersediaan.</p>
            </div>

            <div id="selection-form" class="hidden">
                <h3 class="text-lg font-bold text-gray-800 mb-1" id="display-date">8 Mei 2024</h3>
                <p class="text-xs text-gray-500 mb-6">Sesuaikan operasional untuk tanggal ini.</p>

                <form action="{{ route('admin.kalender.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal" id="input-tanggal">
                    
                    <div class="space-y-6">
                        <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_libur" id="input-is-libur" value="1" class="sr-only peer" onchange="toggleLibur(this.checked)">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                <span class="ml-3 text-sm font-bold text-gray-700">Set sebagai Hari Libur</span>
                            </label>
                        </div>

                        <div id="sesi-container">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sesi Tersedia</label>
                            <div class="space-y-2">
                                @foreach($sesi as $s)
                                <label class="flex items-center p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer group">
                                    <input type="checkbox" name="sesi_tersedia[]" value="{{ $s->id }}" class="sesi-checkbox rounded border-gray-300 text-upi-red focus:ring-upi-red">
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-700">{{ $s->nama }}</p>
                                        <p class="text-[10px] text-gray-400">{{ substr($s->jam_mulai, 0, 5) }} - {{ substr($s->jam_selesai, 0, 5) }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Catatan (Opsional)</label>
                            <textarea name="catatan" id="input-catatan" rows="2" class="w-full border-gray-200 rounded-xl text-sm focus:ring-upi-red focus:border-upi-red" placeholder="Misal: Libur Idul Fitri"></textarea>
                        </div>

                        <div class="pt-4 flex flex-col gap-2">
                            <button type="submit" class="w-full bg-upi-red text-white py-2.5 rounded-xl font-bold shadow-lg shadow-red-200 hover:bg-red-800 transition-colors">
                                Simpan Pengaturan
                            </button>
                            <button type="button" id="btn-reset" class="w-full text-gray-400 text-xs hover:text-gray-600 py-2 hidden">
                                Reset ke Default
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Form untuk Reset --}}
<form id="form-delete-kalender" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
function openSetting(date, isLibur, sesiIds, catatan, id) {
    document.getElementById('no-selection').classList.add('hidden');
    document.getElementById('selection-form').classList.remove('hidden');
    
    // Set date display
    const d = new Date(date);
    document.getElementById('display-date').innerText = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('input-tanggal').value = date;
    
    // Set libur
    document.getElementById('input-is-libur').checked = isLibur;
    toggleLibur(isLibur);
    
    // Set sesi
    const checkboxes = document.querySelectorAll('.sesi-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = sesiIds.includes(parseInt(cb.value)) || sesiIds.includes(cb.value);
    });
    
    // Set catatan
    document.getElementById('input-catatan').value = catatan || '';
    
    // Set tombol reset
    const btnReset = document.getElementById('btn-reset');
    if (id) {
        btnReset.classList.remove('hidden');
        btnReset.onclick = function() {
            if (confirm('Kembalikan tanggal ini ke aturan ketersediaan default?')) {
                const form = document.getElementById('form-delete-kalender');
                form.action = `/admin/kalender/${id}`;
                form.submit();
            }
        };
    } else {
        btnReset.classList.add('hidden');
    }
}

function toggleLibur(checked) {
    const container = document.getElementById('sesi-container');
    if (checked) {
        container.classList.add('opacity-40', 'pointer-events-none');
    } else {
        container.classList.remove('opacity-40', 'pointer-events-none');
    }
}
</script>
@endsection
