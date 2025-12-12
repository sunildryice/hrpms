@extends('layouts.container')

@section('title', 'Update Off Day Work Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#off-day-work-requests-index').addClass('active');

            $('#project_id, #send_to').addClass('select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

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

            // initial month load
            (function initFirstMonth() {
                fetchHolidays();
            })();

            const $input = $('[name="date"]');

            // use your existing jQuery datepicker plugin
            $input.datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                filter: holidayFilter,
            });

            // normal change for validation
            $input.on('change', function() {
                if (window.fv) {
                    fv.revalidateField('date');
                }
            });


            const form = document.getElementById('offDayWorkEditForm');

            $('[name="date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function() {
                if (window.fv) {
                    fv.revalidateField('date');
                    fv.revalidateField('end_date');
                }
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function() {
                if (window.fv) {
                    fv.revalidateField('date');
                    fv.revalidateField('end_date');
                }
            });

            function updateRemoveButtons() {
                const $rows = $('#deliverables-table tbody tr');
                $rows.each(function(index) {
                    const $btn = $(this).find('.remove-row');
                    if ($rows.length > 1 && index > 0) {
                        $btn.removeClass('d-none');
                    } else {
                        $btn.addClass('d-none');
                    }
                });
            }

            $(document).off('click', '#add-task').on('click', '#add-task', function() {
                const newRow = `
                <tr>
                    <td><input type="text" class="form-control" name="deliverables[]" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                </tr>`;
                $('#deliverables-table tbody').append(newRow);
                if (window.fv) {
                    fv.revalidateField('deliverables[]');
                }
                updateRemoveButtons();
            });

            $(document).off('click', '.remove-row').on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                if (window.fv) {
                    fv.revalidateField('deliverables[]');
                }
                updateRemoveButtons();
            });

            if (form) {
                window.fv = FormValidation.formValidation(form, {
                    fields: {
                        project_id: {
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
                        'deliverables[]': {
                            validators: {
                                notEmpty: {
                                    message: 'Deliverable task is required'
                                },
                                callback: {
                                    message: 'Add at least one deliverable',
                                    callback: function() {
                                        const items = $(
                                            '#deliverables-table tbody input[name="deliverables[]"]'
                                        );
                                        const filledItems = items.filter(function() {
                                            return $(this).val().trim() !== '';
                                        });
                                        return filledItems.length > 0;
                                    }
                                }
                            }
                        }
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
                                field: 'date',
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

            // Revalidate selects on change
            $(form).on('change', '#project_id', function() {
                fv.revalidateField('project_id');
            });
            $(form).on('change', '#send_to', function() {
                fv.revalidateField('send_to');
            });

            // Initialize remove buttons state on load
            updateRemoveButtons();
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
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Edit Off Day Work Request
                </h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded">
        <div class="card-body">
            <form action="{{ route('off.day.work.update', $offDayWork->id) }}" id="offDayWorkEditForm" method="POST"
                autocomplete="off">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="mb-3 col-8">
                        <label for="project_id" class="form-label required-label">Project</label>
                        <select class="form-control" id="project_id" name="project_id" required>
                            <option value="">Select Project</option>
                            @foreach ($projects as $id => $title)
                                <option value="{{ $id }}" @if ($offDayWork->project_id == $id) selected @endif>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-4">
                        <label for="date" class="form-label required-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" readonly
                            value="{{ $offDayWork->date->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label required-label">Reason</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ $offDayWork->reason_for_work ?? $offDayWork->reason }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="deliverables" class="form-label required-label">Deliverables</label>
                    <table class="table table-bordered" id="deliverables-table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $items = is_array($offDayWork->deliverables)
                                    ? $offDayWork->deliverables
                                    : (json_decode($offDayWork->deliverables, true) ?:
                                    []);
                            @endphp

                            @if (empty($items))
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="deliverables[]" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row d-none">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            @else
                                @foreach ($items as $idx => $task)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" name="deliverables[]"
                                                value="{{ $task }}" required>
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-danger btn-sm remove-row @if ($loop->first) d-none @endif">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" id="add-task">Add Task</button>
                </div>

                <div class="mb-3">
                    <label for="send_to" class="form-label required-label">Send To</label>
                    <select class="form-control" id="send_to" name="send_to" required>
                        <option value="">Select Approver</option>
                        @foreach ($supervisors as $id => $fullName)
                            <option value="{{ $id }}" @if ($offDayWork->approver_id == $id) selected @endif>
                                {{ $fullName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="gap-2 border-0 card-footer justify-content-end d-flex off-day-work-form-actions">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update</button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">Submit</button>
                    <a href="{{ route('off.day.work.index') }}" class="btn btn-danger btn-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
