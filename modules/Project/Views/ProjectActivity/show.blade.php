@extends('layouts.container')

@section('title', 'Show Project Activity')

@section('page_js')

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#project-index').addClass('active');

            var oTable = $('#activityTimeSheetTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('project-activity-timesheet.index', ['projectActivity' => $projectActivity->id]) }}',
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
                        data: 'timesheet_date',
                        name: 'timesheet_date'
                    },
                    {
                        data: 'activity_title',
                        name: 'activity_title'
                    },
                    {
                        data: 'hours_spent',
                        name: 'hours_spent'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#activityTimeSheetTable').on('click', '.delete-record', function(e) {
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

            $(document).on('click', '.delete-project-activity-attachment', function(e) {
                e.preventDefault();
                const $button = $(this);
                const url = $button.data('href');
                ajaxDeleteSweetAlert(url, function(response) {
                    toastr.success(response.message || 'Attachment deleted successfully.',
                        'Success', {
                            timeOut: 5000
                        });
                    window.location.reload();
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
                            attachment: {
                                validators: {
                                    file: {
                                        extension: 'jpeg,jpg,png,pdf',
                                        type: 'image/jpeg,image/png,application/pdf',
                                        maxSize: '5097152',
                                        message: 'The selected file is not valid file or must not be greater than 5 MB.',
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


                    $('[name="timesheet_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                        endDate: new Date(),
                        todayHighlight: true,
                        todayBtn: true
                    }).on('change', function(e) {
                        fv.revalidateField('timesheet_date');
                    });

                    // Auto-select today only if the field is empty (create mode)
                    if (!$('[name="timesheet_date"]').val().trim()) {
                        const today = new Date().toISOString().split('T')[0];
                        $('[name="timesheet_date"]').val(today);
                        $('[name="timesheet_date"]').datepicker('setDate', today);
                    }
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('project.show', $projectActivity->project_id) }}"
                                class="text-decoration-none text-dark">Project Details</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Project Activity</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">View Project Activity</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header fw-bold">Project Activity Information</div>
                <div class="card-body">
                    @include('Project::Partials.activity-detail')
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold">Project Activity TimeSheets</span>
                        <div class="justify-content-end d-flex gap-2">
                            <button data-toggle="modal" class="btn btn-primary btn-sm open-timesheet-modal-form"
                                href="{{ route('project-activity.timesheet.create', ['projectActivity' => $projectActivity->id]) }}"><i
                                    class="bi-plus"></i> Add TimeSheet
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="activityTimeSheetTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>{{ __('label.date') }}</th>
                                    <th>{{ __('label.activity') }}</th>
                                    <th>Hours Spent</th>
                                    <th>{{ __('label.attachment') }}</th>
                                    <th>{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tablebody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold">Output/Deliverables Documents</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($projectActivity->attachments->isEmpty())
                        <p class="text-muted mb-0">No deliverable documents uploaded yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('label.sn') }}</th>
                                        <th>Document Name</th>
                                        <th>Uploaded On</th>
                                        <th class="text-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projectActivity->attachments as $index => $attachment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $attachment->title }}</td>
                                            <td>{{ $attachment->created_at?->format('M d, Y h:i A') ?? '-' }}</td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('project-activity.attachments.view', $attachment->id) }}"
                                                    class="btn btn-outline-info btn-sm" target="_blank" rel="noopener"
                                                    title="View document">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('project-activity.attachments.download', $attachment->id) }}"
                                                    class="btn btn-outline-primary btn-sm" title="Download document">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @can('delete', $attachment)
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm delete-project-activity-attachment"
                                                        data-href="{{ route('project-activity.attachments.destroy', $attachment->id) }}"
                                                        title="Delete document">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
