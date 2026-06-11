<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi pengaturan kalender kunjungan.
 *
 * Pengaturan kalender memungkinkan admin untuk menandai tanggal tertentu
 * sebagai hari libur atau membatasi sesi yang tersedia pada tanggal tersebut,
 * meng-override aturan default (Senin-Kamis).
 */
class StoreKalenderRequest extends FormRequest
{
    /**
     * Otorisasi ditangani oleh middleware 'role.admin' pada route group.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Pengaturan kalender hanya untuk admin, middleware sudah mengamankan ini
        return true;
    }

    /**
     * Aturan validasi untuk pengaturan tanggal kalender.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Tanggal yang diatur: wajib dan harus format tanggal valid
            'tanggal'       => 'required|date',

            // Flag libur: opsional, jika dicentang maka tanggal ini dianggap libur
            'is_libur'      => 'nullable|boolean',

            // Array ID sesi yang tersedia pada tanggal ini (jika bukan libur)
            'sesi_tersedia' => 'nullable|array',

            // Setiap elemen array sesi_tersedia harus referensi sesi yang valid
            'sesi_tersedia.*' => 'integer|exists:sesi,id',

            // Catatan opsional untuk admin (misalnya: alasan hari libur khusus)
            'catatan'       => 'nullable|string|max:255',
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
            'tanggal.required'        => 'Tanggal wajib dipilih.',
            'tanggal.date'            => 'Format tanggal tidak valid.',
            'sesi_tersedia.array'     => 'Data sesi tersedia tidak valid.',
            'sesi_tersedia.*.exists'  => 'Salah satu sesi yang dipilih tidak terdaftar di sistem.',
            'catatan.max'             => 'Catatan maksimal 255 karakter.',
        ];
    }
}
