<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Add Documents</h3>
</div>
<form class="g-3 needs-validation" action="{{ route('profile.document.store') }}" method="post" id="documentForm"
    enctype="multipart/form-data" autocomplete="off">
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdocument" class="m-0">Signature</label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="file" id="validationsignature" placeholder="" name="signature"
                    class="form-control js-document-upload @if ($errors->has('signature')) is-invalid @endif">
                <small>Supported file types jpeg/jpg/png and file size of upto 2MB.</small>
                @if ($errors->has('signature'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="signature">{!! $errors->first('signature') !!}</div>
                    </div>
                @endif
                @if (file_exists('storage/' . $employee->signature) && $employee->signature != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $employee->signature) !!}" target="_blank" class="fs-5" title="View Attachment">
                            <i class="bi bi-file-earmark-medical"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdocument" class="m-0">Profile Picture</label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="file" id="validationprofilepicture" name="profile_picture"
                    class="form-control js-document-upload @if ($errors->has('profile_picture')) is-invalid @endif">
                <small>Supported file types jpeg/jpg/png and file size of upto 2MB.</small>
                @if ($errors->has('profile_picture'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="profile_picture">{!! $errors->first('profile_picture') !!}</div>
                    </div>
                @endif
                @if (file_exists('storage/' . $employee->profile_picture) && $employee->profile_picture != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $employee->profile_picture) !!}" target="_blank" class="fs-5" title="View Attachment">
                            <i class="bi bi-file-earmark-medical"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdocument" class="form-label">CV</label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="file"
                    class="form-control js-document-upload @if ($errors->has('cv_attachment')) is-invalid @endif"
                    id="validationprofilepicture" value="{{ old('cv_attachment') }}" placeholder=""
                    name="cv_attachment">
                <small>Supported file type pdf only and file size of upto 2MB.</small>
                @if ($errors->has('cv_attachment'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="cv_attachment">{!! $errors->first('cv_attachment') !!}</div>
                    </div>
                @endif
                @if (file_exists('storage/' . $employee->cv_attachment) && $employee->cv_attachment != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $employee->cv_attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
                            <i class="bi bi-file-earmark-medical"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button class="btn btn-primary btn-sm" type="submit">
            @if ($employee->signature != null || $employee->profile_picture)
                Update
            @else
                Save
            @endif
        </button>
    </div>
</form>
@push('scripts')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('documentFormM');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    upload: {
                        selector: '.js-document-upload',
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png',
                                type: 'image/jpeg,image/png',
                                maxSize: '2097152',
                                message: 'The selected file is not valid image or must not be greater than 2 MB.',
                            },

                            callback: {
                                message: 'You must upload at least one file.',
                                callback: function(input) {
                                    let isEmpty = true;
                                    const uploadElements = fv.getElements('upload');
                                    for (const i in uploadElements) {
                                        if (uploadElements[i].value !== '') {
                                            isEmpty = false;
                                            break;
                                        }
                                    }

                                    if (!isEmpty) {
                                        fv.updateFieldStatus('upload', 'Valid', 'callback');
                                        return true;
                                    }

                                    return false;
                                }
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
                },
            });
        });
    </script>
@endpush
