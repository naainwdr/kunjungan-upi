<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $table = 'sekolah';

    protected $fillable = ['nama', 'npsn', 'alamat', 'email', 'telepon'];

    public function kontak(): HasMany
    {
        return $this->hasMany(KontakSekolah::class, 'sekolah_id');
    }

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'sekolah_id');
    }
}
