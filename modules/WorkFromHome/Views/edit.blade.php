@extends('layouts.container')

@section('title', 'Edit Work From Home Request')

@section('page_css')
    <link href="{{ asset('plugins/slim-select/dist/slimselect.css') }}" rel="stylesheet">
    <style>
        #deliverables-table th,
        #deliverables-table td {
            border-color: #dee2e6;
        }

        .task-item+.task-item {
            margin-top: .35rem;
        }

        .task-item .btn {
            padding-inline: .35rem;
        }

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
            $('#navbarVerticalMenu').find('#wfh-requests-index').addClass('active');

            $('#send_to, #type').addClass('select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            // Initialize select2 for project and activities
            $('.project-select, .activities-select').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            const form = document.getElementById('wfhRequestEditForm');
            const $tbody = $('#deliverables-body');

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: new Date()
            });
            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: new Date()
            });

            // detect last existing row index
            let rowIndex = (function() {
                let maxIndex = 0;
                $tbody.find('.deliverable-row').each(function() {
                    const idx = parseInt($(this).data('row-index'), 10);
                    if (!isNaN(idx) && idx > maxIndex) {
                        maxIndex = idx;
                    }
                });
                return maxIndex;
            })();

            function buildDeliverableRow(idx) {
                return `
                    <tr class="deliverable-row" data-row-index="${idx}">
                        <td style="width: 10%;">
                            <input type="text" class="form-control date" readonly name="deliverables[${idx}][date]">
                        </td>
                        <td style="width: 15%;">
                            <select class="form-select project-select"
                                    name="deliverables[${idx}][project_id]" required>
                                <option value="" disabled selected>Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" data-activities='@json($project->activities)'>{{ $project->short_name ?: $project->title }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select activities-select" name="deliverables[${idx}][activity_id]" required>
                                <option value="">Select Activity</option>
                            </select>
                        </td>
                        <td>
                            <input type="text"
                                   class="form-control"
                                   name="deliverables[${idx}][task]"
                                   required>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm add-row"
                                    title="Add deliverable row">
                                <i class="bi bi-plus"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm remove-row"
                                    title="Remove row">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }

            // Populate activities select based on selected project
            function populateActivities($projectSelect, $activitiesSelect, selectedActivityId = null) {
                const activitiesData = $projectSelect.find('option:selected').data('activities');
                $activitiesSelect.empty();
                $activitiesSelect.append('<option value="">Select Activity</option>');
                if (activitiesData && Array.isArray(activitiesData)) {
                    activitiesData.forEach(function(activity) {
                        const selected = selectedActivityId && String(activity.id) === String(
                            selectedActivityId) ? 'selected' : '';
                        $activitiesSelect.append(
                            `<option value="${activity.id}" ${selected}>${activity.name || activity.title || activity.activity_name}</option>`
                        );
                    });
                }
                // Notify Select2 of updates
                $activitiesSelect.trigger('change');
            }

            // On project change, update activities select
            $(document).on('change', '.project-select', function() {
                const $row = $(this).closest('tr');
                const $activitiesSelect = $row.find('.activities-select');
                populateActivities($(this), $activitiesSelect);
            });

            // On page load, initialize activities selects for existing rows
            $('#deliverables-body .deliverable-row').each(function() {
                const $row = $(this);
                const $projectSelect = $row.find('.project-select');
                const $activitiesSelect = $row.find('.activities-select');
                // If old value exists, set it
                const selectedActivityId = $activitiesSelect.data('selected');
                populateActivities($projectSelect, $activitiesSelect, selectedActivityId);

                // Initialize datepicker for existing date input
                const $dateInput = $row.find('input.date');
                $dateInput.datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: $('[name="start_date"]').val() || new Date(),
                    endDate: $('[name="end_date"]').val() || null,
                    enableOnReadonly: true
                });
                $dateInput.prop('disabled', !($('[name="start_date"]').val() && $('[name="end_date"]')
                    .val()));
            });

            $(document).on('click', '.add-row', function() {
                rowIndex++;
                const $newRow = $(buildDeliverableRow(rowIndex));
                $tbody.append($newRow);

                // Initialize select2 on new row
                $newRow.find('.project-select, .activities-select').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                });

                // Initialize datepicker on new row
                $newRow.find('input.date').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: $('[name="start_date"]').val() || new Date(),
                    endDate: $('[name="end_date"]').val() || null,
                    enableOnReadonly: true
                });
                $newRow.find('input.date').prop('disabled', !($('[name="start_date"]').val() && $(
                    '[name="end_date"]').val()));

                if (window.fv) {
                    fv.revalidateField('deliverables');
                }
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();

                if (window.fv) {
                    fv.revalidateField('deliverables');
                }
            });

            if (form) {
                window.fv = FormValidation.formValidation(form, {
                    fields: {
                        type: {
                            validators: {
                                notEmpty: {
                                    message: 'The type is required'
                                }
                            }
                        },
                        start_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The start date is required'
                                }
                            }
                        },
                        end_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The end date is required'
                                }
                            }
                        },
                        reason: {
                            validators: {
                                notEmpty: {
                                    message: 'Reason is required'
                                }
                            }
                        },
                        send_to: {
                            validators: {
                                notEmpty: {
                                    message: 'The approver is required'
                                }
                            }
                        },
                        deliverables: {
                            validators: {
                                callback: {
                                    message: 'Project, activity and task are required for each deliverable',
                                    callback: function() {
                                        const projectSelects = $(
                                            '#deliverables-body select[name*="[project_id]"]');
                                        const activitySelects = $(
                                            '#deliverables-body select[name*="[activity_id]"]');
                                        const taskInputs = $(
                                            '#deliverables-body input[name*="[task]"]');

                                        const allProjectsFilled = projectSelects.length > 0 &&
                                            projectSelects.filter(function() {
                                                return $(this).val() && $(this).val() !== '';
                                            }).length === projectSelects.length;

                                        const allActivitiesFilled = activitySelects.length > 0 &&
                                            activitySelects.filter(function() {
                                                return $(this).val() && $(this).val() !== '';
                                            }).length === activitySelects.length;

                                        const allTasksFilled = taskInputs.length > 0 &&
                                            taskInputs.filter(function() {
                                                return $(this).val().trim() !== '';
                                            }).length === taskInputs.length;

                                        return allProjectsFilled && allActivitiesFilled &&
                                            allTasksFilled;
                                    }
                                }
                            }
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.mb-3',
                            eleInvalidClass: 'is-invalid',
                            eleValidClass: 'is-valid',
                        }),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                        startEndDate: new FormValidation.plugins.StartEndDate({
                            format: 'YYYY-MM-DD',
                            startDate: {
                                field: 'start_date',
                                message: 'Start date must be earlier than end date.'
                            },
                            endDate: {
                                field: 'end_date',
                                message: 'End date must be later than start date.'
                            },
                        }),
                    },
                });
            }

            $(form).on('change', '#send_to, #type', function() {
                fv.revalidateField($(this).attr('id'));
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
                            <a href="{{ route('wfh.requests.index') }}" class="text-decoration-none text-dark">
                                Work From Home Requests
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Edit Work From Home Request
                </h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded">
        <div class="card-body">
            <form action="{{ route('wfh.requests.update', $workFromHome->id) }}" id="wfhRequestEditForm" method="POST"
                autocomplete="off">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="mb-3 col-2">
                        <label for="type" class="form-label required-label">Type</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                            required>
                            <option value="">Select Type</option>
                            @foreach ($typeOptions as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('type', $workFromHome->type) == $val ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-4">
                        <label for="start_date" class="form-label required-label">Start Date</label>
                        <input type="text" readonly class="form-control @error('start_date') is-invalid @enderror"
                            id="start_date" name="start_date"
                            value="{{ old('start_date', $workFromHome->start_date->format('Y-m-d')) }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-4">
                        <label for="end_date" class="form-label required-label">End Date</label>
                        <input type="text" readonly class="form-control @error('end_date') is-invalid @enderror"
                            id="end_date" name="end_date"
                            value="{{ old('end_date', $workFromHome->end_date->format('Y-m-d')) }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label required-label">Reason</label>
                    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3"
                        required>{{ old('reason', $workFromHome->reason) }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label required-label">Deliverables</label>

                    <table class="table table-bordered" id="deliverables-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Date</th>
                                <th style="width: 15%;">Project</th>
                                <th style="width:15%">Activities</th>
                                <th>Task</th>
                                <th style="width: 12%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="deliverables-body">
                            @php
                                $dbDeliverables = $workFromHome->deliverables ?? [];

                                // Ensure dbDeliverables is cast to array if it is a collection, or handled as array
                                // old() expects array structure for the second argument usually if it's data
// But here we are iterating.
// If $dbDeliverables is a collection of models, accessing as array works (Model implements ArrayAccess)
// But let's be safe.

                                $oldDeliverables = old(
                                    'deliverables',
                                    $dbDeliverables ?: [['project_id' => null, 'activity_id' => null, 'task' => null]],
                                );
                            @endphp

                            @foreach ($oldDeliverables as $idx => $deliverable)
                                @php
                                    $dateErrorKey = "deliverables.$idx.date";
                                    $projectErrorKey = "deliverables.$idx.project_id";
                                    $activityErrorKey = "deliverables.$idx.activity_id";
                                    $taskErrorKey = "deliverables.$idx.task";
                                    $selectedProject = $projects[$deliverable['project_id'] ?? ''] ?? null;
                                    $activities = $selectedProject ? $selectedProject->activities : [];
                                @endphp
                                <tr class="deliverable-row" data-row-index="{{ $idx }}">
                                    <td style="width: 10%;">
                                        <input type="text"
                                            class="form-control date @error($dateErrorKey) is-invalid @enderror" readonly
                                            name="deliverables[{{ $idx }}][date]"
                                            value="{{ $deliverable['date'] ?? '' }}">
                                        @error($dateErrorKey)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td style="width: 15%;">
                                        <select
                                            class="form-select project-select @error($projectErrorKey) is-invalid @enderror"
                                            name="deliverables[{{ $idx }}][project_id]" required>
                                            <option value="" disabled
                                                {{ empty($deliverable['project_id']) ? 'selected' : '' }}>Select Project
                                            </option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}"
                                                    data-activities='@json($project->activities)'
                                                    {{ (string) $project->id === (string) ($deliverable['project_id'] ?? '') ? 'selected' : '' }}>
                                                    {{ $project->short_name ?: $project->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error($projectErrorKey)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <select
                                            class="form-select activities-select @error($activityErrorKey) is-invalid @enderror"
                                            name="deliverables[{{ $idx }}][activity_id]"
                                            data-selected="{{ $deliverable['activity_id'] ?? '' }}" required>
                                            <option value="">Select Activity</option>
                                            @if (!empty($activities))
                                                @foreach ($activities as $activity)
                                                    <option value="{{ $activity['id'] ?? $activity->id }}"
                                                        {{ (string) ($activity['id'] ?? $activity->id) === (string) ($deliverable['activity_id'] ?? '') ? 'selected' : '' }}>
                                                        {{ $activity['name'] ?? ($activity['title'] ?? ($activity['activity_name'] ?? '')) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error($activityErrorKey)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control @error($taskErrorKey) is-invalid @enderror"
                                            name="deliverables[{{ $idx }}][task]"
                                            value="{{ $deliverable['task'] ?? '' }}" required>
                                        @error($taskErrorKey)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        @if ($loop->first)
                                            <button type="button" class="btn btn-outline-primary btn-sm add-row"
                                                title="Add deliverable row">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-primary btn-sm add-row"
                                                title="Add deliverable row">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row"
                                                title="Remove row">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <span class="text-muted text-sm">
                        Only date between start and end date can be selected.
                    </span>
                </div>

                <div class="mb-3">
                    <label for="send_to" class="form-label required-label">{{ __('label.approval') }}</label>
                    <select class="form-control @error('send_to') is-invalid @enderror" id="send_to" name="send_to"
                        required>
                        <option value="">Select Approver</option>
                        @foreach ($supervisors as $id => $fullName)
                            <option value="{{ $id }}" @selected(old('send_to', $workFromHome->approver_id) == $id)>
                                {{ $fullName }}
                            </option>
                        @endforeach
                    </select>
                    @error('send_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="gap-2 border-0 card-footer justify-content-end d-flex wfh-form-actions">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save</button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">Submit</button>
                    <a href="{{ route('wfh.requests.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
