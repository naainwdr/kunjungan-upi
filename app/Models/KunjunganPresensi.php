<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KunjunganPresensi extends Model
{
    protected $table = 'kunjungan_presensi';

    protected $fillable = [
        'kunjungan_id',
        'waktu_masuk',
        'waktu_keluar',
        'petugas_masuk_id',
        'petugas_keluar_id',
        'catatan',
    ];

    protected $casts = [
        'waktu_masuk'  => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }

    public function petugasMasuk(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'petugas_masuk_id');
    }

    public function petugasKeluar(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'petugas_keluar_id');
    }

    public function getStatusAttribute(): string
    {
        if ($this->waktu_keluar) return 'checkout';
        if ($this->waktu_masuk)  return 'checkin';
        return 'belum';
    }

    public function getDurasiAttribute(): ?string
    {
        if (!$this->waktu_masuk || !$this->waktu_keluar) return null;
        $menit = $this->waktu_masuk->diffInMinutes($this->waktu_keluar);
        $jam   = intdiv($menit, 60);
        $sisa  = $menit % 60;
        return $jam > 0 ? "{$jam}j {$sisa}m" : "{$sisa} menit";
    }
}
