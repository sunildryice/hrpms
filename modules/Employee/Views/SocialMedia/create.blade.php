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
                <label for="linkedin" class="form-label">Linkedin</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control @if ($errors->has('linkedin')) is-invalid @endif"
                name="linkedin" placeholder="Linkedin URL" value="{{ old('linkedin') }}">
            @if ($errors->has('linkedin'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="linkedin">{!! $errors->first('linkedin') !!}</div>
                </div>
            @endif
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
                    linkedin: {
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
