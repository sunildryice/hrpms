@extends('layouts.container')

@section('title', 'Edit Employee Requisition')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#employee-requests-menu').addClass('active');
            const stringLimit = 191;
            const form = document.getElementById('employeeRequestEditForm');
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
                                message: 'The selected file is not valid type or must not be greater than 2 MB.',
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
                            htmlToReplace += '<option value="' + accountCode.id + '">' +
                                accountCode.title + ' ' + accountCode.description + '</option>';
                        });
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
                    $(form).find('[name="tentative_submission_date"]').val('');

                } else {
                    $(form).find('#attachment_block').hide();
                    $(form).find('#tentative_date').show();
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
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
            }).on('change', function(e) {
                fv.revalidateField('required_date');
            });

            $('[name="tentative_submission_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}',
            }).on('change', function(e) {
                fv.revalidateField('tentative_submission_date');
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
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
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <section class="registration">
        <form action="{{ route('employee.requests.update', $employeeRequest->id) }}" id="employeeRequestEditForm"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="card">
                <div class="card-header fw-bold">General Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label">Position</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" name="position_title"
                                    value="{{ old('position_title') ?: $employeeRequest->position_title }}"
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3"><label class="form-label required-label"> For FY</label></div>
                            <div class="col-lg-9">
                                @php $selectedFy = old('fiscal_year_id') ?: $employeeRequest->fiscal_year_id @endphp
                                <select name="fiscal_year_id"
                                    class="form-control select2 @if ($errors->has('fiscal_year_id')) is-invalid @endif">
                                    <option value="">Select FY</option>
                                    @foreach ($fiscalYears as $year)
                                        <option value="{{ $year->id }}"
                                            @if ($selectedFy == $year->id) selected @endif>{{ $year->getFiscalYear() }}
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label"> Requested Level</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" name="position_level"
                                    value="{{ old('position_level') ?: $employeeRequest->position_level }}"
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label"> Replacement for</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" name="replacement_for"
                                    value="{{ old('replacement_for') ?: $employeeRequest->replacement_for }}"
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label"> {{ __('label.duty-station') }}</label>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedDutyStationId = old('duty_station_id') ?: $employeeRequest->duty_station_id @endphp
                                <select name="duty_station_id"
                                    class="form-control select2 @if ($errors->has('duty_station_id')) is-invalid @endif">
                                    <option value="">Select {{ __('label.duty-station') }}</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}"
                                            @if ($selectedDutyStationId == $district->id) selected @endif>
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label"> Date required from</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text"
                                    class="form-control @if ($errors->has('required_date')) is-invalid @endif" readonly
                                    value="{{ old('required_date') ?: $employeeRequest->required_date }}"
                                    name="required_date">
                                @if ($errors->has('required_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="required_date">
                                            {!! $errors->first('required_date') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label">Types of Employment</label>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedEmployeeTypeId = old('employee_type_id') ?: $employeeRequest->employee_type_id @endphp
                                <select name="employee_type_id"
                                    class="form-control select2 @if ($errors->has('employee_type_id')) is-invalid @endif">
                                    <option value="">Select Type</option>
                                    @foreach ($employeeTypes as $employeeType)
                                        <option value="{{ $employeeType->id }}"
                                            @if ($selectedEmployeeTypeId == $employeeType->id) selected @endif>
                                            {{ $employeeType->getEmployeeType() }}</option>
                                    @endforeach
                                    <option value="0" @if ($selectedEmployeeTypeId == 0) selected @endif>
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
                            if ($selectedEmployeeTypeId == '0') {
                                $employeeTypeBlock = '';
                            } else {
                                $employeeTypeBlock = 'display: none;';
                            }
                        @endphp

                        <div class="col-lg-6 mb-2" id="employeeTypeBlock" style="{{ $employeeTypeBlock }}">
                            <div class="col-lg-3">
                                <label class="form-label">Other Specify</label>
                            </div>
                            <div class="col-lg-9">
                                <input name="employee_type_other"
                                    value="{{ old('employee_type_other') ?: $employeeRequest->employee_type_other }}"
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

                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label"> Is this position budgeted</label>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedBudgeted = old('budgeted') ?: $employeeRequest->budgeted @endphp
                                <select name="budgeted" class="form-control select2">
                                    <option value="1">Yes</option>
                                    <option value="0" @if ($selectedBudgeted == 0) selected @endif>No
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label">Activity Code</label>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedActivityCodeId = old('activity_code_id') ?: $employeeRequest->activity_code_id @endphp
                                <select name="activity_code_id"
                                    class="form-control select2 @if ($errors->has('activity_code_id')) is-invalid @endif">
                                    <option value="">Select Activity Code</option>
                                    @foreach ($activityCodes as $activityCode)
                                        <option value="{{ $activityCode->id }}"
                                            @if ($selectedActivityCodeId == $activityCode->id) selected @endif>
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label">Account Code</label>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedAccountCodeId = old('account_code_id') ?: $employeeRequest->account_code_id @endphp
                                <select name="account_code_id"
                                    class="form-control select2 @if ($errors->has('account_code_id')) is-invalid @endif">
                                    <option value="">Select Account Code</option>
                                    @foreach ($accountCodes as $accountCode)
                                        <option value="{{ $accountCode->id }}"
                                            @if ($selectedAccountCodeId == $accountCode->id) selected @endif>
                                            {{ $accountCode->getAccountCodeWithDescription() }}</option>
                                    @endforeach
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label">Donor Code</label>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedDonorCodeId = old('donor_code_id') ?: $employeeRequest->donor_code_id @endphp
                                <select name="donor_code_id"
                                    class="form-control select2 @if ($errors->has('donor_code_id')) is-invalid @endif">
                                    <option value="">Select Donor Code</option>
                                    @foreach ($donorCodes as $donorCode)
                                        <option value="{{ $donorCode->id }}"
                                            @if ($selectedDonorCodeId == $donorCode->id) selected @endif>
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
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-4">
                                <label class="form-label required-label">Work Load (Hours per week)</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="number" name="work_load" placeholder="Work Load Hours per week"
                                    class="form-control @if ($errors->has('work_load')) is-invalid @endif"
                                    value="{{ old('work_load') ?: $employeeRequest->work_load }}">
                                @if ($errors->has('work_load'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="work_load">
                                            {!! $errors->first('work_load') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">Duration</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" name="duration" placeholder="Duration"
                                    class="form-control @if ($errors->has('duration')) is-invalid @endif"
                                    value="{{ old('duration') ?: $employeeRequest->duration }}">
                                @if ($errors->has('duration'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="duration">
                                            {!! $errors->first('duration') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-12 mb-2">
                            <div class="col-lg-3">
                                <label class="form-label required-label">Reason for request</label>
                            </div>
                            <div class="col-lg-12">
                                <textarea name="reason_for_request" cols="30" rows="4"
                                    class="form-control @if ($errors->has('reason_for_request')) is-invalid @endif">{!! old('reason_for_request') ?: $employeeRequest->reason_for_request !!}</textarea>
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
                    <div class="row mb-2">
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
                                        @php $selectedEducationRequired = old('education_required') ?: $employeeRequest->education_required @endphp
                                        <select aria-describedby="basic-addon2"
                                            class="form-control @if ($errors->has('education_required')) is-invalid @endif"
                                            name="education_required">
                                            <option value="">Select Level</option>
                                            @foreach ($educationLevels as $level)
                                                <option value="{{ $level->title }}"
                                                    @if ($selectedEducationRequired == $level->title) selected @endif>{{ $level->title }}
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
                                        @php $selectedEducationPreferred = old('education_preferred') ?: $employeeRequest->education_preferred @endphp
                                        <select aria-describedby="basic-addon2"
                                            class="form-control @if ($errors->has('education_preferred')) is-invalid @endif"
                                            name="education_preferred">
                                            <option value="0">Select Level</option>
                                            @foreach ($educationLevels as $level)
                                                <option value="{{ $level->title }}"
                                                    @if ($selectedEducationPreferred == $level->title) selected @endif>{{ $level->title }}
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
                    <div class="row mb-2">
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
                                            value="{{ old('experience_required') ?: $employeeRequest->experience_required }}"
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
                                            value="{{ old('experience_preferred') ?: $employeeRequest->experience_preferred }}"
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
                    <div class="row mb-2">
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
                                            value="{{ old('skills_required') ?: $employeeRequest->skills_required }}"
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
                                            value="{{ old('skills_preferred') ?: $employeeRequest->skills_preferred }}"
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
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">TOR/ JD Submitted</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php $selectedTorSubmitted = old('tor_jd_submitted') ?: $employeeRequest->tor_jd_submitted @endphp
                            <select class="form-select select2" name="tor_jd_submitted">
                                <option value="1">Yes</option>
                                <option value="0" @if ($selectedTorSubmitted == 0) selected @endif>No
                                </option>
                            </select>
                        </div>
                    </div>
                    @php
                        if ($selectedTorSubmitted == 0) {
                            $attachment_block = 'display: none;';
                            $tentative_date = '';
                        } else {
                            $attachment_block = '';
                            $tentative_date = 'display: none;';
                        }

                        if ($attachment == '') {
                            $attachment_exist = 'display: none;';
                            $class = 'col-lg-9';
                            $class_media = '';
                        } else {
                            $attachment_exist = '';
                            $class = 'col-lg-7';
                            $class_media = 'col-lg-2';
                        }
                    @endphp
                    <div class="row mb-2" id="attachment_block" style='{{ $attachment_block }}'>
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="fileattch" class="form-label required-label">Attach File(s)</label>
                            </div>
                        </div>
                        <div class="{{ $class }}">
                            <input type="file"
                                class="form-control  @if ($errors->has('attachment')) is-invalid @endif" id="fileattch"
                                name="attachment">
                            <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                            @if ($errors->has('attachment'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="attachment">
                                        {!! $errors->first('attachment') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="media {{ $class_media }}" style="{{ $attachment_exist }}">
                            <a href="{{ $attachment }}" target="_blank" name='attachment_exist' class="fs-5"
                                title="View Attachment">
                                <i class="bi bi-file-earmark-medical"></i>
                            </a>
                        </div>
                    </div>

                    <div class="row mb-2" id="tentative_date" style='{{ $tentative_date }}'>
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">If no, tentative date
                                    of submission</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input name="tentative_submission_date" readonly
                                class="form-control @if ($errors->has('tentative_submission_date')) is-invalid @endif"
                                value="{{ old('tentative_submission_date') ?: $employeeRequest->tentative_submission_date }}" />
                            @if ($errors->has('tentative_submission_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="tentative_submission_date">
                                        {!! $errors->first('tentative_submission_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">Logistics Requirements
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea name="logistics_requirement" cols="30" rows="5"
                                class="form-control @if ($errors->has('logistics_requirement')) is-invalid @endif">{!! old('logistics_requirement') ?: $employeeRequest->logistics_requirement !!}</textarea>
                            @if ($errors->has('logistics_requirement'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="logistics_requirement">
                                        {!! $errors->first('logistics_requirement') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">Reviewer
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            @php $selectedReviewerId = old('reviewer_id') ?: $employeeRequest->reviewer_id @endphp
                            <select name="reviewer_id"
                                class="select2 form-control @if ($errors->has('logistics_requirement')) is-invalid @endif">
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
                            <div class="d-flex align-items-start h-100">
                                <label for="acccde" class="form-label required-label">Approver
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            @php $selectedApproverId = old('approver_id') ?: $employeeRequest->approver_id @endphp
                            <select name="approver_id"
                                class="select2 form-control @if ($errors->has('logistics_requirement')) is-invalid @endif">
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
            <div class="justify-content-end d-flex gap-2">
                <button type="submit" value="save" name="btn" class="btn btn-primary btn-sm">Save</button>
                <button type="submit" value="submit" name="btn" class="btn btn-success btn-sm">Submit
                </button>
                <a href="{!! route('employee.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
            {!! csrf_field() !!}
            {!! method_field('PUT') !!}
        </form>
    </section>
@stop
