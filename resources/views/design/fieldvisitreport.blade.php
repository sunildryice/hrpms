@extends('layouts.container-report')

@section('title', 'Annual Performance Review Form')
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

        /* tbody,
                    td,
                    tfoot,
                    th,
                    thead,
                    tr {
                        width:5%;
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
            <div class="fs-8"> Field Visit Report Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

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

            <div class="row">
                <div class="col-lg-12">
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                <th scope="row" width="30%">Reference:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Project::</th>
                                <td></td>

                            </tr>
                            <tr>
                                <th scope="row">Visit conducted by:*</th>
                                <td></td>

                            </tr>
                            <tr>
                                <th scope="row">Visit duration with date*:</th>
                                <td>21-Mar-22 to 28-Mar-22</td>

                            </tr>
                            <tr>
                                <th scope="row">Visit Location*:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Overview of specific objectives and expected outputs: *(identified in TOR
                                    format for field visit):</th>
                                <td></td>

                            </tr>

                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">A. Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row" width="15px">1</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">4</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">C. Activities conducted * </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row" width="15px">1</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">4</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="5">C. Recommendations and Plan of Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col">SN</th>
                                <th scope="col">What?</th>
                                <th scope="col">When?</th>
                                <th scope="col">Who?</th>
                                <th scope="col">Remarks?</th>
                            </tr>
                            <tr>
                                <th scope="row">1</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border">
                        <thead>
                            <tr>
                                <th scope="col">D. Other comments (if any)</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td>h</td>
                            </tr>

                        </tbody>
                    </table>
                    <div class="row mt-4">
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
            </div>
        </div>
    </section>

@endsection
