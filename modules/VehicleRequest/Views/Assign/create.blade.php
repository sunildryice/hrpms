@extends('layouts.container')

@section('title', 'Assign Vehicle')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assign-vehicle-requests-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function(e) {
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
                    assigned_vehicle_id: {
                        validators: {
                            notEmpty: {
                                message: 'Vehicle is required.',
                            },
                        },
                    },
                    // log_remarks: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'The remarks is required',
                    //         },
                    //     },
                    // },
                    driver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Driver is required.'
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]')
                                .value);
                            return (field === 'assigned_vehicle_id' && statusId !== 6) || (
                                field === 'status_id' && statusId === 6);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                if (this.value == 6) {
                    $(form).find('#approveBlock').show();
                } else {
                    $(form).find('#approveBlock').hide();
                }
            }).on('change', '[name="assigned_vehicle_id"]', function(e) {
                fv.revalidateField('assigned_vehicle_id');
            }).on('change', '[name="driver_id"]', function(e) {
                fv.revalidateField('driver_id');
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
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('assign.vehicle.requests.index') }}"
                                class="text-decoration-none text-dark">Vehicle
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
        <div class="card">
            <div class="card-header fw-bold">Vehicle Request Details</div>
            <div class="card-body">
                @include('VehicleRequest::Partials.detail')
            </div>
        </div>
        <div class="card">
            <div class="card-header fw-bold">
                Vehicle Request Process
            </div>
            <form action="{{ route('assign.vehicle.requests.store', $vehicleRequest->id) }}" id="vehicleRequestApproveForm"
                method="post" enctype="multipart/form-data" autocomplete="off">

                <div class="card-body">
                    <div class="c-b">
                        @foreach ($vehicleRequest->logs as $log)
                            <div class="d-flex pb-3 flex-row gap-2 mb-2 border-bottom ">
                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                    <i class="bi-person-circle fs-5"></i>
                                </div>
                                <div class="w-100">
                                    <div
                                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                            <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                            <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                        </div>
                                        <small
                                            title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <p class="text-justify comment-text mb-0 mt-1">
                                        {{ $log->log_remarks }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-top pt-4">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationvehicletype" class="form-label required-label">Status </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="status_id" class="select2 form-control" data-width="100%">
                                    <option value="">Select a Status</option>
                                    <option value="2">Return to Requester</option>
                                    <option value="8">Reject</option>
                                    <option value="6">Assign</option>
                                </select>
                                @if ($errors->has('status_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="status_id">
                                            {!! $errors->first('status_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2" id="approveBlock" style="display: none;">
                            {{-- Vehicle --}}
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationvehicletype" class="form-label required-label">Vehicle </label>
                                </div>
                            </div>

                            <div class="col-lg-9 mb-2">
                                <select name="assigned_vehicle_id" class="select2 form-control" data-width="100%">
                                    <option value="">Select a Vehicle</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                            @if (old('assigned_vehicle_id') == $vehicle->id) selected @endif>
                                            {{ $vehicle->getVehicleNumberWithCapacity() }}</option>
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

                            {{-- Driver --}}
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">Driver</label>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <select name="driver_id" class="select2 form-control" data-width="100%">
                                    <option value="">Select Driver</option>

                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}"
                                            @if (old('driver_id') == $driver->id) selected @endif>
                                            {{ $driver->getFullName() }}
                                        </option>
                                    @endforeach

                                </select>

                                @if ($errors->has('driver_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        {!! $errors->first('driver_id') !!}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="form-label">Remarks </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                @if ($errors->has('log_remarks'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {!! csrf_field() !!}
                    </div>
                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                        Submit
                    </button>
                    <a href="{!! route('approve.vehicle.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@stop
