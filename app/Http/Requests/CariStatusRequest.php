<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi input pencarian status kunjungan.
 *
 * Digunakan pada endpoint POST /cek-status untuk memastikan
 * query pencarian tidak kosong dan minimal memiliki panjang karakter
 * yang cukup untuk mengidentifikasi nomor registrasi atau email.
 */
class CariStatusRequest extends FormRequest
{
    /**
     * Form pencarian status dibuka untuk publik tanpa autentikasi.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Pencarian status permohonan adalah fitur publik, tidak perlu login
        return true;
    }

    /**
     * Aturan validasi untuk input pencarian.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Minimal 3 karakter agar tidak terlalu generik yang bisa memperlambat query
            'query' => 'required|string|min:3',
        ];
    }

    /**
     * Pesan error kustom dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Masukkan nomor registrasi atau email.',
            'query.min'      => 'Pencarian minimal 3 karakter.',
        ];
    }
}
