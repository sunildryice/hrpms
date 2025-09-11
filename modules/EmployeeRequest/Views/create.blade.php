@extends('layouts.container')

@section('title', 'Add New Employee Requisition')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#employee-requests-menu').addClass('active');
            const stringLimit = 191;
            const form = document.getElementById('employeeRequestAddForm');
            const oldActCode = "{{ old('activity_code_id') }}";
            const oldAccCode = "{{ old('account_code_id') }}";
            const fv = FormValidation.formValidation(form, {
                fields: {
                    fiscal_year_id: {
                        validators: {
                            notEmpty: {
                                message: 'The year is required',
                            },
                        },
                    },
                    employee_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'The employee type is required',
                            },
                        },
                    },
                    activity_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The activity code is required',
                            },
                        },
                    },
                    account_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The account code is required',
                            },
                        },
                    },
                    duty_station_id: {
                        validators: {
                            notEmpty: {
                                message: 'The duty station is required',
                            },
                        },
                    },

                    position_title: {
                        validators: {
                            notEmpty: {
                                message: 'The position is required',
                            },
                        },
                    },

                    position_level: {
                        validators: {
                            notEmpty: {
                                message: 'The level is required',
                            },
                        },
                    },

                    replacement_for: {
                        validators: {
                            notEmpty: {
                                message: 'The replacement for is required',
                            },
                        },
                    },

                    required_date: {
                        validators: {
                            notEmpty: {
                                message: 'The required date is required'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },

                    work_load: {
                        validators: {
                            notEmpty: {
                                message: 'The work load is required',
                            },
                            lessThan: {
                                max: 255,
                                message: 'The work load exceeds the limit',
                            }
                        },
                    },
                    duration: {
                        validators: {
                            notEmpty: {
                                message: 'The duration is required',
                            },
                        },
                    },
                    reason_for_request: {
                        validators: {
                            notEmpty: {
                                message: 'The reason for request is required',
                            },
                        },
                    },
                    education_required: {
                        validators: {
                            notEmpty: {
                                message: 'The required education is required',
                            },
                        },
                    },
                    experience_required: {
                        validators: {
                            notEmpty: {
                                message: 'The required experience is required',
                            },
                            stringLength: {
                                max: stringLimit,
                                message: 'The field exceeds the character limit',
                            }
                        },
                    },
                    experience_preferred: {
                        validators: {
                            stringLength: {
                                max: stringLimit,
                                message: 'The field exceeds the character limit',
                            }
                        },
                    },
                    skills_required: {
                        validators: {
                            notEmpty: {
                                message: 'The required skills is required',
                            },
                            stringLength: {
                                max: stringLimit,
                                message: 'The field exceeds the character limit',
                            }
                        },
                    },
                    skills_preferred: {
                        validators: {
                            stringLength: {
                                max: stringLimit,
                                message: 'The characters exceeds the limit',
                            }
                        },
                    },
                    logistics_requirement: {
                        validators: {
                            notEmpty: {
                                message: 'The logistics requirement is required',
                            },
                            stringLength: {
                                max: stringLimit,
                                message: 'The characters exceeds the limit',
                            }
                        },
                    },
                    // approver_id: {
                    //     validators: {notEmpty: {message: 'Approver is required'}}
                    // },
                    tentative_submission_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf,doc,docx',
                                type: 'image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                maxSize: '2097152',
                                message: 'Please add file with valid format (jpeg, jpg, png, pdf, doc, docx) and size upto 2MB'
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

            $(form).on('change', '[name="duty_station_id"]', function(e) {
                fv.revalidateField('duty_station_id');
            }).on('change', '[name="fiscal_year_id"]', function(e) {
                fv.revalidateField('fiscal_year_id');
            }).on('change', '[name="education_required"]', function(e) {
                fv.revalidateField('education_required');
            }).on('change', '[name="account_code_id"]', function(e) {
                fv.revalidateField('account_code_id');
            }).on('change', '[name="activity_code_id"]', function(e) {
                $element = $(this);
                var activityCodeId = $element.val();
                var htmlToReplace = '<option value="">Select Account Code</option>';
                if (activityCodeId) {
                    var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                    var successCallback = function(response) {
                        response.accountCodes.forEach(function(accountCode) {
                            htmlToReplace += '<option value="' + accountCode.id + '" ' + (
                                    oldActCode ? 'selected' : '') + '>' +
                                accountCode.title + ' ' + accountCode.description + '</option>';
                        });
                        console.log(htmlToReplace);
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace)
                            .trigger('change');
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace);
                }
                fv.revalidateField('activity_code_id');
                fv.revalidateField('account_code_id');
            });

            $(form).on('change', '[name="tor_jd_submitted"]', function(e) {
                if (this.value == 1) {
                    $(form).find('#attachment_block').show();
                    $(form).find('#tentative_date').hide();

                } else {
                    $(form).find('#attachment_block').hide();
                    $(form).find('[name="attachment"]').val('');
                }
            });

            $(form).on('change', '[name="employee_type_id"]', function(e) {
                fv.revalidateField('employee_type_id');
                if (this.value == 0) {
                    $(form).find('#employeeTypeBlock').show();
                } else {
                    $(form).find('#employeeTypeBlock').hide();
                }
            });

            $('[name="required_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: "{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}",
            }).on('change', function(e) {
                fv.revalidateField('required_date');
            });

            $('[name="tentative_submission_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: "{{ date('Y-m-d') }}",
            }).on('change', function(e) {
                fv.revalidateField('tentative_submission_date');
            });

            if (oldActCode) {
                console.log('trigger');
                $('[name="activity_code_id"]').trigger('change');
            }

        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('employee.requests.index') }}" class="text-decoration-none text-dark">Employee
                                Requisition</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <section class="registration">
        <form action="{{ route('employee.requests.store') }}" id="employeeRequestAddForm" method="post"
            enctype="multipart/form-data" autocomplete="off">
            <div class="card">
                <div class="card-header fw-bold">General Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label">Position</label>
                            <div>
                                <input type="text" name="position_title" value="{{ old('position_title') }}"
                                    class="form-control @if ($errors->has('position_title')) is-invalid @endif">
                                @if ($errors->has('position_title'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="position_title">
                                            {!! $errors->first('position_title') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label"> For FY</label>
                            <div>
                                <select name="fiscal_year_id"
                                    class="form-control select2 @if ($errors->has('fiscal_year_id')) is-invalid @endif">
                                    <option value="">Select FY</option>
                                    @foreach ($fiscalYears as $year)
                                        <option value="{{ $year->id }}"
                                            @if (old('fiscal_year_id') == $year->id) selected @endif>{{ $year->getFiscalYear() }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('fiscal_year_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="fiscal_year_id">
                                            {!! $errors->first('fiscal_year_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label"> Requested Level</label>
                            <div>
                                <input type="text" name="position_level" value="{{ old('position_level') }}"
                                    class="form-control @if ($errors->has('position_level')) is-invalid @endif">
                                @if ($errors->has('position_level'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="position_level">
                                            {!! $errors->first('position_level') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label"> Replacement for</label>
                            <div>
                                <input type="text" name="replacement_for" value="{{ old('replacement_for') }}"
                                    class="form-control @if ($errors->has('replacement_for')) is-invalid @endif">
                                @if ($errors->has('replacement_for'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="replacement_for">
                                            {!! $errors->first('replacement_for') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label"> {{ __('label.duty-station') }}</label>
                            <div>
                                <select name="duty_station_id"
                                    class="form-control select2 @if ($errors->has('duty_station_id')) is-invalid @endif">
                                    <option value="">Select {{ __('label.duty-station') }}</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}"
                                            @if (old('duty_station_id') == $district->id) selected @endif>
                                            {{ $district->getDistrictName() }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('duty_station_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="duty_station_id">
                                            {!! $errors->first('duty_station_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label"> Date required from</label>
                            <div>
                                <input type="text"
                                    class="form-control @if ($errors->has('required_date')) is-invalid @endif" readonly
                                    value="{{ old('required_date') }}" name="required_date">
                                @if ($errors->has('required_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="required_date">
                                            {!! $errors->first('required_date') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label">Types of Employment</label>
                            <div>
                                <select name="employee_type_id"
                                    class="form-control select2 @if ($errors->has('employee_type_id')) is-invalid @endif">
                                    <option value="">Select Type</option>
                                    @foreach ($employeeTypes as $employeeType)
                                        <option value="{{ $employeeType->id }}"
                                            @if (old('employee_type_id') == $employeeType->id) selected @endif>
                                            {{ $employeeType->getEmployeeType() }}</option>
                                    @endforeach
                                    <option value="0" @if (old('employee_type_id') == '0') selected @endif>
                                        Other
                                    </option>
                                </select>
                                @if ($errors->has('employee_type_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="employee_type_id">
                                            {!! $errors->first('employee_type_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @php
                            $employeeTypeBlock = 'display: none;';
                            if (old('employee_type_id') == '0') {
                                $employeeTypeBlock = '';
                            } else {
                                $employeeTypeBlock = 'display: none;';
                            }
                        @endphp
                        <div class="mb-2 col-lg-4" id="employeeTypeBlock" style="{{ $employeeTypeBlock }}">
                            <label class="form-label">Other Specify</label>
                            <div>
                                <input name="employee_type_other" value="{{ old('employee_type_other') }}"
                                    class="form-control @if ($errors->has('employee_type_other')) is-invalid @endif" />
                                @if ($errors->has('employee_type_other'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="employee_type_other">
                                            {!! $errors->first('employee_type_other') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2 col-lg-4">
                            <label class="required-label"> Is this position budgeted</label>
                            <div>
                                <select name="budgeted" class="form-control select2">
                                    <option value="1">Yes</option>
                                    <option value="0" @if (old('budgeted') == 0) selected @endif>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-2 col-lg-2">
                            <label class="form-label required-label">Activity Code</label>
                            <div>
                                <select name="activity_code_id"
                                    class="form-control select2 @if ($errors->has('activity_code_id')) is-invalid @endif">
                                    <option value="">Select Activity Code</option>
                                    @foreach ($activityCodes as $activityCode)
                                        <option value="{{ $activityCode->id }}"
                                            @if (old('activity_code_id') == $activityCode->id) selected @endif>
                                            {{ $activityCode->getActivityCodeWithDescription() }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('activity_code_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="activity_code_id">
                                            {!! $errors->first('activity_code_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-2">
                            <label class="form-label required-label">Account Code</label>
                            <div>
                                <select name="account_code_id"
                                    class="form-control select2 @if ($errors->has('account_code_id')) is-invalid @endif">
                                    <option value="">Select Account Code</option>
                                </select>
                                @if ($errors->has('account_code_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="account_code_id">
                                            {!! $errors->first('account_code_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label">Donor Code</label>
                            <div>
                                <select name="donor_code_id"
                                    class="form-control select2 @if ($errors->has('donor_code_id')) is-invalid @endif">
                                    <option value="">Select Donor Code</option>
                                    @foreach ($donorCodes as $donorCode)
                                        <option value="{{ $donorCode->id }}"
                                            @if (old('donor_code_id') == $donorCode->id) selected @endif>
                                            {{ $donorCode->getDonorCodeWithDescription() }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('donor_code_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="donor_code_id">
                                            {!! $errors->first('donor_code_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label">Duration</label>
                            <div>
                                <input type="text" name="duration" placeholder="Duration"
                                    class="form-control @if ($errors->has('duration')) is-invalid @endif"
                                    value="{{ old('duration') }}">
                                @if ($errors->has('duration'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="duration">
                                            {!! $errors->first('duration') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 col-lg-4">
                            <label class="form-label required-label">Work Load (Hours per week)</label>
                            <div>
                                <input type="number" name="work_load" placeholder="Work Load Hours per week"
                                    class="form-control @if ($errors->has('work_load')) is-invalid @endif"
                                    value="{{ old('work_load') }}">
                                @if ($errors->has('work_load'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="work_load">
                                            {!! $errors->first('work_load') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <div class="mb-2 col-lg-12">
                            <label class="form-label required-label">Reason for request</label>
                            <div>
                                <textarea name="reason_for_request" cols="30" rows="4"
                                    class="form-control @if ($errors->has('reason_for_request')) is-invalid @endif">{!! old('reason_for_request') !!}</textarea>
                                @if ($errors->has('reason_for_request'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="reason_for_request">
                                            {!! $errors->first('reason_for_request') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card">
                <div class="card-header fw-bold">Qualification</div>
                <div class="card-body">
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <label class="form-label required-label">Education</label>
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text required-label"
                                                id="basic-addon2">Required</span>
                                        </div>
                                        <select aria-describedby="basic-addon2"
                                            class="form-control @if ($errors->has('education_required')) is-invalid @endif"
                                            name="education_required">
                                            <option value="">Select Level</option>
                                            @foreach ($educationLevels as $level)
                                                <option value="{{ $level->title }}"
                                                    @if (old('education_required') == $level->title) selected @endif>{{ $level->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('education_required'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="education_required">
                                                    {!! $errors->first('education_required') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">Preferred</span>
                                        </div>
                                        <select aria-describedby="basic-addon2"
                                            class="form-control @if ($errors->has('education_preferred')) is-invalid @endif"
                                            name="education_preferred">
                                            <option value="0">Select Level</option>
                                            @foreach ($educationLevels as $level)
                                                <option value="{{ $level->title }}"
                                                    @if (old('education_preferred') == $level->title) selected @endif>{{ $level->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('education_preferred'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="education_preferred">
                                                    {!! $errors->first('education_preferred') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <label class="form-label required-label">Work Experience</label>
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text required-label"
                                                id="basic-addon2">Required</span>
                                        </div>
                                        <input aria-describedby="basic-addon2" name="experience_required"
                                            value="{{ old('experience_required') }}"
                                            class="form-control @if ($errors->has('experience_required')) is-invalid @endif" />
                                        @if ($errors->has('experience_required'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="experience_required">
                                                    {!! $errors->first('experience_required') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">Preferred</span>
                                        </div>
                                        <input aria-describedby="basic-addon2" name="experience_preferred"
                                            value="{{ old('experience_preferred') }}"
                                            class="form-control @if ($errors->has('experience_preferred')) is-invalid @endif" />
                                        @if ($errors->has('experience_preferred'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="experience_preferred">
                                                    {!! $errors->first('experience_preferred') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <label class="form-label required-label">Skill</label>
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text required-label"
                                                id="basic-addon2">Required</span>
                                        </div>
                                        <input aria-describedby="basic-addon2" name="skills_required"
                                            value="{{ old('skills_required') }}"
                                            class="form-control @if ($errors->has('skills_required')) is-invalid @endif" />
                                        @if ($errors->has('skills_required'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="skills_required">
                                                    {!! $errors->first('skills_required') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">Preferred</span>
                                        </div>
                                        <input aria-describedby="basic-addon2" name="skills_preferred"
                                            value="{{ old('skills_preferred') }}"
                                            class="form-control @if ($errors->has('skills_preferred')) is-invalid @endif" />
                                        @if ($errors->has('skills_preferred'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="skills_preferred">
                                                    {!! $errors->first('skills_preferred') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header fw-bold">Other Information</div>
                <div class="card-body">
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">TOR/ JD Submitted</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-select select2" name="tor_jd_submitted">
                                <option value="1">Yes</option>
                                <option value="0" @if (old('tor_jd_submitted') == 0) selected @endif>No
                                </option>
                            </select>
                        </div>
                    </div>
                    @php
                        if (old('tor_jd_submitted') == 0) {
                            $attachment_block = 'display: none;';
                            $tentative_date = '';
                        } else {
                            $attachment_block = '';
                            $tentative_date = 'display: none;';
                        }
                    @endphp
                    <div class="mb-2 row" id="attachment_block" style="{{ $attachment_block }}">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="fileattch" class="form-label required-label">Attach File(s)</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="file"
                                class="form-control  @if ($errors->has('attachment')) is-invalid @endif" id="fileattch"
                                name="attachment">
                            <small>Supported file types jpeg/jpg/png/pdf/docx and file size of upto 2MB.</small>
                            @if ($errors->has('attachment'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="attachment">
                                        {!! $errors->first('attachment') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2 row" id="tentative_date" style='{{ $tentative_date }}'>
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">If no, tentative date
                                    of submission</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input name="tentative_submission_date" readonly
                                class="form-control @if ($errors->has('tentative_submission_date')) is-invalid @endif"
                                value="{{ old('tentative_submission_date') }}" />
                            @if ($errors->has('tentative_submission_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="tentative_submission_date">
                                        {!! $errors->first('tentative_submission_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">Logistics Requirements
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea name="logistics_requirement" cols="30" rows="5"
                                class="form-control @if ($errors->has('logistics_requirement')) is-invalid @endif">{!! old('logistics_requirement') !!}</textarea>
                            @if ($errors->has('logistics_requirement'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="logistics_requirement">
                                        {!! $errors->first('logistics_requirement') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label">Reviewer
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            @php $selectedReviewerId = old('reviewer_id') @endphp
                            <select name="reviewer_id"
                                class="select2 form-control @if ($errors->has('reviewer_id')) is-invalid @endif">
                                <option value="">Select Reviewer</option>
                                @foreach ($reviewers as $reviewer)
                                    <option value="{{ $reviewer->id }}"
                                        @if ($selectedReviewerId == $reviewer->id) selected @endif>{{ $reviewer->getFullName() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('reviewer_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="reviewer_id">
                                        {!! $errors->first('reviewer_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start justify-content-start justify-content-md-end h-100">
                                <label for="acccde" class="form-label required-label">Approver</label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            @php $selectedApproverId = old('approver_id') @endphp
                            <select name="approver_id"
                                class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif">
                                <option value="">Select Approver</option>
                                @foreach ($approvers as $approver)
                                    <option value="{{ $approver->id }}"
                                        @if ($selectedApproverId == $approver->id) selected @endif>{{ $approver->getFullName() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('approver_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="approver_id">
                                        {!! $errors->first('approver_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="gap-2 mt-4 justify-content-end d-flex">
                <button type="submit" value="save" name="btn" class="btn btn-primary btn-sm">Save</button>
                <button type="submit" value="submit" name="btn" class="btn btn-success btn-sm">Submit
                </button>
                <a href="{!! route('employee.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
            {!! csrf_field() !!}
        </form>
    </section>
@stop
