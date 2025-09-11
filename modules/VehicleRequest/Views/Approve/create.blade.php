@extends('layouts.container')

@section('title', 'Approve Vehicle Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approve-vehicle-requests-menu').addClass('active');
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
                if (this.value == '{{config("constant.RECOMMENDED_STATUS")}}') {
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
                        @include('VehicleRequest::Partials.detail')
                    </div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            Vehicle Request Process
                        </div>
                        <form action="{{ route('approve.vehicle.requests.store', $vehicleRequest->id) }}"
                              id="vehicleRequestApproveForm" method="post"
                              enctype="multipart/form-data" autocomplete="off">

                            <div class="card-body">
                                <div>
                                    <div>
                                        @include('VehicleRequest::Partials.log')
                                    </div>
                                    <div class="border-top pt-4">
                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationVehicleType"
                                                           class="form-label required-label">Status </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select name="status_id" class="select2 form-control"
                                                        data-width="100%">
                                                    <option value="">Select a Status</option>
                                                    <option value="2">Return to Requester</option>
                                                    <option value="8">Reject</option>
                                                    @if($vehicleRequest->vehicle_request_type_id != 1 && $vehicleRequest->status_id==3)
                                                        <option value="4">Recommend</option>
                                                    @endif
                                                    <option value="6">Approve</option>
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

                                        <div class="row mb-2" id="recommendBlock" style="display: none;">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationvehicletype"
                                                           class="form-label required-label">Recommended To </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select name="recommended_to" class="select2 form-control"
                                                        data-width="100%">
                                                    <option value="">Select Recommended To</option>
                                                    @foreach ($approvers as $approver)
                                                        <option value="{{ $approver->id }}">
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
                                                    <label for="validationRemarks"
                                                           class="form-label required-label">Remarks </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                    <textarea type="text"
                                                              class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                              name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                @if ($errors->has('log_remarks'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div
                                                            data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('approve.vehicle.requests.index') !!}"
                                   class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
            </section>
@stop
