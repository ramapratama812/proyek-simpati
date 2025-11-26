<?php

namespace App\Mail;

use App\Models\Publication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PublicationStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Publication $publication;

    public function __construct(Publication $publication)
    {
        $this->publication = $publication->load('owner');
    }

    public function build()
    {
        return $this->subject('Status Publikasi "' . $this->publication->judul . '" Diperbarui')
            ->view('emails.publications.status_changed');
    }
}
