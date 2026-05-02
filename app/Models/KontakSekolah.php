<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KontakSekolah extends Model
{
    protected $table = 'kontak_sekolah';

    protected $fillable = ['sekolah_id', 'nama', 'jabatan', 'email', 'telepon'];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'kontak_id');
    }

    public function getJabatanFormatAttribute(): string
    {
        return match($this->jabatan) {
            'kepsek' => 'Kepala Sekolah',
            'guru'   => 'Guru',
            'tendik' => 'Tenaga Kependidikan',
            default  => ucfirst($this->jabatan ?? ''),
        };
    }
}
