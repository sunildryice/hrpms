@extends('layouts.container-report')

@section('title', 'Travel Authorization')
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


    <section class="print-info bg-white p-3" id="print-info">
        <div class="print-title fw-bold mb-3 translate-middle text-center ">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Travel Authorization</div>
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

            <div class="row">
                <div class="col-lg-12">
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                <th scope="row">Name:</th>
                                <td></td>
                                <th scope="row">Title:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Person Type:</th>
                                <td></td>
                                <th scope="row">Address:</th>
                                <td></td>

                            </tr>
                            <tr>
                                <th scope="row">Duty Station: </th>
                                <td></td>
                                <th scope="row">Phone:</th>
                                <td></td>

                            </tr>
                            <tr>
                                <th scope="row">Accompanying Staff:</th>
                                <td colspan="3"></td>

                            </tr>
                            <tr>
                                <th scope="row">Purpose of travel: </th>
                                <td colspan="3">travel</td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                <th scope="row">Departure Date: :</th>
                                <td></td>
                                <th scope="row">Return Date:</th>
                                <td></td>
                                <th scope="row">Issue Date:</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th colspan="2">Itinerary</th>
                                <th colspan="2">Mode of Travel</th>
                                <th colspan="2">Office/Public</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>h</td>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td>h</td>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td>h</td>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th scope="row">Special Instructions </td>
                                <td colspan="6">X</td>
                            </tr>
                            <tr>
                                <th scope="row">Excess Baggage, Air Kilos: </td>
                                <td colspan="6">X</td>
                            </tr>
                            <tr>
                                <th scope="row">Estimated Cost</td>
                                <td></td>
                                <th scope="col">Rate</th>
                                <th scope="col">Days</th>
                                <th scope="col">NRs</th>
                                <th scope="col">Requested Advance</th>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2">DSA (Hetauda, Pokhara, Dharan, Itahari, Ghorepani, Salleri-Solukhumbu
                                    DHQ)</td>
                                <td>3500</td>
                                <td>3</td>
                                <td>10,500</td>
                                <td>Less: Previous Advance (if Yes)</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2">DSA (Biratmode, Damak, Bardibas, Chandrapur of Rautahat, Butwal, Ilam
                                    DHQ, Phikkal of Ilam, Charikot-Dhulikhel-Kavre DHQ) </td>
                                <td>3500</td>
                                <td>3</td>
                                <td>10,500</td>
                                <td>Less: Previous Advance (if Yes)</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2">DSA (All other DHQ, Lahan of Siraha, Mahadevsthan of Khotang)</td>
                                <td>3500</td>
                                <td>3</td>
                                <td>10,500</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="2">DSA (All other non-DHQ)</td>
                                <td>3500</td>
                                <td>3</td>
                                <td>-</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th scope="row" colspan="4" class="text-end">Total: </th>
                                <td>150000</td>
                                <th scope="row">Net Advance</th>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table border mb-4">
                        <tbody>
                            <tr>
                                <th scope="row">Activity Code</th>
                                <td> From Finance</td>
                                <th scope="row">Account Code</th>
                                <td> From Finance</td>
                                <th scope="row">Funding Amount </th>
                                <td>-</td>
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
                            <div><strong>Authorized By:</strong></div>
                            <div><strong>Name:</strong> Ram Krishna Shrestha </div>
                            <div><strong>Title:</strong> Ram Krishna Shrestha </div>
                            <div><strong>Date:</strong> Ram Krishna Shrestha </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
