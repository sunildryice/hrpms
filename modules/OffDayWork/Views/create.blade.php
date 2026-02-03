@extends('layouts.container')

@section('title', 'Create Off Day Work Request')

@section('page_css')
    <style>
        #deliverables-table th,
        #deliverables-table td {
            border-color: #dee2e6;
        }

        .deliverable-row .btn {
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
            $('#navbarVerticalMenu').find('#off-day-work-index').addClass('active');

            let enabledDates = [];
            let holidayTitles = {};

            function fetchHolidays() {
                const url = "{{ route('api.offday.work.holidays.index') }}";

                const successCallback = function(response) {
                    enabledDates = response.enabled_dates || [];
                    holidayTitles = response.holiday_titles || {};
                };

                const errorCallback = function(error) {
                    console.error(error);
                };

                ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
            }

            function formatDateObj(date) {
                const d = new Date(date);
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            function holidayFilter(date) {
                const formatted = formatDateObj(date);
                return enabledDates.includes(formatted);
            }

            (function initFirstMonth() {
                fetchHolidays();
            })();

            const $dateInput = $('[name="date"]');
            $dateInput.datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                filter: holidayFilter,
            });

            $dateInput.on('change', function() {
                if (window.fv) {
                    fv.revalidateField('date');
                }
            });

            $('#send_to').addClass('select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            // Initialize select2 for project and activities
            $('.project-select, .activities-select').select2({
                width: '100%',
                dropdownAutoWidth: true,
            });

            const form = document.getElementById('offDayWorkRequestAddForm');
            const $tbody = $('#deliverables-body');

            // Get last index from existing rows so JS continues from there
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
                        'deliverables': {
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
                        send_to: {
                            validators: {
                                notEmpty: {
                                    message: 'The approver is required'
                                }
                            }
                        },
                        date: {
                            validators: {
                                notEmpty: {
                                    message: 'The Off Day Work Date is required'
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
                    },
                });
            }

            $(form).on('change', '#send_to', function() {
                fv.revalidateField('send_to');
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
                            <a href="{{ route('off.day.work.index') }}" class="text-decoration-none text-dark">
                                Off Day Work Requests
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Create Off Day Work Request
                </h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded">
        <div class="card-body">
            <form action="{{ route('off.day.work.store') }}" id="offDayWorkRequestAddForm" method="POST"
                autocomplete="off">
                @csrf

                <div class="row">
                    <div class="mb-3 col-4">
                        <label for="date" class="form-label required-label">Off Day Work Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ old('date') }}"
                            required>
                        <span class="text-sm text-muted">Only Holidays and Off Days are selectable</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label required-label">Reason for Off Day Work</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                </div>

                {{-- Deliverables --}}
                <div class="mb-3">
                    <label class="form-label required-label">Deliverables</label>
                    <table class="table table-bordered" id="deliverables-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Project</th>
                                <th style="width: 15%;">Activities</th>
                                <th>Task</th>
                                <th style="width: 12%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="deliverables-body">
                            @php
                                // old('deliverables') is an array: [idx => ['project_id' => ..., 'activity_id' => ..., 'task' => ...], ...]
                                $oldDeliverables = old('deliverables', [
                                    ['project_id' => null, 'activity_id' => null, 'task' => null],
                                ]);
                            @endphp

                            @foreach ($oldDeliverables as $idx => $deliverable)
                                @php
                                    $selectedProject = $projects->find($deliverable['project_id'] ?? '') ?? null;
                                    $activities = $selectedProject ? $selectedProject->activities : [];
                                @endphp
                                <tr class="deliverable-row" data-row-index="{{ $idx }}">
                                    <td style="width: 15%;">
                                        <select class="form-select project-select"
                                            name="deliverables[{{ $idx }}][project_id]" required>
                                            <option value="" disabled
                                                {{ empty($deliverable['project_id']) ? 'selected' : '' }}>
                                                Select Project
                                            </option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}"
                                                    data-activities='@json($project->activities)'
                                                    {{ (string) $project->id === (string) ($deliverable['project_id'] ?? '') ? 'selected' : '' }}>
                                                    {{ $project->short_name ?: $project->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select activities-select"
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
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            name="deliverables[{{ $idx }}][task]"
                                            value="{{ $deliverable['task'] ?? '' }}" required>
                                    </td>
                                    <td>
                                        @if ($loop->first)
                                            {{-- first row: only plus button --}}
                                            <button type="button" class="btn btn-outline-primary btn-sm add-row"
                                                title="Add deliverable row">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        @else
                                            {{-- subsequent rows: plus + trash --}}
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
                </div>

                <div class="mb-3">
                    <label for="send_to" class="form-label required-label">{{ __('label.approval') }}</label>
                    <select class="form-control" id="send_to" name="send_to" required>
                        <option value="">Select Approver</option>
                        @foreach ($supervisors as $id => $fullName)
                            <option value="{{ $id }}"
                                @if (old('send_to')) {{ old('send_to') == $id ? 'selected' : '' }}
                                @else
                                    {{ $supervisors->count() == 1 ? 'selected' : '' }} @endif>
                                {{ $fullName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="gap-2 border-0 card-footer justify-content-end d-flex off-day-work-form-actions">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save</button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">Submit</button>
                    <a href="{{ route('off.day.work.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
