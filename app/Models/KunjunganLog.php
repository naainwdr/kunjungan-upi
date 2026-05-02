<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KunjunganLog extends Model
{
    protected $table = 'kunjungan_log';

    public $timestamps = false;

    protected $fillable = ['kunjungan_id', 'status_sebelum', 'status_sesudah', 'catatan', 'changed_by'];

    protected $casts = ['created_at' => 'datetime'];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }
}
