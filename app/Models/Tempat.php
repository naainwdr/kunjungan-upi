<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tempat extends Model
{
    protected $table = 'tempat';

    protected $fillable = ['nama', 'kapasitas', 'deskripsi', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'tempat_id');
    }
}
