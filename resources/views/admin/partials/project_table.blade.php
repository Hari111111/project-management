<table class="table table-bordered">
    <thead>
        <tr>
            <th>Project</th>
            <th>Submitter</th>
            <th>Submitted</th>
            <th>Status</th>
            <th>Updated</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $project)
            <tr>
                <td>{{ $project->title }}</td>
                <td>{{ $project->user->name ?? '-' }}</td>
                <td>{{ $project->created_at->format('Y-m-d') }}</td>
                <td>
                    <span class="badge bg-{{ $project->status === 'approved' ? 'success' : ($project->status === 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </td>
                <td>{{ $project->updated_at->setTimezone('Asia/Kolkata')->diffForHumans() }}</td>
             

                <td>
                    @can('approve', $project)
                    <button class="btn btn-success btn-sm Approve-btn" data-id="{{ $project->id }}">
                        <span class="spinner-border spinner-border-sm d-none" id="Approve-spinner-{{$project->id }}" role="status" aria-hidden="true"></span>
                                    <span id="Approve-btn-text-{{$project->id }}">Approve</span>
                    </button>
                    <button class="btn btn-danger btn-sm Reject-btn" data-id="{{ $project->id }}">
                        <span class="spinner-border spinner-border-sm d-none" id="Reject-spinner-{{$project->id }}" role="status" aria-hidden="true"></span>
                        <span id="Reject-btn-text-{{$project->id }}">Reject</span>
                    </button>
                    @endcan
                    <button 
                    class="btn btn-secondary btn-sm" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#history-{{ $project->id }}"
                >
                    History
                </button>
                 <!-- Add Edit and Delete buttons -->
                 @if( $project->status === 'pending' || currentUserRole()=='admin')

    <button class="btn btn-primary btn-sm edit-btn" data-id="{{ $project->id }}">
        <span class="spinner-border spinner-border-sm d-none" id="edit-spinner-{{ $project->id }}"></span>
        <span id="edit-btn-text-{{ $project->id }}">Edit</span>
    </button>
    <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $project->id }}">
        <span class="spinner-border spinner-border-sm d-none" id="delete-spinner-{{ $project->id }}"></span>
        <span id="delete-btn-text-{{ $project->id }}">Delete</span>
    </button>
    @endif
    
                </td>
            </tr>
            <tr>
                <td colspan="6" class="p-0">
                    <div class="collapse" id="history-{{ $project->id }}" style="visibility: visible !important">
                        
                        <div class="p-3 bg-light">
                            <ul>
                                @foreach($project->histories as $h)
                                    <li>
                                        {{ ucfirst($h->status) }} by {{ $h->user->name ?? 'System' }} 
                                        on {{ $h->created_at->format('Y-m-d H:i') }}
                                        @if($h->reason) — Reason: "{{ $h->reason }}" @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3">
    {{ $projects->links() }}
</div>
<!-- Add these modals at the bottom of your view -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="editProjectId">
                    <div class="mb-3">
                        <label for="projectName" class="form-label">Project Name</label>
                        <input type="text" class="form-control" id="projectName" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="projectDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="projectDescription" name="description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this project?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>