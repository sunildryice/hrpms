@extends('layouts.container')

@section('title', 'Update Consultant/STE Profile')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#consultant-menu').addClass('active');
        });
        var queryTab = @json(request()->query('tab'));
        var $selectedTab = queryTab ?? localStorage.getItem('c-edit-tab') ?? 'generalInformation';
        if ($("[data-tag='" + $selectedTab + "']").length == 0 || $('#' + $selectedTab).length == 0) {
            $selectedTab = 'generalInformation';
        }
        document.addEventListener('DOMContentLoaded', function (e) {
            const employeeEditForm = document.getElementById('employeeEditForm');
            const panAttachmentField = $(employeeEditForm.querySelector('[name="pan_attachment"]'));
            const citizenshipAttachmentField = $(employeeEditForm.querySelector('[name="citizenship_attachment"]'));
            const fv = FormValidation.formValidation(employeeEditForm, {
                fields: {
                    employee_code: {
                        validators: {
                            notEmpty: {
                                message: 'Consultant/STE code is required',
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
                            // notEmpty: {
                            //     message: 'The official email address is required',
                            // },
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
                                compare: function () {
                                    return employeeEditForm.querySelector(
                                        '[name="official_email_address"]').value;
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
                             notEmpty: {
                                message: 'The citizenship number is required',
                            },
                            // callback: {
                            //     message: 'The citizenship number is required.',
                            //     callback: function (input) {
                            //         const value = citizenshipAttachmentField.val();
                            //         return value === '' || employeeEditForm.querySelector(
                            //             '[name="citizenship_number"]').value !== '';
                            //     },
                            // },
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
                                message: 'The pan number is of 9 digits.',
                            },
                            callback: {
                                message: 'The PAN number is required.',
                                callback: function (input) {
                                    const value = panAttachmentField.val();
                                    return value === '' || employeeEditForm.querySelector(
                                        '[name="pan_number"]').value !== '';
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

            employeeEditForm.querySelector('[name="official_email_address"]').addEventListener('input', function () {
                fv.revalidateField('official_email_address');
                fv.revalidateField('personal_email_address');
            });
            employeeEditForm.querySelector('[name="personal_email_address"]').addEventListener('input', function () {
                fv.revalidateField('official_email_address');
                fv.revalidateField('personal_email_address');
            });
            employeeEditForm.querySelector('[name="pan_attachment"]').addEventListener('input', function () {
                fv.revalidateField('pan_number');
            });
            employeeEditForm.querySelector('[name="citizenship_attachment"]').addEventListener('input', function () {
                fv.revalidateField('citizenship_number');
            });
            employeeEditForm.querySelector('[name="citizenship_number"]').addEventListener('input', function () {
                fv.revalidateField('citizenship_number');
            });
            employeeEditForm.querySelector('[name="pan_number"]').addEventListener('input', function () {
                fv.revalidateField('pan_number');
            });

            $('[name="date_of_birth"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function (e) {
                fv.revalidateField('date_of_birth');
            });

            $('[name="probation_complete_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            });

            $('.step-item').click(function () {
                $selectedTab = $(this).data('tag');
                localStorage.setItem('c-edit-tab', $selectedTab);
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            }).ready(function () {
                $('[data-tag="' + $selectedTab + '"]').addClass('active');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + $selectedTab).addClass('active').removeClass('hide');
            });
        });

        function displayEducationEditForm($object, url) {
            $element = $(this);
            var successCallback = function (response) {
                $('#addEducationBlock').hide();
                $('#editEducationBlock').show();
                $('#editEducationBlock').find('[name="education_level_id"]').val(response.education.education_level_id)
                    .select2('destroy').select2();
                $('#editEducationBlock').find('[name="degree"]').val(response.education.degree);
                $('#editEducationBlock').find('[name="institution"]').val(response.education.institution);
                $('#editEducationBlock').find('[name="passed_year"]').val(response.education.passed_year).select2('destroy').select2();
                if (response.attachment != '') {
                    $('#editEducationBlock').find('[name="attachment_exist"]').attr('href', response.attachment);
                    $('.media').show();
                }
                $('#editEducationBlock').find('form').attr('action', response.updateAction);
            }
            var errorCallback = function (error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayExperienceEditForm($object, url) {
            $element = $(this);
            var successCallback = function (response) {
                $('#addExperienceBlock').hide();
                $('#editExperienceBlock').show();
                $('#editExperienceBlock').find('[name="institution"]').val(response.experience.institution);
                $('#editExperienceBlock').find('[name="position"]').val(response.experience.position);
                $('#editExperienceBlock').find('[name="period_from"]').val(response.period_from);
                $('#editExperienceBlock').find('[name="period_to"]').val(response.period_to);
                $('#editExperienceBlock').find('[name="remarks"]').val(response.experience.remarks);
                if (response.attachment != '') {
                    $('#editExperienceBlock').find('[name="attachment_exist"]').attr('href', response.attachment);
                    $('.media').show();
                }
                $('#editExperienceBlock').find('form').attr('action', response.updateAction);
            }
            var errorCallback = function (error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayFamilyEditForm($object, url) {
            $element = $(this);
            var successCallback = function (response) {
                $('#addFamilyMemberBlock').hide();
                $('#editFamilyMemberBlock').show();
                var fields = ['full_name', 'remarks', 'contact_number', 'province_id', 'district_id', 'local_level_id',
                    'ward', 'tole'
                ];
                $('#editFamilyMemberBlock').find('[name="family_detail_id"]').val(response.familyMember.id);
                $('#editFamilyMemberBlock').find('[name="full_name"]').val(response.familyMember.full_name);
                $('#editFamilyMemberBlock').find('[name="family_relation_id"]').val(response.familyMember
                    .family_relation_id).select2('destroy').select2();
                $('#editFamilyMemberBlock').find('[name="date_of_birth"]').val(response.dateOfBirth);
                $('#editFamilyMemberBlock').find('[name="remarks"]').val(response.familyMember.remarks);
                $('#editFamilyMemberBlock').find('[name="province_id"]').val(response.familyMember.province_id).select2(
                    'destroy').select2();
                $('#editFamilyMemberBlock').find('[name="district_id"]').val(response.familyMember.district_id).select2(
                    'destroy').select2();
                $('#editFamilyMemberBlock').find('[name="local_level_id"]').val(response.familyMember.local_level_id)
                    .select2('destroy').select2();
                $('#editFamilyMemberBlock').find('[name="ward"]').val(response.familyMember.ward);
                $('#editFamilyMemberBlock').find('[name="tole"]').val(response.familyMember.tole);
                $('#editFamilyMemberBlock').find('[name="contact_number"]').val(response.familyMember.contact_number);
                $('#editFamilyMemberBlock').find('form').attr('action', response.updateAction);

                $('#editFamilyMemberBlock').find('[name="nominee"]').attr('checked', false);
                if (response.familyMember.nominee_at) {
                    $('#editFamilyMemberBlock').find('[name="nominee"]').attr('checked', true);
                } else {
                    $('#editFamilyMemberBlock').find('[name="nominee"]').attr('checked', false);
                }
                if (response.familyMember.emergency_contact_at) {
                    $('#editFamilyMemberBlock').find('[name="emergency_contact"]').attr('checked', true).trigger(
                        'change');
                } else {
                    $('#editFamilyMemberBlock').find('[name="emergency_contact"]').attr('checked', false).trigger(
                        'change');
                }
            }
            var errorCallback = function (error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayInsuranceEditForm($object, url) {
            $element = $(this);
            var successCallback = function (response) {
                $('#addInsuranceBlock').hide();
                $('#editInsuranceBlock').show();
                $('#editInsuranceBlock').find('[name="insurer"]').val(response.insurance.insurer);
                $('#editInsuranceBlock').find('[name="amount"]').val(response.insurance.amount);
                $('#editInsuranceBlock').find('[name="payroll_fiscal_year_id"]').val(response.insurance.payroll_fiscal_year_id);
                $('#editInsuranceBlock').find('[name="paid_date"]').val(response.paid_date);
                if (response.attachment != '') {
                    $('#editInsuranceBlock').find('[name="attachment_exist"]').attr('href', response.attachment);
                    $('.media').show();
                }
                $('#editInsuranceBlock').find('form').attr('action', response.updateAction);
            }
            var errorCallback = function (error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayTenureEditForm($object, url) {
            $element = $(this);
            var successCallback = function (response) {
                console.log(response);
                $('#addTenureBlock').hide();
                $('#editTenureBlock').show();
                $('#editTenureBlock').find('[name="office_id"]').val(response.tenure.office_id)
                    .select2('destroy').select2();
                $('#editTenureBlock').find('[name="designation_id"]').val(response.tenure.designation_id)
                    .select2('destroy').select2();
                $('#editTenureBlock').find('[name="department_id"]').val(response.tenure.department_id)
                    .select2('destroy').select2();
                $('#editTenureBlock').find('[name="supervisor_id"]').val(response.tenure.supervisor_id)
                    .select2('destroy').select2();
                $('#editTenureBlock').find('[name="cross_supervisor_id"]').val(response.tenure.cross_supervisor_id)
                    .select2('destroy').select2();
                $('#editTenureBlock').find('[name="next_line_manager_id"]').val(response.tenure.next_line_manager_id)
                    .select2('destroy').select2();
                $('#editTenureBlock').find('[name="duty_station_id"]').val(response.tenure.duty_station_id)
                    .select2('destroy').select2();
                $('#editTenureBlock').find('[name="joined_date"]').val(response.joined_date);
                $('#editTenureBlock').find('[name="remarks"]').val(response.tenure.remarks);

                $('#editTenureBlock').find('form').attr('action', response.updateAction);


            }
            var errorCallback = function (error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayTrainingEditForm($object, url) {
            $element = $(this);
            var successCallback = function (response) {
                $('#addTrainingBlock').hide();
                $('#editTrainingBlock').show();
                $('#editTrainingBlock').find('[name="institution"]').val(response.training.institution);
                $('#editTrainingBlock').find('[name="training_topic"]').val(response.training.training_topic);
                $('#editTrainingBlock').find('[name="period_from"]').val(response.period_from);
                $('#editTrainingBlock').find('[name="period_to"]').val(response.period_to);
                $('#editTrainingBlock').find('[name="remarks"]').val(response.training.remarks);
                if (response.attachment != '') {
                    $('#editTrainingBlock').find('[name="attachment_exist"]').attr('href', response.attachment);
                    $('.media').show();
                }
                $('#editTrainingBlock').find('form').attr('action', response.updateAction);
            }
            var errorCallback = function (error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function cancelEducationEditForm($object) {
            $('#editEducationBlock').hide();
            $('#addEducationBlock').show();
        }

        function cancelExperienceEditForm($object) {
            $('#editExperienceBlock').hide();
            $('#addExperienceBlock').show();
        }

        function cancelFamilyEditForm($object) {
            $('#editFamilyMemberBlock').hide();
            $('#addFamilyMemberBlock').show();
        }

        function cancelInsuranceEditForm($object) {
            $('#editInsuranceBlock').hide();
            $('#addInsuranceBlock').show();
        }

        function cancelTrainingEditForm($object) {
            $('#editTrainingBlock').hide();
            $('#addTrainingBlock').show();
        }
    </script>
@endsection
@section('page-content')



            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{!! route('consultant.index') !!}"
                                       class="text-decoration-none text-dark">{{ __('label.consultant') }}</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')
                                    : {{ $employee->getFullNameWithCode() }}</li>
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
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                       data-tag="generalInformation">
                                        <i class="nav-icon bi-info-circle"></i> General Information
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none" data-tag="address">
                                        <i class="nav-icon bi-pin-map"></i> Address
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                       data-tag="family-details">
                                        <i class="nav-icon bi-people"></i>Family Details
                                    </a>
                                </li>
                                @if($authUser->can('manage-tenure'))
                                    <li class="nav-item">
                                        <a href="#" class="nav-link step-item text-decoration-none"
                                           data-tag="tenure-details">
                                            <i class="nav-icon bi bi-person-workspace"></i> Tenure
                                        </a>
                                    </li>
                                    {{-- <li class="nav-item"> --}}
                                    {{--     <a href="#" class="nav-link step-item text-decoration-none" --}}
                                    {{--        data-tag="finance-details"> --}}
                                    {{--         <i class="nav-icon bi bi-bank"></i> Finance --}}
                                    {{--     </a> --}}
                                    {{-- </li> --}}
                                    {{-- <li class="nav-item"> --}}
                                    {{--     <a href="#" class="nav-link step-item text-decoration-none" --}}
                                    {{--        data-tag="insurance-details"> --}}
                                    {{--         <i class="nav-icon bi bi-bank"></i> Insurance --}}
                                    {{--     </a> --}}
                                    {{-- </li> --}}
                                @endif
                                {{-- <li class="nav-item"> --}}
                                {{--     <a href="#" class="nav-link step-item text-decoration-none" --}}
                                {{--        data-tag="medicalInformation"> --}}
                                {{--         <i class="nav-icon bi-calendar-heart"></i> Medical --}}
                                {{--         Information --}}
                                {{--     </a> --}}
                                {{-- </li> --}}
                                {{-- <li class="nav-item"> --}}
                                {{--     <a href="#" class="nav-link step-item text-decoration-none" --}}
                                {{--        data-tag="education-details"> --}}
                                {{--         <i class="nav-icon bi bi-journal-text"></i> Education --}}
                                {{--     </a> --}}
                                {{-- </li> --}}
                                {{-- <li class="nav-item"> --}}
                                {{--     <a href="#" class="nav-link step-item text-decoration-none" --}}
                                {{--        data-tag="experience-details"> --}}
                                {{--         <i class="nav-icon bi bi-explicit"></i> Experience --}}
                                {{--     </a> --}}
                                {{-- </li> --}}
                                {{-- <li class="nav-item"> --}}
                                {{--     <a href="#" class="nav-link step-item text-decoration-none" --}}
                                {{--        data-tag="training-details"> --}}
                                {{--         <i class="nav-icon bi bi-calendar4-range"></i> Training --}}
                                {{--     </a> --}}
                                {{-- </li> --}}
                                 <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                        data-tag="finance-details">
                                        <i class="nav-icon bi bi-bank"></i> Finance
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                       data-tag="document-upload-details">
                                        <i class="nav-icon bi bi-explicit"></i> Document Upload
                                    </a>
                                </li>
                                @if($authUser->can('update-user-role'))
                                    <li class="nav-item">
                                        <a href="#" class="nav-link step-item text-decoration-none"
                                           data-tag="login-details">
                                            <i class="nav-icon bi bi-lock"></i> Login Credentials
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="c-tabs-content" id="generalInformation">
                            <div class="card">
                                @include('Employee::Consultant.General.edit')
                            </div>
                        </div>
                        <div class="c-tabs-content" id="address">
                            <div class="card mb-5">
                                @include('Employee::Address.index')
                            </div>
                        </div>
                        <div class="c-tabs-content" id="family-details">
                            <div class="card mb-5" id="addFamilyMemberBlock">
                                @include('Employee::FamilyDetail.create')
                            </div>
                            <div class="card mb-5" id="editFamilyMemberBlock"
                                 style="display: none;">
                                @include('Employee::FamilyDetail.edit')
                            </div>
                            @if ($employee->familyDetails->count())
                                <div class="card mb-5">
                                    @include('Employee::FamilyDetail.index')
                                </div>
                            @endif
                        </div>

                        @if($authUser->can('manage-tenure'))
                            <div class="c-tabs-content" id="tenure-details">
                                <div class="card mb-5" id="addTenureBlock">
                                    @include('Employee::Tenure.create')
                                </div>
                                @if ($employee->tenures->count())
                                    <div class="card mb-5" id="editTenureBlock"
                                         style="display: none;">
                                        @include('Employee::Tenure.edit')
                                    </div>
                                    <div class="card mb-5">
                                        @include('Employee::Tenure.index')
                                    </div>
                                @endif
                            </div>
                            <div class="c-tabs-content" id="finance-details">
                                <div class="card mb-5">
                                    @include('Employee::Finance.index')
                                </div>
                            </div>
                            <div class="c-tabs-content" id="insurance-details">
                                <div class="card mb-5" id="addInsuranceBlock">
                                    @include('Employee::Insurance.create')
                                </div>
                                <div class="card mb-5" id="editInsuranceBlock"
                                     style="display: none;">
                                    @include('Employee::Insurance.edit')
                                </div>
                                <div class="card mb-5">
                                    @include('Employee::Insurance.index')
                                </div>
                            </div>
                        @endif

                        <div class="card c-tabs-content" id="medicalInformation">
                            @include('Employee::MedicalCondition.index')
                        </div>

                        <div class="c-tabs-content" id="education-details">
                            <div class="card mb-5" id="addEducationBlock">
                                @include('Employee::Education.create')
                            </div>
                            <div class="card mb-5" id="editEducationBlock"
                                 style="display: none;">
                                @include('Employee::Education.edit')
                            </div>
                            @if ($employee->education->count())
                                <div class="card mb-5">
                                    @include('Employee::Education.index')
                                </div>
                            @endif
                        </div>

                        <div class="c-tabs-content" id="experience-details">
                            <div class="card mb-5" id="addExperienceBlock">
                                @include('Employee::Experience.create')
                            </div>
                            <div class="card mb-5" id="editExperienceBlock"
                                 style="display: none;">
                                @include('Employee::Experience.edit')
                            </div>
                            @if ($employee->experiences->count())
                                <div class="card mb-5">
                                    @include('Employee::Experience.index')
                                </div>
                            @endif
                        </div>

                        <div class="c-tabs-content" id="training-details">
                            <div class="card mb-5" id="addTrainingBlock">
                                @include('Employee::Training.create')
                            </div>
                            <div class="card mb-5" id="editTrainingBlock"
                                 style="display: none;">
                                @include('Employee::Training.edit')
                            </div>
                            @if ($employee->trainings->count())
                                <div class="card mb-5">
                                    @include('Employee::Training.index')
                                </div>
                            @endif
                        </div>
                        <div class="c-tabs-content" id="document-upload-details">
                            <div class="card mb-5" id="addDocumentBlock">
                                @include('Employee::DocumentUpload.create')
                            </div>
                            @if ($employee->signature != null || $employee->profile_picture)
                                <div class="card mb-5">
                                    @include('Employee::DocumentUpload.index')
                                </div>
                            @endif
                        </div>

                        @if($authUser->can('update-user-role'))
                            <div class="c-tabs-content" id="login-details">
                                <div class="card mb-5">
                                    @include('Employee::Consultant.user')
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </section>

@stop
