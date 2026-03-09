@extends('layouts.container')

@section('title', 'Add New Vehicle Request')

@section('page_css')

@endsection
@section('page_js')
    <script type="text/javascript">
        const sevenDaysAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
        $('.filter-items-radio').on('click', function() {
            // //
            var chk_box = $(this).find('.f-check-input');
            obj = document.getElementsByClassName("filter-items-radio");

            var chk_status = !(chk_box.is(':checked'));
            console.log(chk_status);
            var chkdata = $(this).data("id");
            chk_box.attr('checked', chk_status);
            if (chk_status == true) {
                $(obj).removeClass('active');
                $(obj).find('i').removeClass('bi-check-square-fill').addClass('bi-square');
                $(this).find('i').removeClass('bi-square').addClass('bi-check-square-fill');
                $(this).addClass('active');
            }
        });

        $('[data-toggle="datepicker-time"]').daterangepicker({
            "singleDatePicker": true,
            "timePicker": true,
            "timePicker24Hour": true,
            "autoApply": false,
            "minDate": sevenDaysAgo,
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });

        $('[name="pickup_time"]').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            locale: {
                format: 'HH:mm'
            }
        }).on('show.daterangepicker', function(ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

        $('[data-toggle="datepicker-range"]').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
            },
            "minDate": "{!! date('m/d/Y') !!}",
            "autoApply": false,
            "drops": "auto",
        }, function(start, end, label) {
            $('form').find('[name="start_datetime"]').val(start.format('YYYY-MM-DD'));
            $('form').find('[name="end_datetime"]').val(end.format('YYYY-MM-DD'));
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        $('.filter-items').on('click', function() {
            var chk_box = $(this).find('.f-check-input');

            var chk_status = !(chk_box.is(':checked'));
            var chkdata = $(this).data("id");

            chk_box.attr('checked', chk_status);
            $(this).find('i').toggleClass('bi-check-square-fill').toggleClass('bi-square');
            $(this).toggleClass('active');
        });
        var $selectedTab = '{!! old('tab') ?: 'office-vehicle' !!}';
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#vehicle-requests-menu').addClass('active');
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

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('officeVehicleRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Project is required',
                            },
                        },
                    },
                    office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office is required',
                            },
                        },
                    },
                    vehicle_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Vehicle Type is required',
                            },
                        },
                    },
                    start_datetime: {
                        validators: {
                            notEmpty: {
                                message: 'The start date time is required',
                            },
                        },
                    },
                    end_datetime: {
                        validators: {
                            notEmpty: {
                                message: 'The end date time is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is required',
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

            $(form).on('change', '[name="vehicle_type_id"]', function(e) {
                fv.revalidateField('vehicle_type_id');
            }).on('change', '[name="approver_id"]', function(e) {
                fv.revalidateField('approver_id');
            }).on('change', '[name="office_id"]', function(e) {
                fv.revalidateField('office_id');
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('hireVehicleRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Project is required',
                            },
                        },
                    },
                    vehicle_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Vehicle Type is required',
                            },
                        },
                    },
                    start_datetime: {
                        validators: {
                            notEmpty: {
                                message: 'The start date time is required',
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

            $(form).on('change', '[name="vehicle_type_id"]', function(e) {
                fv.revalidateField('vehicle_type_id');
            }).on('change', '[name="approver_id"]', function(e) {
                fv.revalidateField('approver_id');
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
                            <a href="{{ route('vehicle.requests.index') }}" class="text-decoration-none text-dark">Vehicle
                                Requests</a>
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
            <div class="col-lg-3 mb-3">
                <div class="rounded border shadow-sm vertical-navigation pt-3 pb-3">
                    <ul class="m-0 list-unstyled v-mneu">
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item  text-decoration-none" data-tag="office-vehicle">
                                <i class="nav-icon bi-truck"></i> Office Vehicle
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="hire-vehicle">
                                <i class="nav-icon bi-truck-flatbed"></i> Hire Vehicle</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card c-tabs-content" id="office-vehicle">
                    <div class="card-header fw-bold">Office vehicle Request Form</div>
                    <form action="{{ route('vehicle.requests.store') }}" id="officeVehicleRequestAddForm" method="post"
                        enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationProject" class="form-label required-label">Project
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="project_code_id"
                                        class="select2 form-control
                                        @if ($errors->has('project_code_id')) is-invalid @endif"
                                        data-width="100%">
                                        <option value="">Select Project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ $project->id == old('project_code_id') ? 'selected' : '' }}>
                                                {{ $project->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('project_code_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="project_code_id">
                                                {!! $errors->first('project_code_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Office </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="office_id"
                                        class="form-control select2 @if ($errors->has('office_id')) is-invalid @endif">
                                        <option value="">Select Office</option>
                                        @foreach ($offices as $office)
                                            <option @if (old('office_id') == $office->id) selected @endif
                                                value="{{ $office->id }}">{{ $office->office_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('office_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="office_id">
                                                {!! $errors->first('office_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationfullname" class="form-label required-label">Vehicle
                                            Type</label>
                                    </div>

                                </div>
                                <div class="col-lg-9">
                                    <select class="form-control select2 @if ($errors->has('vehicle_type_id')) is-invalid @endif"
                                        name="vehicle_type_id">
                                        <option value="">Select Vehicle Type</option>
                                        @foreach ($selectiveVehicleTypes as $vehicleType)
                                            <option value="{{ $vehicleType->id }}"
                                                @if (old('vehicle_type_id') == $vehicleType->id) selected @endif>{{ $vehicleType->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('vehicle_type_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="vehicle_type_id">
                                                {!! $errors->first('vehicle_type_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Date </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">From</span>
                                                </div>
                                                <input data-toggle="datepicker-time" type="text"
                                                    name="office_start_datetime" value="{!! old('office_start_datetime') !!}"
                                                    class="form-control @if ($errors->has('office_start_datetime')) is-invalid @endif">
                                                @if ($errors->has('office_start_datetime'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="office_start_datetime">
                                                            {!! $errors->first('office_start_datetime') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group has-validation">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">To</span>
                                                </div>
                                                <input data-toggle="datepicker-time" type="text"
                                                    name="office_end_datetime" value="{!! old('office_end_datetime') !!}"
                                                    class="form-control @if ($errors->has('office_end_datetime')) is-invalid @endif">
                                                @if ($errors->has('office_end_datetime'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="office_end_datetime">
                                                            {!! $errors->first('office_end_datetime') !!}
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
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Purpose </label>
                                    </div>
                                </div>
                                <div class="col-lg-9 mb-2">
                                    <input type="text" name="purpose_of_travel" value="{{ old('purpose_of_travel') }}"
                                        class="form-control @if ($errors->has('purpose_of_travel')) is-invalid @endif" />
                                    @if ($errors->has('purpose_of_travel'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="purpose_of_travel">
                                                {!! $errors->first('purpose_of_travel') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Travel </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">From</span>
                                                </div>
                                                <input type="text" class="form-control" name="travel_from"
                                                    placeholder="Travel From" value="{{ old('travel_from') }}"
                                                    aria-label="Travel From" aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group has-validation">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Destination</span>
                                                </div>
                                                <input type="text" class="form-control fv-plugins-icon-input"
                                                    name="destination" placeholder="Destination Point"
                                                    value="{{ old('destination') }}" aria-label="Destination Point"
                                                    aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validateusers" class="form-label">Accompanying Staff
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="employee_ids[]"
                                        class="form-control select2 @if ($errors->has('employee_ids')) is-invalid @endif"
                                        multiple>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                @if (old('employee_ids') && in_array($employee->id, old('employee_ids'))) selected @endif>
                                                {{ $employee->getFullNameWithCode() }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('employee_ids'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="employee_ids">
                                                {!! $errors->first('employee_ids') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Pick Up </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Time</span>
                                                </div>
                                                <input type="text" class="form-control" name="pickup_time"
                                                    value="{{ old('pickup_time') }}" readonly aria-label="Pick Up time"
                                                    aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group has-validation">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Location</span>
                                                </div>
                                                <input type="text" class="form-control fv-plugins-icon-input"
                                                    name="pickup_place" placeholder="Pick Up Location"
                                                    value="{{ old('pickup_place') }}" aria-label="Pick Up Location"
                                                    aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Remarks </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea rows="5" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') }}</textarea>
                                    @if ($errors->has('remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="remarks">
                                                {!! $errors->first('remarks') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">{{ __('label.send-to') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="approver_id"
                                        class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif"
                                        data-width="100%">
                                        <option value="">Select a Approver</option>
                                        @foreach ($approvers as $approver)
                                            <option value="{{ $approver->id }}"
                                                @if ($approver->id == old('approver_id')) selected="selected" @endif>
                                                {{ $approver->getFullName() }}
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
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                            </button>
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{{ route('vehicle.requests.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                        {!! csrf_field() !!}
                        <input name="vehicle_request_type_id" value="1" type="hidden" />
                        <input type="hidden" name="tab" value="office-vehicle" />
                    </form>
                </div>
                <div class="card c-tabs-content" id="hire-vehicle">
                    <div class="card-header fw-bold">Hire vehicle Request Form</div>
                    <form action="{{ route('vehicle.requests.store') }}" id="hireVehicleRequestAddForm" method="post"
                        enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationProject" class="form-label required-label">Project
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="project_code_id"
                                        class="select2 form-control
                                        @if ($errors->has('project_code_id')) is-invalid @endif"
                                        data-width="100%">
                                        <option value="">Select Project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ $project->id == old('project_code_id') ? 'selected' : '' }}>
                                                {{ $project->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('project_code_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="project_code_id">
                                                {!! $errors->first('project_code_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationfullname" class="form-label required-label">Date </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" data-toggle="datepicker-range"
                                        value="{{ old('start_end_datetime') }}"
                                        class="form-control @if ($errors->has('start_end_datetime')) is-invalid @endif"
                                        name="start_end_datetime">
                                    <input type="hidden" class="form-control" name="start_datetime"
                                        value="{{ old('start_datetime') ?: date('Y-m-d') }}">
                                    <input type="hidden" class="form-control" name="end_datetime"
                                        value="{{ old('end_datetime') ?: date('Y-m-d') }}">
                                    @if ($errors->has('start_datetime') || $errors->has('end_datetime'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="start_end_datetime">
                                                {!! $errors->first('start_datetime') !!}
                                                {!! $errors->first('end_datetime') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Purpose of Travel </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" name="purpose_of_travel"
                                        class="form-control @if ($errors->has('purpose_of_travel')) is-invalid @endif"
                                        value="{{ old('purpose_of_travel') }}" />
                                    @if ($errors->has('purpose_of_travel'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="purpose_of_travel">
                                                {!! $errors->first('purpose_of_travel') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationGender" class="form-label">Accompanying Staff
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="employee_ids[]"
                                        class="form-control select2 @if ($errors->has('employee_ids')) is-invalid @endif"
                                        multiple>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                @if (old('employee_ids') && in_array($employee->id, old('employee_ids'))) selected @endif>
                                                {{ $employee->getFullNameWithCode() }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('employee_ids'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="employee_ids">
                                                {!! $errors->first('employee_ids') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">Vehicle Type </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        @foreach ($vehicleTypes as $vehicleType)
                                            <div class="col-lg-6">
                                                <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                    data-id="value">
                                                    <span class="filter-check">
                                                        <input type="checkbox" class="f-check-input"
                                                            name="vehicle_type_ids[]" value="{{ $vehicleType->id }}">
                                                        <span class="filter-checkbox">
                                                            <i class="bi-square"></i>
                                                        </span>
                                                    </span>
                                                    <span class="filter-body">
                                                        {{ $vehicleType->title }}
                                                    </span>
                                                </span>
                                            </div>
                                        @endforeach
                                        <div class="col-lg-4">
                                            <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                data-id="value">
                                                <span class="filter-check">
                                                    <input type="checkbox" name="vehicle_type_ids[]" value="-1"
                                                        class="f-check-input"
                                                        @if (old('other_remarks')) checked @endif>
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="">Others</span>
                                                        <input type="text" name="other_remarks" class="form-control"
                                                            value="{{ old('other_remarks') }}">
                                                    </div>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    @if ($errors->has('vehicle_type_ids'))
                                        <div class="fv-plugins-message-container text-danger">
                                            <div data-field="vehicle_type_ids">
                                                {!! $errors->first('vehicle_type_ids') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100 mt-3">
                                        <label class="form-label">For</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <span class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100"
                                                data-id="value">
                                                <span class="filter-check">
                                                    <input type="radio" class="f-check-input" value="1"
                                                        name="for_hours_flag">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    Full Day
                                                </span>
                                            </span>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100"
                                                data-id="value">
                                                <span class="filter-check">
                                                    <input type="radio" class="f-check-input" value="2"
                                                        name="for_hours_flag">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    Half Day
                                                </span>
                                            </span>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100"
                                                data-id="value">
                                                <span class="filter-check">
                                                    <input type="radio" class="f-check-input" value="3"
                                                        name="for_hours_flag">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="">Hrs</span>
                                                        <input type="number" name="for_hours" class="form-control">
                                                    </div>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100"
                                                data-id="value">
                                                <span class="filter-check">
                                                    <input type="checkbox" class="f-check-input" value="4"
                                                        name="for_hours_flag">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="">Others</span>
                                                        <input type="text" name="for_hours_other_remarks"
                                                            class="form-control">
                                                    </div>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Pick Up </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Time</span>
                                                </div>
                                                <input type="text" class="form-control" name="pickup_time"
                                                    value="{{ old('pickup_time') }}" readonly aria-label="Pick Up time"
                                                    aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group has-validation">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Location</span>
                                                </div>
                                                <input type="text" class="form-control fv-plugins-icon-input"
                                                    name="pickup_place" placeholder="Pick Up Location"
                                                    value="{{ old('pickup_place') }}" aria-label="Pick Up Location"
                                                    aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Travel </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">From</span>
                                                </div>
                                                <input type="text" class="form-control" name="travel_from"
                                                    placeholder="Travel From" value="{{ old('travel_from') }}"
                                                    aria-label="Travel From" aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="input-group has-validation">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Destination</span>
                                                </div>
                                                <input type="text" class="form-control fv-plugins-icon-input"
                                                    name="destination" placeholder="Destination Point"
                                                    value="{{ old('destination') }}" aria-label="Destination Point"
                                                    aria-describedby="basic-addon2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Extra Travel from DHQ (in KM) </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input name="extra_travel" type="number" value="{{ old('extra_travel') }}"
                                        class="form-control @if ($errors->has('extra_travel')) is-invalid @endif" />
                                    @if ($errors->has('extra_travel'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="extra_travel">
                                                {!! $errors->first('extra_travel') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div> --}}
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Tentative Cost (in NPR) </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input name="tentative_cost" type="number" value="{{ old('tentative_cost') }}"
                                        class="form-control @if ($errors->has('tentative_cost')) is-invalid @endif" />
                                    @if ($errors->has('tentative_cost'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="tentative_cost">
                                                {!! $errors->first('tentative_cost') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Activity Code </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="activity_code_id"
                                        class="form-control select2 @if ($errors->has('activity_code_id')) is-invalid @endif">
                                        <option value="">Select Activity Code</option>
                                        @foreach ($activityCodes as $activityCode)
                                            <option @if (old('activity_code_id') == $activityCode->id) selected @endif
                                                value="{{ $activityCode->id }}">
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
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Account Code </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
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
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Donor Code </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="donor_code_id"
                                        class="form-control select2 @if ($errors->has('donor_code_id')) is-invalid @endif">
                                        <option value="">Select Donor Code</option>
                                        @foreach ($donorCodes as $donorCode)
                                            <option @if (old('donor_code_id') == $donorCode->id) selected @endif
                                                value="{{ $donorCode->id }}">
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

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationGender" class="form-label">Districts
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="district_ids[]"
                                        class="form-control select2 @if ($errors->has('district_ids')) is-invalid @endif"
                                        multiple>
                                        <option value="">Select Districts</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('district_ids'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="district_ids">
                                                {!! $errors->first('district_ids') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div> --}}
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label required-label">{{ __('label.send-to') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="approver_id"
                                        class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif"
                                        data-width="100%">
                                        <option value="">Select a Approver</option>
                                        @foreach ($hireApprovers as $approver)
                                            <option value="{{ $approver->id }}"
                                                @if ($approver->id == old('approver_id')) selected="selected" @endif>
                                                {{ $approver->getFullName() }}
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

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationdistributiontype" class="form-label ">Procurement
                                            Officers</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    @php
                                        $selected = old('procurement_officer') ?? [];
                                    @endphp
                                    <select name="procurement_officer[]" class="select2 form-control" data-width="100%"
                                        multiple>
                                        @foreach ($officers as $officer)
                                            <option value="{{ $officer->id }}" data-distribution="{{ $officer->id }}"
                                                {{ in_array($officer->id, $selected) ? 'selected' : '' }}>
                                                {{ $officer->getFullNameWithEmpCode() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('district_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="district_id">
                                                {!! $errors->first('district_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label class="form-label">Remarks </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea rows="5" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') }}</textarea>
                                    @if ($errors->has('remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="remarks">
                                                {!! $errors->first('remarks') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                            </button>
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{{ route('vehicle.requests.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                        {!! csrf_field() !!}
                        <input type="hidden" name="vehicle_request_type_id" value="2" />
                        <input type="hidden" name="tab" value="hire-vehicle" />
                    </form>
                </div>
            </div>
        </div>
    </section>

@stop
