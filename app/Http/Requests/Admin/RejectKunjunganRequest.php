<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi data saat admin menolak permohonan kunjungan.
 *
 * Berbeda dengan approve, catatan admin bersifat WAJIB saat reject.
 * Alasan penolakan harus disampaikan kepada pemohon melalui email notifikasi
 * agar pemohon dapat memperbaiki dan mengajukan ulang.
 */
class RejectKunjunganRequest extends FormRequest
{
    /**
     * Otorisasi ditangani oleh middleware 'role.admin' pada route group.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Middleware role.admin memastikan hanya admin yang dapat mengakses ini
        return true;
    }

    /**
     * Aturan validasi untuk aksi penolakan kunjungan.
     *
     * Alasan penolakan dibuat wajib karena akan dikirimkan kepada pemohon
     * melalui email, memberikan transparansi proses seleksi.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Catatan penolakan: WAJIB agar pemohon tahu alasan dan bisa mengajukan ulang
            'catatan_admin' => 'required|string|max:500',
        ];
    }

    /**
     * Pesan validasi kustom dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi agar dapat dikirimkan kepada pemohon.',
            'catatan_admin.max'      => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}
