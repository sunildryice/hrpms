@extends('layouts.container')

@section('title', 'Add New Construction Track')

@section('page_js')
    <script type="text/javascript">
        $(function () {
            $('#navbarVerticalMenu').find('#construction-index').addClass('active');

            const form = document.getElementById('constructionAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    signed_date: {
                        validators: {
                            notEmpty: {
                                message: 'Signed date is required.'
                            }
                        }
                    },
                    health_facility_name: {
                        validators: {
                            notEmpty: {
                                message: 'Health facility name is required.'
                            }
                        }
                    },
                    facility_type: {
                        validators: {
                            notEmpty: {
                                message: 'Facility type is required.'
                            }
                        }
                    },
                    type_of_work: {
                        validators: {
                            notEmpty: {
                                message: 'Type of work is required.'
                            }
                        }
                    },
                    province_id: {
                        validators: {
                            notEmpty: {
                                message: 'Province is required',
                            },
                        },
                    },
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'District is required',
                            },
                        },
                    },
                    local_level_id: {
                        validators: {
                            notEmpty: {
                                message: 'Local Level is required',
                            },
                        },
                    },
                    engineer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Engineer name is required.'
                            }
                        }
                    },
                    effective_date_from: {
                        validators: {
                            notEmpty: {
                                message: 'Effective Date From is required',
                            },
                        },
                    },
                    effective_date_to: {
                        validators: {
                            notEmpty: {
                                message: 'Effective Date To is required',
                            },
                        },
                    },
                    ohw_contribution: {
                        validators: {
                            notEmpty: {
                                message: 'Ohw Contribution Amount is required',
                            },
                            // lessThan: {
                            //     message: 'The value must be less than or equal to 100',
                            //     max: 100,
                            // },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0',
                                min: 0,
                            }
                        },
                    },
                    work_start_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    work_completion_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
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
                            field: 'effective_date_from',
                            message: 'Effective Date From must be a valid date and earlier than Effective Date To.',
                        },
                        endDate: {
                            field: 'effective_date_to',
                            message: 'Effective Date To must be a valid date and later than Effective Date From.',
                        },
                    }),
                    signedDateEffectiveDateFrom: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'signed_date',
                            message: 'Signed Date must be a valid date and earlier than Effective Date From.',
                        },
                        endDate: {
                            field: 'effective_date_from',
                            message: 'Effective Date From must be a valid date and later than signed date.',
                        },
                    }),
                    workStartDateWorkCompletionDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'work_start_date',
                            message: 'Work start date must be a valid date and earlier than work completion date.',
                        },
                        endDate: {
                            field: 'work_completion_date',
                            message: 'Work end date must be a valid date and later than work start date.',
                        },
                    }),
                    signedDateWorkStartDateFrom: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'signed_date',
                            message: 'Signed date must be a valid date and earlier than work start date.',
                        },
                        endDate: {
                            field: 'work_start_date',
                            message: 'Work start date must be a valid date and later than signed date.',
                        },
                    }),
                },
            });

            $(form).on('change', '[name="district_id"]', function (e) {
                $element = $(this);
                var districtId = $element.val();
                var htmlToReplace = '<option value="">Select a Local Level</option>';
                if (districtId) {
                    var url = baseUrl + '/api/master/districts/' + districtId;
                    var successCallback = function (response) {
                        response.localLevels.forEach(function (localLevel) {
                            htmlToReplace += '<option value="' + localLevel.id + '">' + localLevel.local_level_name + '</option>';
                        });
                        $($element).closest('form').find('[name="local_level_id"]').html(htmlToReplace).trigger('change');
                        $($element).closest('form').find('[name="local_level_id"]').select2('destroy').select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="local_level_id"]').html(htmlToReplace);
                }
                fv.revalidateField('district_id');
            }).on('change', '[name="province_id"]', function (e) {
                $element = $(this);
                var provinceId = $element.val();
                var htmlToReplace = '<option value="">Select a District</option>';
                if (provinceId) {
                    var url = baseUrl + '/api/master/provinces/' + provinceId;
                    var successCallback = function (response) {
                        response.districts.forEach(function (district) {
                            htmlToReplace += '<option value="' + district.id + '">' + district.district_name + '</option>';
                        });
                        $($element).closest('form').find('[name="district_id"]').html(htmlToReplace).trigger('change');
                        $($element).closest('form').find('[name="district_id"]').select2('destroy').select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="district_id"]').html(htmlToReplace);
                }
                fv.revalidateField('province_id');
            }).on('change', '[name="local_level_id"]', function (e) {
                fv.revalidateField('local_level_id');
            }).on('change', '[name="engineer_id"]', function (e) {
                fv.revalidateField('engineer_id');
            });

            $('[name="signed_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('signed_date');
                fv.revalidateField('effective_date_from');
            });

            $('[name="effective_date_from"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                    {{--startDate: '{{ date('Y - m - d') }}', --}}
                }).on('change', function (e) {
                    fv.revalidateField('signed_date');
                    fv.revalidateField('effective_date_from');
                    fv.revalidateField('effective_date_to');
                });

        $('[name="effective_date_to"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
                    {{--startDate: '{{ date('Y - m - d') }}', --}}
                }).on('change', function (e) {
                fv.revalidateField('effective_date_from');
                fv.revalidateField('effective_date_to');
            });

        $('[name="work_start_date"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
            zIndex: 2048,
        }).on('change', function (e) {
            fv.revalidateField('work_start_date');
        });

        $('[name="work_completion_date"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
            zIndex: 2048,
        }).on('change', function (e) {
            fv.revalidateField('work_completion_date');
        });
            });
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
                                <a href="{!! route('dashboard.index') !!}"
                                    class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('construction.index') }}"
                                    class="text-decoration-none">Construction</a>
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

                <div class="col-lg-12">
                    <div class="card">
                        <form action="{{ route('construction.store') }}" id="constructionAddForm" method="post"
                            enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">


                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="validationRemarks" class="m-0">General Information</label>
                                    </div>
                                </div>

                                <div class="row mb-2">

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Signed
                                                Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if($errors->has('signed_date')) is-invalid @endif"
                                            type="text" name="signed_date" value="{{ old('signed_date') }}" />
                                        @if($errors->has('signed_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="signed_date">
                                                    {!! $errors->first('signed_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Nepali Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if($errors->has('nepali_date')) is-invalid @endif"
                                            type="text" readonly name="nepali_date" value="{{ old('nepali_date') }}" />
                                        @if($errors->has('nepali_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="nepali_date">
                                                    {!! $errors->first('nepali_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Health Facility
                                                Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if($errors->has('health_facility_name')) is-invalid @endif"
                                            type="text" name="health_facility_name"
                                            value="{{ old('health_facility_name') }}" />
                                        @if($errors->has('health_facility_name'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="health_facility_name">
                                                    {!! $errors->first('health_facility_name') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Effective Date
                                                AD
                                                From</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if($errors->has('effective_date_from')) is-invalid @endif"
                                            type="text" name="effective_date_from"
                                            value="{{ old('effective_date_from') }}" />
                                        @if($errors->has('effective_date_from'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_from">
                                                    {!! $errors->first('effective_date_from') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Type of
                                                Facility</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if($errors->has('facility_type')) is-invalid @endif"
                                            type="text" name="facility_type" value="{{ old('facility_type') }}" />
                                        @if($errors->has('facility_type'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="facility_type">
                                                    {!! $errors->first('facility_type') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Effective Date BS From</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if($errors->has('effective_date_from_bs')) is-invalid @endif"
                                            type="text" readonly name="effective_date_from_bs"
                                            value="{{ old('effective_date_from_bs') }}" />
                                        @if($errors->has('effective_date_from_bs'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_from_bs">
                                                    {!! $errors->first('effective_date_from_bs') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Type of
                                                Work</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if($errors->has('type_of_work')) is-invalid @endif"
                                            type="text" name="type_of_work" value="{{ old('type_of_work') }}" />
                                        @if($errors->has('type_of_work'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="type_of_work">
                                                    {!! $errors->first('type_of_work') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Effective Date
                                                AD to</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if($errors->has('effective_date_to')) is-invalid @endif"
                                            type="text" name="effective_date_to"
                                            value="{{ old('effective_date_to') }}" />
                                        @if($errors->has('effective_date_to'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_to">
                                                    {!! $errors->first('effective_date_to') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Province</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <select name="province_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Province</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}" data-purchase="{{ $province->id }}" {{ $province->id == old('province_id') ? "selected" : "" }}>
                                                    {{ $province->getProvinceName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('province_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="province_id">
                                                    {!! $errors->first('province_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Effective Date BS To</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if($errors->has('effective_date_bs_to')) is-invalid @endif"
                                            type="text" readonly name="effective_date_bs_to"
                                            value="{{ old('effective_date_bs_to') }}" />
                                        @if($errors->has('effective_date_bs_to'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_bs_to">
                                                    {!! $errors->first('effective_date_bs_to') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">District</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <select name="district_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a District</option>
                                            @foreach($districts as $district)
                                                <option value="{{ $district->id }}" data-purchase="{{ $district->id }}" {{ $district->id == old('district_id') ? "selected" : "" }}>
                                                    {{ $district->getDistrictName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('district_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="district_id">
                                                    {!! $errors->first('district_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">OHW
                                                Contribution Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if($errors->has('ohw_contribution')) is-invalid @endif"
                                            type="number" name="ohw_contribution"
                                            value="{{ old('ohw_contribution') }}" />
                                        @if($errors->has('ohw_contribution'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="ohw_contribution">
                                                    {!! $errors->first('ohw_contribution') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Local
                                                Level</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <select name="local_level_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Local Level</option>
                                            @foreach($localLevels as $local)
                                                <option value="{{ $local->id }}" data-purchase="{{ $local->id }}" {{ $local->id == old('local_level_id') ? "selected" : "" }}>
                                                    {{ $local->getLocalLevelName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('local_level_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="local_level_id">
                                                    {!! $errors->first('local_level_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Approval</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class=" form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="physicallyabled" name="approval" checked="">
                                            <label class="form-check-label" for="physicallyabled"></label>
                                        </div>
                                    </div> --}}

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Engineer
                                                Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        {{-- <input type="text" class="form-control" name="engineer_name"> --}}

                                        <select class="select2" name="engineer_id" id="engineer_id">
                                            <option value="" selected disabled>Select engineer</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}">{{$employee->getFullName()}}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('engineer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="engineer_id">
                                                    {!! $errors->first('engineer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>


                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="work_start_date" class="m-0">Work Start Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text"
                                            class="form-control @if($errors->has('work_start_date')) is-invalid @endif"
                                            name="work_start_date" value="" placeholder="Work start date">
                                        @if($errors->has('work_start_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="work_start_date">
                                                    {!! $errors->first('work_start_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="work_completion_date" class="m-0">Work Completion Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text"
                                            class="form-control @if($errors->has('work_completion_date')) is-invalid @endif"
                                            name="work_completion_date" value="" placeholder="Work completion date">
                                        @if($errors->has('work_completion_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="work_completion_date">
                                                    {!! $errors->first('work_completion_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                {{-- <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="donor_codes" class="m-0">Donors</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <select
                                            class="form-control select2 @if($errors->has('donor_codes')) is-invalid @endif"
                                            name="donor_codes[]" id="donor_codes" multiple>
                                            @foreach ($donors as $donor)
                                            <option value="{{$donor->id}}">{{$donor->getDonorCodeWithDescription()}}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('donor_codes'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="donor_codes">
                                                {!! $errors->first('donor_codes') !!}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="metal_plaque_text" class="m-0">Metal Plaque Text</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text"
                                            class="form-control @if($errors->has('metal_plaque_text')) is-invalid @endif"
                                            name="metal_plaque_text" placeholder="Metal Plaque Text">
                                        @if($errors->has('metal_plaque_text'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="metal_plaque_text">
                                                {!! $errors->first('metal_plaque_text') !!}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div> --}}


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="donor" class="m-0">Donors</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text"
                                            class="form-control @if($errors->has('donor')) is-invalid @endif"
                                            name="donor" placeholder="Donor">

                                        @if($errors->has('donor_codes'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="donor_codes">
                                                    {!! $errors->first('donor_codes') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="metal_plaque_text" class="m-0">Metal Plaque Text</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <textarea
                                            class="form-control @if($errors->has('metal_plaque_text')) is-invalid @endif"
                                            placeholder="Metal Plaque Text" name="metal_plaque_text"
                                            id="metal_plaque_text" rows="2"></textarea>
                                        @if($errors->has('metal_plaque_text'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="metal_plaque_text">
                                                    {!! $errors->first('metal_plaque_text') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if (auth()->user()->can('manage-cluster'))
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="form-label required-label">Cluster</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            @php
                                                $selected = old('office_id');
                                            @endphp
                                            <select name="office_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select an Office</option>
                                                @foreach ($offices as $local)
                                                    <option value="{{ $local->id }}" {{ $local->id == $selected ? 'selected' : '' }}>
                                                        {{ $local->office_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('local_level_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="local_level_id">
                                                        {!! $errors->first('local_level_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {!! csrf_field() !!}
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                                </button>
                                <a href="{!! route('construction.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </section>

    </div>
</div>

@stop