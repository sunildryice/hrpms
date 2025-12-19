@extends('layouts.container-report')

@section('title', 'Payment Sheet')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

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
@section('page_js')

@endsection

@section('page-content')


    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">{{ $paymentSheet->requester->getOfficeName() }}</div>
        <div class="fs-8"> Payment Sheet </div>
    </div>

    <div class="print-header mb-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="print-code fs-6 fw-bold mb-3">

                </div>

                <div class="print-header-info mb-3">
                    <ul class="list-unstyled m-0 p-0 fs-7">
                        <li><span class="fw-bold me-2">Vendor
                                Name:</span><span>{{ $paymentSheet->getSupplierName() }}</span></li>
                        <li><span class="fw-bold me-2">VAT/PAN NO.
                            </span><span>{{ $paymentSheet->getSupplierVatPanNumber() }}</span></li>
                        <li><span class="fw-bold me-2">Purpose:</span><span>{{ $paymentSheet->purpose }}</span></li>
                        <li><span class="fw-bold me-2">Bank A/C No. :
                            </span><span>{{ $paymentSheet->supplier->account_number }}</span></li>
                        <li><span class="fw-bold me-2">Bank and Branch
                                :</span><span>{{ $paymentSheet->supplier->bank_name . ', ' . $paymentSheet->supplier->branch_name }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Email
                                :</span><span>{{ $paymentSheet->supplier->email_address }}</span></li>
                        <li><span class="fw-bold me-2">Phone/Mobile
                                :</span><span>{{ $paymentSheet->supplier->contact_number }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                        </div>

                    </div>
                    <ul class="list-unstyled m-0 p-0 fs-7 align-self-end">
                        <li><span class="fw-bold me-2">Ref:</span><span>{{ $paymentSheet->getPaymentSheetNumber() }}</span>
                        </li>
                        @php
                            $purchaseOrderNumbers = $paymentSheet->purchaseOrders
                                ->map(function ($purchaseOrder) {
                                    return $purchaseOrder->getPurchaseOrderNumber();
                                })
                                ->join(', ');
                        @endphp
                        <li><span class="fw-bold me-2">Purchase Order NO.</span><span>{{ $purchaseOrderNumbers }}</span>
                        </li>
                        {{--                        <li><span class="fw-bold me-2">Invoice No.</span> --}}
                        {{--                            <span>Bill NO. --}}
                        {{--                                {{ $paymentSheet->getPaymentBillNumber() }}</span> --}}
                        {{--                        </li> --}}
                        <li><span class="fw-bold me-2">Bill Amount</span><span>{{ $paymentSheet->net_amount }}</span></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="print-body mb-5">
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th>SN</th>
                    <th style="width: 15%">Bill Number</th>
                    <th>Activity Code</th>
                    <th>Account Code</th>
                    <th>Donor Code</th>
                    <th>Charged Office Code</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>VAT</th>
                    <th>Total Amount with VAT</th>
                    <th>Less TDS</th>
                    <th>Net Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentSheet->paymentSheetDetails as $key => $paymentSheetDetail)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $paymentSheetDetail->getBillNumber() }}</td>
                        <td>{{ $paymentSheetDetail->activityCode->getActivityCode() }}</td>
                        <td>{{ $paymentSheetDetail->accountCode->getAccountCode() }}</td>
                        <td>{{ $paymentSheetDetail->getDonorCode() }}</td>
                        <td>{{ $paymentSheetDetail->getChargedOffice() }}</td>
                        <td>{{ $paymentSheetDetail->getDescription() }}</td>
                        <td>{{ $paymentSheetDetail->total_amount }}</td>
                        <td>{{ $paymentSheetDetail->vat_amount }}</td>
                        <td>{{ $paymentSheetDetail->amount_with_vat }}</td>
                        <td>{{ $paymentSheetDetail->tds_amount }}</td>
                        <td>{{ $paymentSheetDetail->net_amount }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-end">Total</td>
                    <td>{{ $paymentSheet->total_amount }}</td>
                    <td>{{ $paymentSheet->vat_amount }}</td>
                    <td>{{ $paymentSheet->getTotalAmountWithVat() }}</td>
                    <td>{{ $paymentSheet->tds_amount }}</td>
                    <td>{{ $paymentSheet->net_amount }}</td>
                </tr>
                <tr>
                    <td colspan="11" class="text-end">Deduction</td>
                    <td>{{ $paymentSheet->deduction_amount }}</td>
                </tr>
                <tr>
                    <th colspan="11" class="text-end">Grand Total</th>
                    <th>{{ $paymentSheet->net_amount - $paymentSheet->deduction_amount }}</th>
                </tr>
                <tr>
                    <th class="text-end">Deduction Remarks</th>
                    <th colspan="100%">{{ $paymentSheet->deduction_remarks }}</th>
                </tr>
            </tbody>
        </table>


        <div class="row">
            <div class="col-lg-6 mb-4">
                <div><strong>Submitted By:</strong></div>
                <div><strong>Name:</strong> {{ $paymentSheet->requester->getFullName() }} </div>
                <div><strong>Title:</strong> {{ $paymentSheet->requester->employee->designation->title }} </div>
                <div><strong>Date:</strong> {{ $paymentSheet->getSubmittedDate() }} </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div><strong>Checked By:</strong></div>
                <div><strong>Name:</strong> {{ $paymentSheet->verifier->getFullName() }}</div>
                <div><strong>Title:</strong> {{ $paymentSheet->verifier->employee->designation->title }}</div>
                <div><strong>Date:</strong> {{ $paymentSheet->getVerifiedDate() }}</div>
            </div>
            <div class="col-lg-6 mb-4">
                <div><strong>Recommended By:</strong></div>
                <div><strong>Name:</strong> {{ $paymentSheet->recommender->getFullName() }}</div>
                <div><strong>Title:</strong> {{ $paymentSheet->recommender->employee->designation->title }}</div>
                <div><strong>Date:</strong> {{ $paymentSheet->getRecommendedDate() }}</div>
            </div>
            <div class="col-lg-6 mb-4">
                <div><strong>Approved By:</strong></div>
                <div><strong>Name:</strong> {{ $paymentSheet->approver->getFullName() }} </div>
                <div><strong>Title:</strong> {{ $paymentSheet->approver->employee->designation->title }} </div>
                <div><strong>Date:</strong> {{ $paymentSheet->getApprovedDate() }} </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = print;
    </script>


@endsection
