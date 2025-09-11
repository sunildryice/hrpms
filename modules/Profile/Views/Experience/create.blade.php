<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Add New Experience</h3>
</div>
<form class="g-3 needs-validation" action="{{ route('profile.experiences.store') }}" method="post"
      id="experienceAddForm" enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Institution </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" autofocus class="form-control @if($errors->has('institution')) is-invalid @endif" name="institution" placeholder="Institution name"
                       value="{{ old('institution') }}" />
                @if($errors->has('institution'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="institution">{!! $errors->first('institution') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Position </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if($errors->has('position')) is-invalid @endif" name="position" placeholder="Experience topic"
                       value="{{ old('position') }}">
                @if($errors->has('position'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="position">{!! $errors->first('position') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label required-label">Period From </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if($errors->has('period_from')) is-invalid @endif"
                       name="period_from" placeholder="Period From" value="{{ old('period_from') }}" readonly>
                @if($errors->has('period_from'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="period_from">{!! $errors->first('period_from') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdob" class="form-label required-label">Period To </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if($errors->has('period_to')) is-invalid @endif"
                       name="period_to" placeholder="Period To" value="{{ old('period_to') }}" readonly>
                @if($errors->has('period_to'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="period_to">{!! $errors->first('period_to') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="remarks" class="form-control" placeholder="Remarks">{!! old('remarks') !!}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationcitizenship" class="m-0">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control @if($errors->has('attachment')) is-invalid @endif" name="attachment"/>
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                @if($errors->has('attachment'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                    </div>
                @endif
{{--                <p>Only JPEG, PNG and PDF files are allowed.</p>--}}
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
            const form = document.getElementById('experienceAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    institution: {
                        validators: {
                            notEmpty: {
                                message: 'Institution is required',
                            },
                        },
                    },
                    position: {
                        validators: {
                            notEmpty: {
                                message: 'Position is required',
                            },
                        },
                    },
                    period_from: {
                        validators: {
                            notEmpty: {
                                message: 'From date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    period_to: {
                        validators: {
                            notEmpty: {
                                message: 'To date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: 2097152, // 2048 * 1024
                                message: 'The selected file is not valid',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    excluded: new FormValidation.plugins.Excluded(),
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
                            field: 'period_from',
                            message: 'From date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'period_to',
                            message: 'To date must be a valid date and later than from date.',
                        },
                    }),
                },
            });

            $('[name="period_from"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function (e) {
                fv.revalidateField('period_from');
                fv.revalidateField('period_to');
            });

            $('[name="period_to"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function (e) {
                fv.revalidateField('period_from');
                fv.revalidateField('period_to');
            });
        });
    </script>
@endpush
