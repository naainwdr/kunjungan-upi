<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi data saat admin menyetujui permohonan kunjungan.
 *
 * Catatan admin bersifat opsional saat approve — admin bisa menyetujui
 * tanpa memberikan catatan tambahan. Namun jika diisi, dibatasi 500 karakter
 * agar proporsional untuk ditampilkan di halaman detail kunjungan.
 */
class ApproveKunjunganRequest extends FormRequest
{
    /**
     * Hanya user dengan role 'admin' yang berhak mengakses endpoint ini.
     * Gate/middleware role.admin sudah menangani ini di level route,
     * sehingga di sini cukup true.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Otorisasi role ditangani oleh middleware 'role.admin' pada route group
        return true;
    }

    /**
     * Aturan validasi untuk aksi persetujuan kunjungan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Catatan persetujuan: opsional, maksimal 500 karakter
            'catatan_admin' => 'nullable|string|max:500',
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
            'catatan_admin.max' => 'Catatan admin maksimal 500 karakter.',
        ];
    }
}
