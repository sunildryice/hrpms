@extends('layouts.container-report')

@section('title', 'Travel Request and Travel Claim')
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

        @media print {
            .pagebreak {
                page-break-after: always;
            }
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
            <div class="fs-8"> Travel Authorization </div>
        </div>

        <div class="travel-authorization pagebreak">
            <div class="print-header">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="print-header-info mb-3">
                            <ul class="list-unstyled m-0 p-0 fs-7">
                                <li><span class="fw-bold me-2"> Name :</span><span>Kathmandu</span></li>
                                <li><span class="fw-bold me-2">Title:</span><span>Kathmandu</span>
                                </li>
                                <li><span class="fw-bold me-2">Person Type :</span><span>Jan 14, 2022</span></li>
                                <li><span class="fw-bold me-2">Address :</span><span>CR 1.1</span></li>
                                <li><span class="fw-bold me-2">Duty Station :</span><span>CR 1.1</span></li>
                                <li><span class="fw-bold me-2">Phone :</span><span>CR 1.1</span></li>
                                <li><span class="fw-bold me-2">Accompanying Staff :</span><span>CR 1.1</span></li>
                                <li><span class="fw-bold me-2">Purpose of travel:</span><span>CR 1.1</span></li>
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
                                <li><span class="fw-bold me-2">Departure Date :</span><span>6502</span></li>
                                <li><span class="fw-bold me-2">Return Date :</span><span>Kathmandu</span></li>
                                <li><span class="fw-bold me-2">Issue Date :</span><span>Kathmandu</span></li>

                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <div class="print-body mt-3 mb-5">

                <table class="table border">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th style="width: 30%;">Itinerary</th>
                            <th>Mode of Travel</th>
                            <th>Office/Public</th>
                        </tr>

                    </thead>
                    <tbody>

                        <tr>
                            <td>Jan 14, 2022</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Special Instructions</td>
                            <td colspan="3">X</td>
                        </tr>
                        <tr>
                            <td>Excess Baggage, Air Kilos:</td>
                            <td colspan="3">X</td>
                        </tr>

                    </tbody>
                </table>
                <table class="table border">
                    <thead>
                        <tr>
                            <th style="width: 30%">Estimated Cost</th>
                            <th>Rate</th>
                            <th>Days</th>
                            <th>NRs.</th>
                            <th>Requested Advance:</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DSA (Hetauda, Pokhara, Dharan, Itahari, Ghorepani, Salleri-Solukhumbu DHQ)</td>
                            <td>3,500.00</td>
                            <td>3</td>
                            <td>10,500.00</td>
                            <td>Less: Previous Advance (if Yes)</td>
                        </tr>
                        <tr>
                            <td>DSA (Hetauda, Pokhara, Dharan, Itahari, Ghorepani, Salleri-Solukhumbu DHQ)</td>
                            <td>3,500.00</td>
                            <td>3</td>
                            <td>10,500.00</td>
                            <td>Less: Previous Advance (if Yes)</td>
                        </tr>
                        <tr>
                            <td>DSA (Hetauda, Pokhara, Dharan, Itahari, Ghorepani, Salleri-Solukhumbu DHQ)</td>
                            <td>3,500.00</td>
                            <td>3</td>
                            <td colspan="1">10,500.00</td>
                        </tr>
                        <tr>
                            <td>DSA (Hetauda, Pokhara, Dharan, Itahari, Ghorepani, Salleri-Solukhumbu DHQ)</td>
                            <td>3,500.00</td>
                            <td>3</td>
                            <td>10,500.00</td>
                        </tr>
                        <tr>
                            <td>DSA (Hetauda, Pokhara, Dharan, Itahari, Ghorepani, Salleri-Solukhumbu DHQ)</td>
                            <td>3,500.00</td>
                            <td>3</td>
                            <td>10,500.00</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total:</td>
                            <td>15,500.00</td>
                            <td>Net Advance</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <table class="table border">
                    <thead>
                        <tr>
                            <th>Activity Code</th>
                            <th>From Finance</th>
                            <th>Account Code</th>
                            <th>From Finance</th>
                            <th>Funding Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CR 1.1</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <div class="row mt-4">
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div><strong>Requested By:</strong></div>
                        <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div><strong>Recommended By:</strong></div>
                        <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div><strong>Authorized By:</strong></div>
                        <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                        <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="travel-claim">
            <div class="print-header">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="print-code fs-6 fw-bold mb-3"> Travel Claim Form </div>
                        <div class="print-header-info mb-3">
                            <ul class="list-unstyled m-0 p-0 fs-7">
                                <li><span class="fw-bold me-2"> All Claims must be Accompanied by original TA</span></li>
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
                <table class="table border">
                    <thead>
                        <tr>
                            <th>Name:</th>
                            <th></th>
                            <th>Date:</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Date</td>
                            <td style="width: 60%">Description of Expenses (Other than DSA)</td>
                            <td>Amount</td>
                            <td>Acvt. Code</td>
                        </tr>
                        <tr>
                            <td>21-Mar-22</td>
                            <td>Taxi claim from Bagdol to Airport</td>
                            <td>600.00</td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end">Sub-Total (A)</td>
                            <td colspan="2">600.00</td>
                        </tr>
                    </tfoot>
                </table>

                <table class="table border">
                    <thead>
                        <tr>
                            <th rowspan="2" colspan="2">TRAVEL ITINERARY</th>
                            <th colspan="3" class="text-center">Date</th>
                            <th rowspan="2">No. of overnights</th>
                            <th rowspan="2">DSA rate</th>
                            <th rowspan="2">% of DSA charged</th>
                            <th rowspan="2">Total DSA</th>
                            <th rowspan="2">Activity Code</th>
                        </tr>
                        <tr>
                            <th>Day</th>
                            <th>Month</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DEP:</td>
                            <td>KTM</td>
                            <td>20</td>
                            <td>Mar</td>
                            <td>2022</td>
                            <td rowspan="2">1</td>
                            <td rowspan="2">646464</td>
                            <td rowspan="2">100%</td>
                            <td rowspan="2">747467</td>
                            <td rowspan="2"></td>
                        </tr>
                        <tr>
                            <td>ARR: </td>
                            <td>KTM</td>
                            <td>20</td>
                            <td>Mar</td>
                            <td>2022</td>
                        </tr>
                        <tr>
                            <td>DEP:</td>
                            <td>KTM</td>
                            <td>20</td>
                            <td>Mar</td>
                            <td>2022</td>
                            <td rowspan="2">1</td>
                            <td rowspan="2">646464</td>
                            <td rowspan="2">100%</td>
                            <td rowspan="2">747467</td>
                            <td rowspan="2"></td>
                        </tr>
                        <tr>
                            <td>ARR: </td>
                            <td>KTM</td>
                            <td>20</td>
                            <td>Mar</td>
                            <td>2022</td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-end">Sub-Total (B)</td>
                            <td></td>
                            <td>645464</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-end">Grand Total A+B</td>
                            <td></td>
                            <td>645464</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-end">Advance Taken</td>
                            <td></td>
                            <td>-</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-end">Amount refundable/(reimbursable)</td>
                            <td></td>
                            <td>645464</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="10">I certify that the following information is correct and per the approved
                                Travel
                                authorization. I authorize OHW to treat this as the final claim and I will repay any travel
                                allowances to which I am not entitled. <br>
                                If office provides breakfast, lunch, dinner or accommodation, this must be duducted from
                                claim,
                                i.e.
                                % charge should be 100%-deducted % </td>
                        </tr>
                    </tbody>
                </table>

                <div class="row mt-4">
                    <div class="col-lg-6 mb-4">
                        <div><strong>Claimed By:</strong></div>
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
                        <div><strong>Certified By:</strong></div>
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
        </div>

    </section>



@endsection
