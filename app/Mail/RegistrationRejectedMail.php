<?php

namespace App\Mail;

use App\Models\RegistrationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public RegistrationRequest $request;

    /**
     * Create a new message instance.
     */
    public function __construct(RegistrationRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Permohonan Akun SIMPATI Ditolak')
            ->markdown('emails.registration.rejected');
    }
}
