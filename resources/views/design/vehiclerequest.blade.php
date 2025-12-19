@extends('layouts.container-report')

@section('title', 'Vechicle Request')
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

        .table tr th {
            padding: 0.45rem 0.75rem;
            width: 10%;
        }

        .table tr td {
            padding: 0.25rem 0.75rem;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">XYZ Office</div>
        <div class="fs-8"> Vechicle Request Form </div>
    </div>


    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="print-body mb-5">
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="4">Vehicle Requirement Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row" rowspan="2">Travel Date</th>
                    <td>From:</td>
                    <td colspan="2">To:</td>
                </tr>
                <tr>
                    <td colspan="3">Day(s):</td>
                </tr>
                <tr>
                    <th scope="row">Purpose of travel (List all details)</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">User(s)</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">Vehicle Type</th>
                    <td colspan="3">
                        <div class="row">
                            <div class="col-sm-6 col-lg-4">Car</div>
                            <div class="col-sm-6 col-lg-4">Pick-up Jeep</div>
                            <div class="col-sm-6 col-lg-4">SML Truck</div>
                            <div class="col-sm-6 col-lg-4">4WD Jeep Scorpio</div>
                            <div class="col-sm-6 col-lg-4">4WD Bolero</div>
                            <div class="col-sm-6 col-lg-4">Mini Truck</div>
                            <div class="col-sm-6 col-lg-4">4WD Jeep Toyota/Prado/Land Cruiser</div>
                            <div class="col-sm-6 col-lg-4">Other</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Vehicle Needed For</th>
                    <td colspan="3">
                        <div class="row">
                            <div class="col-sm-6 col-lg-3">Full Day</div>
                            <div class="col-sm-6 col-lg-3">Half Day</div>
                            <div class="col-sm-6 col-lg-3">Hour(s)</div>
                            <div class="col-sm-6 col-lg-3">Other</div>

                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pick-up Time:</th>
                    <td></td>
                    <th scope="row">Pick-up Point</th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">Travel From:</th>
                    <td></td>
                    <th scope="row">Destination</th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">End Time:</th>
                    <td></td>
                    <th scope="row">Number of Overnight Stay</th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">Extra travel from DHQ:</th>
                    <td></td>
                    <th scope="row">Tentative Total Cost (NPR)</th>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="4">Expenses Charging Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Activity Code(s)</th>
                    <td></td>
                    <th scope="row">Account Code(s)</th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">District(s)</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">Grants</th>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-lg-4 mb-4">
                <div><strong>Requested By:</strong></div>
                <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                <div><strong>Date:</strong> Ram Krishna Shrestha </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div><strong>Recommended By:</strong></div>
                <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                <div><strong>Date:</strong> Ram Krishna Shrestha </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div><strong>Approved By:</strong></div>
                <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                <div><strong>Date:</strong> Ram Krishna Shrestha </div>
            </div>
        </div>





    </div>



@endsection
