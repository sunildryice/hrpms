@extends('layouts.container-report')

@section('title', 'Employee Requistion')
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
            width: 5%;
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
        <div class="fs-8"> Employee Requistion Form </div>
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
        </div>
    </div>

    <div class="print-body mb-5">
        <p> <strong>INSTRUCTIONS: </strong> Kindly accomplish the form completely. Check the item that corresponds to your
            request and write
            the details needed on the appropriate
            places. Thank you</p>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="4">Position Information</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Position Title</th>
                    <td></td>
                    <th scope="row">Requested Level</th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">Work Station</th>
                    <td></td>
                    <th scope="row">Request Date</th>
                    <td></td>
                </tr>
            </tbody>

        </table>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="4">Types Of Employement</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Full Time Employee</th>
                    <td></td>
                    <th scope="row">Date required from</th>
                    <td></td>

                </tr>
                <tr>
                    <th scope="row">For Fiscal Year</th>
                    <td></td>
                    <th scope="row">Replacement for</th>
                    <td></td>
                </tr>
            </tbody>

        </table>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="4">Reason For Request</th>
                </tr>
                <tr>
                    <td colspan="4">y</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">Is this Position Budgeted? YES </td>
                </tr>
                <tr>
                    <th scope="row">Account Code.:</th>
                    <td></td>
                    <th scope="row">Budget Code</th>
                    <td></td>

                </tr>
                <tr>
                    <th scope="row">Workload: </th>
                    <td>6 hours per week</td>
                    <th scope="row">Duration:</th>
                    <td></td>
                </tr>

            </tbody>

        </table>
        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="3">Qualifications</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="2" scope="column" class="text-center">Required</th>
                    <th scope="column">Prefered</th>
                </tr>
                <tr>
                    <th scope="column">Education</th>
                    <td>k</td>
                    <td>k</td>
                </tr>
                <tr>
                    <th scope="column">WORK EXPERIENCE</th>
                    <td>k</td>
                    <td>k</td>
                </tr>
                <tr>
                    <th scope="column">TOR/JD submitted?</th>
                    <td colspan="2"> YES NO If no, tentative date of submission</td>

                </tr>

            </tbody>

        </table>

        <table class="table border mb-4">
            <thead>
                <tr>
                    <th colspan="3">Logistics Requirements</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                </tr>
                <tr>
                    <td>1</td>
                </tr>

            </tbody>

        </table>



        <div class="row mt-4">
            <div class="col-lg-4 mb-4">
                <div><strong>Requested By:</strong></div>
                <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                <div><strong>Designation :</strong> Ram Krishna Shrestha </div>
                <div><strong>Date:</strong> Ram Krishna Shrestha </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div><strong>Recommended By:</strong></div>
                <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                <div><strong>Designation :</strong> Ram Krishna Shrestha </div>
                <div><strong>Date:</strong> Ram Krishna Shrestha </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div><strong>Approved By:</strong></div>
                <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                <div><strong>Designation :</strong> Ram Krishna Shrestha </div>
                <div><strong>Date:</strong> Ram Krishna Shrestha </div>
            </div>
        </div>





    </div>



@endsection
