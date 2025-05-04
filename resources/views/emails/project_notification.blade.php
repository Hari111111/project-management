@php
    $isStatusUpdate = isset($status);
@endphp

<h2>
    @if ($isStatusUpdate)
        Project {{ ucfirst($status) }}
    @else
        Project Submitted
    @endif
</h2>

<p>Hello {{ $user->name }},</p>

<p>
    Your project <strong>{{ $project->title }}</strong>
    @if ($isStatusUpdate)
        was <strong>{{ $status }}</strong> on <strong>{{ $timestamp }}</strong>.
    @else
        was successfully submitted on <strong>{{ $project->created_at->format('F j, Y, g:i a') }}</strong>.
    @endif
</p>

@if ($isStatusUpdate && $status === 'rejected')
    <p><strong>Reason:</strong> {{ $reason }}</p>
@endif

<p>Thank you,<br>{{ config('app.name') }} Team</p>
