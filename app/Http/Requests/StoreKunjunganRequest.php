<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi data permohonan kunjungan baru dari publik.
 *
 * Kelas ini memisahkan tanggung jawab validasi dari Controller sesuai
 * prinsip Single Responsibility. Validasi akan dijalankan otomatis oleh
 * Laravel sebelum method Controller yang menggunakannya dieksekusi.
 */
class StoreKunjunganRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna saat ini berhak membuat request ini.
     *
     * Form ini bersifat publik (tidak memerlukan autentikasi),
     * sehingga selalu mengembalikan true.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Permohonan kunjungan dibuka untuk publik tanpa autentikasi
        return true;
    }

    /**
     * Mendapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Informasi sekolah — wajib diisi semuanya
            'nama_sekolah'    => 'required|string|max:255',
            'npsn'            => 'required|string|max:20',
            'alamat'          => 'required|string|max:500',
            'email_sekolah'   => 'required|email|max:255',
            'telepon_sekolah' => 'required|string|max:20',

            // Informasi PIC (Person In Charge) — wajib
            'nama_pic'        => 'required|string|max:255',
            'jabatan_pic'     => 'required|in:kepsek,guru,tendik', // hanya 3 jabatan yang valid
            'email_pic'       => 'required|email|max:255',
            'telepon_pic'     => 'required|string|max:20',

            // Jadwal kunjungan — minimal H+10 dari hari ini untuk memberi waktu proses administrasi
            'tanggal_kunjungan' => 'required|date|after_or_equal:' . now()->addDays(10)->format('Y-m-d'),

            // Sesi dan tempat harus referensi data yang valid di database
            'sesi_id'         => 'required|exists:sesi,id',
            'tempat_id'       => 'required|exists:tempat,id',

            // Peserta — jumlah total wajib, rincian boleh kosong
            'jumlah_peserta'  => 'required|integer|min:1',
            'jumlah_kepsek'   => 'nullable|integer|min:0',
            'jumlah_guru'     => 'nullable|integer|min:0',
            'jumlah_tendik'   => 'nullable|integer|min:0',

            // Surat permohonan — wajib diunggah, hanya PDF/JPG, maksimal 1 MB
            'file_surat'      => 'required|file|mimes:pdf,jpg,jpeg|max:1024',
        ];
    }

    /**
     * Mendapatkan pesan error kustom dalam Bahasa Indonesia.
     *
     * Pesan yang spesifik membantu pemohon memahami apa yang perlu diperbaiki
     * tanpa harus menebak maksud dari pesan error generik Laravel.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_sekolah.required'           => 'Nama sekolah wajib diisi.',
            'npsn.required'                   => 'NPSN wajib diisi.',
            'alamat.required'                 => 'Alamat sekolah wajib diisi.',
            'email_sekolah.required'          => 'Email sekolah wajib diisi.',
            'email_sekolah.email'             => 'Format email sekolah tidak valid.',
            'telepon_sekolah.required'        => 'Nomor telepon sekolah wajib diisi.',
            'nama_pic.required'               => 'Nama penanggungjawab wajib diisi.',
            'jabatan_pic.required'            => 'Jabatan penanggungjawab wajib dipilih.',
            'jabatan_pic.in'                  => 'Jabatan harus salah satu dari: Kepala Sekolah, Guru, atau Tendik.',
            'email_pic.required'              => 'Email penanggungjawab wajib diisi.',
            'email_pic.email'                 => 'Format email penanggungjawab tidak valid.',
            'telepon_pic.required'            => 'Nomor telepon penanggungjawab wajib diisi.',
            'tanggal_kunjungan.required'      => 'Tanggal kunjungan wajib diisi.',
            'tanggal_kunjungan.date'          => 'Format tanggal kunjungan tidak valid.',
            'tanggal_kunjungan.after_or_equal'=> 'Tanggal kunjungan minimal 10 hari dari sekarang.',
            'sesi_id.required'                => 'Sesi kunjungan wajib dipilih.',
            'sesi_id.exists'                  => 'Sesi yang dipilih tidak valid.',
            'tempat_id.required'              => 'Tempat kunjungan wajib dipilih.',
            'tempat_id.exists'                => 'Tempat yang dipilih tidak valid.',
            'jumlah_peserta.required'         => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min'              => 'Jumlah peserta minimal 1 orang.',
            'file_surat.required'             => 'Surat permohonan wajib diunggah.',
            'file_surat.mimes'               => 'Format file surat harus PDF atau JPG.',
            'file_surat.max'                  => 'Ukuran file surat maksimal 1 MB.',
        ];
    }

    /**
     * Menambahkan validasi ekstra setelah validasi dasar selesai.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $tanggal = $this->input('tanggal_kunjungan');
            $tempatId = $this->input('tempat_id');
            $sesiId = $this->input('sesi_id');

            if ($tanggal && $sesiId) {
                // Ambil semua ID sesi yang terblokir/penuh pada tanggal tersebut
                $kunjunganService = app(\App\Services\KunjunganService::class);
                $bookedSesi = $kunjunganService->getBookedSesi($tanggal, $tempatId);

                if (in_array((int)$sesiId, $bookedSesi) || in_array((string)$sesiId, $bookedSesi)) {
                    $validator->errors()->add('sesi_id', 'Mohon maaf, sesi yang Anda pilih sudah tidak tersedia atau tanggal tersebut ditutup untuk kunjungan.');
                }
            }
        });
    }
}
