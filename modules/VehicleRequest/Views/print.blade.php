@extends('layouts.container-report')

@section('title', 'Vehicle Request')

@section('page-content')
    {{--    <script type="text/javascript"> --}}
    {{--        window.print(); --}}
    {{--    </script> --}}

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $vehicleRequest->getOfficeName() }}</div>
            <div class="fs-8"> Vehicle Request</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold mb-2">
                        Vehicle Request No: {!! $vehicleRequest->getVehicleRequestNumber() !!} <br>
                        Print Date : <?= date('Y-m-d G:i:s') ?>
                    </div>

                    <div class="print-header-info mb-3 d-none">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-1">Travel Date From:</span><span>{!! $vehicleRequest->getStartDatetime() !!}</span></li>
                            <li><span class="fw-bold me-1">Travel Date To :</span><span>{!! $vehicleRequest->getEndDatetime() !!}</span></li>
                            <li><span class="fw-bold me-1">District :</span><span>{!! $vehicleRequest->getDistricts() !!}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt=""
                                    class="align-self-end pe-5 logo-img">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="print-body border-top">
            <div class="card-body">
                <div class="row">

                    <div class="col-9 offset-1">
                        <h4 class="m-0 lh1 mt-0 mb-2 fs-6 text-uppercase fw-bold">Vehicle Requirement Details:
                        </h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Date </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {!! $vehicleRequest->getStartDatetime() !!} - {!! $vehicleRequest->getEndDatetime() !!}

                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Purpose of Travel </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <span style="min-height: 150px;">

                            {!! $vehicleRequest->purpose_of_travel !!}
                        </span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="validationGender" class="m-0 text-end flex-grow-1 fw-bold">Accompanying Staff
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {!! $vehicleRequest->getAccompanyingStaffs() !!}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Vehicle Type </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {!! $vehicleRequest->getVehicleTypes() !!}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">For</label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $vehicleRequest->getFor() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Pick Up </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> Time</strong></span>
                                    <span>{{ $vehicleRequest->pickup_time }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> Point</strong></span>
                                    <span>{{ $vehicleRequest->pickup_place }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Travel </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> From</strong></span>
                                    <span>{{ $vehicleRequest->travel_from }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> Destination</strong></span>
                                    <span>{{ $vehicleRequest->destination }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        &nbsp;
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> End Time</strong></span>
                                    <span>{{ $vehicleRequest->getEndDatetime() }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> Number of Overnight Stay</strong></span>
                                    <span>{{ $vehicleRequest->getOvernights() }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Extra Travel from DHQ (in KM)
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $vehicleRequest->extra_travel }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Tentative Cost (in NPR) </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        Rs.{{ $vehicleRequest->tentative_cost }}
                    </div>
                </div>
                <div class="row">

                    <div class="col-9 offset-1">
                        <h4 class="m-0 lh1 mt-4 mb-2 fs-6 text-uppercase fw-bold">Expenses Charging Details:
                        </h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Activity Code </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $vehicleRequest->activityCode->getActivityCode() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Account Code </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $vehicleRequest->accountCode->getAccountCode() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Grants</label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $vehicleRequest->getDonorCode() }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="validationGender" class="m-0 text-end flex-grow-1 fw-bold">Districts
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $vehicleRequest->getDistricts() }}
                    </div>
                </div>


                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Remarks </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $vehicleRequest->remarks }}
                    </div>
                </div>
            </div>

            <hr>

            <div class="row justify-content-between my-3">
                <div class="col-lg-4">
                    <ul class="list-unstyled">
                        <li><strong>Prepared By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {{ $vehicleRequest->getRequesterName() }} </li>
                        <li><strong class="me-1">Title:</strong>
                            {{ $vehicleRequest->requester->employee->latestTenure->getDesignationName() }} </li>
                        <li> <strong class="me-1">Date:</strong>
                            {{ $vehicleRequest->getRequestSubmissionDate() }}
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <ul class="list-unstyled">
                        <li><strong>Recommended By:</strong></li>
                        <li><strong class="me-1">Name:</strong>
                            {{-- {!! $vehicleRequest->getReviewerName() !!} --}}
                        </li>
                        <li><strong class="me-1">Title:</strong>
                            {{-- {!! $vehicleRequest->reviewer?->employee->latestTenure->getDesignationName() !!} --}}
                        </li>
                        <li><strong class="me-1">Date:</strong>
                            {{-- {!! $vehicleRequest->getRequestReviewDate() !!} --}}
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <ul class="list-unstyled">
                        <li><strong>Approved By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {{ $vehicleRequest->getApproverName() }}</li>
                        <li><strong class="me-1">Title:</strong>
                            {{ $vehicleRequest->approver->employee->latestTenure->getDesignationName() }}</li>
                        {{-- <li><strong class="me-1">Date:</strong>{!! $vehicleRequest->approvedLog ? $vehicleRequest->approvedLog->getCreatedAt() : '' !!}</li> --}}
                        <li><strong class="me-1">Date:</strong>{{ $vehicleRequest->getRequestApprovalDate() }}</li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
        <div class="print-footer">
        </div>

    </section>

    <script>
        window.onload = print;
    </script>


@endsection
