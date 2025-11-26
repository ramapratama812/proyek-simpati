<?php

namespace App\Mail;

use App\Models\ResearchProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResearchProjectStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public ResearchProject $project;

    public function __construct(ResearchProject $project)
    {
        $this->project = $project->load('ketua', 'members');
    }

    public function build()
    {
        return $this->subject('Status Kegiatan "' . $this->project->judul . '" Diperbarui')
            ->view('emails.projects.status_changed');
    }
}
