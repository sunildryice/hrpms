@extends('layouts.container')

@section('title', 'Create Off Day Work Request')

@section('page_css')

    <link href="{{ asset('plugins/slim-select/dist/slimselect.css') }}" rel="stylesheet">
    <style>
        #deliverables-table td:first-child,
        #deliverables-table td:nth-child(2) {
            border-bottom: 1px solid #b2a6a6 !important;
        }

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

        .ss-value {
            background-color: var(--ohw-blue) !important;
        }

        .ss-option:hover {
            background-color: var(--ohw-blue) !important;
        }
    </style>
@endsection

@section('page_js')

    <script src="{{ asset('plugins/slim-select/dist/slimselect.min.js') }}"></script>
    <script>
        new SlimSelect({
            select: '#project_ids',
            placeholder: 'Select Project',
            closeOnSelect: false,
        });
    </script>
    </style>

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

            const form = document.getElementById('offDayWorkRequestAddForm');
            const $tbody = $('#deliverables-table tbody');

            $(document).ready(function() {
                $('#send_to').addClass('select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                });
            });

            function buildTaskItem(projectId, value = '') {
                return `
                    <div class="row task-item" data-project-id="${projectId}">
                        <div class="col-9">
                            <input type="text" class="form-control"
                                   name="deliverables[${projectId}][]"
                                   value="${value}" required>
                        </div>
                        <div class="col-3 d-flex justify-content-start gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-task" title="Remove task">
                                <i class="bi bi-trash"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm add-task-inline" title="Add task">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                `;
            }

            function buildProjectRow(projectId, projectName, tasks = []) {
                if (!tasks || tasks.length === 0) {
                    tasks = [''];
                }

                let tasksHtml = '';
                tasks.forEach(function(t) {
                    tasksHtml += buildTaskItem(projectId, t);
                });

                return `
                    <tr data-project-id="${projectId}">
                        <td class="align-middle text-truncate" style="max-width: 150px;">
                            ${projectName}
                        </td>
                        <td>
                            <div class="task-list" data-project-id="${projectId}">
                                ${tasksHtml}
                            </div>
                        </td>
                    </tr>
                `;
            }

            function refreshTaskButtons() {
                $('.task-list').each(function() {
                    const $items = $(this).find('.task-item');

                    $items.find('.add-task-inline').addClass('d-none');
                    $items.last().find('.add-task-inline').removeClass('d-none');

                    if ($items.length === 1) {
                        $items.find('.remove-task').addClass('d-none');
                    } else {
                        $items.find('.remove-task').removeClass('d-none');
                    }
                });
            }

            $('#project_ids').on('change', function() {
                const selectedIds = $(this).val() || [];

                $tbody.find('tr').each(function() {
                    const pid = String($(this).data('project-id'));
                    if (!selectedIds.includes(pid)) {
                        $(this).remove();
                    }
                });

                const existingIds = $tbody.find('tr').map(function() {
                    return String($(this).data('project-id'));
                }).get();

                $(this).find('option:selected').each(function() {
                    const projectId = $(this).val();
                    const projectName = $(this).text();
                    if (!existingIds.includes(projectId)) {
                        $tbody.append(buildProjectRow(projectId, projectName, []));
                    }
                });

                refreshTaskButtons();

                if (window.fv) {
                    fv.revalidateField('project_ids');
                    fv.revalidateField('deliverables');
                }
            });

            $(document).on('click', '.add-task-inline', function() {
                const $item = $(this).closest('.task-item');
                const projectId = $item.data('project-id');
                const $list = $('.task-list[data-project-id="' + projectId + '"]');

                $list.append(buildTaskItem(projectId, ''));
                refreshTaskButtons();

                if (window.fv) {
                    fv.revalidateField('deliverables');
                }
            });

            $(document).on('click', '.remove-task', function() {
                const $item = $(this).closest('.task-item');
                const projectId = $item.data('project-id');
                const $list = $('.task-list[data-project-id="' + projectId + '"]');

                $item.remove();

                if ($list.find('.task-item').length === 0) {
                    $list.append(buildTaskItem(projectId, ''));
                }

                refreshTaskButtons();

                if (window.fv) {
                    fv.revalidateField('deliverables');
                }
            });

            refreshTaskButtons();


            if (form) {
                window.fv = FormValidation.formValidation(form, {
                    fields: {
                        'project_ids[]': {
                            validators: {
                                notEmpty: {
                                    message: 'Project is required'
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
                                    message: 'The date is required'
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
                        deliverables: {
                            validators: {
                                callback: {
                                    message: 'Add at least one deliverable and fill all tasks',
                                    callback: function() {
                                        const items = $(
                                            '#deliverables-table tbody input[name^="deliverables"]');
                                        return items.length > 0 && items.filter(function() {
                                            return $(this).val().trim() !== '';
                                        }).length === items.length;
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
                    },
                });
            }

            $(form).on('change', '#project_ids', function() {
                fv.revalidateField('project_ids');
            });
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
                        <label for="date" class="form-label required-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ old('date') }}"
                            required>
                        <span class="text-sm text-muted">Only Holidays and Off Days are selectable</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label required-label">Reason for Off Day Work</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                </div>

                <div class="mb-3 col-12">
                    <label for="project_ids" class="form-label required-label">Projects</label>
                    <select id="project_ids" multiple name="project_ids[]" required>
                        <option value="" disabled>Select Project</option>
                        @foreach ($projects as $id => $title)
                            <option value="{{ $id }}" @if (in_array($id, old('project_ids', []))) selected @endif>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label required-label">Deliverables</label>
                    <table class="table table-bordered" id="deliverables-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Project</th>
                                <th>Task</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="mb-3">
                    <label for="send_to" class="form-label required-label">{{ __('label.approval') }}</label>
                    <select class="form-control" id="send_to" name="send_to" required>
                        <option value="">Select Approver</option>
                        @foreach ($supervisors as $id => $fullName)
                            <option value="{{ $id }}" @if ($supervisors->count() == 1) selected @endif>
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
