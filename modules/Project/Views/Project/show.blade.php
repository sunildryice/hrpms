@extends('layouts.container')

@section('title', 'Edit Project')

@section('page_css')
    <style>
        #deliverables-table th,
        #deliverables-table td {
            border-color: #dee2e6;
        }

        .deliverable-row .btn {
            padding-inline: .35rem;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#project-index').addClass('active');
            $('select[name="members[]"]').select2({
                placeholder: "Select Members",
                width: '100%',
                closeOnSelect: false,
            });

            // Open Create Project Activity modal via AJAX
            $(document).on('click', '#btn-open-project-activity', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var url = $btn.data('url');
                if (!url) return;

                $btn.prop('disabled', true);
                $.get(url)
                    .done(function(html) {
                        var $container = $('#project-activity-modal-container');
                        $container.html(html);

                        var modalEl = document.getElementById('addProjectActivityModal');
                        if (!modalEl) return;

                        // Initialize Select2 inside modal if present
                        if ($.fn.select2) {
                            $(modalEl).find('.select2').select2({
                                width: '100%',
                                dropdownParent: $(modalEl)
                            });
                        }

                        // Initialize datepicker (Chen Fengyuan) for newly injected elements
                        if ($.fn.datepicker) {
                            $(modalEl).find('[data-toggle="datepicker"]').each(function() {
                                $(this).datepicker({
                                    language: 'en-GB',
                                    autoHide: true,
                                    format: 'yyyy-mm-dd'
                                });
                            });
                        }

                        // Show modal (Bootstrap 5 preferred, fallback to jQuery plugin)
                        if (window.bootstrap && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                        } else if (typeof $(modalEl).modal === 'function') {
                            $(modalEl).modal('show');
                        }
                    })
                    .fail(function() {
                        alert('Failed to load form. Please try again.');
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                    });
            });
        });
    </script>
@endsection

@section('page-content')
    {{-- Breadcrumb Header --}}
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('project.index') }}" class="text-decoration-none text-dark">Project</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Project Details</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Project Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header fw-bold">Project Information</div>
                <div class="card-body">
                    @include('Project::Partials.detail')
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @include('Project::ProjectActivity.index')
        </div>
    </div>

    <div id="project-activity-modal-container">
        kldsfmlk
    </div>
@endsection
