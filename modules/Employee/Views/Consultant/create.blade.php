@extends('layouts.container')

@section('title', 'Add New Consultant')

@section('page_js')
    <script>
        $('#navbarVerticalMenu').find('#consultant-menu').addClass('active');
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('employeeAddForm');
            const panAttachmentField = $(form.querySelector('[name="pan_attachment"]'));
            const citizenshipAttachmentField = $(form.querySelector('[name="citizenship_attachment"]'));
            const fv = FormValidation.formValidation(form, {
                fields: {
                    employee_code: {
                        validators: {
                            notEmpty: {
                                message: 'Staff code is required',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 1',
                                min: 1,
                            },
                            lessThan: {
                                message: 'The value must be less than or equal to 10000',
                                max: 10000,
                            },
                        },
                    },
                    full_name: {
                        validators: {
                            notEmpty: {
                                message: 'Full name is required',
                            },
                            regexp: {
                                regexp: /^[a-z\s]+$/i,
                                message: 'The full name can consist of alphabetical characters and spaces only',
                            },
                        },
                    },
                    official_email_address: {
                        validators: {
                            notEmpty: {
                                message: 'The official email address is required',
                            },
                            emailAddress: {
                                message: 'The input must be a email address',
                            },
                        },
                    },
                    mobile_number: {
                        validators: {
                            notEmpty: {
                                message: 'The mobile number is required',
                            },
                            regexp: {
                                regexp: /^[9][7-8]\d{8}$/,
                                message: 'The mobile number is not valid',
                            },
                        },
                    },
                    personal_email_address: {
                        validators: {
                            emailAddress: {
                                message: 'The input must be a email address',
                            },
                            different: {
                                compare: function() {
                                    return form.querySelector('[name="official_email_address"]').value;
                                },
                                message: 'The official and personal email address cannot be the same.',
                            },
                        },
                    },
                    date_of_birth: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    citizenship_number: {
                        validators: {
                            callback: {
                                message: 'The citizenship number is required.',
                                callback: function(input) {
                                    const value = citizenshipAttachmentField.val();
                                    return value === '' || form.querySelector(
                                        '[name="citizenship_number"]').value !== '';
                                },
                            },
                        },
                    },
                    citizenship_attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: '2097152',
                                message: 'The selected file is not valid file or must not be greater than 2 MB.',
                            },
                        },
                    },
                    pan_number: {
                        validators: {
                            between: {
                                min: 100000000,
                                max: 999999999,
                                message: 'The PAN number is of 9 digits.',
                            },
                            callback: {
                                message: 'The PAN number is required.',
                                callback: function(input) {
                                    const value = panAttachmentField.val();
                                    return value === '' || form.querySelector('[name="pan_number"]')
                                        .value !== '';
                                },
                            },
                        },
                    },
                    pan_attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: '2097152',
                                message: 'The selected file is not valid file or must not be greater than 2 MB.',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),

                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'date_of_birth',
                            message: 'The date of birth must be a valid date and earlier than today',
                        },
                        endDate: {
                            field: 'today',
                            message: 'The date of birth must be a valid date and earlier than today',
                        },
                    }),
                },
            });

            form.querySelector('[name="pan_attachment"]').addEventListener('input', function() {
                fv.revalidateField('pan_number');
            });
            form.querySelector('[name="citizenship_attachment"]').addEventListener('input', function() {
                fv.revalidateField('citizenship_number');
            });
            form.querySelector('[name="citizenship_number"]').addEventListener('input', function() {
                fv.revalidateField('citizenship_number');
            });
            form.querySelector('[name="pan_number"]').addEventListener('input', function() {
                fv.revalidateField('pan_number');
            });
            form.querySelector('[name="mobile_number"]').addEventListener('input', function() {
                fv.revalidateField('mobile_number');
            });

            $(form.querySelector('[name="date_of_birth"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
                onChange: function() {
                    fv.revalidateField('date_of_birth');
                },
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('consultant.index') !!}"
                                class="text-decoration-none text-dark">Consultant</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                {{-- <div class="pt-3 pb-3 bg-white rounded shadow-sm vertical-navigation sm-menu-vr"> --}}
                {{--     <ul class="m-0 list-unstyled"> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none active"><i --}}
                {{--                     class="nav-icon bi-info-circle"></i> General Information</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi-pin-map"></i> Address</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi-people"></i> Family Details</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi bi-person-workspace"></i> Tenure</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi-calendar-heart"></i> Medical information</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi bi-journal-text"></i> Education</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi bi-explicit"></i> Experience</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi bi-calendar4-range"></i> Training</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"> --}}
                {{--                 <i class="nav-icon bi bi-explicit"></i> Document Upload</a></li> --}}
                {{--         <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i --}}
                {{--                     class="nav-icon bi bi-lock"></i> Login Credentials</a></li> --}}
                {{--     </ul> --}}
                {{-- </div> --}}
            </div>
            {{-- <div class="col-lg-9"> --}}
            <div class="">
                <div class="card">
                    <div class="card-header fw-bold">General Information</div>
                    <form action="{{ route('consultant.store') }}" id="employeeAddForm" method="post"
                        enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Consultant Code</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" min="1"
                                        class="form-control @if ($errors->has('employee_code')) is-invalid @endif"
                                        name="employee_code" value="{!! old('employee_code') !!}" autofocus />
                                    @if ($errors->has('employee_code'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="employee_code">{!! $errors->first('employee_code') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationfullname" class="form-label required-label">Full Name </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text"
                                        class="form-control @if ($errors->has('full_name')) is-invalid @endif"
                                        name="full_name" value="{{ old('full_name') }}" placeholder="Full name" />
                                    @if ($errors->has('full_name'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="full_name">{!! $errors->first('full_name') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- <div class="mb-2 row"> --}}
                            {{--     <div class="col-lg-3"> --}}
                            {{--         <div class="d-flex align-items-start h-100"> --}}
                            {{--             <label class="form-label required-label">Official Email  </label> --}}
                            {{--         </div> --}}
                            {{--     </div> --}}
                            {{--     <div class="col-lg-9"> --}}
                            {{--         <input type="email" --}}
                            {{--             class="form-control @if ($errors->has('official_email_address')) is-invalid @endif" --}}
                            {{--             name="official_email_address" value="{{ old('official_email_address') }}" --}}
                            {{--             placeholder="example@example.com"> --}}
                            {{--         @if ($errors->has('official_email_address')) --}}
                            {{--             <div class="fv-plugins-message-container invalid-feedback"> --}}
                            {{--                 <div data-field="official_email_address">{!! $errors->first('official_email_address') !!}</div> --}}
                            {{--             </div> --}}
                            {{--         @endif --}}
                            {{--     </div> --}}
                            {{-- </div> --}}
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Personal Email </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="email"
                                        class="form-control @if ($errors->has('personal_email_address')) is-invalid @endif"
                                        name="personal_email_address" value="{{ old('personal_email_address') }}"
                                        placeholder="example@example.com">
                                    @if ($errors->has('personal_email_address'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="personal_email_address">{!! $errors->first('personal_email_address') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationphone" class="form-label">Contact Number
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text required-label"
                                                        id="basic-addon2">Mobile</span>
                                                </div>
                                                <input type="text"
                                                    class="form-control  @if ($errors->has('mobile_number')) is-invalid @endif"
                                                    name="mobile_number" placeholder="Mobile Number"
                                                    value="{{ old('mobile_number') }}" aria-label="Recipient's username"
                                                    aria-describedby="basic-addon2" />

                                                @if ($errors->has('mobile_number'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="mobile_number">{!! $errors->first('mobile_number') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Telephone</span>
                                                </div>
                                                <input type="text" class="form-control" name="telephone_number"
                                                    placeholder="Telephone Number" value="{{ old('telephone_number') }}"
                                                    aria-label="Recipient's username" aria-describedby="basic-addon2" />

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdob" class="form-label">Date of Birth
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" readonly name="date_of_birth"
                                        value="{{ old('date_of_birth') }}" />
                                    <input type="hidden" value="{{ date('Y-m-d') }}" name="today"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationcitizenship" class="form-label">Citizenship
                                            No.</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <input type="text"
                                                class="form-control @if ($errors->has('citizenship_number')) is-invalid @endif"
                                                name="citizenship_number" value="{{ old('citizenship_number') }}"
                                                placeholder="Citizenship No">
                                            @if ($errors->has('citizenship_number'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="citizenship_number">{!! $errors->first('citizenship_number') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="file" class="form-control" name="citizenship_attachment" />
                                            <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                                            @if ($errors->has('citizenship_attachment'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="citizenship_attachment">{!! $errors->first('citizenship_attachment') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationpan" class="form-label">Pan No.
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <input type="number"
                                                class="form-control @if ($errors->has('pan_number')) is-invalid @endif"
                                                name="pan_number" value="{{ old('pan_number') }}" placeholder="Pan No">
                                            @if ($errors->has('pan_number'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="pan_number">{!! $errors->first('pan_number') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="file" class="form-control" name="pan_attachment" />
                                            <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                                            @if ($errors->has('pan_attachment'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="pan_attachment">{!! $errors->first('pan_attachment') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationGender" class="form-label">Gender
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="gender" class="select2 form-control" data-width="100%">
                                        <option value="">Select a Gender</option>
                                        @foreach ($genders as $gender)
                                            <option value="{{ $gender->id }}"
                                                @if ($gender->id == old('gender')) selected @endif>{{ $gender->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationMaritalstaatus" class="form-label">Marital Status
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="marital_status" class="select2 form-control" data-width="100%">
                                        <option value="">Select a Marital Status</option>
                                        @foreach ($maritalStatus as $status)
                                            <option value="{{ $status->id }}"
                                                @if ($status->id == old('marital_status')) selected @endif>{{ $status->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {!! csrf_field() !!}

                        </div>
                        <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            {{-- <button class="btn btn-success btn-sm">Update</button> --}}
                            <a href="{!! route('consultant.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

@stop
