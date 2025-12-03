@extends('layouts.container')

@section('title', 'Update Profile')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#dashboard-menu').addClass('active');
        });
        var $selectedTab = '{!! request()->query('tab') ?: 'generalInformation' !!}';
        document.addEventListener('DOMContentLoaded', function(e) {
            const employeeEditForm = document.getElementById('employeeEditForm');
            const panAttachmentField = $(employeeEditForm.querySelector('[name="pan_attachment"]'));
            const citizenshipAttachmentField = $(employeeEditForm.querySelector('[name="citizenship_attachment"]'));
            const fv = FormValidation.formValidation(employeeEditForm, {
                fields: {
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
                    gender: {
                        validators: {
                            notEmpty: {
                                message: 'Gender is required',
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
                                    return employeeEditForm.querySelector(
                                        '[name="official_email_address"]').value;
                                },
                                message: 'The official and personal email address cannot be the same.',
                            },
                        },
                    },
                    date_of_birth: {
                        validators: {
                            notEmpty: {
                                message: 'Date of birth is required',
                            },
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
                                callback: function(input) {
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

            $(employeeEditForm).on('change', '[name="gender"]', function(e) {
                fv.revalidateField('gender');
            });

            employeeEditForm.querySelector('[name="personal_email_address"]').addEventListener('input', function() {
                fv.revalidateField('official_email_address');
                fv.revalidateField('personal_email_address');
            });
            employeeEditForm.querySelector('[name="pan_attachment"]').addEventListener('input', function() {
                fv.revalidateField('pan_number');
            });
            employeeEditForm.querySelector('[name="citizenship_attachment"]').addEventListener('input', function() {
                fv.revalidateField('citizenship_number');
            });
            employeeEditForm.querySelector('[name="citizenship_number"]').addEventListener('input', function() {
                fv.revalidateField('citizenship_number');
            });
            employeeEditForm.querySelector('[name="pan_number"]').addEventListener('input', function() {
                fv.revalidateField('pan_number');
            });

            $('[name="date_of_birth"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('date_of_birth');
            });

            $('.step-item').click(function() {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            }).ready(function() {
                $('[data-tag="' + $selectedTab + '"]').addClass('active');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + $selectedTab).addClass('active').removeClass('hide');
            });
        });

        function displayEducationEditForm($object, url) {
            $element = $(this);
            var successCallback = function(response) {
                $('#addEducationBlock').hide();
                $('#editEducationBlock').show();
                $('#editEducationBlock').find('[name="education_level_id"]').val(response.education.education_level_id)
                    .select2('destroy').select2();
                $('#editEducationBlock').find('[name="degree"]').val(response.education.degree);
                $('#editEducationBlock').find('[name="institution"]').val(response.education.institution);
                $('#editEducationBlock').find('[name="passed_year"]').val(response.education.passed_year).select2(
                    'destroy').select2();
                if (response.attachment != '') {
                    $('#editEducationBlock').find('[name="attachment_exist"]').attr('href', response.attachment);
                    $('.media').show();
                }
                $('#editEducationBlock').find('form').attr('action', response.updateAction);
            }
            var errorCallback = function(error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayExperienceEditForm($object, url) {
            $element = $(this);
            var successCallback = function(response) {
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
            var errorCallback = function(error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayFamilyEditForm($object, url) {
            $element = $(this);
            var successCallback = function(response) {
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
            var errorCallback = function(error) {
                console.log(error);
            }
            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
        }

        function displayTrainingEditForm($object, url) {
            $element = $(this);
            var successCallback = function(response) {
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
            var errorCallback = function(error) {
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

        function cancelTrainingEditForm($object) {
            $('#editTrainingBlock').hide();
            $('#addTrainingBlock').show();
        }
    </script>
@endsection
@section('page-content')

    <div class="m-content p-3">
        <div class="container-fluid">

            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
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
                        <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
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
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                        data-tag="medicalInformation">
                                        <i class="nav-icon bi-calendar-heart"></i> Medical
                                        Information
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                        data-tag="education-details">
                                        <i class="nav-icon bi bi-journal-text"></i> Education
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                        data-tag="experience-details">
                                        <i class="nav-icon bi bi-explicit"></i> Experience
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                        data-tag="training-details">
                                        <i class="nav-icon bi bi-calendar4-range"></i> Training
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                        data-tag="document-upload-details">
                                        <i class="nav-icon bi bi-explicit"></i> Document Upload
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="#" class="nav-link step-item text-decoration-none"
                                        data-tag="social-media">
                                        <i class="nav-icon bi bi-globe"></i> Social Media
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="c-tabs-content" id="generalInformation">
                            <div class="card">
                                @include('Profile::General.edit')
                            </div>
                        </div>
                        <div class="c-tabs-content" id="address">
                            <div class="card shadow-sm border rounded mb-5">
                                @include('Profile::Address.index')
                            </div>
                        </div>
                        <div class="c-tabs-content" id="family-details">
                            <div class="card shadow-sm border rounded mb-5" id="addFamilyMemberBlock">
                                @include('Profile::FamilyDetail.create')
                            </div>
                            <div class="card shadow-sm border rounded mb-5" id="editFamilyMemberBlock"
                                style="display: none;">
                                @include('Profile::FamilyDetail.edit')
                            </div>
                            @if ($employee->familyDetails->count())
                                <div class="card shadow-sm border rounded mb-5">
                                    @include('Profile::FamilyDetail.index')
                                </div>
                            @endif
                        </div>

                        <div class="c-tabs-content" id="tenure-details">
                            @if ($employee->tenures->count())
                                <div class="card shadow-sm border rounded mb-5">
                                    @include('Profile::Tenure.index')
                                </div>
                            @endif
                        </div>

                        <div class="card shadow-sm border rounded c-tabs-content" id="medicalInformation">
                            @include('Profile::MedicalCondition.index')
                        </div>

                        <div class="c-tabs-content" id="education-details">
                            <div class="card shadow-sm border rounded mb-5" id="addEducationBlock">
                                @include('Profile::Education.create')
                            </div>
                            <div class="card shadow-sm border rounded mb-5" id="editEducationBlock" style="display: none;">
                                @include('Profile::Education.edit')
                            </div>
                            @if ($employee->education->count())
                                <div class="card shadow-sm border rounded mb-5">
                                    @include('Profile::Education.index')
                                </div>
                            @endif
                        </div>

                        <div class="c-tabs-content" id="experience-details">
                            <div class="card shadow-sm border rounded mb-5" id="addExperienceBlock">
                                @include('Profile::Experience.create')
                            </div>
                            <div class="card shadow-sm border rounded mb-5" id="editExperienceBlock" style="display: none;">
                                @include('Profile::Experience.edit')
                            </div>
                            @if ($employee->experiences->count())
                                <div class="card shadow-sm border rounded mb-5">
                                    @include('Profile::Experience.index')
                                </div>
                            @endif
                        </div>

                        <div class="c-tabs-content" id="training-details">
                            <div class="card shadow-sm border rounded mb-5" id="addTrainingBlock">
                                @include('Profile::Training.create')
                            </div>
                            <div class="card shadow-sm border rounded mb-5" id="editTrainingBlock"
                                style="display: none;">
                                @include('Profile::Training.edit')
                            </div>
                            @if ($employee->trainings->count())
                                <div class="card shadow-sm border rounded mb-5">
                                    @include('Profile::Training.index')
                                </div>
                            @endif
                        </div>
                        <div class="c-tabs-content" id="document-upload-details">
                            <div class="card shadow-sm border rounded mb-5" id="addDocumentBlock">
                                @include('Profile::DocumentUpload.create')
                            </div>
                            @if ($employee->signature != null || $employee->profile_picture || $employee->cv_attachment)
                                <div class="card shadow-sm border rounded mb-5">
                                    @include('Profile::DocumentUpload.index')
                                </div>
                            @endif
                        </div>

                        <div class="c-tabs-content" id="social-media">
                            <div class="card shadow-sm border rounded mb-5">
                                @include('Profile::SocialMedia.edit')
                            </div>
                        </div>
                    </div>
            </section>

        </div>
    </div>

@stop
