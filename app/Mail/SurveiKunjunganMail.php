<?php

namespace App\Mail;

use App\Models\Kunjungan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveiKunjunganMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Kunjungan $kunjungan,
        public string $surveiUrl
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Form Survei Kepuasan Kunjungan UPI — ' . $this->kunjungan->nomor_registrasi)
            ->view('emails.survei-kunjungan');
    }
}
