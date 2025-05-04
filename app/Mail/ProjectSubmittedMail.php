<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function build()
    {
        return $this->subject('Project Submitted Successfully')
            ->view('emails.project_notification')
            ->with([
                'project' => $this->project,
                'user' => $this->project->user,
            ]);
    }
}
