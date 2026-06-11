<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi data sesi kunjungan yang dikelola admin.
 *
 * Sesi mendefinisikan slot waktu kunjungan (misalnya: Sesi Pagi 08:00-10:00).
 * Aturan `after:jam_mulai` memastikan jam selesai selalu lebih akhir
 * dari jam mulai untuk mencegah data sesi yang tidak logis.
 */
class StoreSesiRequest extends FormRequest
{
    /**
     * Otorisasi ditangani oleh middleware 'role.admin' pada route group.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Pengelolaan sesi hanya untuk admin, middleware sudah mengamankan ini
        return true;
    }

    /**
     * Aturan validasi untuk data sesi kunjungan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Label sesi untuk ditampilkan di UI (misal: "Sesi Pagi")
            'nama'        => 'required|string|max:255',

            // Jam mulai dan selesai — format time (H:i) yang divalidasi Laravel
            'jam_mulai'   => 'required|date_format:H:i',

            // Jam selesai harus setelah jam mulai untuk memastikan durasi positif
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',

            // Status aktif: dikontrol via checkbox, tidak perlu validasi khusus
            'aktif'       => 'nullable|boolean',
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
            'nama.required'           => 'Nama sesi wajib diisi.',
            'jam_mulai.required'      => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format'   => 'Format jam mulai tidak valid (gunakan HH:MM).',
            'jam_selesai.required'    => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid (gunakan HH:MM).',
            'jam_selesai.after'       => 'Jam selesai harus lebih akhir dari jam mulai.',
        ];
    }
}
