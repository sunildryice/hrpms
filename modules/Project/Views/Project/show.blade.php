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

        #projectActivityTable th:nth-child(8),
        #projectActivityTable td:nth-child(8) {
            min-width: 140px;
            width: 140px !important;
        }

        /* Bootstrap modal styling for status change */
        #activityStatusModalLabel {
            font-weight: 600;
        }

        #activityStatusMessageLabel {
            text-align: left;
            width: 100%;
        }

        #activityStatusModal .btn-status-save {
            background-color: var(--ohw-blue);
            border-color: var(--ohw-blue);
        }

        .bg-orange {
            background-color: #fd7e14 !important;
            color: #fff;
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
                bFilter: true,
                bPaginate: true,
                bInfo: true,
                scrollX: true,
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
                        data: 'status',
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

            // Track previous status value before change
            $('#projectActivityTable').on('focus', '.activity-status-change', function() {
                $(this).data('previous', this.value);
            });

            // Handle status change with Bootstrap modal for Reason/Remarks
            $('#projectActivityTable').on('change', '.activity-status-change', function() {
                const $select = $(this);
                const newStatus = $select.val();
                const oldStatus = $select.data('previous');
                const activityId = $select.data('activity-id');

                if (!activityId) {
                    return;
                }

                const url = "{{ url('/project-activity') }}/" + activityId + "/status";


                if (newStatus === 'no_required' || newStatus === 'completed' || newStatus ===
                    'under_progress') {
                    const $modal = $('#activityStatusModal');

                    let title, label, dateLabel;
                    if (newStatus === 'no_required') {
                        title = 'Mark activity as Not Required';
                        label = 'Reason';
                        dateLabel = 'Completion Date';
                    } else if (newStatus === 'completed') {
                        title = 'Mark activity as Completed';
                        label = 'Remarks';
                        dateLabel = 'Completion Date';
                    } else if (newStatus === 'under_progress') {
                        title = 'Mark activity as Under Progress';
                        label = 'Remarks';
                        dateLabel = 'Actual Start Date';
                    }

                    $modal.data('url', url);
                    $modal.data('select', $select);
                    $modal.data('oldStatus', oldStatus);
                    $modal.data('status', newStatus);
                    $modal.data('updated', false);

                    $('#activityStatusModalLabel').text(title);
                    $('#activityStatusMessageLabel').text(label);
                    $('#activityStatusDateLabel').text(dateLabel);
                    $('#activityStatusMessage').val('').removeClass('is-invalid');

                    // Set default date to today and initialize datepicker
                    const today = new Date();
                    const formattedDate = today.getFullYear() + '-' +
                        String(today.getMonth() + 1).padStart(2, '0') + '-' +
                        String(today.getDate()).padStart(2, '0');

                    $('#activityStatusDate').val(formattedDate).removeClass('is-invalid');

                    // Destroy existing datepicker if any and reinitialize
                    $('#activityStatusDate').datepicker('destroy');
                    $('#activityStatusDate').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                    });

                    // Hide remarks field for under_progress
                    if (newStatus === 'under_progress') {
                        $('#activityStatusMessageContainer').hide();
                    } else {
                        $('#activityStatusMessageContainer').show();
                    }

                    $modal.modal('show');
                } else {
                    // For other statuses, just update directly without extra input
                    ajaxSubmit(url, 'POST', {
                        status: newStatus
                    }, function(response) {
                        toastr.success(response.message || 'Status updated successfully');
                        $('#projectActivityTable').DataTable().ajax.reload(null, false);
                    });
                }
            });

            // Submit handler for Bootstrap status modal
            $('#activityStatusForm').on('submit', function(e) {
                e.preventDefault();

                const $modal = $('#activityStatusModal');
                const url = $modal.data('url');
                const statusValue = $modal.data('status');
                const message = $('#activityStatusMessage').val().trim();
                const dateValue = $('#activityStatusDate').val().trim();

                if (!url || !statusValue) {
                    return;
                }

                let isValid = true;

                // Remarks is only required for no_required and completed
                if (statusValue !== 'under_progress' && !message) {
                    $('#activityStatusMessage').addClass('is-invalid');
                    $('#activityStatusMessage').siblings('.invalid-feedback').show();
                    isValid = false;
                }

                if (!dateValue) {
                    $('#activityStatusDate').addClass('is-invalid');
                    $('#activityStatusDate').siblings('.invalid-feedback').show();
                    isValid = false;
                }

                if (!isValid) {
                    return;
                }

                const data = {
                    status: statusValue,
                    // Backend will always receive this as `remarks`,
                    // label text (Reason/Remarks) is only for display.
                    remarks: message || null,
                    status_date: dateValue,
                };

                ajaxSubmit(url, 'POST', data, function(response) {
                    $modal.data('updated', true);
                    $modal.modal('hide');
                    toastr.success(response.message || 'Status updated successfully');
                    $('#projectActivityTable').DataTable().ajax.reload(null, false);
                });
            });

            $('#activityStatusMessage').on('input', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').hide();
            });

            $('#activityStatusDate').on('change', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').hide();
            });

            // On modal close without update, revert dropdown to previous value
            $('#activityStatusModal').on('hidden.bs.modal', function() {
                const $modal = $(this);
                const updated = $modal.data('updated');
                const $select = $modal.data('select');
                const oldStatus = $modal.data('oldStatus');

                if (!updated && $select && typeof oldStatus !== 'undefined') {
                    $select.val(oldStatus);
                }
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
                                        message: 'Activity is required',
                                        callback: function(input) {
                                            const level = $activityLevelSelect.val();
                                            if (level === 'theme') return true;
                                            return input.value !== '';
                                        }
                                    }
                                }
                            },
                            'completion_date': {
                                validators: {
                                    callback: {
                                        message: 'End date must be on or after start date',
                                        callback: function(input) {
                                            const startVal = $('[name="start_date"]')
                                                .val();
                                            const endVal = input.value;
                                            if (!startVal || !endVal) {
                                                return true;
                                            }
                                            return endVal >= startVal;
                                        }
                                    }
                                }
                            },
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
                        startDate: new Date(
                            '{{ $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d') }}'
                        ),
                        endDate: new Date(
                            '{{ $project->completion_date ? $project->completion_date->format('Y-m-d') : date('Y-m-d') }}'
                        ),
                        zIndex: 2048,
                    });

                    $('[name="completion_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        startDate: new Date(
                            '{{ $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d') }}'
                        ),
                        endDate: new Date(
                            '{{ $project->completion_date ? $project->completion_date->format('Y-m-d') : date('Y-m-d') }}'
                        ),
                        zIndex: 2048,
                    });

                    const $startInput = $('[name="start_date"]');
                    const $endInput = $('[name="completion_date"]');
                    const $startHint = $('#start-date-hint');
                    const $endHint = $('#end-date-hint');
                    const defaultMinDate =
                        '{{ $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d') }}';
                    const defaultMaxDate =
                        '{{ $project->completion_date ? $project->completion_date->format('Y-m-d') : date('Y-m-d') }}';

                    // Filter Theme/Partent Activity Based on the Activity Level, and Toggle Parent Activity, Stage and Members fields based on the Activity Level

                    const $activityLevelSelect = $('[name="activity_level"]');
                    const $parentRow = $('#parent-activity-row');
                    const $membersRow = $('#members-row');
                    const $deliverablesRow = $('#deliverables-row');
                    const $budgetDescriptionRow = $('#budget_description-row');
                    const $stageRow = $('#stage-row');
                    const $parentSelect = $('#parent_activity_select');
                    const $membersSelect = $('#members_select');

                    const allParentOptions = $parentSelect.html();

                    function formatHintDate(dateStr) {
                        if (!dateStr) {
                            return '';
                        }

                        const clean = String(dateStr).substring(0, 10); // keep YYYY-MM-DD
                        const parts = clean.split('-');
                        if (parts.length !== 3) {
                            return dateStr;
                        }

                        const d = new Date(clean);
                        if (isNaN(d.getTime())) {
                            return dateStr;
                        }

                        const options = {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit'
                        };
                        return d.toLocaleDateString('en-GB', options);
                    }

                    function setDateRange(minDate, maxDate) {
                        if (!minDate || !maxDate) {
                            return;
                        }

                        const min = new Date(minDate);
                        const max = new Date(maxDate);

                        $startInput.datepicker('setStartDate', min);
                        $startInput.datepicker('setEndDate', max);
                        $endInput.datepicker('setStartDate', min);
                        $endInput.datepicker('setEndDate', max);

                        $startInput.data('range-min', minDate);
                        $startInput.data('range-max', maxDate);
                        $endInput.data('range-min', minDate);
                        $endInput.data('range-max', maxDate);

                        const minLabel = formatHintDate(minDate);
                        const maxLabel = formatHintDate(maxDate);

                        if ($startHint.length) {
                            $startHint.text('Allowed: ' + minLabel + ' to ' + maxLabel);
                        }
                        if ($endHint.length) {
                            $endHint.text('Allowed: ' + minLabel + ' to ' + maxLabel);
                        }
                    }

                    function updateRangeFromParent() {
                        const level = $activityLevelSelect.val();
                        if (level === 'theme') {
                            setDateRange(defaultMinDate, defaultMaxDate);
                            return;
                        }

                        const $selected = $parentSelect.find('option:selected');
                        const pStart = $selected.data('start-date');
                        const pEnd = $selected.data('end-date');

                        if (pStart && pEnd) {
                            setDateRange(pStart, pEnd);
                        } else {
                            setDateRange(defaultMinDate, defaultMaxDate);
                        }
                    }

                    function updateEndMinFromStart() {
                        const startVal = $startInput.val();
                        const rangeMin = $endInput.data('range-min') || defaultMinDate;

                        let newMin = rangeMin;
                        if (startVal && startVal > newMin) {
                            newMin = startVal;
                        }

                        $endInput.datepicker('setStartDate', new Date(newMin));

                        const endVal = $endInput.val();
                        if (endVal && startVal && endVal < startVal) {
                            $endInput.val(startVal);
                        }

                        fv.revalidateField('completion_date');
                    }

                    const $parentLabel = $('#parent-activity-row .form-label');

                    function updateParentLabel() {
                        const level = $activityLevelSelect.val();

                        let newLabel = 'Parent Activity';

                        if (level === 'activity') {
                            newLabel = 'Theme Activity';
                        } else if (level === 'sub_activity') {
                            newLabel = 'Parent Activity';
                        }
                        $parentLabel.text(newLabel);
                    }

                    function updateParentOptions() {
                        const level = $activityLevelSelect.val();

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

                    function updateStageFromParent() {
                        const level = $activityLevelSelect.val();
                        if (level === 'theme') {
                            // For theme → user selects stage manually → do nothing here
                            return;
                        }
                        const $selectedParent = $parentSelect.find('option:selected');
                        const parentStageId = $selectedParent.data('stage');
                        if (parentStageId) {
                            $('[name="activity_stage_id"]').val(parentStageId).trigger('change');
                        } else {
                            $('[name="activity_stage_id"]').val('').trigger('change');
                        }
                    }

                    function toggleFieldsBasedOnLevel(level) {
                        if (!level) {
                            $stageRow.show();
                            $parentRow.hide();
                            $membersRow.hide();
                            $deliverablesRow.hide();
                            $budgetDescriptionRow.hide();
                            $parentSelect.val(null).trigger('change');
                            $membersSelect.val(null).trigger('change');
                            return;
                        }
                        if (level === 'theme') {
                            $stageRow.show();
                            $parentRow.hide();
                            $membersRow.hide();
                            $deliverablesRow.hide();
                            $budgetDescriptionRow.hide();
                            $parentSelect.val(null).trigger('change');
                            $membersSelect.val(null).trigger('change');
                            setDateRange(defaultMinDate, defaultMaxDate);
                        } else {
                            $stageRow.hide();
                            $parentRow.show();
                            $membersRow.show();
                            $deliverablesRow.show();
                            $budgetDescriptionRow.show();
                            updateParentLabel();
                            updateParentOptions();
                            updateRangeFromParent();
                        }
                    }

                    $activityLevelSelect.on('change', function() {
                        const level = $(this).val();
                        toggleFieldsBasedOnLevel(level);
                    });

                    const initialLevel = $activityLevelSelect.val();
                    if (initialLevel) {
                        toggleFieldsBasedOnLevel(initialLevel);
                    } else {
                        $parentRow.hide();
                        $membersRow.hide();
                        $deliverablesRow.hide();
                        $budgetDescriptionRow.hide();
                    }

                    $parentSelect.select2({
                        dropdownParent: $parentSelect.parent(),
                        width: '100%'
                    });

                    $membersSelect.select2({
                        dropdownParent: $membersSelect.parent(),
                        width: '100%'
                    });

                    // Initial date range and listeners
                    setDateRange(defaultMinDate, defaultMaxDate);
                    updateRangeFromParent();

                    $parentSelect.on('change', function() {
                        updateRangeFromParent();
                        updateEndMinFromStart();
                        updateStageFromParent();
                    });

                    $startInput.on('change', function() {
                        updateEndMinFromStart();
                    });
                    $activityLevelSelect.on('change', function() {
                        const level = $(this).val();
                        toggleFieldsBasedOnLevel(level);
                        updateStageFromParent();
                    });
                    updateStageFromParent();
                });
            });

            $(document).on('click', '.open-import-modal-form', function(e) {
                e.preventDefault();
                document.querySelector(".preloader").style.display = "block";
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    document.querySelector(".preloader").style.display = "none";
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
                        document.querySelector(".preloader").style.display = "block";
                        ajaxSubmitFormData($url, 'POST', data, function(response) {
                            successCallback(response);
                            document.querySelector(".preloader").style.display =
                                "none";
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

            $(document).on('click', '.open-project-activity-extension-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('ProjectActivityExtensionForm');

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            extended_completion_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The extended completion date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The date is not a valid date'
                                    }
                                },
                            },
                            reason: {
                                validators: {
                                    notEmpty: {
                                        message: 'The reason is required'
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


                    $('[name="extended_completion_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                        startDate: new Date(
                            '{{ $projectActivity->min('completion_date') ?? '' }}'),
                    }).on('change', function(e) {
                        fv.revalidateField('extended_completion_date');
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('project.dashboard', ['id' => $project->id]) }}"
                                class="text-decoration-none text-dark">
                                {{ $project->short_name }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Project Details</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Project Details -
                    {{ $project->short_name }}
                </h4>
            </div>

            @include('Project::Partials.project-header-actions', ['project' => $project])
        </div>
    </div>

    <div class="row mb-2">

        <div class="col-lg-12">
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
                                <a class="btn btn-secondary btn-sm text-capitalize"
                                    href="{{ route('project-activity.export.activities', $project) }}" target="_blank"><i
                                        class="bi bi-download"></i> Export Activity</a>
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
                                    <th>Activity Name</th>
                                    <th>Parent Activity</th>
                                    <th>Start Date</th>
                                    <th>Completion Date</th>
                                    <th width="100">Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tablebody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Status Reason/Remarks Modal --}}
    <div class="modal fade" id="activityStatusModal" tabindex="-1" aria-labelledby="activityStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="activityStatusModalContent">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title mb-0 fs-6" id="activityStatusModalLabel">Update Activity Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="activityStatusForm" autocomplete="off">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required-label" id="activityStatusDateLabel"
                                for="activityStatusDate">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="activityStatusDate" readonly />
                            <div class="invalid-feedback" style="display: none;">This field is required.</div>
                        </div>
                        <div class="mb-3" id="activityStatusMessageContainer">
                            <label class="form-label" id="activityStatusMessageLabel" for="activityStatusMessage">Reason
                                <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="activityStatusMessage" rows="3"></textarea>
                            <div class="invalid-feedback" style="display: none;">This field is required.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-status-save">OK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
