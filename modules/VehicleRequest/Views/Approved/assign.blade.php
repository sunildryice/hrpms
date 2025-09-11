@extends('layouts.container')

@section('title', 'Assign Vehicle')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approved-vehicle-requests-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('vehicleRequestApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    recommended_to: {
                        validators: {
                            notEmpty: {
                                message: 'Recommended to is required.',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function (field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]').value);
                            return (field === 'recommended_to' && statusId !== 4) || (field === 'status_id' && statusId === 4);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function (e) {
                fv.revalidateField('status_id');
                if (this.value == 4) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="recommended_to"]', function (e) {
                fv.revalidateField('recommended_to');
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
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('approve.vehicle.requests.index') }}"
                                       class="text-decoration-none">Vehicle
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
                    <div class="card mb-4 shadow mb-3">
                        <div class="card-header bg-light p-2 px-3">
                            <h3 class="m-0 fs-6 text-capitalize">Vehicle Request Details</h3>
                        </div>
                        @include('VehicleRequest::Partials.detail')
                    </div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            Vehicle Request Process
                        </div>
                        <form action="{{ route('approved.vehicle.requests.assign.store', $vehicleRequest->id) }}"
                              id="vehicleRequestApproveForm" method="post"
                              enctype="multipart/form-data" autocomplete="off">

                            <div class="card-body">
                                <div class="row">
                                    <div class="c-b">
                                        @foreach($vehicleRequest->logs as $log)
                                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                <div width="40" height="40"
                                                     class="rounded-circle mr-3 user-icon">
                                                    <i class="bi-person-circle fs-5"></i>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                            <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                            <span
                                                                class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                        </div>
                                                        <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                    </div>
                                                    <p class="text-justify comment-text mb-0 mt-1">
                                                        {{ $log->log_remarks }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationvehicletype" class="form-label required-label">Vehicle </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select name="assigned_vehicle_id" class="select2 form-control"
                                                        data-width="100%">
                                                    <option value="">Select a Vehicle</option>
                                                    @foreach($vehicles as $vehicle)
                                                        <option value="{{ $vehicle->id }}"
                                                                @if(old('assigned_vehicle_id') == $vehicle->id) selected @endif>{{ $vehicle->getVehicleNumberWithCapacity() }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('assigned_vehicle_id'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="assigned_vehicle_id">
                                                            {!! $errors->first('assigned_vehicle_id') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationRemarks"
                                                           class="form-label required-label">Remarks </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                    <textarea type="text"
                                                              class="form-control @if ($errors->has('assigned_remarks')) is-invalid @endif"
                                                              name="assigned_remarks">{{ old('assigned_remarks') }}</textarea>
                                                @if ($errors->has('assigned_remarks'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div
                                                            data-field="assigned_remarks">{!! $errors->first('assigned_remarks') !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                                    Save
                                </button>
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('approve.vehicle.requests.index') !!}"
                                   class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
