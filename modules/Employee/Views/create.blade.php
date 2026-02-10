@extends('layouts.container')

@section('title', 'Add New Employee')

@section('page_js')
    <script>
        var queryTab = @json(request()->query('tab'));

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
                                regexp: /^[a-z\s.]+$/i,
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
                            notEmpty: {
                                message: 'The personal email address is required',
                            },
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
                    joined_date: {
                        validators: {
                            notEmpty: {
                                message: 'The joined date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
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
                            notEmpty: {
                                message: 'The citizenship number is required',
                            },
                            // callback: {
                            //     message: 'The citizenship number is required.',
                            //     callback: function(input) {
                            //         const value = citizenshipAttachmentField.val();
                            //         return value === '' || form.querySelector(
                            //             '[name="citizenship_number"]').value !== '';
                            //     },
                            // },
                        },
                    },
                    citizenship_attachment: {
                        validators: {
                            notEmpty: {
                                message: 'The citizenship attachment is required.'
                            },
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
                    nid_number: {
                        validators: {
                            stringLength: {
                                max: 50
                            }
                        }
                    },
                    passport_number: {
                        validators: {
                            stringLength: {
                                max: 20
                            }
                        }
                    },
                    vehicle_license_number: {
                        validators: {
                            stringLength: {
                                max: 50
                            }
                        }
                    },
                    passport_attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: 2097152,
                                message: 'The selected file is not valid file or must not be greater than 2 MB.',
                            }
                        }
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

            form.querySelector('[name="official_email_address"]').addEventListener('input', function() {
                fv.revalidateField('official_email_address');
                fv.revalidateField('personal_email_address');
            });
            form.querySelector('[name="personal_email_address"]').addEventListener('input', function() {
                fv.revalidateField('official_email_address');
                fv.revalidateField('personal_email_address');
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

            $(form.querySelector('[name="joined_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
                onChange: function() {
                    fv.revalidateField('joined_date');
                },
            });

        });
    </script>
@endsection
@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('employees.index') !!}"
                                class="text-decoration-none text-dark">Employees</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="rounded shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                    <ul class="m-0 list-unstyled">
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="generalInformation">
                                <i class="nav-icon bi-info-circle"></i> General Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none">
                                <i class="nav-icon bi-pin-map"></i> Address
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none">
                                <i class="nav-icon bi-people"></i>Family Details
                            </a>
                        </li>
                        @if ($authUser->can('manage-tenure'))
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-person-workspace"></i> Tenure
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-clock"></i>Working Hours
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-bank"></i> Finance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-bank"></i> Insurance
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none">
                                <i class="nav-icon bi-calendar-heart"></i> Medical
                                Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="education-details">
                                <i class="nav-icon bi bi-journal-text"></i> Education
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="experience-details">
                                <i class="nav-icon bi bi-explicit"></i> Experience
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="training-details">
                                <i class="nav-icon bi bi-calendar4-range"></i> Training
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none"
                                data-tag="document-upload-details">
                                <i class="nav-icon bi bi-explicit"></i> Document Upload
                            </a>
                        </li>
                        @if ($authUser->can('update-user-role'))
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item text-decoration-none" data-tag="login-details">
                                    <i class="nav-icon bi bi-lock"></i> Login Credentials
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none"
                                data-tag="social-media-details">
                                <i class="nav-icon bi bi-globe"></i> Social Media
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="c-tabs-content active">
                    @include('Employee::General.create')
                </div>

            </div>
        </div>
    </section>

@stop
