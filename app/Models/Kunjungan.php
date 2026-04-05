<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'nomor_registrasi',
        'nama_sekolah',
        'npsn',
        'alamat',
        'nama_pic',
        'email',
        'telepon',
        'tanggal_kunjungan',
        'jumlah_peserta',
        'file_surat',
        'status',
        'catatan_admin',
        'email_notified_at',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'email_notified_at' => 'datetime',
    ];

    /**
     * Generate nomor registrasi unik: UPI-YYYYMMDD-XXXX
     */
    public static function generateNomorRegistrasi(): string
    {
        $prefix = 'UPI-' . now()->format('Ymd') . '-';
        $lastReg = static::where('nomor_registrasi', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $sequence = $lastReg
            ? (int) substr($lastReg->nomor_registrasi, -4) + 1
            : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Tidak Diketahui',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }

    public function getTanggalFormatAttribute(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $tgl = $this->tanggal_kunjungan;
        return $tgl->day . ' ' . $bulan[$tgl->month] . ' ' . $tgl->year;
    }
}
