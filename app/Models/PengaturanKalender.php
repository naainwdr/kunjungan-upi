<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanKalender extends Model
{
    protected $table = 'pengaturan_kalender';

    protected $fillable = [
        'tanggal',
        'is_libur',
        'sesi_tersedia',
        'catatan',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'is_libur'      => 'boolean',
        'sesi_tersedia' => 'array',
    ];
}
