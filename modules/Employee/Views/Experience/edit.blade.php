<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Edit Experience <span class="experience_topic"></span></h3>
</div>
<form class="g-3 needs-validation" action="{{ route('employees.experiences.update', ['employee','experience']) }}"
      method="post" id="experienceEditForm" enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Institution</label>
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
                    <label for="Fdname" class="m-0">Remark</label>
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
                <div class="media" style="display:none;">
                    <a href="#" target="_blank"
                        class="" title="View Attachment">
                        Attachment already exists.
                    </a>
                </div>
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
        <button type="submit" class="btn btn-primary btn-sm">Update</button>
        <a onclick="cancelExperienceEditForm(this)" class="btn btn-danger btn-sm">Cancel</a>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>


<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function (e) {
        const form = document.getElementById('experienceEditForm');
        const fv = FormValidation.formValidation(form, {
            fields: {
                institution: {
                    validators: {
                        notEmpty: {
                            message: 'Institution is required',
                        },
                    },
                },
                experience_topic: {
                    validators: {
                        notEmpty: {
                            message: 'Experience topic is required',
                        },
                    },
                },
                period_from: {
                    validators: {
                        notEmpty: {
                            message: 'Experience from date is required',
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
                            message: 'Experience to date is required',
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
