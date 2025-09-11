@extends('layouts.container-report')

@section('title', 'Advance Request Print')
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
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8"> Cash Advance Request Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                    <div class="print-header-info mb-3">
                        <ul class="list-unstyled m-0 p-0 fs-7">
                            <li><span class="fw-bold me-2"> Project :</span><span>Kathmandu</span></li>
                            <li><span class="fw-bold me-2">Activity Code:</span><span>Kathmandu</span>
                            </li>
                            <li><span class="fw-bold me-2">Required Date:</span><span>CR 1.1</span></li>
                            <li><span class="fw-bold me-2">Ref. #</span><span>Jan 14, 2022</span></li>
                            <li><span class="fw-bold me-2">Account Code :</span><span>CR 1.1</span></li>
                            <li><span class="fw-bold me-2">Donor Code:</span><span>CR 1.1</span></li>
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

        <div class="print-body mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table border" style="margin-top: 2.3rem;">
                        <tbody>
                            <tr>
                                <th scope="row">Employee Name:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Title:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">District/Office:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Signature: </th>
                                <td></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table border caption-top">
                        <caption>Activity start and end dates:</caption>
                        <tbody>
                            <tr>
                                <th scope="row">Start Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">End Date:</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Tentative Settlement Date:</th>
                                <td></td>
                            </tr>

                        </tbody>
                    </table>

                </div>
            </div>
            <table class="table border">
                <tbody>
                    <tr>
                        <th colspan="3" scope="row">Requested Amount in Figure:</th>
                    </tr>
                    <tr>
                        <th colspan="3" scope="row">Amount in Words:</th>
                    </tr>
                    <tr>
                        <td>Purpose:</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Amount (Rs.)</td>
                    </tr>
                    <tr>
                        <td>Activity Code</td>
                        <td class="text-center">Description</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>30,000.00 </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>30,000.00 </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>30,000.00 </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>30,000.00 </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end">Total Amount</td>
                        <td>50,000.00</td>
                    </tr>
                </tfoot>
            </table>
            <p>To be filled by Finance Section</p>
            <p>Outstanding Advance, if any:</p>

            <div class="row mt-4">
                <div class="col-lg-6 mb-4">
                    <div><strong>Verified By:</strong></div>
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
                    <div><strong>Reviewed By</strong></div>
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
            <i>Advance should be settled as soon as the task completes but not later than 30 days from the receipt. Any
                outstanding advance should be settled
                before taking any new advance.</i>
            <div>Finance team: Cash Advance released on Date and Time (Button)</div>
        </div>

    </section>

@endsection
