@extends('layouts.container-report')

@section('title', 'Leave Encash Request')

@section('page_css')

    <style>
        .table thead th {
            font-size: 0.8rem;
            font-weight: 700;
        }
    </style>
@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8">{{ $leaveEncash->getOfficeName() }}</div>
            <div class="fs-8"> Leave Encashment</div>
        </div>
        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold mb-2">
                        Leave Encash Request No: {{ $leaveEncash->getEncashNumber() }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                            <div class="d-flex flex-column justify-content-end float-right">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
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
                        <h4 class="m-0 lh1 mt-0 mb-2 fs-6 text-uppercase fw-bold">Leave Encashment Details:
                        </h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="validationfullname" class="m-0 text-end flex-grow-1 fw-bold">Request Date </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveEncash->getRequestDate() }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Employee Name </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveEncash->getEmployeeName() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Employee Title </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveEncash->employee->getDesignationName() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Leave Type </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveEncash->getLeaveType() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Available Balance:</label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveEncash->available_balance . ' ' . $leaveEncash->leaveType->getLeaveBasis() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Encash Balance </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveEncash->encash_balance . ' ' . $leaveEncash->leaveType->getLeaveBasis() }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Remarks </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveEncash->remarks }}
                    </div>
                </div>
                <hr>

                <div class="row justify-content-between my-3">
                    <div class="col">
                        <ul class="list-unstyled">
                            <li><strong>Requested By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {{ $leaveEncash->getRequesterName() }} </li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $leaveEncash->requester->employee->getDesignationName() }}</li>
                            <li> <strong class="me-1">Date:</strong>{{ $leaveEncash->submittedLog->created_at }}
                            </li>
                        </ul>
                    </div>
                    <div class="col">
                        <ul class="list-unstyled">
                            <li><strong>Reviewed By:</strong></li>
                            <li><strong class="me-1">Name:</strong>
                                {{ $leaveEncash->reviewedLog ? $leaveEncash->reviewer->getFullName() : '' }}</li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $leaveEncash->reviewedLog ? $leaveEncash->reviewer->employee->getDesignationName() : '' }}
                            </li>
                            <li><strong
                                    class="me-1">Date:</strong>{{ $leaveEncash->reviewedLog ? $leaveEncash->reviewedLog->created_at : '' }}
                            </li>
                        </ul>
                    </div>
                    <div class="col">
                        <ul class="list-unstyled">
                            <li><strong>Approved By:</strong></li>
                            <li><strong class="me-1">Name:</strong>
                                {{ $leaveEncash->approvedLog ? $leaveEncash->getApproverName() : '' }}</li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $leaveEncash->approvedLog ? $leaveEncash->approver->employee->getDesignationName() : '' }}
                            </li>
                            <li><strong
                                    class="me-1">Date:</strong>{{ $leaveEncash->approvedLog ? $leaveEncash->approvedLog->created_at : '' }}
                            </li>
                        </ul>

                    </div>
                    @if (isset($leaveEncash->paid_at))
                        <div class="col">
                            <ul class="list-unstyled">
                                <li><strong>Paid By:</strong></li>
                                <li><strong class="me-1">Name:</strong>
                                    {{ $leaveEncash->getPayerName() }}</li>
                                <li><strong class="me-1">Title:</strong>
                                    {{ $leaveEncash->getPayerDesignation() }}
                                </li>
                                <li><strong class="me-1">Date:</strong>{{ $leaveEncash->pay_date->format('M d, Y') }}
                                </li>
                            </ul>

                        </div>
                    @endif
                </div>
            </div>
        </div>
        </div>
        <div class="print-footer">
        </div>
    </section>
@endsection
