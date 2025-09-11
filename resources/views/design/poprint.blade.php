@extends('layouts.container-report')

@section('title', 'Work Log')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: 'Mukta', sans-serif;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->
    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8"> Purchase Order</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code fs-7 fw-bold mb-3">
                        Purchase Order No: PO-09
                    </div>

                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2"> Name :</span><span>Kathmandu</span></li>
                            <li><span class="fw-bold me-2">Vendor's / Suppliers' Name :</span><span>Kathmandu</span>
                            </li>
                            <li><span class="fw-bold me-2">Date :</span><span>Jan 14, 2022</span></li>
                            <li><span class="fw-bold me-2">Activity Code :</span><span>CR 1.1</span></li>
                            <li><span class="fw-bold me-2">Account Code :</span><span>6502</span></li>
                            <li><span class="fw-bold me-2">Doror / Grant :</span><span>Kathmandu</span></li>
                        </ul>
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
        <div class="print-body">
            <div class="fw-bold me-2">Please send the following items(s)</div>

            <table class="table mb-3">
                <thead>
                    <tr>
                        <th>SN.</th>
                        <th>Particular</th>
                        <th>Unit</th>
                        <th>Qty.</th>
                        <th>Rate</th>
                        <th class="amount-td">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td> 1 </td>
                        <td> Particular Information </td>
                        <td>PC</td>
                        <td>3000</td>
                        <td>200</td>
                        <td class="amount-td">6000</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="amount-td" colspan="5">Sub-total</th>
                        <th class="amount-td">6000</th>
                    </tr>
                    <tr>
                        <th class="amount-td" colspan="5">13% VAT</th>
                        <th class="amount-td">6000</th>
                    </tr>
                    <tr>
                        <th class="amount-td" colspan="5">Other Cost</th>
                        <th class="amount-td">6000</th>
                    </tr>
                    <tr>
                        <th class="amount-td" colspan="5">Total</th>
                        <th class="amount-td">6000</th>
                    </tr>
                </tfoot>
            </table>
            <p>(In words <span class="border-bottom text-capitalize">One thousand ten </span> Only )</p>
            <div> <strong>Note:</strong> Please deliver by <span class="border-bottom text-capitalize">Date</span> at OHW
                Office Bagdol
                - 4 Lalitpur </div>
            <div class="mt-2 ">
                <p class="text-decoration-underline mb-1">In case of Delary (Unless the delay is due to force majeure)
                    following penality wil be charged.</p>
                <ul class="list-unstyled w-50">
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between"><span>1-7 days delay in
                            delivery </span> <span>0.5% in undelivered Amount</span></li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between"><span>8-14 days delay
                            in delivery </span> <span>0.5% in undelivered Amount</span></li>
                    <li class="d-flex align-content-between flex-grow-1 justify-content-between"><span>15-21 days delay
                            in delivery </span> <span>0.5% in undelivered Amount</span></li>
                </ul>
                <p class="mb-1">More than 21 days delay the PO will be terninated or 0.125% on undelivered
                    amount per day will be charged.</p>
                <div> <strong> Note: </strong> Penalty will be charged in without VAT amount.</div>
                <div class="row my-3">
                    <div class="col-lg-6 mb-4">
                        <div><strong>Prepared By:</strong></div>
                        <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Post:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div><strong>Approved By:</strong></div>
                        <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Position:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                    </div>
                </div>

                <p class="fw-bold ">Recived Orginal purchase order and agreed above the terms and
                    condition.</p>

                <div class="row my-3">
                    <div class="col-lg-6 mb-4">
                        <div><strong>Signature:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Stamp:</strong> Ram Krishna Shrestha </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="print-footer">
        </div>

    </section>


@endsection
