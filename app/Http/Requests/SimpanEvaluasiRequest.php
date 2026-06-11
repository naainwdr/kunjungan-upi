<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi input form evaluasi kunjungan.
 *
 * Evaluasi adalah mekanisme umpan balik internal yang disimpan di kolom
 * `catatan_admin` tabel kunjungan. Berbeda dengan SurveiKepuasan yang
 * menyimpan ke tabel terpisah, evaluasi ini bersifat lebih ringkas.
 *
 * CATATAN ARSITEKTUR: Dalam jangka panjang, disarankan untuk memigrasikan
 * data evaluasi ini ke tabel terpisah agar tidak mencampur fungsi kolom
 * `catatan_admin` antara catatan admin dan umpan balik pemohon.
 */
class SimpanEvaluasiRequest extends FormRequest
{
    /**
     * Form evaluasi dapat diakses publik (tanpa login) karena
     * dibagikan melalui link personal via email.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Link evaluasi dikirim personal ke pemohon, dianggap terautentikasi via token URL
        return true;
    }

    /**
     * Aturan validasi form evaluasi kunjungan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Rating 1-5 bintang untuk setiap aspek — wajib diisi
            'rating_pelayanan' => 'required|integer|min:1|max:5',
            'rating_fasilitas' => 'required|integer|min:1|max:5',
            'rating_informasi' => 'required|integer|min:1|max:5',

            // Komentar dan saran bersifat opsional, dibatasi 1000 karakter
            'komentar'         => 'nullable|string|max:1000',
            'saran'            => 'nullable|string|max:1000',
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
            'rating_pelayanan.required' => 'Penilaian pelayanan wajib diisi.',
            'rating_pelayanan.min'      => 'Rating minimal 1 bintang.',
            'rating_pelayanan.max'      => 'Rating maksimal 5 bintang.',
            'rating_fasilitas.required' => 'Penilaian fasilitas wajib diisi.',
            'rating_informasi.required' => 'Penilaian informasi wajib diisi.',
            'komentar.max'              => 'Komentar maksimal 1000 karakter.',
            'saran.max'                 => 'Saran maksimal 1000 karakter.',
        ];
    }
}
