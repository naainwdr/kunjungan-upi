<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveiKepuasan extends Model
{
    protected $table = 'survei_kepuasan';

    protected $fillable = [
        'kunjungan_id',
        'rating_pelayanan',
        'rating_fasilitas',
        'rating_informasi',
        'komentar',
        'saran',
        'tampilkan_publik',
    ];

    protected $casts = [
        'tampilkan_publik' => 'boolean',
    ];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }

    /** Rata-rata ketiga rating */
    public function getRatingRataAttribute(): float
    {
        return round(($this->rating_pelayanan + $this->rating_fasilitas + $this->rating_informasi) / 3, 1);
    }

    /** Bintang UTF-8 */
    public function getBintangAttribute(): string
    {
        return str_repeat('★', (int) round($this->rating_rata)) . str_repeat('☆', 5 - (int) round($this->rating_rata));
    }
}
