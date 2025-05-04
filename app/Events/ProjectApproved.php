<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project;
    public $approver;

    public function __construct($project, $approver)
    {
        $this->project = $project;
        $this->approver = $approver;
    }

    public function broadcastOn()
    {
        return new Channel('project-approvals');
    }

    public function broadcastWith()
    {
        return [
            'message' => "Project {$this->project->title} has been approved by {$this->approver->name}",
            'project_id' => $this->project->id,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}