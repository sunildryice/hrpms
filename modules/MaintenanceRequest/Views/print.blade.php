@extends('layouts.container-report')

@section('title', 'Maintenance Request Print')
@section('page_css')
    <style>


        table {
            border: 1px solid;
        }

        .table tbody th,
        .table tbody th {
            font-size: 1rem;

        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            width: 10%;
        }


        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
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
            <div class="fs-8">{{ $maintenanceRequest->getOfficeName() }}</div>
            <div class="fs-8"> Maintenance Request</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

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
                <div class="col-lg-12">
                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7 d-flex justify-content-between">
                            <li><span class="fw-bold me-2">Ref.
                                    #</span><span>{{ $maintenanceRequest->getMaintenanceRequestNumber() }}</span>
                            </li>
                            <li>
                                <span class="fw-bold me-2">Request Date :</span>
                                <span>{{ $maintenanceRequest->request_date }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="print-body mb-5">
            <div class="row">
                <div class="col-lg-12">
                    <table class="table border mb-4">
                        <tbody>
                        <tr>
                            <th>Item</th>
                            <th>Purpose</th>
                            <th>Activity</th>
                            <th>Account</th>
                            <th>Donor</th>
                            <th>Estimated Cost</th>
                            <th>Remarks</th>
                        </tr>
                        @foreach($maintenanceRequest->maintenanceRequestItems as $maintenanceRequestItem)
                            <tr>
                                <td>{{ $maintenanceRequestItem->getItemName() }}</td>
                                <td>{{ $maintenanceRequestItem->problem }}</td>
                                <td>{{ $maintenanceRequestItem->getActivityCode() }}</td>
                                <td>{{ $maintenanceRequestItem->getAccountCode() }}</td>
                                <td>{{ $maintenanceRequestItem->getDonorCode() }}</td>
                                <td>{{ $maintenanceRequestItem->estimated_cost }}</td>
                                <td>{{ $maintenanceRequestItem->remarks }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5">Total Amount</td>
                            <td>{{ $maintenanceRequest->estimated_cost }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row mt-4">
                        <div class="col-lg-4 mb-4">
                            <div><strong>Requested By:</strong></div>
                            <div><strong>Name:</strong> {{ $maintenanceRequest->getRequesterName() }} </div>
                            <div><strong>Title:</strong> {{ $requester->getDesignationName() }} </div>
                            <div><strong>Date:</strong> {{ $maintenanceRequest->submittedLog?->created_at?->format('Y-m-d') }}</div>
                        </div>

                        <div class="col-lg-4 mb-4">
                            <div><strong>Recommended By:</strong></div>
                            <div><strong>Name:</strong>
                                {{ $maintenanceRequest->reviewedLog ? $maintenanceRequest->getReviewer() : '' }}
                            </div>
                            <div><strong>Title:</strong>
                                {{ $maintenanceRequest->reviewedLog ? $maintenanceRequest->reviewer->employee->getDesignationName() : '' }}
                            </div>
                            <div><strong>Date:</strong> {{ $maintenanceRequest->reviewedLog?->created_at?->format('Y-m-d')}} </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div><strong>Authorized By:</strong></div>
                            <div><strong>Name:</strong> {{ $maintenanceRequest->getApproverName() }} </div>
                            <div><strong>Title:</strong>
                                {{ $maintenanceRequest->approvedLog ? $maintenanceRequest->approver->employee->getDesignationName() : '' }}
                            </div>
                            <div><strong>Date:</strong> {{ $maintenanceRequest->approvedLog?->created_at?->format('Y-m-d') }} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
