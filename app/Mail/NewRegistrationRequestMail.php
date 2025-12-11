<?php

namespace App\Mail;

use App\Models\RegistrationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRegistrationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public RegistrationRequest $requestData;

    public function __construct(RegistrationRequest $requestData)
    {
        $this->requestData = $requestData;
    }

    public function build()
    {
        return $this->subject('Pendaftaran Akun SIMPATI Baru')
            ->view('emails.registration.new_request');
    }
}
