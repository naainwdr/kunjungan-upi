<?php

namespace App\Mail;

use App\Models\Kunjungan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusKunjunganMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Kunjungan $kunjungan)
    {
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->kunjungan->status) {
            'pending'  => '[UPI] Konfirmasi Penerimaan Pengajuan Kunjungan',
            'approved' => '[UPI] Pengajuan Kunjungan Anda Disetujui 🎉',
            'rejected' => '[UPI] Pengajuan Kunjungan Anda Tidak Dapat Diproses',
            default    => '[UPI] Informasi Status Kunjungan',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.status-kunjungan',
            with: ['kunjungan' => $this->kunjungan],
        );
    }
}
