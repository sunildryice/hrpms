<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Add Educational Details</h3>
</div>
<form class="g-3 needs-validation" action="{{ route('profile.education.store') }}" method="post"
    id="educationAddForm" enctype="multipart/form-data" autocomplete="off">
    {!! csrf_field() !!}
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationeducationlevel" class="form-label required-label">Education Level </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="education_level_id" autofocus data-width="100%" class="select2 form-control @if($errors->has('education_level_id')) is-invalid @endif"
                        autocomplete="off" id = "validationeducationlevel">
                    <option value="">Select a Education Level</option>
                    @foreach($educationLevels as $educationLevel)
                    <option value="{{ $educationLevel->id }}">{{ $educationLevel->title }}</option>
                    @endforeach
                </select>
                @if($errors->has('education_level_id'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div data-field="education_level_id">{!! $errors->first('education_level_id') !!}</div>
                </div>
            @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdegree" class="form-label required-label">Name of Degree </label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if($errors->has('degree')) is-invalid @endif" id="validationdegree1"
                value="{{ old('degree') }}"
                    placeholder="Degree" name="degree">
                @if($errors->has('degree'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="degree">{!! $errors->first('degree') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationinstitution" class="form-label required-label">Institution </label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if($errors->has('institution')) is-invalid @endif" id="validationinstitution" value="{{ old('institution') }}"
                    placeholder="Institution" name="institution" autofocus>
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
                    <label for="validationpassedyear" class="form-label required-label">Passed Year </label>
                </div>
            </div>
            <?php $years = range(date('Y'), 1970);?>
            <div class="col-lg-9">
                <select id="validationpassed_year" data-width="100%" class="select2 form-control @if($errors->has('passed_year')) is-invalid @endif"
                    name="passed_year" autocomplete="off">
                    <option value="">Select a Passed Year</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{$year == old('passed_year') ? "selected":""}}>
                        {{ $year }}
                    </option>
                @endforeach
                </select>
                @if($errors->has('passed_year'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="passed_year">{!! $errors->first('passed_year') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdocument" class="form-label required-label">Document </label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control @if($errors->has('attachment')) is-invalid @endif" id="validationdocument" value="{{ old('attachment') }}"
                    placeholder="" name="attachment">
                @if($errors->has('attachment'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button class="btn btn-primary btn-sm"  type="submit">Save</button>
        {{-- <button class="btn btn-success btn-sm">Update</button> --}}
        {{-- <button class="btn btn-danger btn-sm" type="reset" value="reset">Reset</button> --}}
    </div>
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('educationAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    education_level_id: {
                        validators: {
                            notEmpty: {
                                message: 'Education Level is required',
                            },
                        },
                    },
                    degree: {
                        validators: {
                            notEmpty: {
                                message: 'Degree is required',
                            },
                        },
                    },
                    institution: {
                        validators: {
                            notEmpty: {
                                message: 'Institution name is required',
                            },
                        },
                    },
                    passed_year: {
                        validators: {
                            notEmpty: {
                                message: 'Passed Year is required',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            notEmpty: {
                                message: 'Attachment is required',
                            },
                            file: {extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: '2097152',
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
                },
            });
        });
    </script>
@endpush

