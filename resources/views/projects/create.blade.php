<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Submit New Project</h3>
                        <p class="mb-0 opacity-75">Fill in the details below to submit your project</p>
                    </div>

                    <div class="card-body p-4">
                        <form id="projectForm" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            
                            <!-- Title Field -->
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold text-dark">Project Title</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </span>
                                    <input type="text" 
                                           name="title" 
                                           id="title"
                                           class="form-control form-control-lg" 
                                           placeholder="Enter project title"
                                           required>
                                </div>
                                <div class="invalid-feedback" id="title-error"></div>
                            </div>

                            <!-- Description Field -->
                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold text-dark">Project Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light align-items-start">
                                        <i class="bi bi-text-paragraph text-primary"></i>
                                    </span>
                                    <textarea name="description" 
                                              id="description"
                                              class="form-control form-control-lg" 
                                              rows="4"
                                              placeholder="Describe your project in detail"
                                              required></textarea>
                                </div>
                                <div class="invalid-feedback" id="description-error"></div>
                            </div>

                            <!-- File Upload Field -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Project Attachments</label>
                                <div class="border-2 border-dashed rounded-3 p-4 text-center bg-light">
                                    <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                    <div class="mt-2">
                                        <input type="file" 
                                               name="file" 
                                               id="fileInput"
                                               class="form-control d-none"
                                               accept=".pdf,.doc,.docx,.zip">
                                        <label for="fileInput" 
                                               class="btn btn-outline-primary btn-sm">
                                            Choose Files
                                        </label>
                                        <div class="text-muted small mt-2">Max file size: 5MB • PDF, DOC, ZIP</div>
                                        <div id="file-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mt-4">
                                <button type="submit" 
                                        class="btn btn-primary btn-lg rounded-pill">
                                    <i class="bi bi-send-check me-2"></i>
                                    <span class="spinner-border spinner-border-sm d-none" id="submit-spinner" role="status" aria-hidden="true"></span>
                                    <span id="submit-btn-text">Submit</span>
                                </button>
                            </div>

                            <!-- Success Message -->
                            <div id="success-message" 
                                 class="alert alert-success alert-dismissible fade show mt-4 d-none" 
                                 role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <span class="message"></span>
                                <button type="button" 
                                        class="btn-close" 
                                        data-bs-dismiss="alert" 
                                        aria-label="Close"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        // Keep existing JavaScript functionality
        $('#projectForm').on('submit', function(e) {
            e.preventDefault();
            $('#success-message').addClass('d-none');
            $('.invalid-feedback').text('').removeClass('d-block');
            $('.is-invalid').removeClass('is-invalid');
            // Disable the button
        $('#submit-btn').prop('disabled', true);

// Show spinner, hide text
$('#submit-spinner').removeClass('d-none');
$('#submit-btn-text').text('Submitting...');


            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('projects.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#success-message').removeClass('d-none').find('.message').text(res.message);
                    $('#projectForm')[0].reset();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.title) {
                        $('#title').addClass('is-invalid');
                        $('#title-error').text(errors.title[0]).addClass('d-block');
                    }
                    if (errors.description) {
                        $('#description').addClass('is-invalid');
                        $('#description-error').text(errors.description[0]).addClass('d-block');
                    }
                    if (errors.file) {
                        $('#fileInput').addClass('is-invalid');
                        $('#file-error').text(errors.file[0]).addClass('d-block');
                    }
                },
                complete: function() {
                    $('#submit-btn').prop('disabled', false);
                    $('#submit-spinner').addClass('d-none');
                    $('#submit-btn-text').text('Submit');
                }
            });
        });
    </script>
    @endsection
</x-app-layout>