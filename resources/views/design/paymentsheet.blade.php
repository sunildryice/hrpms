@extends('layouts.container-report')

@section('title', 'Payment Sheet')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: 'Mukta', sans-serif;
        }

        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.74375rem;

        }

        /* tbody,
                            td,
                            tfoot,
                            th,
                            thead,
                            tr {
                                width: 10%;
                            } */


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

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8">XYZ Office</div>
            <div class="fs-8"> Payment Sheet</div>
        </div>

        <div class="print-header mb-4">
            <div class="row">
                <div class="col-lg-8">

                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2">Vendor Name:</span><span>Kathmandu</span></li>
                            <li><span class="fw-bold me-2">VAT/PAN NO. </span><span>66765557</span></li>
                            <li><span class="fw-bold me-2">Purpose:</span><span>576545</span></li>
                            <li><span class="fw-bold me-2">Bank A/C No. : </span><span>57654533333</span></li>
                            <li><span class="fw-bold me-2">Bank and Branch :</span><span>57654533333</span></li>
                            <li><span class="fw-bold me-2">Email :</span><span>57654533333</span></li>
                            <li><span class="fw-bold me-2">Phone/Mobile :</span><span>57654533333</span></li>
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
                            <li><span class="fw-bold me-2">Ref:</span><span>Kathmandu</span></li>
                            <li><span class="fw-bold me-2">Purchase Order NO.</span><span>66765557</span></li>
                            <li><span class="fw-bold me-2">Invoice No.</span><span>Bill NO. 746646</span></li>
                            <li><span class="fw-bold me-2">Bill Amount</span><span>747646</span></li>
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
                        <th style="width: 13%">Description</th>
                        <th>Activity Code</th>
                        <th>Account Code</th>
                        <th>Donor Code</th>
                        <th>Office Code</th>
                        <th>Percentage</th>
                        <th>Amount</th>
                        <th>VAT</th>
                        <th>Total Amount with VAT</th>
                        <th>Less TDS</th>
                        <th>Net Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Bill No. 65353553</td>
                        <td>PC 2.2</td>
                        <td>999</td>
                        <td>schoonor</td>
                        <td>ktm</td>
                        <td>10%</td>
                        <td>747464</td>
                        <td>74746487</td>
                        <td>74746487</td>
                        <td>74746487</td>
                        <td>74746487</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Bill No. 65353553</td>
                        <td>PC 2.2</td>
                        <td>999</td>
                        <td>schoonor</td>
                        <td>ktm</td>
                        <td>10%</td>
                        <td>747464</td>
                        <td>74746487</td>
                        <td>74746487</td>
                        <td>74746487</td>
                        <td>74746487</td>
                    </tr>
                    <tr>
                        <td colspan="7" class="text-end">Total</td>
                        <td></td>
                        <td>400</td>
                        <td>400</td>
                        <td>400</td>
                        <td>400</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end">Grand Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>


            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div><strong>Submitted By:</strong></div>
                    <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div><strong>Checked By:</strong></div>
                    <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div><strong>Recommended By:</strong></div>
                    <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div><strong>Approved By:</strong></div>
                    <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                </div>
            </div>
        </div>
    </section>



@endsection
