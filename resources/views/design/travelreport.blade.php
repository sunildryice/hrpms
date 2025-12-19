@extends('layouts.container-report')

@section('title', 'Travel Report')
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
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Field Visit Report Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">


                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2">Project:</span><span>Kathmandu</span></li>
                            <li><span class="fw-bold me-2">Visit conducted by:*</span><span>Shreejana Sunuwar and Jayesh
                                    Shrestha</span></li>
                            <li><span class="fw-bold me-2">Visit duration with date*:</span><span>21-Mar-22 to
                                    28-Mar-22</span>
                            </li>
                            <li><span class="fw-bold me-2">Visit Location* </span><span>57654533333</span></li>
                            <li><span class="fw-bold me-2">Ref:</span><span>Kathmandu</span></li>

                            <li><span class="fw-bold me-2">Overview of specific objectives and expected outputs: <br>
                                    *(identified in
                                    TOR format for field visit)</span><span>57654533333</span></li>
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

                    </div>

                </div>
            </div>
        </div>

        <div class="print-body mb-5">
            <div class="my-4">A. Observations</div>
            <table class="table border">
                <tbody>
                    <tr>
                        <td>1</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="my-4">B. Activities conducted *</div>
            <table class="table border">
                <tbody>
                    <tr>
                        <td>1</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="my-4">C. Recommendations and Plan of Action </div>
            <table class="table border">
                <tbody>
                    <tr>
                        <td>SN </td>
                        <td>What?</td>
                        <td>When? </td>
                        <td>Who? </td>
                        <td>Remarks </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                </tbody>
            </table>
            <div>D. Other comments (if any)</div>
            <div style="min-height: 100px;" class="border mb-5"></div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div><strong>Submitted By:</strong></div>
                    <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Position:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Signature:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div><strong>Approved By:</strong></div>
                    <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Position:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Signature:</strong> Ram Krishna Shrestha </div>
                    <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                </div>
            </div>

        </div>
    </section>



@endsection
