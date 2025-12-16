@extends('layouts.container-report')

@section('title', 'Lieu Leave Request')

@section('page_css')

    <style>
        .table thead th {
            font-size: 0.8rem;
            font-weight: 700;
        }

        .logo-img {
            width: 200px;
        }
    </style>
@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International </div>
            <div class="fs-8">{{ $lieuLeaveRequest->getOfficeName() }}</div>
            <div class="fs-8"> Lieu Leave Request</div>
        </div>
        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold mb-2">
                        Lieu Leave Request No: {{ $lieuLeaveRequest->getRequestId() }}
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

                {{-- Section title --}}
                <div class="row mb-3">
                    <div class="col-10 offset-1">
                        <h4 class="m-0 lh-1 fs-6 text-uppercase fw-bold">
                            Lieu Leave Request Details
                        </h4>
                    </div>
                </div>

                {{-- Detail rows --}}
                <div class="row mb-2">
                    <div class="col-lg-3 text-lg-end fw-bold">
                        <span>Date</span>
                    </div>
                    <div class="col-lg-9">
                        {{ $lieuLeaveRequest->getRequestDate() }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3 text-lg-end fw-bold">
                        <span>Leave Date</span>
                    </div>
                    <div class="col-lg-9">
                        {{ $lieuLeaveRequest->getStartDate() }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3 text-lg-end fw-bold">
                        <span>Reason for Lieu Leave Request</span>
                    </div>
                    <div class="col-lg-9">
                        {{ $lieuLeaveRequest->reason }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-3 text-lg-end fw-bold">
                        <span>Substitutes</span>
                    </div>
                    <div class="col-lg-9">
                        {{ $lieuLeaveRequest->getSubstitutes() }}
                    </div>
                </div>

                <hr>

                {{-- Sign-off section --}}
                <div class="row justify-content-between my-3">
                    {{-- Requested By --}}
                    <div class="col-lg-3 mb-3 mb-lg-0">
                        <h6 class="fw-bold mb-2">Requested By</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li><span class="fw-bold me-1">Name:</span> {{ $lieuLeaveRequest->getRequesterName() }}</li>
                            <li>
                                <span class="fw-bold me-1">Title:</span>
                                {{ $lieuLeaveRequest->requester->employee->getDesignationName() }}
                            </li>
                            <li>
                                <span class="fw-bold me-1">Date:</span>
                                {{ $lieuLeaveRequest->submittedLog->created_at->format('M j, Y') }}
                            </li>
                        </ul>
                    </div>

                    {{-- Approved By --}}
                    <div class="col-lg-3">
                        <h6 class="fw-bold mb-2">Approved By</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li>
                                <span class="fw-bold me-1">Name:</span>
                                {{ $lieuLeaveRequest->approvedLog ? $lieuLeaveRequest->getApproverName() : '' }}
                            </li>
                            <li>
                                <span class="fw-bold me-1">Title:</span>
                                {{ $lieuLeaveRequest->approvedLog ? $lieuLeaveRequest->approver->employee->getDesignationName() : '' }}
                            </li>
                            <li>
                                <span class="fw-bold me-1">Date:</span>
                                {{ $lieuLeaveRequest->approvedLog ? $lieuLeaveRequest->approvedLog->created_at->format('M j, Y') : '' }}
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
