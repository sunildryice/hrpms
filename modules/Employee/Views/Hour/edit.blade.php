@section('page_css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-time .flatpickr-am-pm {
            width: 50px;
            padding: 0 8px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: #393939;
        }

        .flatpickr-time .flatpickr-am-pm:hover {
            background: #eee;
        }
    </style>
@endsection

@section('page_script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

<div class="card-header fw-bold">Edit Working Hour</div>
<form class="needs-validation" action="{{ route('employees.hours.update', [$hour->employee_id, $hour->id]) }}"
    method="post" id="hourEditForm" enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="start_date" class="form-label required-label">Start Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('start_date')) is-invalid @endif"
                    name="start_date"
                    value="{{ old('start_date', $hour->start_date ? $hour->start_date->format('Y-m-d') : '') }}"
                    readonly />
                @if ($errors->has('start_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="start_date">{!! $errors->first('start_date') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="end_date" class="form-label required-label">End Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('end_date')) is-invalid @endif"
                    name="end_date"
                    value="{{ old('end_date', $hour->end_date ? $hour->end_date->format('Y-m-d') : '') }}" readonly />
                @if ($errors->has('end_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="end_date">{!! $errors->first('end_date') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="start_time" class="form-label required-label">Start Time</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('start_time')) is-invalid @endif"
                    id="start_time_input" name="start_time" value="{{ old('start_time', $hour->start_time) }}"
                    placeholder="HH:MM AM/PM" />
                @if ($errors->has('start_time'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="start_time">{!! $errors->first('start_time') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="end_time" class="form-label required-label">End Time</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('end_time')) is-invalid @endif"
                    id="end_time_input" name="end_time" value="{{ old('end_time', $hour->end_time) }}"
                    placeholder="HH:MM AM/PM" />
                @if ($errors->has('end_time'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="end_time">{!! $errors->first('end_time') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="remarks" class="form-control" placeholder="Remarks">{!! old('remarks', $hour->remarks) !!}</textarea>
            </div>
        </div>

    </div>
    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
        <button type="submit" class="btn btn-primary btn-sm">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Initialize jQuery datepicker for dates
            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function() {
                if (typeof fv !== 'undefined') {
                    fv.revalidateField('start_date');
                    fv.revalidateField('end_date');
                }
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function() {
                if (typeof fv !== 'undefined') {
                    fv.revalidateField('end_date');
                    fv.revalidateField('start_date');
                }
            });

            // Convert 24-hour time to 12-hour format for flatpickr default value
            function convertTo12Hour(time24) {
                if (!time24) return '';

                // If already in 12-hour format, return as is
                if (time24.includes('AM') || time24.includes('PM')) {
                    return time24;
                }

                // Parse 24-hour time (e.g., "09:00:00" or "09:00")
                const timeParts = time24.split(':');
                let hours = parseInt(timeParts[0]);
                const minutes = timeParts[1];

                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // 0 should be 12

                return hours + ':' + minutes + ' ' + ampm;
            }

            // Initialize flatpickr time pickers with 12-hour format
            if (typeof flatpickr !== 'undefined') {
                const timeOptions = {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "h:i K",
                    time_24hr: false,
                    minuteIncrement: 1,
                    allowInput: false,
                    clickOpens: true,
                };

                const startTimeEl = document.getElementById('start_time_input');
                const endTimeEl = document.getElementById('end_time_input');

                if (startTimeEl) {
                    // Get current value and convert if needed
                    const startTimeValue = startTimeEl.value;
                    const convertedStartTime = convertTo12Hour(startTimeValue);

                    // Set converted value
                    startTimeEl.value = convertedStartTime;

                    // Initialize flatpickr
                    flatpickr(startTimeEl, {
                        ...timeOptions,
                        defaultDate: convertedStartTime
                    });
                }

                if (endTimeEl) {
                    // Get current value and convert if needed
                    const endTimeValue = endTimeEl.value;
                    const convertedEndTime = convertTo12Hour(endTimeValue);

                    // Set converted value
                    endTimeEl.value = convertedEndTime;

                    // Initialize flatpickr
                    flatpickr(endTimeEl, {
                        ...timeOptions,
                        defaultDate: convertedEndTime
                    });
                }
            }

            // Form validation
            const form = document.getElementById('hourEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'Start date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'End date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    start_time: {
                        validators: {
                            notEmpty: {
                                message: 'Start time is required',
                            },
                        },
                    },
                    end_time: {
                        validators: {
                            notEmpty: {
                                message: 'End time is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'start_date',
                            message: 'Start date must be a valid date and earlier than end date.',
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'End date must be a valid date and later than start date.',
                        },
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
        });
    </script>
@endpush
