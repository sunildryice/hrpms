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
                bPaginate: true,
                bInfo: false,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'activity_level',
                        name: 'activity_level'
                    },
                    {
                        data: 'activity_stage',
                        name: 'activity_stage'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'parent',
                        name: 'parent'
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
                            'parent_id': {
                                validators: {
                                    callback: {
                                        message: 'Parent activity is required',
                                        callback: function(input) {
                                            const level = $activityLevelSelect.val();
                                            if (level === 'theme') return true;
                                            return input.value !== '';
                                        }
                                    }
                                }
                            },
                            // start_date: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'The start date is required'
                            //         },
                            //         date: {
                            //             format: 'YYYY-MM-DD',
                            //             message: 'The start date is not a valid date'
                            //         }
                            //     },
                            // },
                            // completion_date: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'The completion date is required'
                            //         },
                            //         date: {
                            //             format: 'YYYY-MM-DD',
                            //             message: 'The completion date is not a valid date'
                            //         }
                            //     },
                            // },
                            'members[]': {
                                validators: {
                                    callback: {
                                        message: 'At least one member is required',
                                        callback: function(input) {
                                            const level = $activityLevelSelect.val();
                                            if (level === 'theme') return true;
                                            return $membersSelect.val() &&
                                                $membersSelect.val().length > 0;
                                        }
                                    }
                                }
                            }
                        },
                        plugins: {
                            // Remove live validation trigger to validate only on submit
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
                        startDate: new Date('{{ $project->start_date->format('Y-m-d') }}'),
                        endDate: new Date(
                            '{{ $project->completion_date->format('Y-m-d') }}'),
                        zIndex: 2048,
                    });

                    $('[name="completion_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        startDate: new Date('{{ $project->start_date->format('Y-m-d') }}'),
                        endDate: new Date(
                            '{{ $project->completion_date->format('Y-m-d') }}'),
                        zIndex: 2048,
                    });

                    // Filter Partent Activity Based on the Activity Level and Stage, and Toggle Parent Activity and Members fields based on the Activity Level

                    const $activityLevelSelect = $('[name="activity_level"]');
                    const $stageSelect = $('[name="activity_stage_id"]');
                    const $parentRow = $('#parent-activity-row');
                    const $membersRow = $('#members-row');
                    const $parentSelect = $('#parent_activity_select');
                    const $membersSelect = $('#members_select');

                    const allParentOptions = $parentSelect.html();

                    function updateParentOptions() {
                        const level = $activityLevelSelect.val();
                        const stage = $stageSelect.val();

                        $parentSelect.html('<option value="">Select Parent Activity</option>');
                        if (level === 'theme') {
                            $parentSelect.html(
                                '<option value="">Not applicable for Theme</option>');
                            $parentSelect.trigger('change');
                            return;
                        }
                        let allowedParentLevel = null;
                        let placeholderText = "Select Parent Activity";

                        if (level === 'activity') {
                            allowedParentLevel = 'theme';
                            placeholderText = "Select Theme Activity";
                        } else if (level === 'sub_activity') {
                            allowedParentLevel = 'activity';
                            placeholderText = "Select Parent Activity";
                        }

                        if (!allowedParentLevel) {
                            return;
                        }
                        let filtered = $(allParentOptions).filter(function() {
                            const $opt = $(this);
                            if (!$opt.val()) return false;
                            return $opt.data('level') === allowedParentLevel;
                        });

                        if (stage) {
                            filtered = filtered.filter(function() {
                                const $opt = $(this);
                                return String($opt.data('stage')) === String(stage);
                            });
                        }
                        $parentSelect.html('<option value="">' + placeholderText + '</option>');

                        if (filtered.length === 0) {
                            $parentSelect.append(
                                '<option value="" disabled>No matching parent activities found</option>'
                            );
                        } else {
                            $parentSelect.append(filtered);
                        }
                        $parentSelect.trigger('change');
                    }

                    function toggleFieldsBasedOnLevel(level) {
                        if (level === 'theme') {
                            $parentRow.hide();
                            $membersRow.hide();
                            $parentSelect.val(null).trigger('change');
                            $membersSelect.val(null).trigger('change');
                        } else {
                            $parentRow.show();
                            $membersRow.show();
                            updateParentOptions();
                        }
                    }

                    $activityLevelSelect.on('change', function() {
                        const level = $(this).val();
                        toggleFieldsBasedOnLevel(level);
                    });

                    $stageSelect.on('change', function() {
                        const level = $activityLevelSelect.val();
                        if (level && level !== 'theme') {
                            updateParentOptions();
                        }
                    });

                    const initialLevel = $activityLevelSelect.val();
                    if (initialLevel) {
                        toggleFieldsBasedOnLevel(initialLevel);
                    } else {
                        $parentRow.hide();
                        $membersRow.hide();
                    }

                    $parentSelect.select2({
                        dropdownParent: $parentSelect.parent(),
                        width: '100%'
                    });

                    $membersSelect.select2({
                        dropdownParent: $membersSelect.parent(),
                        width: '100%'
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

            $(document).on('click', '.open-timesheet-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('ProjectActivityTimeSheetForm');

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            timesheet_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The date is not a valid date'
                                    }
                                },
                            },
                            hours_spent: {
                                validators: {
                                    notEmpty: {
                                        message: 'The hours spent is required'
                                    },
                                    numeric: {
                                        message: 'The hours spent must be a number'
                                    },
                                    between: {
                                        min: 0.1,
                                        max: 24,
                                        message: 'Hours spent should be between 0.1 and 24'
                                    }
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


                    $('[name="timesheet_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        startDate: new Date(
                            '{{ $projectActivity->min('start_date') ?? '' }}'),
                        endDate: new Date(
                            '{{ $projectActivity->max('completion_date') ?? '' }}'),
                        zIndex: 2048,
                    }).on('change', function(e) {
                        fv.revalidateField('timesheet_date');
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

            <div>
                <a href="{{ route('project.gantt.index', ['id' => $project->id]) }}"
                    class="btn btn-primary btn-sm">Overview</a>
                <a href="{{ route('project.gantt.index', ['id' => $project->id]) }}" class="btn btn-primary btn-sm">Project
                    Activites</a>
                <a href="{{ route('project.gantt.index', ['id' => $project->id]) }}"
                    class="btn btn-primary btn-sm">Gantt</a>

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
                                    <th>Activity Level</th>
                                    <th>Stage</th>
                                    <th>Activity Title</th>
                                    <th>Parent Activity</th>
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
