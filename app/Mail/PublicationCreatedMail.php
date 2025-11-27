<?php

namespace App\Mail;

use App\Models\Publication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PublicationCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Publication $publication;

    public function __construct(Publication $publication)
    {
        $this->publication = $publication->load('owner');
    }

    public function build()
    {
        return $this->subject('Publikasi Baru: ' . $this->publication->judul)
            ->view('emails.publications.created');
    }
}
