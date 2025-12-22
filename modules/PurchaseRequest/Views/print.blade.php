@extends('layouts.container-report')

@section('title', 'Purchase Request')

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>
    <!-- CSS only -->



    <section class="p-3 bg-white print-info" id="print-info">

        <div class="mb-3 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $purchaseRequest->getOfficeName() }}</div>
            <div class="fs-8"> Purchase Request</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-2 print-code fs-7 fw-bold">
                        Purchase Request No: {!! $purchaseRequest->getPurchaseRequestNumber() !!}
                    </div>

                    <div class="mb-3 print-header-info">
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            <li><span class="fw-bold me-1">Request Date :</span><span>{!! $purchaseRequest->getRequestDate() !!}</span></li>
                            <li><span class="fw-bold me-1">Required Date :</span><span>{!! $purchaseRequest->getRequiredDate() !!}</span></li>
                            <li><span class="fw-bold me-1">District :</span><span>{!! $purchaseRequest->getDistrictNames() !!}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt=""
                                    class="align-self-end pe-5 logo-img">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="print-body">
            <table class="table mb-3">
                <thead>
                    <tr>
                        <th>SN.</th>
                        <th style="width: 25%;">Activity Code</th>
                        <th>Account Code</th>
                        <th>Donor Code</th>
                        <th>Office</th>
                        <th>Particular</th>
                        <th>Specification</th>
                        <th>Unit</th>
                        <th>Qty.</th>
                        <th class="amount-td">Tentative Rate</th>
                        <th class="amount-td">Tentative Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseRequest->purchaseRequestItems as $index => $purchaseRequestItem)
                        <tr>
                            <td>{!! $index + 1 !!}</td>
                            <td>{!! $purchaseRequestItem->activityCode->getActivityCode() !!}</td>
                            <td>{!! $purchaseRequestItem->accountCode->getAccountCode() !!}</td>
                            <td>{!! $purchaseRequestItem->getDonorCode() !!}</td>
                            <td>{!! $purchaseRequestItem->getOffice() !!}</td>
                            <td>{!! $purchaseRequestItem->getItemName() !!}</td>
                            <td>{!! $purchaseRequestItem->specification !!}</td>
                            <td>{!! $purchaseRequestItem->getUnitName() !!}</td>
                            <td>{!! $purchaseRequestItem->quantity !!}</td>
                            <td class="amount-td">{!! $purchaseRequestItem->unit_price !!}</td>
                            <td class="amount-td">{!! $purchaseRequestItem->total_price !!}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="amount-td" colspan="10">Total Tentative Amount</th>
                        <th class="amount-td">{!! number_format($purchaseRequest->purchaseRequestItems->sum('total_price'), 2) !!}</th>
                    </tr>
                    {{-- <tr>
                        <th colspan="4"></th>
                        <th class="amount-td">Balance Budget</th>
                        <th class="amount-td">{!! number_format($purchaseRequest->balance_budget,2) !!}</th>
                        <th class="amount-td">Less Commitments*</th>
                        <th class="amount-td">{!! number_format($purchaseRequest->commitment_amount,2) !!}</th>
                        <th class="amount-td">Estimated Balance Budget</th>
                        <th class="amount-td">{!! number_format($purchaseRequest->estimated_balance_budget, 2) !!}</th>
                    </tr> --}}
                </tfoot>
            </table>

            <div class="card">
                <div class="card-header fw-bold">
                    Purchase Request Budget
                </div>
                <div class="card-body">
                    <div class="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Office</th>
                                    <th scope="col">Activity</th>
                                    <th scope="col">Total Est. Amount</th>

                                    <th scope="col">Balance Budget</th>
                                    <th scope="col">Commitment Amount</th>
                                    <th scope="col">Estimated Balance Budget</th>
                                    <th scope="col">Budgeted</th>
                                    <th scope="col">Justification (if not budgeted)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseRequest->purchaseRequestBudgets as $prBudget)
                                    <tr>
                                        <td>{{ $prBudget->getOffice() }}</td>
                                        <td>{{ $prBudget->activityCode?->getActivityCode() }}</td>
                                        <td>{{ $purchaseRequest->purchaseRequestItems()->select('total_price')->where('activity_code_id', $prBudget->activity_code_id)->where('office_id', $prBudget->office_id)->sum('total_price') }}
                                        </td>
                                        <td>{{ $prBudget->balance_budget }}</td>
                                        <td>{{ $prBudget->commitment_amount }}</td>
                                        <td>{{ $prBudget->estimated_balance_budget }}</td>
                                        <td>{{ (bool) $prBudget->budgeted ? 'Yes' : 'No' }}</td>
                                        <td>{{ $prBudget->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p>*Commitments are the approved PR/PO/Contracts and outstanding payments/settlements those are yet not
                accounted
                for in the expenditure.</p>
            <p>Is this procurement budgeted? <span class="text-capitalize">{!! $purchaseRequest->getBudgeted() !!}</span></p>
            @if ($purchaseRequest->getBudgeted() != 'Yes')
                <p>Budget Description : <span class="text-capitalize">{!! $purchaseRequest->getBudgetDescription() !!}</span></p>
            @endif
            <p> <strong>Delivery Instructions : </strong> <span class="text-capitalize">{!! $purchaseRequest->delivery_instructions !!}</span></p>
            <p> <strong>Purpose :</strong> <span class="text-capitalize">{!! $purchaseRequest->purpose !!}</span></p>
            @if ($purchaseRequest->modification_number)
                <p> <strong>Amendment Remarks :</strong> <span class="text-capitalize">{!! $purchaseRequest->modification_remarks !!}</span></p>
            @endif

            <div class="mt-2 d-flex">
                <strong>Note: </strong>
                <ul class="list-unstyled ms-2">
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span>Approval limits apply to thresholds specified in OHW policy.</span>
                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span>If the estimated price varies >10%, to the actual price, additional approval required.</span>
                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span>Please use separate PR for separate activity as can as possible, if there is small number (1
                            or 2
                            items only) use additional sheet as shown in additional sheet
                        </span>
                    </li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                        <span>If PR for different districts use additional sheet to separate districts</span>
                    </li>
                </ul>
            </div>

            <div class="my-3 row justify-content-between">
                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Prepared By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {{ $purchaseRequest->getRequesterName() }} </li>
                        <li><strong class="me-1">Title:</strong>
                            {{ $purchaseRequest->submittedLog?->getDesignation() }} </li>
                        <li> <strong class="me-1">Date:</strong>{!! $purchaseRequest->submittedLog ? $purchaseRequest->submittedLog->getCreatedAt() : '' !!}
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Reviewed By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {!! $purchaseRequest->verifiedLog ? $purchaseRequest->verifiedLog->getCreatedBy() : '' !!}</li>
                        <li><strong class="me-1">Title:</strong>
                            {!! $purchaseRequest->verifiedLog ? $purchaseRequest->verifiedLog->getDesignation() : '' !!}
                        </li>
                        <li><strong class="me-1">Date:</strong>{!! $purchaseRequest->verifiedLog ? $purchaseRequest->verifiedLog->getCreatedAt() : '' !!}
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Recommended By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {!! $purchaseRequest->recommender->getFullName() !!}</li>
                        <li><strong class="me-1">Title:</strong>
                            {!! $purchaseRequest->recommendedLog?->getDesignation() !!}
                        </li>
                        <li><strong class="me-1">Date:</strong>{!! $purchaseRequest->getRecommendedDate() !!}
                        </li>
                    </ul>
                </div>
                {{-- <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Recommended By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {!! $purchaseRequest->reviewedLog ? $purchaseRequest->reviewedLog->getCreatedBy() : '' !!}</li>
                        <li><strong class="me-1">Title:</strong>
                            {!! $purchaseRequest->reviewedLog
                                ? $purchaseRequest->reviewedLog->createdBy->employee->getDesignationName()
                                : '' !!}
                        </li>
                        <li><strong class="me-1">Date:</strong>{!! $purchaseRequest->reviewedLog ? $purchaseRequest->reviewedLog->getCreatedAt() : '' !!}
                        </li>
                    </ul>
                </div> --}}

                <div class="col-lg-3">
                    <ul class="list-unstyled">
                        <li><strong>Approved By:</strong></li>
                        <li><strong class="me-1">Name:</strong> {!! $purchaseRequest->getApproverName() !!}</li>
                        <li><strong class="me-1">Title:</strong>
                            {{ $purchaseRequest->approvedLog?->getDesignation() }}</li>
                        <li><strong class="me-1">Date:</strong>{!! $purchaseRequest->approvedLog ? $purchaseRequest->approvedLog->getCreatedAt() : '' !!}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
        <div class="print-footer">
        </div>

    </section>


@endsection
