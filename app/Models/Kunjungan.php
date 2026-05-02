<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'nomor_registrasi',
        'sekolah_id',
        'kontak_id',
        'tempat_id',
        'sesi_id',
        'tanggal_kunjungan',
        'jumlah_peserta',
        'jumlah_kepsek',
        'jumlah_guru',
        'jumlah_tendik',
        'file_surat',
        'status',
        'catatan_admin',
        'email_notified_at',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'email_notified_at' => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────────

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function kontak(): BelongsTo
    {
        return $this->belongsTo(KontakSekolah::class, 'kontak_id');
    }

    public function tempat(): BelongsTo
    {
        return $this->belongsTo(Tempat::class, 'tempat_id');
    }

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(KunjunganLog::class, 'kunjungan_id')->orderByDesc('created_at');
    }

    public function presensi(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KunjunganPresensi::class, 'kunjungan_id');
    }

    public function survei(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SurveiKepuasan::class, 'kunjungan_id');
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getTanggalFormatAttribute(): string
    {
        return $this->tanggal_kunjungan->isoFormat('dddd, D MMMM Y');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            default     => 'Tidak Diketahui',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'bg-yellow-100 text-yellow-800',
            'approved'  => 'bg-green-100 text-green-800',
            'rejected'  => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-200 text-gray-700',
            'completed' => 'bg-purple-100 text-purple-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }

    // ── Helpers ─────────────────────────────────────────────────

    public static function generateNomorRegistrasi(): string
    {
        $prefix = 'UPI-' . now()->format('Ymd') . '-';
        $last   = static::where('nomor_registrasi', 'like', $prefix . '%')
                        ->orderByDesc('id')->first();
        $seq    = $last ? (int) substr($last->nomor_registrasi, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Catat perubahan status ke kunjungan_log
     */
    public function logStatus(string $baru, ?string $catatan = null, ?int $adminId = null): void
    {
        KunjunganLog::create([
            'kunjungan_id'   => $this->id,
            'status_sebelum' => $this->status,
            'status_sesudah' => $baru,
            'catatan'        => $catatan,
            'changed_by'     => $adminId,
        ]);
    }
}
