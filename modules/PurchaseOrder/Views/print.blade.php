@extends('layouts.container-report')

@section('title', 'Purchase Order')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        @media print {

            .table th,
            .table td {
                padding: 3px !important;
                line-height: 1;
                font-size: 14px;
            }

            .col-lg-8,
            .col-lg-4 {
                float: left;
                width: 60%;
            }

            .col-lg-4 {
                width: 40%
            }

            .table tfoot th,
            {}
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">{{ $purchaseOrder->getOfficeName() }}</div>
            <div class="fs-8"> Purchase Order</div>
        </div>


        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold">
                        Purchase Order No: {!! $purchaseOrder->getPurchaseOrderNumber() !!}
                        @if ($purchaseOrder->status_id == config('constant.CANCELLED_STATUS'))
                            <span class="text-danger"><strong>(Cancelled)</strong></span>
                        @endif
                    </div>


                    <div class="print-header-info my-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2"> District :
                                    {{ $purchaseOrder->getDistrictNames() }}</span><span></span></li>
                            <li><span class="fw-bold me-2">Vendor's / Suppliers' Name
                                    :</span><span>{!! $purchaseOrder->getSupplierName() !!}</span>
                            </li>
                            <li><span class="fw-bold me-2">Date :</span><span>{!! $purchaseOrder->getOrderDate() !!}</span>
                            </li>
                            {{-- <li><span class="fw-bold me-2">Activity Code :</span><span>CR 1.1</span></li> --}}
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
                        <ul class="list-unstyled m-0 p-0 fs-7 align-self-end">
                            {{-- <li><span class="fw-bold me-2">Account Code :</span><span>6502</span></li> --}}
                            {{-- <li><span class="fw-bold me-2">Donor / Grant :</span><span>Kathmandu</span></li> --}}

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="print-body">
            <div class="fw-bold my-3">Please send the following items(s)</div>
            <table class="table mb-3">
                <thead>
                    <tr>
                        <th>SN.</th>
                        <th>Particular</th>
                        <th>Unit</th>
                        <th>Specification</th>
                        <th>Activity Code</th>
                        <th>Account Code</th>
                        <th>Donor Code</th>
                        <th>Qty.</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $orderItem)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $orderItem->item->title }}</td>
                            <td>{{ $orderItem->getUnitName() }}</td>
                            <td>{{ $orderItem->specification }}</td>
                            <td>{{ $orderItem->activityCode->getActivityCode() }}</td>
                            <td>{{ $orderItem->accountCode->getAccountCode() }}</td>
                            <td>{{ $orderItem->getDonorCode() }}</td>
                            <td>{{ $orderItem->quantity }}</td>
                            <td>{{ $orderItem->unit_price }}</td>
                            <td class="amt">{{ $orderItem->total_price }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="text-end">
                    <tr>
                        <th colspan="8">Sub-total</th>
                        <th>{{ $purchaseOrder->sub_total }}</th>
                    </tr>
                    <tr>
                        <th colspan="8">{{ config('constant.VAT_PERCENTAGE') }}% VAT</th>
                        <th>{{ $purchaseOrder->vat_amount }}</th>
                    </tr>
                    {{-- <tr> --}}
                    {{-- <th colspan="8">Other Cost</th> --}}
                    {{-- <th>{{ $purchaseOrder->other_charge_amount }}</th> --}}
                    {{-- </tr> --}}
                    <tr>
                        <th colspan="8">Total</th>
                        <th>{{ $purchaseOrder->total_amount }}</th>
                    </tr>
                </tfoot>
            </table>
            <p>(In words <span
                    class="border-bottom text-capitalize">{{ \App\Helper::convertNumberToWords($purchaseOrder->total_amount) }}</span>
                Only )</p>
            <p> <strong>Note:</strong> Please deliver by <span
                    class="border-bottom text-capitalize">{{ $purchaseOrder->getDeliveryDate() }}</span> at
                {!! $purchaseOrder->delivery_location ?: 'OHW Office Bagdol-4, Lalitpur' !!} </p>
            <p class="text-decoration-underline mb-1">In case of Delay (Unless the delay is due to force
                majeure)
                following penality wil be charged.</p>
            <div class="row my-3">
                <div class="col-lg-4">
                    <ul class="list-unstyled">
                        <li>1-7 days delay in delivery</li>
                        <li>8-14 days delay in delivery</li>
                        <li>15-21 days delay in delivery</li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <ul class="list-unstyled">
                        <li>0.5% in undelivered Amount</li>
                        <li>1% in undelivered Amount</li>
                        <li>1.5% in undelivered Amount</li>
                    </ul>
                </div>
            </div>
            <p>More than 21 days delay the PO will be terninated or 0.125% on undelivered
                amount per day will be charged.</p>
            <p><strong>Note: </strong> Penalty will be charged in without VAT amount. </p>

            <div class="row justify-content-between my-3">
                {{-- <ul class="list-unstyled w-100">
                            <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                                <span><strong>Prepared By:</strong> </span> <span><strong> Approved By:</strong></span>
                            </li>
                            <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                                <span><strong class="me-2">Name:</strong> {{ $purchaseOrder->getCreatedBy() }} </span>
                                <span><strong class="me-2">Name:</strong> {{ $purchaseOrder->getApproverName() }} </span>
                            </li>
                            <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                                <span><strong class="me-2">Post:</strong>
                                    {{ $purchaseOrder->createdBy->employee->getDesignationName() }} </span>
                                <span><strong class="me-2">Post:</strong>
                                    {{ $purchaseOrder->approver->employee ? $purchaseOrder->approver->employee->getDesignationName() : '' }}
                                </span>
                            </li>
                            <li class="d-flex align-content-between flex-grow-1 justify-content-between">
                                <span><strong class="me-2">Date:</strong>
                                    {{ $purchaseOrder->submittedLog ? $purchaseOrder->submittedLog->getCreatedAt() : '' }}
                                </span>
                                <span><strong class="me-2">Date:</strong>
                                    {{ $purchaseOrder->approvedLog ? $purchaseOrder->approvedLog->getCreatedAt() : '' }}
                                </span>
                            </li>
                        </ul> --}}
                <div class="col-lg-4">
                    <ul class="list-unstyled w-100">
                        <li><strong>Prepared By:</strong></li>
                        <li><strong class="me-2">Name:</strong> {{ $purchaseOrder->getCreatedBy() }}</li>
                        <li><strong
                                class="me-2">Post:</strong>{{ $purchaseOrder->createdBy->employee->getDesignationName() }}
                        </li>
                        <li><strong class="me-2">Date:</strong>
                            {{ $purchaseOrder->submittedLog ? $purchaseOrder->submittedLog->getCreatedAt() : '' }}
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <ul class="list-unstyled w-100 ">
                        <li> <strong> Reviewed By:</strong> </li>
                        <li><strong class="me-2">Name:</strong> {{ $purchaseOrder->getReviewerName() }}</li>
                        <li> <strong class="me-2">Post:</strong>
                            {{ $purchaseOrder->reviewer->employee ? $purchaseOrder->reviewer->employee->getDesignationName() : '' }}
                        </li>
                        <li><strong
                                class="me-2">Date:</strong>{{ $purchaseOrder->reviewedLog ? $purchaseOrder->reviewedLog->getCreatedAt() : '' }}
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <ul class="list-unstyled w-100 ">
                        <li> <strong> Approved By:</strong> </li>
                        <li><strong class="me-2">Name:</strong> {{ $purchaseOrder->getApproverName() }}</li>
                        <li> <strong class="me-2">Post:</strong>
                            {{ $purchaseOrder->approver->employee ? $purchaseOrder->approver->employee->getDesignationName() : '' }}
                        </li>
                        <li><strong
                                class="me-2">Date:</strong>{{ $purchaseOrder->approvedLog ? $purchaseOrder->approvedLog->getCreatedAt() : '' }}
                        </li>
                    </ul>
                </div>
            </div>
            <strong>Received Original purchase order and agreed above the terms and condition.</strong>
            <ul class="list-unstyled my-3">
                <li><strong class="me-2">Name :</strong></li>
                <li><strong class="me-2">Signature :</strong></li>
                <li> <strong class="me-2">Date:</strong></li>
                <li><strong class="me-2">Stamp:</strong></li>
            </ul>
        </div>
        <div class="print-footer"></div>
    </section>
@stop
