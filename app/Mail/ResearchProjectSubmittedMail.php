<?php

namespace App\Mail;

use App\Models\ResearchProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResearchProjectSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public ResearchProject $project;

    public function __construct(ResearchProject $project)
    {
        $this->project = $project->load('ketua');
    }

    public function build()
    {
        return $this->subject('Usulan Kegiatan Baru: ' . $this->project->judul)
            ->view('emails.projects.submitted');
    }
}
