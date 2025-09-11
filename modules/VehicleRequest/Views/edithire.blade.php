@extends('layouts.container')

@section('title', 'Edit Vehicle Request')

@section('page_css')

@endsection
@section('page_js')
    <script type="text/javascript">
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
            "autoApply": true,
            startDate: moment().startOf('hour'),
            endDate: '',
            locale: {
                format: 'YYYY-MM-DD, hh:mm A'
            }
        }, function (start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });

        $('[data-toggle="datepicker-range"]').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
            },

            // "minDate": "{!! date('m/d/Y') !!}",
            // "startDate": "{!! $vehicleRequest->start_datetime->format('m/d/Y') !!}",
            // "endDate": "{!! $vehicleRequest->end_datetime->format('m/d/Y') !!}",

            "startDate": "{{$vehicleRequest->start_datetime->format('m/d/Y')}}",
            "endDate": "{{$vehicleRequest->end_datetime->format('m/d/Y')}}",
            // "minDate": "{{$vehicleRequest->start_datetime->timestamp}}" > Math.floor(new Date().getTime()/1000) ? "{!! date('m/d/Y') !!}" : "{{$vehicleRequest->start_datetime->format('m/d/Y')}}",

            "autoApply": false,
            "drops": "auto",
        }, function (start, end, label) {
            $('form').find('[name="start_datetime"]').val(start.format('YYYY-MM-DD'));
            $('form').find('[name="end_datetime"]').val(end.format('YYYY-MM-DD'));
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        $('.filter-items').on('click', function () {
            var chk_box = $(this).find('.f-check-input');

            var chk_status = !(chk_box.is(':checked'));
            var chkdata = $(this).data("id");

            chk_box.attr('checked', chk_status);
            $(this).find('i').toggleClass('bi-check-square-fill').toggleClass('bi-square');
            $(this).toggleClass('active');
        });

        $('[name="pickup_time"]').daterangepicker({
            singleDatePicker:true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            locale: {
                format: 'HH:mm'
            }
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#vehicle-requests-menu').addClass('active');
            $('.step-item').click(function () {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('hireVehicleRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
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
                    approver_id :{
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

            $(form).on('change', '[name="vehicle_type_id"]', function (e) {
                fv.revalidateField('vehicle_type_id');
            }).on('change', '[name="activity_code_id"]', function (e) {
                $element = $(this);
                var activityCodeId = $element.val();
                var htmlToReplace = '<option value="">Select Account Code</option>';
                if (activityCodeId) {
                    var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                    var successCallback = function (response) {
                        response.accountCodes.forEach(function (accountCode) {
                            htmlToReplace += '<option value="' + accountCode.id + '">' + accountCode.title + ' ' + accountCode.description + '</option>';
                        });
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace).trigger('change');
                    }
                    var errorCallback = function (error) {
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
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('vehicle.requests.index') }}" class="text-decoration-none">Vehicle
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
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation pt-3 pb-3">
                        <ul class="m-0 list-unstyled v-mneu">
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item text-decoration-none active"
                                   data-tag="hire-vehicle">
                                    <i class="nav-icon bi-truck-flatbed"></i> Hire Vehicle</a>
                            </li>
                        </ul>
                    </div>
                  @include('VehicleRequest::Partials.return')

                </div>
                <div class="col-lg-9">
                    <div class="card shadow-sm border rounded c-tabs-content active" id="hire-vehicle">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Hire vehicle Request Form</h3>
                        </div>
                        <form action="{{ route('vehicle.requests.update', $vehicleRequest->id) }}"
                              id="hireVehicleRequestEditForm"
                              method="post"
                              enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationfullname" class="form-label required-label">Date </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" data-toggle="datepicker-range"
                                               class="form-control @if($errors->has('start_end_datetime')) is-invalid @endif"
                                               name="start_end_datetime">
                                        <input type="hidden" class="form-control" name="start_datetime"
                                               value="{{ $vehicleRequest->start_datetime }}">
                                        <input type="hidden" class="form-control" name="end_datetime"
                                               value="{{ $vehicleRequest->end_datetime }}">
                                        @if($errors->has('start_datetime') || $errors->has('end_datetime'))
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
                                            <label for="" class="m-0">Purpose of Travel </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" name="purpose_of_travel"
                                               class="form-control @if($errors->has('purpose_of_travel')) is-invalid @endif"
                                               value="{{ old('purpose_of_travel') ?: $vehicleRequest->purpose_of_travel }}"/>
                                        @if($errors->has('purpose_of_travel'))
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
                                            <label for="validationGender" class="m-0">Accompanying Staff
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedEmployeeIds = json_decode($vehicleRequest->employee_ids) @endphp
                                        <select name="employee_ids[]"
                                                class="form-control select2 @if($errors->has('employee_ids')) is-invalid @endif"
                                                multiple>
                                            @foreach($employees as $employee)
                                                <option @if(in_array($employee->id, $selectedEmployeeIds)) selected
                                                        @endif
                                                        value="{{ $employee->id }}">{{ $employee->getFullName() }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('employee_ids'))
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
                                            <label for="" class="form-label required-label">Vehicle Type </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="mt-3">
                                            @php $selectedVehicleTypes = $vehicleRequest->vehicle_type_ids ? json_decode($vehicleRequest->vehicle_type_ids) : [] @endphp
                                            <div class="row">
                                                @foreach($vehicleTypes as $vehicleType)
                                                    <div class="col-lg-4">
                                                    <span
                                                        class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input"
                                                                   name="vehicle_type_ids[]"
                                                                   value="{{ $vehicleType->id }}"
                                                                   @if(in_array($vehicleType->id, $selectedVehicleTypes)) checked @endif />
                                                            <span class="filter-checkbox">
                                                                <i class="@if(in_array($vehicleType->id, $selectedVehicleTypes)) bi-check-square-fill @else bi-square @endif"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            {{ $vehicleType->title }}
                                                        </span>
                                                    </span>
                                                    </div>
                                                @endforeach
                                                <div class="col-lg-4">
                                                    <span
                                                        class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" name="vehicle_type_ids[]" value="-1"
                                                                   class="f-check-input"
                                                                   @if(old('other_remarks') || $vehicleRequest->other_remarks) checked @endif>
                                                            <span class="filter-checkbox">
                                                                <i class="@if(old('other_remarks') || $vehicleRequest->other_remarks) bi-check-square-fill @else bi-square @endif"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="">Others</span>
                                                                <input type="text" name="other_remarks"
                                                                       class="form-control"
                                                                       value="{{ old('other_remarks') ?: $vehicleRequest->other_remarks }}">
                                                            </div>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">For</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">

                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <span
                                                        class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="radio" class="f-check-input" value="1"
                                                                   name="for_hours_flag"
                                                                   @if($vehicleRequest->for_hours_flag == 1) checked @endif>
                                                            <span class="filter-checkbox">
                                                                <i class="@if($vehicleRequest->for_hours_flag == 1) bi-check-square-fill @else bi-square @endif"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            Full Day
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-2">
                                                    <span
                                                        class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100 active"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="radio" class="f-check-input" value="2"
                                                                   name="for_hours_flag"
                                                                   @if($vehicleRequest->for_hours_flag == 2) checked @endif>
                                                            <span class="filter-checkbox">
                                                                <i class="@if($vehicleRequest->for_hours_flag == 2) bi-check-square-fill @else bi-square @endif"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            Half Day
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <span
                                                        class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="radio" class="f-check-input" value="3"
                                                                   name="for_hours_flag"
                                                                   @if($vehicleRequest->for_hours_flag == 3) checked @endif>
                                                            <span class="filter-checkbox">
                                                                <i class="@if($vehicleRequest->for_hours_flag == 3) bi-check-square-fill @else bi-square @endif"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="">Hrs</span>
                                                                <input type="number" name="for_hours"
                                                                       value="{{ $vehicleRequest->for_hours }}"
                                                                       class="form-control">
                                                            </div>
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <span
                                                        class="filter-items-radio d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="radio" class="f-check-input" value="4"
                                                                   name="for_hours_flag"
                                                                   @if($vehicleRequest->for_hours_flag == 4) checked @endif>
                                                            <span class="filter-checkbox">
                                                                <i class="@if($vehicleRequest->for_hours_flag == 4) bi-check-square-fill @else bi-square @endif"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="">Others</span>
                                                                <input type="text" name="for_hours_other_remarks"
                                                                       value="{{ $vehicleRequest->for_hours_other_remarks }}"
                                                                       class="form-control">
                                                            </div>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Pick Up </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">Time</span>
                                                    </div>
                                                    <input type="text" class="form-control" name="pickup_time"
                                                           value="{{ old('pickup_time') ?: $vehicleRequest->pickup_time }}"
                                                           readonly
                                                           aria-label="Pick Up time"
                                                           aria-describedby="basic-addon2">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">Location</span>
                                                    </div>
                                                    <input type="text" class="form-control fv-plugins-icon-input"
                                                           name="pickup_place" placeholder="Pick Up Location"
                                                           value="{{ old('pickup_place') ?: $vehicleRequest->pickup_place }}"
                                                           aria-label="Pick Up Location"
                                                           aria-describedby="basic-addon2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Travel </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">From</span>
                                                    </div>
                                                    <input type="text" class="form-control" name="travel_from"
                                                           placeholder="Travel From"
                                                           value="{{ old('travel_from') ?: $vehicleRequest->travel_from }}"
                                                           aria-label="Travel From"
                                                           aria-describedby="basic-addon2">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"
                                                              id="basic-addon2">Destination</span>
                                                    </div>
                                                    <input type="text" class="form-control fv-plugins-icon-input"
                                                           name="destination" placeholder="Destination Point"
                                                           value="{{ old('destination') ?: $vehicleRequest->destination }}"
                                                           aria-label="Destination Point"
                                                           aria-describedby="basic-addon2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Extra Travel from DHQ (in KM) </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input name="extra_travel" type="number"
                                               value="{{ old('extra_travel') ?: $vehicleRequest->extra_travel }}"
                                               class="form-control @if($errors->has('extra_travel')) is-invalid @endif"/>
                                        @if($errors->has('extra_travel'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="extra_travel">
                                                    {!! $errors->first('extra_travel') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Tentative Cost (in NPR) </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input name="tentative_cost" type="number"
                                               value="{{ old('tentative_cost') ?: $vehicleRequest->tentative_cost }}"
                                               class="form-control @if($errors->has('tentative_cost')) is-invalid @endif"/>
                                        @if($errors->has('tentative_cost'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="tentative_cost">
                                                    {!! $errors->first('tentative_cost') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Activity Code </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedActivityCodeId = old('activity_code_id') ?: $vehicleRequest->activity_code_id @endphp
                                        <select name="activity_code_id"
                                                class="form-control select2 @if($errors->has('activity_code_id')) is-invalid @endif">
                                            <option value="">Select Activity Code</option>
                                            @foreach($activityCodes as $activityCode)
                                                <option @if($selectedActivityCodeId == $activityCode->id) selected
                                                        @endif
                                                        value="{{ $activityCode->id }}">{{ $activityCode->getActivityCodeWithDescription() }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('activity_code_id'))
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
                                            <label for="" class="m-0">Account Code </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedAccountCodeId = old('account_code_id') ?: $vehicleRequest->account_code_id @endphp
                                        <select name="account_code_id"
                                                class="form-control select2 @if($errors->has('account_code_id')) is-invalid @endif">
                                            <option value="">Select Account Code</option>
                                            @foreach($accountCodes as $accountCode)
                                                <option @if($selectedAccountCodeId == $accountCode->id) selected @endif
                                                value="{{ $accountCode->id }}">{{ $accountCode->getAccountCodeWithDescription() }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('account_code_id'))
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
                                            <label for="" class="m-0">Donor Code </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedDonorCodeId = old('donor_code_id') ?: $vehicleRequest->donor_code_id @endphp
                                        <select name="donor_code_id"
                                                class="form-control select2 @if($errors->has('donor_code_id')) is-invalid @endif">
                                            <option value="">Select Donor Code</option>
                                            @foreach($donorCodes as $donorCode)
                                                <option @if($selectedDonorCodeId == $donorCode->id) selected @endif
                                                value="{{ $donorCode->id }}">{{ $donorCode->getDonorCodeWithDescription() }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('donor_code_id'))
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
                                            <label for="validationGender" class="m-0">Districts
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedDistrictIds = $vehicleRequest->district_ids ? json_decode($vehicleRequest->district_ids) : [] @endphp
                                        <select name="district_ids[]"
                                                class="form-control select2 @if($errors->has('district_ids')) is-invalid @endif"
                                                multiple>
                                            <option value="">Select Districts</option>
                                            @foreach($districts as $district)
                                                <option @if(in_array($district->id, $selectedDistrictIds)) selected
                                                        @endif
                                                        value="{{ $district->id }}">{{ $district->district_name }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('district_ids'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="district_ids">
                                                    {!! $errors->first('district_ids') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="form-label required-label">{{__('label.send-to')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedApproverId = old('approver_id') ?: $vehicleRequest->approver_id @endphp
                                        <select name="approver_id"
                                                class="select2 form-control @if($errors->has('approver_id')) is-invalid @endif"
                                                data-width="100%">
                                            <option value="">Select a Approver</option>
                                            @foreach($hireApprovers as $approver)
                                                <option value="{{ $approver->id }}"
                                                    @if($selectedApproverId == $approver->id) selected
                                                        @endif>
                                                    {{ $approver->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('approver_id'))
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
                                            <label for="validationProcurement"
                                                class="form-label ">Procurement Officers</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php
                                            $selected = $vehicleRequest->procurementOfficers->pluck('id')->toArray();
                                        @endphp
                                        <select name="procurement_officer[]" class="select2 form-control"
                                            data-width="100%" multiple>
                                            @foreach ($officers as $officer)
                                                <option value="{{ $officer->id }}"
                                                    data-distribution="{{ $officer->id }}"
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
                                            <label for="" class="m-0">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea rows="5"
                                                  class="form-control @if($errors->has('remarks')) is-invalid @endif"
                                                  name="remarks">{{ old('remarks') ?: $vehicleRequest->remarks }}</textarea>
                                        @if($errors->has('remarks'))
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
                            {!! method_field('PUT') !!}
                        </form>
                    </div>
                </div>
            </div>
        </section>
@stop
