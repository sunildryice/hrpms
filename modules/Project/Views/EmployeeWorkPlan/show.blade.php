@extends('layouts.container')

@section('title', 'Employee Work Plan Details')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employee-work-plan-index').addClass('active');

            $('.activity-select').select2({
                dropdownParent: $('#addPlanModal'),
                width: '100%'
            });

            var oTable = $('#WeeklyPlanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: window.location.href,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'project.short_name',
                        name: 'project.short_name',
                        defaultContent: ''
                    },
                    {
                        data: 'activity.title',
                        name: 'activity.title',
                        defaultContent: ''
                    },
                    {
                        data: 'plan_tasks',
                        name: 'plan_tasks',
                        defaultContent: ''
                    },
                    {
                        data: 'status',
                        name: 'status',
                        defaultContent: ''
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        defaultContent: '',
                    },
                ],
                drawCallback: function() {
                    $('.work-plan-status').each(function() {
                        $(this).data('prev', $(this).val());
                    });
                }
            });

            var currentStatusElement = null;
            var currentStatusPreviousValue = null;

            $(document).on('change', '.work-plan-status', function() {
                var $select = $(this);
                var id = $select.data('id');
                var status = $select.val();
                var prev = $select.data('prev');

                if (status === 'completed' || status === 'no_required') {
                    currentStatusElement = $select;
                    currentStatusPreviousValue = prev;

                    var labelText = status === 'completed' ? 'Remarks' : 'Reason';
                    var placeholderText = status === 'completed' ? 'Please provide remarks...' :
                        'Please provide a reason...';
                    var titleText = status === 'completed' ? 'Mark As Completed' : 'Mark As No Required';

                    $('#statusReasonModalLabel').text(titleText);
                    $('label[for="status_reason"]').text(labelText);
                    $('#status_reason').attr('placeholder', placeholderText);

                    $('#status_detail_id').val(id);
                    $('#status_value').val(status);
                    $('#status_reason').val('');
                    $('#statusReasonModal').modal('show');
                } else {
                    updateWorkPlanStatus(id, status, null, $select, prev);
                }
            });

            // Handle Modal Hidden (Cancel/Close)
            $('#statusReasonModal').on('hide.bs.modal', function() {
                if (currentStatusElement) {
                    // Revert
                    currentStatusElement.val(currentStatusPreviousValue);
                    currentStatusElement = null;
                }
            });

            // Handle Modal Submit
            $('#statusReasonForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#status_detail_id').val();
                var status = $('#status_value').val();
                var reason = $('#status_reason').val();

                var $select = currentStatusElement;
                var prev = currentStatusPreviousValue;

                // Mark as handled so hide event doesn't revert
                currentStatusElement = null;

                updateWorkPlanStatus(id, status, reason, $select, prev, function() {
                    $('#statusReasonModal').modal('hide');
                });
            });

            function updateWorkPlanStatus(id, status, reason, $select, prev, successCallback) {
                $.ajax({
                    url: '/work-plan/' + id + '/update-status',
                    type: 'PUT',
                    data: {
                        status: status,
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        $select.data('prev', status);
                        oTable.ajax.reload(null, false); // Reload table to reflect reason change
                        if (successCallback) successCallback();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Error updating status');
                        // Revert
                        $select.val(prev);
                    }
                });
            }

            $(document).on('click', '.open-plan-modal, .edit-work-plan', function(e) {
                e.preventDefault();
                $('#addPlanModal').find('.modal-content').html('');
                $('#addPlanModal').modal('show').find('.modal-content').load($(this).attr('href') || $(this)
                    .data('href'),
                    function(response, status, xhr) {
                        if (status == "error") {
                            toastr.error('Could not load form');
                            return;
                        }

                        $('.project-select').select2({
                            dropdownParent: $('#addPlanModal'),
                            width: '100%'
                        }).on('change', function() {
                            const selectedOption = $(this).find(':selected');
                            const activities = selectedOption.data('activities');
                            const $activitySelect = $('.activity-select');

                            $activitySelect.html('<option value="">Select Activity</option>');

                            if (activities && activities.length > 0) {
                                $.each(activities, function(index, activity) {
                                    $activitySelect.append(new Option(activity.title,
                                        activity.id));
                                });
                            }
                            $activitySelect.trigger('change');
                        });

                        $('.activity-select').select2({
                            dropdownParent: $('#addPlanModal'),
                            width: '100%'
                        });

                        const form = document.getElementById('addPlanForm');
                        FormValidation.formValidation(form, {
                            fields: {
                                project_id: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The Project is required'
                                        }
                                    }
                                },
                                activity_id: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The Activity is required'
                                        }
                                    }
                                },
                                planned_task: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The planned task is required'
                                        }
                                    }
                                }
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
                            const $url = form.getAttribute('action');
                            const method = form.getAttribute('method');
                            const formData = new FormData(form);

                            const successCallback = function(response) {
                                $('#addPlanModal').modal('hide');
                                toastr.success(response.message || 'Saved successfully');
                                oTable.ajax.reload();
                            };

                            // Handle Laravel PUT method override
                            let ajaxMethod = method;
                            if (formData.get('_method') === 'PUT') {
                                ajaxMethod = 'POST';
                            }

                            ajaxSubmitFormData($url, ajaxMethod, formData, successCallback);
                        });
                    });
            });

            // Handle delete
            $('#WeeklyPlanTable').on('click', '.delete-work-plan', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.data('href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee-work-plan.index') }}"
                                class="text-decoration-none text-dark">Employee Work Plan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Plan Details: {{ $week['start_date']->format('M j') }} - {{ $week['end_date']->format('M j, Y') }} -
                    {{ $workPlan->employee->full_name }}
                </h4>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <div class="card border shadow-sm rounded h-100">
                <div class="card-header fw-bold">
                    Plan Summary
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col">
                            <b class="text-muted d-block">Total Tasks</b>
                            <h5 class="my-3">{{ $stats['total'] ?? 0 }}</h5>
                        </div>
                        <div class="col">
                            <b class="text-muted d-block">Completed</b>
                            <h5 class="my-3">{{ $stats['completed'] ?? 0 }}</h5>
                        </div>
                        <div class="col">
                            <b class="text-muted d-block">Partially Completed</b>
                            <h5 class="my-3">{{ $stats['partially_completed'] ?? 0 }}</h5>
                        </div>
                        <div class="col">
                            <b class="text-muted d-block">Not Started</b>
                            <h5 class="my-3">{{ $stats['not_started'] ?? 0 }}</h5>
                        </div>
                        <div class="col">
                            <b class="text-muted d-block">No Required</b>
                            <h5 class="my-3">{{ $stats['no_required'] ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded c-tabs-content active" id="weekly-plan-table">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="WeeklyPlanTable">
                    <thead class="bg-light">
                        <tr>
                            <th>SN</th>
                            <th>Project</th>
                            <th>Activity</th>
                            <th>Planned Tasks</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    @stop
