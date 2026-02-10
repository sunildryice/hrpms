@extends('layouts.container')

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

        .wrap-text {
            white-space: normal !important;
        }

        .plan-modal-dialog {
            max-width: 1200px;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#work-plan-index').addClass('active');

            const statusUpdateUrlTemplate = "{{ route('work-plan.update-status', ['id' => '__ID__']) }}";
            const buildStatusUpdateUrl = (detailId) => statusUpdateUrlTemplate.replace('__ID__', detailId);
            const $statusReasonModal = $('#statusReasonModal');
            const $statusReasonForm = $('#statusReasonForm');

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
                        className: 'wrap-text',
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
                    {
                        data: 'members',
                        className: 'wrap-text',
                    }
                    @if ($isEditable)
                        {
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

            const resetPendingStatusSelection = () => {
                if (currentStatusElement) {
                    currentStatusElement.val(currentStatusPreviousValue).data('prev',
                        currentStatusPreviousValue);
                }
            };

            const handleStatusSuccess = (message, newStatus, $select) => {
                toastr.success(message || 'Status updated successfully');
                if ($select && typeof newStatus !== 'undefined') {
                    $select.data('prev', newStatus).val(newStatus);
                }
                oTable.ajax.reload(null, false);
            };

            function updateWorkPlanStatus(id, status, reason, $select, prev) {
                const payload = {
                    _method: 'PUT',
                    status: status,
                    reason: reason || ''
                };

                ajaxSubmit(buildStatusUpdateUrl(id), 'POST', payload, function(response) {
                    handleStatusSuccess(response.message, status, $select);
                }, function() {
                    if ($select) {
                        $select.val(prev);
                    }
                });
            }

            $statusReasonModal.on('hidden.bs.modal', function() {
                resetPendingStatusSelection();
                currentStatusElement = null;
                currentStatusPreviousValue = null;
            });

            $statusReasonForm.on('submit', function(event) {
                event.preventDefault();

                const detailId = $('#status_detail_id').val();
                if (!detailId) {
                    toastr.error('Work plan reference is missing. Please try again.');
                    return;
                }

                const formData = new FormData(this);
                formData.set('_method', 'PUT');

                ajaxSubmitFormData(buildStatusUpdateUrl(detailId), 'POST', formData, function(response) {
                    const newStatus = $('#status_value').val();
                    handleStatusSuccess(response.message, newStatus, currentStatusElement);
                    currentStatusElement = null;
                    currentStatusPreviousValue = null;
                    $statusReasonModal.modal('hide');
                });
            });

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
                        const $activitySelect = $('.activity-select');
                        const $membersSelect = $('.members-select');
                        const $projectSelect = $('.project-select');
                        let fvInstance = null;
                        let hasAttemptedSubmit = false;

                        const restoreFormData = () => {
                            const projectId = $projectSelect.val();
                            const activityId = $activitySelect.val();
                            const members = $membersSelect.attr('data-selected-members') ?
                                JSON.parse($membersSelect.attr('data-selected-members') || '[]') :
                                [];

                            if (projectId) {
                                $projectSelect.val(projectId).trigger('change');
                            }
                            if (activityId) {
                                $activitySelect.attr('data-selected-activity', activityId);
                            }
                            if (members.length) {
                                $membersSelect.attr('data-selected-members', JSON.stringify(
                                    members));
                            }
                        };

                        const ensureSelect2Parent = ($element) => {
                            if (!$element.parent().hasClass('select2-parent')) {
                                $element.wrap(
                                    '<div class="position-relative w-100 select2-parent"></div>'
                                );
                            }
                            return $element.parent();
                        };

                        const initSelect2Control = ($element, options = {}) => {
                            const $parent = ensureSelect2Parent($element);
                            const baseOptions = {
                                dropdownParent: $parent,
                                width: '100%',
                                dropdownAutoWidth: true,
                            };
                            $element.select2(Object.assign(baseOptions, options));
                            return $parent;
                        };

                        const bindMembersSelectEvents = () => {
                            $membersSelect.off('change.membersSync').on('change.membersSync',
                                function() {
                                    const current = $(this).val() || [];
                                    $(this).attr('data-selected-members', JSON.stringify(
                                        current));
                                    if (hasAttemptedSubmit && fvInstance) {
                                        fvInstance.revalidateField('members[]');
                                    }
                                });
                        };

                        const destroyMembersSelect2 = () => {
                            if ($membersSelect.hasClass('select2-hidden-accessible')) {
                                $membersSelect.select2('destroy');
                            }
                            $membersSelect.off('change.membersSync');
                        };

                        const initMembersSelect2 = () => {
                            initSelect2Control($membersSelect, {
                                placeholder: 'Select members',
                                minimumResultsForSearch: 0,
                            });
                            bindMembersSelectEvents();
                        };

                        const parseJsonAttribute = ($element, attribute) => {
                            const raw = $element.attr(attribute);
                            if (!raw) return [];
                            try {
                                return JSON.parse(raw);
                            } catch (error) {
                                return [];
                            }
                        };

                        const updateMembersSelect = (members) => {
                            destroyMembersSelect2();
                            $membersSelect.empty();

                            if (!members || !members.length) {
                                $membersSelect.prop('disabled', true);
                                $membersSelect.append(
                                    '<option value="">No members available</option>');
                                initMembersSelect2();
                                $membersSelect.val(null).trigger('change');
                                return;
                            }

                            const storedSelection = parseJsonAttribute($membersSelect,
                                'data-selected-members');
                            const selection = storedSelection.filter(id =>
                                members.some(member => String(member.id) === String(id))
                            );

                            members.forEach(member => {
                                const label = member.name || member.full_name || member
                                    .email_address ||
                                    ('Member #' + member.id);
                                const option = new Option(label, member.id, false, false);
                                $membersSelect.append(option);
                            });

                            $membersSelect.prop('disabled', false);
                            initMembersSelect2();
                            $membersSelect.val(selection).trigger('change');
                            $membersSelect.attr('data-selected-members', JSON.stringify(selection));
                        };

                        const handleProjectSelection = (selectedOption) => {
                            if (!selectedOption || !selectedOption.length) {
                                updateMembersSelect([]);
                                if (fvInstance) {
                                    fvInstance.enableValidator('members[]', 'notEmpty', false);
                                    fvInstance.resetField('members[]', true);
                                }
                                return;
                            }

                            const activities = parseJsonAttribute(selectedOption,
                                'data-activities');
                            const projectMembers = parseJsonAttribute(selectedOption,
                                'data-members');
                            const selectedActivityId = $activitySelect.attr(
                                'data-selected-activity') || '';

                            $activitySelect.html('<option value="">Select Activity</option>');

                            activities.forEach(activity => {
                                const option = new Option(activity.title, activity.id,
                                    false, activity.id == selectedActivityId);
                                $activitySelect.append(option);
                            });

                            if (selectedActivityId) {
                                $activitySelect.removeAttr('data-selected-activity');
                            }

                            updateMembersSelect(projectMembers);
                            if (fvInstance) {
                                fvInstance.enableValidator('members[]', 'notEmpty', !!(
                                    projectMembers && projectMembers.length));
                                if (!projectMembers || !projectMembers.length) {
                                    fvInstance.resetField('members[]', true);
                                }
                            }

                            $activitySelect.trigger('change');
                        };

                        setTimeout(() => {
                            restoreFormData();

                            initMembersSelect2();
                            initSelect2Control($projectSelect, {
                                placeholder: 'Select Project'
                            });
                            initSelect2Control($activitySelect, {
                                placeholder: 'Select Activity'
                            });

                            $projectSelect.on('change', function() {
                                handleProjectSelection($(this).find(':selected'));
                                if (hasAttemptedSubmit && fvInstance) {
                                    fvInstance.revalidateField('project_id');
                                }
                            });

                            if ($projectSelect.val()) {
                                setTimeout(() => {
                                    handleProjectSelection($projectSelect.find(
                                        ':selected'));
                                }, 100);
                            }
                        }, 50);

                        const form = document.getElementById('addPlanForm');
                        if (!form) {
                            toastr.error('Unable to initialize the form. Please try again.');
                            return;
                        }

                        const $plannedTask = $(form).find('textarea[name="planned_task"]');

                        fvInstance = FormValidation.formValidation(form, {
                            fields: {
                                project_id: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Project is required',
                                        },
                                    },
                                },
                                activity_id: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Activity is required',
                                        },
                                    },
                                },
                                planned_task: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Planned task is required',
                                        },
                                    },
                                },
                                'members[]': {
                                    validators: {
                                        notEmpty: {
                                            message: 'Select at least one member',
                                        },
                                    },
                                },
                            },
                            plugins: {
                                trigger: new FormValidation.plugins.Trigger({
                                    event: {
                                        project_id: 'submit',
                                        activity_id: 'submit',
                                        planned_task: 'submit',
                                        'members[]': 'submit',
                                    },
                                }),
                                bootstrap5: new FormValidation.plugins.Bootstrap5({
                                    rowSelector: '.row',
                                    eleInvalidClass: '',
                                    eleValidClass: '',
                                }),
                                submitButton: new FormValidation.plugins.SubmitButton(),
                                icon: new FormValidation.plugins.Icon({
                                    valid: 'bi bi-check2-square',
                                    invalid: 'bi bi-x-lg',
                                    validating: 'bi bi-arrow-repeat',
                                }),
                            },
                        });

                        fvInstance.enableValidator('members[]', 'notEmpty', !$membersSelect.prop(
                            'disabled'));

                        $activitySelect.on('change', function() {
                            if (hasAttemptedSubmit && fvInstance) {
                                fvInstance.revalidateField('activity_id');
                            }
                        });

                        if ($plannedTask.length) {
                            $plannedTask.on('input', function() {
                                if (hasAttemptedSubmit && fvInstance) {
                                    fvInstance.revalidateField('planned_task');
                                }
                            });
                        }

                        fvInstance.on('core.form.invalid', function() {
                            hasAttemptedSubmit = true;
                        });

                        fvInstance.on('core.form.valid', function() {
                            hasAttemptedSubmit = true;
                            const submitUrl = form.getAttribute('action');
                            const submitMethod = form.getAttribute('method') || 'POST';
                            const formData = new FormData(form);

                            const successCallback = function(response) {
                                $('#addPlanModal').modal('hide');
                                $('#addPlanModal').find('.modal-content').html('');
                                toastr.success(response.message ||
                                    'Work plan saved successfully');
                                oTable.ajax.reload();
                            };

                            ajaxSubmitFormData(submitUrl, submitMethod, formData,
                                successCallback);
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
    @include('Project::WorkPlan.Detail.partials.status-reason-modal-script')
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
                    <a href="{{ route('work-plan.create', ['from_date' => $week['start_date']->format('Y-m-d'), 'to_date' => $week['end_date']->format('Y-m-d')]) }}"
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
            <div class="modal-dialog modal-xl plan-modal-dialog">
                <div class="modal-content">
                    {{-- Content loaded dynamically --}}
                </div>
            </div>
        </div>

        {{-- Status Reason Modal --}}
        @include('Project::WorkPlan.Detail.partials.status-reason-modal')

    @stop
