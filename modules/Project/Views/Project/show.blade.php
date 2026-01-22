@extends('layouts.container')

@section('title', 'Show Project')

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
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#project-index').addClass('active');

            var oTable = $('#projectActivityTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('project-activity.index', $project->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'activity_stage',
                        name: 'activity_stage'
                    },
                    {
                        data: 'activity_level',
                        name: 'activity_level'
                    },
                    {
                        data: 'parent',
                        name: 'parent'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'completion_date',
                        name: 'completion_date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'sticky-col'
                    },
                ],
            });

            $('#projectActivityTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-project-activity-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('ProjectActivityCreateForm');
                    $(form).find(".select2").each(function() {
                        $(this)
                            .wrap("<div class=\"position-relative\"></div>")
                            .select2({
                                dropdownParent: $(this).parent(),
                                width: '100%',
                                dropdownAutoWidth: true
                            });
                    });
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: 'The title is required'
                                    },
                                },
                            },
                            activity_stage_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'The stage is required'
                                    },
                                },
                            },
                            activity_level: {
                                validators: {
                                    notEmpty: {
                                        message: 'The activity level is required'
                                    },
                                },
                            },
                            start_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The start date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The start date is not a valid date'
                                    }
                                },
                            },
                            completion_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The completion date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The completion date is not a valid date'
                                    }
                                },
                            },
                            members: {
                                validators: {
                                    notEmpty: {
                                        message: 'The members field is required'
                                    },
                                },
                            },
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap5: new FormValidation.plugins.Bootstrap5(),
                            submitButton: new FormValidation.plugins.SubmitButton(),
                            icon: new FormValidation.plugins.Icon({
                                valid: 'bi bi-check2-square',
                                invalid: 'bi bi-x-lg',
                                validating: 'bi bi-arrow-repeat',
                            }),
                        },
                    }).on('core.form.valid', function() {
                        const $url = fv.form.action;
                        const formData = new FormData(form);

                        const successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message || 'Saved successfully');
                            oTable.ajax.reload();
                        };

                        ajaxSubmitFormData($url, 'POST', formData, successCallback);
                    });

                    $('[name="start_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function(e) {
                        fv.revalidateField('start_date');
                    });

                    $('[name="completion_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    }).on('change', function(e) {
                        fv.revalidateField('completion_date');
                    });


                });
            });

            $(document).on('click', '.open-import-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('activityImportForm');
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            attachment: {
                                validators: {
                                    notEmpty: {
                                        message: 'Attachment is required',
                                    },
                                    file: {
                                        extension: 'xls,xlsx',
                                        type: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        message: 'Please choose an Excel file',
                                    },
                                },
                            },
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap5: new FormValidation.plugins.Bootstrap5(),
                            submitButton: new FormValidation.plugins.SubmitButton(),
                            icon: new FormValidation.plugins.Icon({
                                valid: 'bi bi-check2-square',
                                invalid: 'bi bi-x-lg',
                                validating: 'bi bi-arrow-repeat',
                            }),
                        },
                    }).on('core.form.valid', function(event) {
                        const $url = fv.form.action;
                        const $form = fv.form;
                        const data = new FormData($form);

                        const successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            $('#projectActivityTable').DataTable().ajax.reload();
                        };
                        ajaxSubmitFormData($url, 'POST', data, function(response) {
                            successCallback(response);
                        });
                    });
                });
            });
        });
    </script>
@endsection

@section('page-content')
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
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold">Project Activity</span>
                        <div class="justify-content-end d-flex gap-2">

                            @can('manage-project-activity-on-certain-time', $project)
                                <button data-toggle="modal" class="btn btn-secondary btn-sm open-import-modal-form"
                                    href="{{ route('project-activity.import.create', ['project' => $project->id]) }}">
                                    <i class="bi-plus"></i> Import Activity
                                </button>
                                <button data-toggle="modal" class="btn btn-primary btn-sm open-project-activity-modal-form"
                                    href="{{ route('project-activity.create', ['project' => $project->id]) }}"><i
                                        class="bi-plus"></i> Add Project Activity
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="projectActivityTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>SN</th>
                                    <th>Stage</th>
                                    <th>Activity Level</th>
                                    <th>Parent Activity</th>
                                    <th>Activity Title</th>
                                    <th>Start Date</th>
                                    <th>Completion Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tablebody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="project-activity-modal-container"></div>
        </div>
    </div>

    <div id="project-activity-modal-container">
    </div>
@endsection
