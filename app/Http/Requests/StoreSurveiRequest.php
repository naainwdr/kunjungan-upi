<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk memvalidasi input survei kepuasan kunjungan.
 *
 * Survei kepuasan adalah mekanisme penilaian formal yang hasilnya disimpan
 * di tabel `survei_kepuasan` dan dapat ditampilkan sebagai testimonial
 * di halaman publik (jika `tampilkan_publik` diaktifkan admin).
 *
 * Berbeda dengan evaluasi (/evaluasi/{id}), survei ini adalah satu-satunya
 * mekanisme penilaian yang direkomendasikan untuk dikembangkan ke depan.
 */
class StoreSurveiRequest extends FormRequest
{
    /**
     * Form survei diakses publik melalui link personal yang dikirim via email
     * setelah kunjungan selesai (check-out tercatat).
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Link survei bersifat personal (dikirim ke email kontak), dianggap terautentikasi
        return true;
    }

    /**
     * Aturan validasi untuk input survei kepuasan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Tiga dimensi penilaian kepuasan — semuanya wajib (skala 1-5)
            'rating_pelayanan' => 'required|integer|min:1|max:5',
            'rating_fasilitas' => 'required|integer|min:1|max:5',
            'rating_informasi' => 'required|integer|min:1|max:5',

            // Isian teks bersifat opsional, batas 1000 karakter untuk mencegah spam
            'komentar'         => 'nullable|string|max:1000',
            'saran'            => 'nullable|string|max:1000',
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
            'rating_pelayanan.required' => 'Rating pelayanan wajib diisi.',
            'rating_fasilitas.required' => 'Rating fasilitas wajib diisi.',
            'rating_informasi.required' => 'Rating informasi wajib diisi.',
            'rating_pelayanan.min'      => 'Rating minimal 1 bintang.',
            'rating_pelayanan.max'      => 'Rating maksimal 5 bintang.',
            'komentar.max'              => 'Komentar maksimal 1000 karakter.',
            'saran.max'                 => 'Saran maksimal 1000 karakter.',
        ];
    }
}
