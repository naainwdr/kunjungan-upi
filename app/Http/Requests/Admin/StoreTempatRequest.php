<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi data tempat kunjungan yang dikelola admin.
 *
 * Digunakan baik untuk aksi store (tambah baru) maupun update (perbarui).
 * Kapasitas tempat digunakan sebagai batas maksimum peserta dalam logika
 * validasi permohonan kunjungan di StoreKunjunganRequest.
 */
class StoreTempatRequest extends FormRequest
{
    /**
     * Otorisasi ditangani oleh middleware 'role.admin' pada route group.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Pengelolaan tempat hanya untuk admin, middleware sudah mengamankan ini
        return true;
    }

    /**
     * Aturan validasi untuk data tempat kunjungan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Nama tempat: wajib, unik sebaiknya dipertimbangkan jika ada kebutuhan
            'nama'      => 'required|string|max:255',

            // Kapasitas: wajib dan harus bilangan bulat positif
            'kapasitas' => 'required|integer|min:1',

            // Deskripsi: opsional, untuk informasi tambahan tentang fasilitas tempat
            'deskripsi' => 'nullable|string|max:500',

            // Status aktif: dikontrol via checkbox, tidak perlu validasi khusus
            'aktif'     => 'nullable|boolean',
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
            'nama.required'      => 'Nama tempat wajib diisi.',
            'kapasitas.required' => 'Kapasitas tempat wajib diisi.',
            'kapasitas.integer'  => 'Kapasitas harus berupa angka.',
            'kapasitas.min'      => 'Kapasitas minimal 1 orang.',
            'deskripsi.max'      => 'Deskripsi maksimal 500 karakter.',
        ];
    }
}
