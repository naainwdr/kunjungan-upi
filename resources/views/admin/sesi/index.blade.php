@extends('layouts.admin')
@section('page_title', 'Manajemen Sesi Kalender')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Sesi Kunjungan</h2>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')" class="bg-upi-red text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-red-800 transition-colors">
            + Tambah Sesi
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4 font-semibold">Nama Sesi</th>
                    <th class="px-6 py-4 font-semibold">Waktu (Jam)</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($sesi as $s)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $s->nama }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_selesai)->format('H:i') }} WIB</td>
                    <td class="px-6 py-4">
                        @if($s->aktif)
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded-full">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="editSesi({{ $s }})" class="text-blue-600 hover:underline text-sm font-medium">Edit</button>
                        <form action="{{ route('admin.sesi.destroy', $s->id) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Yakin ingin menghapus sesi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm font-medium">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div id="modal-create" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Tambah Sesi Kunjungan</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form action="{{ route('admin.sesi.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sesi</label>
                    <input type="text" name="nama" required class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Contoh: Sesi Pagi">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="aktif" value="1" checked class="rounded border-gray-300 text-upi-red shadow-sm focus:ring-upi-red">
                    <span class="ml-2 text-sm text-gray-700">Sesi Aktif (Bisa Dipilih)</span>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mr-2">Batal</button>
                <button type="submit" class="bg-upi-red text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-800">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Edit Sesi Kunjungan</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="form-edit" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sesi</label>
                    <input type="text" name="nama" id="edit-nama" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="edit-jam-mulai" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="edit-jam-selesai" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="aktif" id="edit-aktif" value="1" class="rounded border-gray-300 text-upi-red shadow-sm focus:ring-upi-red">
                    <span class="ml-2 text-sm text-gray-700">Sesi Aktif (Bisa Dipilih)</span>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mr-2">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editSesi(sesi) {
    document.getElementById('modal-edit').classList.remove('hidden');
    document.getElementById('form-edit').action = `/admin/sesi/${sesi.id}`;
    document.getElementById('edit-nama').value = sesi.nama;
    // Format H:i for time inputs
    document.getElementById('edit-jam-mulai').value = sesi.jam_mulai.substring(0, 5);
    document.getElementById('edit-jam-selesai').value = sesi.jam_selesai.substring(0, 5);
    document.getElementById('edit-aktif').checked = sesi.aktif == 1;
}
</script>
@endsection
