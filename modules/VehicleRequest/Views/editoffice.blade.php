@extends('layouts.container')

@section('title', 'Edit Vehicle Request')

@section('page_css')

@endsection
@section('page_js')
    <script type="text/javascript">
        $('[data-toggle="datepicker-time"]').daterangepicker({
            "singleDatePicker": true,
            "timePicker24Hour": true,
            "timePicker": true,
            "autoApply": true,
            // minDate:moment($('#start_date').val(), "MMMM D, YYYY");
            startDate: "{{ $vehicleRequest->start_datetime }}",
            endDate: "{{ $vehicleRequest->end_datetime }}",
            // minDate: "{{ $vehicleRequest->start_datetime->timestamp }}" > Math.floor(new Date().getTime()/1000) ? new Date() : "{{ $vehicleRequest->start_datetime }}",
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });

        $('[data-toggle="datepicker-time2"]').daterangepicker({
            "singleDatePicker": true,
            "timePicker24Hour": true,
            "timePicker": true,
            "autoApply": true,
            // minDate:moment($('#start_date').val(), "MMMM D, YYYY");
            startDate: "{{ $vehicleRequest->end_datetime }}",
            endDate: "{{ $vehicleRequest->end_datetime }}",
            // minDate: "{{ $vehicleRequest->start_datetime->timestamp }}" > Math.floor(new Date().getTime()/1000) ? new Date() : "{{ $vehicleRequest->start_datetime }}",
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        }, function(start, end, label) {
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

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#vehicle-requests-menu').addClass('active');
            $('.step-item').click(function() {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('officeVehicleRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    vehicle_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Vehicle Type is required',
                            },
                        },
                    },
                    office_start_datetime: {
                        validators: {
                            notEmpty: {
                                message: 'The start date time is required',
                            },
                        },
                    },
                    office_end_datetime: {
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
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD H:i',
                        startDate: {
                            field: 'office_start_datetime',
                            message: 'Date From must be a valid date and earlier than Date To.',
                        },
                        endDate: {
                            field: 'office_end_datetime',
                            message: 'Date To must be a valid date and later than from Date From.',
                        },
                    }),
                },
            });

            $(form).on('change', '[name="vehicle_type_id"]', function(e) {
                fv.revalidateField('vehicle_type_id');
            }).on('change', '[name="office_start_datetime"]', function(e) {
                fv.revalidateField('office_start_datetime');
                fv.revalidateField('office_end_datetime');
            }).on('change', '[name="office_end_datetime"]', function(e) {
                fv.revalidateField('office_start_datetime');
                fv.revalidateField('office_end_datetime');
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
                                <a href="#" class="nav-link step-item  text-decoration-none active"
                                    data-tag="office-vehicle">
                                    <i class="nav-icon bi-truck"></i> Office Vehicle
                                </a>
                            </li>
                        </ul>
                    </div>
                    @include('VehicleRequest::Partials.return')
                </div>
                <div class="col-lg-9">
                    <div class="card shadow-sm border rounded c-tabs-content active" id="office-vehicle">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Office vehicle Request Form</h3>
                        </div>
                        <form action="{{ route('vehicle.requests.update', $vehicleRequest->id) }}"
                            id="officeVehicleRequestEditForm" method="post" enctype="multipart/form-data"
                            autocomplete="off">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationProject" class="form-label required-label">Project
                                            </label>
                                        </div>
                                    </div>
                                    @php $selectedProjectCodeId =  old('project_code_id') ?: $vehicleRequest->project_code_id  @endphp
                                    <div class="col-lg-9">
                                        <select name="project_code_id"
                                            class="select2 form-control
                                                    @if ($errors->has('project_code_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a Project</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}"
                                                    {{ $project->id == $selectedProjectCodeId ? 'selected' : '' }}>
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
                                            <label for="" class="m-0">Office </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedOfficeId = old('office_id') ?: $vehicleRequest->office_id @endphp
                                        <select name="office_id"
                                            class="form-control select2 @if ($errors->has('office_id')) is-invalid @endif">
                                            <option value="">Select Office</option>
                                            @foreach ($offices as $office)
                                                <option @if ($selectedOfficeId == $office->id) selected @endif
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
                                            <label for="validationfullname" class="form-label required-label">Vehicle Type
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedVehicleTypeId = old('vehicle_type_id') ?: collect(json_decode($vehicleRequest->vehicle_type_ids))->first() @endphp
                                        <select
                                            class="form-control select2 @if ($errors->has('vehicle_type_id')) is-invalid @endif"
                                            name="vehicle_type_id">
                                            <option value="">Select Vehicle Type</option>
                                            @foreach ($vehicleTypes as $vehicleType)
                                                <option value="{{ $vehicleType->id }}"
                                                    @if ($selectedVehicleTypeId == $vehicleType->id) selected @endif>
                                                    {{ $vehicleType->title }}</option>
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
                                            <label for="" class="form-label required-label">Date </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">From</span>
                                                    </div>
                                                    {{-- <input name="start_date" value="{!!$vehicleRequest->start_datetime->format('Y-m-d H:i')!!}" hidden> --}}
                                                    <input data-toggle="datepicker-time" type="text"
                                                        name="office_start_datetime" value="{!! old('office_start_datetime')
                                                            ? old('office_start_datetime')
                                                            : $vehicleRequest->start_datetime->format('YYYY-mm-dd H:i') !!}"
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

                                            {{-- @dump(old('office_start_datetime') ? old('office_start_datetime') : $vehicleRequest->start_datetime->format('Y-m-d H:i')) --}}

                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">To</span>
                                                    </div>
                                                    <input data-toggle="datepicker-time2" type="text"
                                                        name="office_end_datetime" value="{!! old('office_end_datetime') ? old('office_end_datetime') : $vehicleRequest->end_datetime->format('Y-m-d H:i') !!}"
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

                                            {{-- @dump(old('office_end_datetime') ? old('office_end_datetime') : $vehicleRequest->end_datetime->format('Y-m-d H:i')) --}}

                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Purpose of Travel</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" name="purpose_of_travel"
                                            value="{{ old('purpose_of_travel') ?: $vehicleRequest->purpose_of_travel }}"
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
                                                        aria-label="Travel From" aria-describedby="basic-addon2">
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
                                                        aria-label="Destination Point" aria-describedby="basic-addon2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validateusers" class="m-0">Accompanying Staff
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedEmployeeIds = json_decode($vehicleRequest->employee_ids) @endphp
                                        <select name="employee_ids[]"
                                            class="form-control select2 @if ($errors->has('employee_ids')) is-invalid @endif"
                                            multiple>
                                            @foreach ($employees as $employee)
                                                <option @if (in_array($employee->id, $selectedEmployeeIds)) selected @endif
                                                    value="{{ $employee->id }}">{{ $employee->getFullName() }}</option>
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
                                            <label for="" class="m-0">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea rows="5" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') ?: $vehicleRequest->remarks }}</textarea>
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
                                            <label for=""
                                                class="form-label required-label">{{ __('label.send-to') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedApproverId = old('approver_id') ?: $vehicleRequest->approver_id @endphp
                                        <select name="approver_id"
                                            class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a Approver</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}"
                                                    @if ($selectedApproverId == $approver->id) selected @endif>
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
                            <input name="vehicle_request_type_id" value="1" type="hidden" />

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
