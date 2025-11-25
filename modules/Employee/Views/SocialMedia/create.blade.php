<div class="card-header fw-bold">Add New Social Media</div>
<form class="needs-validation" method="post" id="trainingAddForm" enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label">Bio</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="bio" class="form-control" placeholder="Bio">{!! old('bio') !!}</textarea>
            </div>
            @if ($errors->has('bio'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="bio">{!! $errors->first('bio') !!}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="Fdname" class="form-label required-label">Training Topic</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control @if ($errors->has('training_topic')) is-invalid @endif"
                name="training_topic" placeholder="Training topic" value="{{ old('training_topic') }}">
            @if ($errors->has('training_topic'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="training_topic">{!! $errors->first('training_topic') !!}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="validationdob" class="form-label required-label">Period From</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control @if ($errors->has('period_from')) is-invalid @endif"
                name="period_from" placeholder="Period From" value="{{ old('period_from') }}" readonly>
            @if ($errors->has('period_from'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="period_from">{!! $errors->first('period_from') !!}</div>
                </div>
            @endif
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="validationdob" class="form-label required-label">Period To</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control @if ($errors->has('period_to')) is-invalid @endif"
                readOnly:true, name="period_to" placeholder="Period To" value="{{ old('period_to') }}" readonly>
            @if ($errors->has('period_to'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="period_to">{!! $errors->first('period_to') !!}</div>
                </div>
            @endif
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="Fdname" class="form-label">Remarks</label>
            </div>
        </div>
        <div class="col-lg-9">
            <textarea name="remarks" class="form-control" placeholder="Remarks">{!! old('remarks') !!}</textarea>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="validationcitizenship" class="form-label">Attachment</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="file" class="form-control @if ($errors->has('attachment')) is-invalid @endif"
                name="attachment" />
            <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
            @if ($errors->has('attachment'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                </div>
            @endif
            {{--                <p>Only JPEG, PNG and PDF files are allowed.</p> --}}
        </div>
    </div>

    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
@push('scripts')
    <script type="text/javascript">
        var end_date = "{!! date('Y-m-d') !!}";
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('trainingAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    institution: {
                        validators: {
                            notEmpty: {
                                message: 'Institution is required',
                            },
                        },
                    },
                    training_topic: {
                        validators: {
                            notEmpty: {
                                message: 'Training topic is required',
                            },
                        },
                    },
                    period_from: {
                        validators: {
                            notEmpty: {
                                message: 'Training from date is required',
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
                                message: 'Training to date is required',
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
                                message: 'The selected file is not valid image or pdf or must not be greater than 2 MB.',
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
                startDate: '2022-04-02',
                endDate: end_date,
            }).on('change', function(e) {
                var start_date = $(this).val();
                $('[name="period_to"]').datepicker("option", "startDate", start_date);
                fv.revalidateField('period_from');
                fv.revalidateField('period_to');
            });

            $('[name="period_to"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: end_date,
            }).on('change', function(e) {
                fv.revalidateField('period_from');
                fv.revalidateField('period_to');
            });
        });
    </script>
@endpush
