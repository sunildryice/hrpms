it @extends('layouts.container')

@section('title', 'Work Plan Details')

@section('page_css')
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#weekly-plan-index').addClass('active');

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
                        defaultContent: ''
                    },
                    {
                        data: 'members',
                        name: 'members'
                    }
                    @if ($isEditable)
                        , {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    @endif
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

                        const $modal = $('#addPlanModal');
                        const $form = $modal.find('#addPlanForm');
                        const $projectSelect = $form.find('.project-select');
                        const $activitySelect = $form.find('.activity-select');
                        const $membersSelect = $form.find('.members-select');

                        const parseJsonPayload = function(payload) {
                            if (!payload) return [];
                            if (Array.isArray(payload)) return payload;
                            if (typeof payload === 'object') return payload;
                            if (typeof payload === 'string') {
                                if (!payload.trim()) return [];
                                try {
                                    return JSON.parse(payload);
                                } catch (error) {
                                    return [];
                                }
                            }

                            return [];
                        };

                        const parseSelectedMembers = function(rawMembers) {
                            const parsed = parseJsonPayload(rawMembers);
                            return Array.isArray(parsed) ? parsed.map(String) : [];
                        };

                        const populateActivityOptions = function(activities, presetValue) {
                            $activitySelect.empty().append(
                                '<option value="">Select Activity</option>');

                            if (!activities.length) {
                                $activitySelect.prop('disabled', true).trigger('change.select2');
                                return;
                            }

                            $activitySelect.prop('disabled', false);

                            activities.forEach(function(activity) {
                                const option = new Option(activity.title, activity.id,
                                    false,
                                    String(activity.id) === String(presetValue || ''));
                                $activitySelect.append(option);
                            });

                            if (presetValue) {
                                $activitySelect.val(String(presetValue));
                            } else {
                                $activitySelect.val('');
                            }

                            $activitySelect.trigger('change.select2');
                        };

                        const populateMemberOptions = function(members, presetValues) {
                            const normalizedPreset = presetValues.map(String);
                            $membersSelect.empty();

                            if (!members.length) {
                                $membersSelect.prop('disabled', true).trigger('change.select2');
                                return;
                            }

                            $membersSelect.prop('disabled', false);

                            members.forEach(function(member) {
                                const isSelected = normalizedPreset.includes(String(member
                                    .id));
                                const option = new Option(member.name, member.id, false,
                                    isSelected);
                                $membersSelect.append(option);
                            });

                            $membersSelect.val(normalizedPreset).trigger('change.select2');
                        };

                        const refreshDependentSelects = function(usePreset = false) {
                            const selectedOption = $projectSelect.find(':selected');
                            const activities = parseJsonPayload(selectedOption.data('activities'));
                            const members = parseJsonPayload(selectedOption.data('members'));
                            const presetActivity = usePreset ? ($activitySelect.data(
                                'selected-activity') || '') : '';
                            const presetMembers = usePreset ? parseSelectedMembers($membersSelect
                                .data('selected-members')) : [];

                            populateActivityOptions(activities, presetActivity);
                            populateMemberOptions(members, presetMembers);

                            if (usePreset) {
                                $activitySelect.removeData('selected-activity');
                                $membersSelect.removeData('selected-members');
                            }
                        };

                        $projectSelect.select2({
                            dropdownParent: $modal,
                            width: '100%'
                        }).on('change', function() {
                            refreshDependentSelects();
                        });

                        $activitySelect.select2({
                            dropdownParent: $modal,
                            width: '100%'
                        });

                        $membersSelect.select2({
                            dropdownParent: $modal,
                            width: '100%',
                            placeholder: 'Select Members'
                        });

                        refreshDependentSelects(true);

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
                        <li class="breadcrumb-item"><a href="{{ route('work-plan.index') }}"
                                class="text-decoration-none text-dark">Work Plan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Plan Details: {{ $week['start_date']->format('M j') }} - {{ $week['end_date']->format('M j, Y') }}
                </h4>
            </div>
            <div class="add-info justify-content-end">
                @if ($isEditable)
                    <a href=""
                        data-href="{{ route('work-plan.create', ['from_date' => $week['start_date']->format('Y-m-d'), 'to_date' => $week['end_date']->format('Y-m-d')]) }}"
                        class="btn btn-primary btn-sm open-plan-modal">
                        <i class="bi bi-plus"></i> Add Plan
                    </a>
                @endif
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
                            <th>Members</th>
                            @if ($isEditable)
                                <th>{{ __('label.action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Plan Modal Container --}}
        <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    {{-- Content loaded dynamically --}}
                </div>
            </div>
        </div>

        {{-- Status Reason Modal --}}
        <div class="modal fade" id="statusReasonModal" tabindex="-1" aria-labelledby="statusReasonModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="statusReasonForm">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title mb-0 fs-6" id="statusReasonModalLabel">Mark As Completed</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="status_detail_id" name="id">
                            <input type="hidden" id="status_value" name="status">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="status_reason" class="form-label required-label m-0">Reason</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea class="form-control" id="status_reason" name="reason" rows="3" required
                                        placeholder="Please provide a reason for completion..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @stop
