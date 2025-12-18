@extends('layouts.container-report')

@section('title', 'Fund Request')
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

    <section class="print-info bg-white p-3" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Fund Request</div>
        </div>

        <div class="print-header mb-4">
            <div class="row">
                <div class="col-lg-8">

                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2">Year:</span><span>2078</span></li>
                            <li><span class="fw-bold me-2">Month</span><span>66765557</span></li>
                            <li><span class="fw-bold me-2">District:</span><span>576545</span></li>
                            <li><span class="fw-bold me-2">Project:</span><span>576545</span></li>
                            <li><span class="fw-bold me-2">NOS:</span><span>576545</span></li>

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
            <table class="table mb-5">
                <thead>
                    <tr>
                        <th>Activity Code</th>
                        <th>Activity Name</th>
                        <th> Estimated Fund required NPR </th>
                        <th> Budget amount NPR</th>
                        <th> Projected Target Unit</th>
                        <th> DIP Target Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Activity 1</td>
                        <td>ABCD</td>
                        <td>111111</td>
                        <td>111111</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Activity 1</td>
                        <td>ABCD</td>
                        <td>111111</td>
                        <td>111111</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Total</td>
                        <td>Total</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2" scope="row">TOTAL FUND REQUIRED</th>
                        <td colspan="4">200000</td>
                    </tr>
                    <tr>
                        <th colspan="2" scope="row">Fund Surplus/(Deficit)</th>
                        <td colspan="4">200000</td>
                    </tr>
                    <tr>
                        <th colspan="2" scope="row">NET FUND REQUIRED</th>
                        <td colspan="4">200000</td>
                    </tr>

                </tbody>
            </table>

            <p>Must be filled by field office.</p>
            <table class="table">
                <tbody>
                    <tr>
                        <th scope="row" style="width: 20%;">Bank Name</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">Account Number</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">Branch</th>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div>Upload Document:</div>

            <div class="row my-4">
                <div class="col-lg-4">
                    <div><strong>Requested By:</strong> Ram Krishna Shrestha </div>
                </div>
                <div class="col-lg-4">
                    <div><strong>Recommended By: </strong>Ram Krishna Shrestha </div>
                </div>
                <div class="col-lg-4">
                    <div><strong>Approved By:</strong>Ram Krishna Shrestha </div>
                </div>
            </div>

            <div>Admin will decide the requester</div>






        </div>
        <div class="print-footer">
        </div>

    </section>

@endsection
