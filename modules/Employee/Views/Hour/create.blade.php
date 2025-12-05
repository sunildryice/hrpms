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


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="card-header fw-bold">Add New Working Hour</div>
<form class="needs-validation" action="{{ route('employees.hours.store', $employee) }}" method="post" id="hourAddForm"
    enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="start_date" class="form-label required-label">Start Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('start_date')) is-invalid @endif"
                    name="start_date" value="{{ old('start_date') }}" readonly />
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
                    name="end_date" value="{{ old('end_date') }}" readonly />
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
                <input type="time" class="form-control @if ($errors->has('start_time')) is-invalid @endif"
                    name="start_time" value="{{ old('start_time') }}" />
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
                <input type="time" class="form-control @if ($errors->has('end_time')) is-invalid @endif"
                    name="end_time" value="{{ old('end_time') }}" />
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
                <textarea name="remarks" class="form-control" placeholder="Remarks">{!! old('remarks') !!}</textarea>
            </div>
        </div>

    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
    </div>
    {!! csrf_field() !!}
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Initialize jQuery datepicker for start_date and end_date
            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function(e) {
                if (typeof fv !== 'undefined') {
                    fv.revalidateField('start_date');
                    fv.revalidateField('end_date');
                }
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function(e) {
                if (typeof fv !== 'undefined') {
                    fv.revalidateField('end_date');
                    fv.revalidateField('start_date');
                }
            });

            if (typeof flatpickr !== 'undefined') {
                const timeOptions = {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "h:i K",
                    minuteIncrement: 1,
                    allowInput: false,
                    clickOpens: true,
                    enableTime: true,
                    time_24hr: false,
                };

                const startTimeEl = document.querySelector("input[name='start_time']");
                const endTimeEl = document.querySelector("input[name='end_time']");

                if (startTimeEl) {
                    flatpickr(startTimeEl, timeOptions);
                }

                if (endTimeEl) {
                    flatpickr(endTimeEl, timeOptions);
                }
            }

            // Form validation setup
            const form = document.getElementById('hourAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'Start date is required'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date'
                            }
                        }
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'End date is required'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date'
                            }
                        }
                    },
                    start_time: {
                        validators: {
                            notEmpty: {
                                message: 'Start time is required'
                            }
                        }
                    },
                    end_time: {
                        validators: {
                            notEmpty: {
                                message: 'End time is required'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'start_date',
                            message: 'Start date must be a valid date and earlier than end date.'
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'End date must be a valid date and later than start date.'
                        }
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat'
                    })
                }
            });
        });
    </script>
@endpush
