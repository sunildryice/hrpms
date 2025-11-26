<div class="card-header fw-bold">Add Documents</div>
<form class="needs-validation" action="{{ route('employees.document.store', $employee->id) }}" method="post"
    id="documentForm" enctype="multipart/form-data" autocomplete="off">
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdocument" class="form-label">Signature</label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="file"
                    class="form-control js-document-upload @if ($errors->has('signature')) is-invalid @endif"
                    id="validationsignature" value="{{ old('signature') }}" placeholder="" name="signature">
                <small>Supported file types jpeg/jpg/png and file size of upto 2MB.</small>
                @if ($errors->has('signature'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="signature">{!! $errors->first('signature') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdocument" class="form-label">Profile Picture</label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="file"
                    class="form-control js-document-upload @if ($errors->has('profile_picture')) is-invalid @endif"
                    id="validationprofilepicture" value="{{ old('profile_picture') }}" placeholder=""
                    name="profile_picture">
                <small>Supported file types jpeg/jpg/png and file size of upto 2MB.</small>
                @if ($errors->has('profile_picture'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="profile_picture">{!! $errors->first('profile_picture') !!}</div>
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
        {{-- <button class="btn btn-success btn-sm">Update</button> --}}
        {{-- <button class="btn btn-danger btn-sm" type="reset" value="reset">Reset</button> --}}
    </div>
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('documentForm');

            FormValidation.formValidation(form, {
                fields: {
                    signature: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png',
                                type: 'image/jpeg,image/jpg,image/png',
                                maxSize: 2097152,
                                message: 'Signature must be a JPEG/JPG/PNG file and less than 2MB.'
                            }
                        }
                    },
                    profile_picture: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png',
                                type: 'image/jpeg,image/jpg,image/png',
                                maxSize: 2097152,
                                message: 'Profile picture must be a JPEG/JPG/PNG file and less than 2MB.'
                            }
                        }
                    },
                    cv_attachment: {
                        validators: {
                            file: {
                                extension: 'pdf',
                                type: 'application/pdf',
                                maxSize: 2097152,
                                message: 'CV must be a PDF file and less than 2MB.'
                            }
                        }
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.row'
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat'
                    }),
                },
            });
        });
    </script>
@endpush
