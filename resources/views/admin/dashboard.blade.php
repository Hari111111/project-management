<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="/projects/create">
            <button class="btn btn-primary">
                Add Project

            </button>
            </a>
        </div>
        
    </x-slot>
<div class="container mt-4">
    {{-- Summary Stats --}}
    <div class="row mb-4">
        @foreach (['total' => 'primary', 'pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'] as $key => $color)
            <div class="col-md-3">
                <div class="card text-center text-white bg-{{ $color }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ ucfirst($key) }}</h5>
                        <p class="card-text">{{ $data[$key] ?? $data['total'] }} @if($key !== 'total') ({{ $data[$key . '_percent'] }}%) @endif</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form id="filter-form" class="row mb-3 g-2">
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" placeholder="Date">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="submitter" class="form-control" placeholder="Submitter name">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Apply Filter</button>
            <button type="button" id="clear-filter" class="btn btn-secondary">Clear</button>
        </div>
    </form>
    
    <div id="project-table">
        @include('admin.partials.project_table', ['projects' => $projects])
    </div>

    <div id="approval-notifications" 
    class="position-fixed bottom-0 end-0 p-3" 
    style="z-index: 9999">   
         <!-- Notifications will appear here -->
    </div>

</div>
@section('scripts')
<script>
$(function () {
    $(document).on('click','.Approve-btn, .Reject-btn', function () {
        const id = $(this).data('id');
        let buttonText = $(this).text().trim(); // trim removes extra spaces
        const status = $(this).hasClass('Approve-btn') ? 'approved' : 'rejected';
        let reason = '';
        console.log(buttonText,status);


        if (status === 'rejected') {
            reason = prompt("Please enter a rejection reason:");
            if (!reason) return alert("Reason required.");
        }
        $(`#${buttonText}-btn-${id}`).prop('disabled', true);
        $(`#${buttonText}-spinner-${id}`).removeClass('d-none');
        $(`#${buttonText}-btn-text-${id}`).text(`${buttonText}`);

        $.ajax({
            url: '{{ url("project/update-status") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                project_id: id,
                status: status,
                rejection_reason: reason
            },
            success: res => {
                alert(res.message);
                location.reload();
            },
            error: err => {
                alert("Error updating status.");
            },
            complete: function() {
                    $(`#${buttonText}-btn-${id}`).prop('disabled', false);
                    $(`#${buttonText}-spinner-${id}`).addClass('d-none');
                    $(`#${buttonText}-btn-text-${id}`).text(buttonText);
            }
        });
    });
});
</script>
@vite(['resources/js/app.js'])

<script>
        $(document).ready(function () {
            $('#filter-form').on('submit', function (e) {
                e.preventDefault();
        
                $.ajax({
                    url: '{{ route("admin.projects.filter") }}',
                    type: 'GET',
                    data: $(this).serialize(),
                    success: function (res) {
                        $('#project-table').html(res.html);
                    },
                    error: function () {
                        alert('Failed to load filtered data.');
                    }
                    
                });
            });
        });
    
        document.getElementById('clear-filter').addEventListener('click', function () {
        document.querySelectorAll('#filter-form input, #filter-form select').forEach(input => input.value = '');
        document.getElementById('filter-form').dispatchEvent(new Event('submit'));
    });
</script>
<script>
$(document).ready(function() {
    // Edit Project
    $('.edit-btn').click(function() {
        let projectId = $(this).data('id');
        let spinner = $(`#edit-spinner-${projectId}`);
        let btnText = $(`#edit-btn-text-${projectId}`);
        
        spinner.removeClass('d-none');
        btnText.text('Loading...');

        $.ajax({
            url: `/projects/${projectId}/edit`,
            method: 'GET',
            success: function(response) {
                $('#editProjectId').val(response.id);
                $('#projectName').val(response.title);
                $('#projectDescription').val(response.description);
                $('#editModal').modal('show');
            },
            complete: function() {
                spinner.addClass('d-none');
                btnText.text('Edit');
            }
        });
    });

    // Update Project
    $('#editForm').submit(function(e) {
        e.preventDefault();
        let projectId = $('#editProjectId').val();
        
        $.ajax({
            url: `/projects/${projectId}`,
            method: 'PUT',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editModal').modal('hide');
                // Update your UI here
                location.reload(); // Or update specific elements
            },
            error: function(xhr) {
                alert('Error updating project');
            }
        });
    });

    // Delete Project
    $('.delete-btn').click(function() {
        let projectId = $(this).data('id');
        $('#deleteModal').data('id', projectId).modal('show');
    });

    $('#confirmDelete').click(function() {
        let projectId = $('#deleteModal').data('id');
        let spinner = $(`#delete-spinner-${projectId}`);
        let btnText = $(`#delete-btn-text-${projectId}`);
        
        spinner.removeClass('d-none');
        btnText.text('Deleting...');

        $.ajax({
            url: `/projects/${projectId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                $(`[data-id="${projectId}"]`).closest('tr').remove();
            },
            complete: function() {
                spinner.addClass('d-none');
                btnText.text('Delete');
            }
        });
    });
});

</script>
    
@endsection

</x-app-layout>
    
