@extends('layouts.container')

@section('title', 'Approve Maintenance Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approve-maintenance-requests-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('maintenanceApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required.',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required.',
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

            $(form).on('change', '[name="status_id"]', function (e) {
                fv.revalidateField('status_id');
                if (this.value == 6) {
                    $(form).find('#forwardBlock').show();
                } else {
                    $(form).find('#forwardBlock').hide();
                }
            }).on('change', '[name="logistic_officer_id"]', function (e) {
                fv.revalidateField('logistic_officer_id');
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
                                    <a href="{{ route('approve.maintenance.requests.index') }}"
                                       class="text-decoration-none text-dark">Maintenance Request</a>
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
                            <div class="card-header fw-bold">
                                Maintenance Request Information
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="atvcde" class="m-0 ">{{ __('label.maintenance-number') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        {{$maintenanceRequest->getMaintenanceRequestNumber()}}
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="atvcde" class="m-0 ">{{ __('label.activity-code') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        {{$maintenanceRequest->getActivityCode()}}
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="acccde" class="m-0 ">{{ __('label.account-code') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <label for="acccde" class="m-0 ">{{$maintenanceRequest->getAccountCode()}}
                                        </label>
                                        </select>
                                    </div>
                                </div>
                                @if($maintenanceRequest->donor_code_id)
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="donor" class="m-0 ">{{ __('label.donor-grant') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            {{$maintenanceRequest->getDonorCode()}}
                                        </div>
                                    </div>
                                @endif
                                <div class="row mb-2">
                                    <div class="px-3">
                                        <h3 class="fs-7 fw-bold  text-uppercase text-primary border-bottom py-2">
                                            Facility being
                                            request for. Details in table below</h3>
                                    </div>

                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="itmname" class="m-0 ">{{ __('label.item-name') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        {{$maintenanceRequest->getItem()}}
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="prblmdsc" class="m-0 ">{{ __('label.service-request-for') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        @if($maintenanceRequest->problem)
                                            {{$maintenanceRequest->problem}}
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="estcst"
                                                   class="m-0 ">{{ __('label.estimation-cost-for') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        @if($maintenanceRequest->estimated_cost)
                                            {{$maintenanceRequest->estimated_cost}}
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="rmsks" class="m-0 ">{{ __('label.remarks') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        @if($maintenanceRequest->remarks)
                                            {{$maintenanceRequest->remarks}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header fw-bold">Process</div>
                            <div class="card-body">
                                    <div class="c-b">
                                        @foreach($maintenanceRequest->logs as $log)
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
                                    <div class="border-top pt-4">
                                        <form
                                            action="{{ route('approve.maintenance.requests.store', $maintenanceRequest->id) }}"
                                            id="maintenanceApproveForm"
                                            method="post"
                                            enctype="multipart/form-data" autocomplete="off">
                                            <div class="card-body">
                                                <div class="row mb-2">
                                                    <div class="col-lg-3">
                                                        <div class="d-flex align-items-start h-100">
                                                            <label for="validationleavetype"
                                                                   class="form-label required-label">Status</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <select name="status_id"
                                                                class="select2 form-control @if($errors->has('status_id')) is-invalid @endif"
                                                                data-width="100%">
                                                            <option value="">Select a Status</option>
                                                            <option value="2"
                                                                    @if(old('status_id') == '2') selected @endif>
                                                                Return to Requester
                                                            </option>
                                                            <option value="8"
                                                                    @if(old('status_id') == '8') selected @endif>
                                                                Reject
                                                            </option>
                                                            @if($maintenanceRequest->status_id == 3)
                                                                <option value="4"
                                                                        @if(old('status_id') == '4') selected @endif>
                                                                    Recommend
                                                                </option>
                                                            @endif
                                                            <option value="6"
                                                                    @if(old('status_id') == '6') selected @endif>
                                                                Approve
                                                            </option>
                                                        </select>
                                                        @if($errors->has('status_id'))
                                                            <div
                                                                class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="status_id">
                                                                    {!! $errors->first('status_id') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(old('status_id') == '6')
                                                    <div class="row mb-2" id="forwardBlock">
                                                        @else
                                                            <div class="row mb-2" id="forwardBlock"
                                                                 style="display: none;">
                                                                @endif
                                                                <div class="col-lg-3">
                                                                    <div class="d-flex align-items-start h-100">
                                                                        <label for="validationleavetype"
                                                                               class="form-label required-label">Recommended</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-9">
                                                                    <select name="logistic_officer_id"
                                                                            class="select2 form-control @if($errors->has('logistic_officer_id')) is-invalid @endif"
                                                                            data-width="100%">
                                                                        <option value="">Select Logistic Officer
                                                                        </option>
                                                                        @foreach ($logisticOfficers as $logisticOfficer)
                                                                            <option
                                                                                value="{{ $logisticOfficer->id }}">
                                                                                {{ $logisticOfficer->getFullName() }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @if ($errors->has('logistic_officer_id'))
                                                                        <div
                                                                            class="fv-plugins-message-container invalid-feedback">
                                                                            <div data-field="logistic_officer_id">
                                                                                {!! $errors->first('logistic_officer_id') !!}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="row mb-2">
                                                                <div class="col-lg-3">
                                                                    <div class="d-flex align-items-start h-100">
                                                                        <label for="validationRemarks"
                                                                               class="form-label required-label">Remarks</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-9">
                                                        <textarea type="text"
                                                                  class="form-control @if($errors->has('log_remarks')) is-invalid @endif"
                                                                  name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                                    @if($errors->has('log_remarks'))
                                                                        <div
                                                                            class="fv-plugins-message-container invalid-feedback">
                                                                            <div
                                                                                data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            {!! csrf_field() !!}
                                                    </div>
                                                    <div class="justify-content-end d-flex gap-2">
                                                        <button type="submit" name="btn" value="submit"
                                                                class="btn btn-success btn-sm">
                                                            Submit
                                                        </button>
                                                        <a href="{!! route('approve.maintenance.requests.index') !!}"
                                                           class="btn btn-danger btn-sm">Cancel</a>
                                                    </div>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                        </div>
            </section>
@stop
