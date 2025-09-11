<div class="card-header fw-bold">Add New Working Hour</div>
<form class="needs-validation" action="{{ route('employees.hours.store', $employee) }}" method="post"
      id="hourAddForm" enctype="multipart/form-data" autocomplete="off">
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
                    value="{{ old('start_date') }}"
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
                    value="{{ old('end_date') }}"
                    readonly />
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
                    <label for="validationinstitution" class="form-label required-label">Work Percentile</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control @if ($errors->has('work_percentile')) is-invalid @endif"
                    id="validationinstitution" value="{{ old('work_percentile') }}"
                    placeholder="" name="work_percentile" autofocus>
                @if ($errors->has('work_percentile'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="work_percentile">{!! $errors->first('work_percentile') !!}</div>
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
        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('hourAddForm');
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
                    work_percentile: {
                        validators: {
                            notEmpty: {
                                message: 'Work percentile is required',
                            },
                            numeric: {
                                message: 'Please enter a valid value',
                            },
                            lessThan: {
                                max: 100,
                                message: 'Work percentile must be less than or equal to 100',
                            }
                        },
                    },
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
                            message: 'Start date must be a valid date and earlier than end date.',
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'End date must be a valid date and later than start date.',
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                //endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                //endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('end_date');
                fv.revalidateField('start_date');
            });
        });
    </script>
@endpush
