<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sesi extends Model
{
    protected $table = 'sesi';

    protected $fillable = ['nama', 'jam_mulai', 'jam_selesai', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'sesi_id');
    }

    /** Format: "Sesi 1 (09:00–12:00 WIB)" */
    public function getLabelAttribute(): string
    {
        return "{$this->nama} (" . substr($this->jam_mulai, 0, 5) . '–' . substr($this->jam_selesai, 0, 5) . ' WIB)';
    }
}
