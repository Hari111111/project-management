<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project, $status, $reason, $timestamp;

    public function __construct($project, $status, $reason = null)
    {
        $this->project = $project;
        $this->status = $status;
        $this->reason = $reason;
        $this->timestamp = now()->format('F j, Y, g:i a');
    }

    public function build()
    {
        return $this->subject('Project ' . ucfirst($this->status))
            ->view('emails.project_notification')
            ->with([
                'project' => $this->project,
                'status' => $this->status,
                'reason' => $this->reason,
                'timestamp' => $this->timestamp,
                'user' => $this->project->user,
            ]);
    }
}
