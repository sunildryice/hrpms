@extends('layouts.container-report')

@section('title', 'Leave Request')

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
            <div class="fs-8">{{ $leaveRequest->getOfficeName() }}</div>
            <div class="fs-8"> Leave Request</div>
        </div>
        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold mb-2">
                        Leave Request No: {{ $leaveRequest->getLeaveNumber() }}
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
                        <h4 class="m-0 lh1 mt-0 mb-2 fs-6 text-uppercase fw-bold">Leave Request Details:
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
                        {{ $leaveRequest->getRequestDate() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Leave Type </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveRequest->getLeaveType() }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Leave </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> From</strong></span>
                                    <span>{{ $leaveRequest->getStartDate() }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-inline-flex gap-2">
                                    <span> <strong> To</strong></span>
                                    <span>{{ $leaveRequest->getEndDate() }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Remarks </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveRequest->remarks }}
                    </div>
                </div>
                @isset($leaveRequest->review_remarks)
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start  h-100">
                                <label for="" class="m-0 text-end flex-grow-1 fw-bold">HR Remarks </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            {{ $leaveRequest->review_remarks }}
                        </div>
                    </div>
                @endisset
                <div class="row">

                    <div class="col-9 offset-1">
                        <h4 class="m-0 lh1 mt-4 mb-2 fs-6 text-uppercase fw-bold">Leave Request Details:
                        </h4>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start  h-100">
                            <label for="" class="m-0 text-end flex-grow-1 fw-bold">Substitutes </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ $leaveRequest->getSubstitutes() }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-9 offset-1">
                        <h4 class="m-0 lh1 mt-4 mb-2 fs-6 text-uppercase fw-bold">Leave Request Details:
                        </h4>
                    </div>
                </div>
                <div class="row">

                    <div class="col-9 offset-1">
                        <table class="table table-bordered  my-4">
                            <thead>
                                <tr>
                                    <th width="10%">DAY</th>
                                    <th>DATE</th>
                                    <th>LEAVE SLOTS</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($leaveRequest->leaveDays as $leaveDay)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $leaveDay->getLeaveDate() }}</td>
                                        <td>{{ $leaveDay->getLeaveMode() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>

                <div class="row justify-content-between my-3">
                    <div class="col-lg-3">
                        <ul class="list-unstyled">
                            <li><strong>Requested By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {{ $leaveRequest->getRequesterName() }} </li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $leaveRequest->requester->employee->getDesignationName() }}</li>
                            <li> <strong class="me-1">Date:</strong>{{ $leaveRequest->submittedLog->created_at }}
                            </li>
                        </ul>
                    </div>
                    @isset($leaveRequest->verifier_id)
                        <div class="col-lg-3">
                            <ul class="list-unstyled">
                                <li><strong>Reviewed By:</strong></li>
                                <li><strong class="me-1">Name:</strong> {{ $leaveRequest->hrReviewer?->getFullName() }}</li>
                                <li><strong class="me-1">Title:</strong>
                                    {{ $leaveRequest->hrReviewer?->employee->getDesignationName() }}</li>
                                <li><strong class="me-1">Date:</strong>{{ $leaveRequest->verifiedLog->created_at }}
                                </li>
                            </ul>
                        </div>
                    @endisset
                    {{-- <div class="col-lg-3">
                        <ul class="list-unstyled">
                            <li><strong>Recommended By:</strong></li>
                            <li><strong class="me-1">Name:</strong> {{ $leaveRequest->reviewedLog ? $leaveRequest->reviewer->getFullName() : '' }}</li>
                            <li><strong class="me-1">Title:</strong> {{ $leaveRequest->reviewedLog ? $leaveRequest->reviewer->employee->getDesignationName() : '' }}</li>
                            <li><strong class="me-1">Date:</strong>{{ $leaveRequest->reviewedLog ? $leaveRequest->reviewedLog->created_at : '' }}
                            </li>
                        </ul>
                    </div> --}}
                    <div class="col-lg-3">
                        <ul class="list-unstyled">
                            <li><strong>Approved By:</strong></li>
                            <li><strong class="me-1">Name:</strong>
                                {{ $leaveRequest->approvedLog ? $leaveRequest->getApproverName() : '' }}</li>
                            <li><strong class="me-1">Title:</strong>
                                {{ $leaveRequest->approvedLog ? $leaveRequest->approver->employee->getDesignationName() : '' }}
                            </li>
                            <li><strong
                                    class="me-1">Date:</strong>{{ $leaveRequest->approvedLog ? $leaveRequest->approvedLog->created_at : '' }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="print-footer">
        </div>
    </section>


@endsection
