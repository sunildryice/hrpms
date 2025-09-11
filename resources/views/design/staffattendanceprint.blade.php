@extends('layouts.container-report')

@section('title', 'Staff Attendance Print')
@section('page_css')
    <style>
        .table tr th,
        .table tr td {
            font-size: 10px;
            padding: 0.35rem .35rem;
        }

        .table tr td {
            min-width: 30px;
        }

        .table thead th {
            font-size: 0.64375rem;
            text-transform: capitalize;
        }

        .holiday {
            color: red;
        }

        input,
        input:focus-visible {
            outline: none;
            border: none;
            padding: 0.3rem 0.5rem;
        }

        .wrapper {
            position: relative;
            overflow: auto;
            white-space: nowrap;
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
        }

        .first-col {
            width: 150px;
            min-width: 150px;
            max-width: 100px;
            left: 0px;
            z-index: 99 !important;
            background: white !important;
        }

        .print-header-info, .last-row {
            font-size: 0.65rem;
        }


        @media print {
            @page {
                size: auto
            }

            small
             {
                font-size: 0.675em;
            }

            .table tr th,
            .table tr td {
                padding: 0.25rem 0.35rem !important;
            }
        }
    </style>

@endsection




@section('page-content')

    <!-- CSS only -->

    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
                <div class="print-code fs-7 fw-bold">
                    Staff Attendance Record
                </div>
                <div class="print-header-info my-3">
                    <ul class="list-unstyled m-0 p-0">
                        <li><span class="fw-bold me-2">Staff Name:</span><span>Shreejana Sunuwar</span></li>
                        <li><span class="fw-bold me-2">Title:</span><span>Sr.Admin & HR Officer</span></li>
                        <li><span class="fw-bold me-2">Duty station:</span><span>Kathmandu</span></li>
                        <li><span class="fw-bold me-2">Month:</span><span>Janurary</span></li>
                        <li><span class="fw-bold me-2">Year:</span><span>2022</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end brand-logo flex-grow-1">
                    <div class="d-flex flex-column justify-content-end float-right">
                        <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="print-body">
        <div class="wrapper table-responsive mb-3">
            <table class="table table-borderless table-bordered  border mb-0 ">
                <thead>
                    <tr>
                        <th class="sticky-col first-col" scope="row">Days</th>
                        <th scope="column">Fri</th>
                        <th scope="column" class="holiday">Sat</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column">Fri</th>
                        <th scope="column" class="holiday">Sat</th>
                    </tr>
                    <tr>
                        <th class="sticky-col first-col">Date</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column">01</th>
                        <th scope="column" class="holiday">02</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Attendance</th>
                        <th>p</th>
                        <th class="holiday">X</th>
                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Time In (hh:mm) </th>
                        <td>09:00</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Time Out (hh:mm)</th>
                        <td>09:00</td>
                        <td> </td>
                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col"> Hours Worked (hh.hh) </th>
                        <td>01</td>
                        <td> </td>
                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col"> <strong>Hours Worked (hh.mm)</strong> </th>
                        <td>01</td>
                        <td> </td>

                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col"><strong>Time Charge (hh.mm)</strong> </th>
                        <td>01</td>
                        <td> </td>

                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Schooner</th>
                        <td></td>
                        <td> </td>

                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Give2Asia</th>
                        <td></td>
                        <td> </td>

                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                    <tr class="total">
                        <th scope="row" class="sticky-col first-col"> <strong>Hours Charged (hh.mm)</strong> </th>
                        <td>01</td>
                        <td> </td>
                    </tr>
                </tfoot>

            </table>
        </div>
        <div>
            <div class="print-code fs-7 fw-bold">Abbreviation</div>
            <div class="row">
                <div class="col-lg-3">
                    <small>P: Present @ Office</small>
                </div>
                <div class="col-lg-3">
                    <small>H :Annual Holiday: OHW approved (13 days/year) </small>
                </div>
                <div class="col-lg-3">
                    <small>X : Weekends</small>
                </div>
                <div class="col-lg-3">
                    <small>H :Annual Holiday: OHW approved (13 days/year) </small>
                </div>
                <div class="col-lg-3">
                    <small>T : Travel @ Office Field Work</small>
                </div>
                <div class="col-lg-3">
                    <small>TL : Time Off in Lieu</small>
                </div>
                <div class="col-lg-3">
                    <small>A½ : Half Day Annual Leave </small>
                </div>
                <div class="col-lg-3">
                    <small>S½ : Half Day Sick Leave</small>
                </div>
                <div class="col-lg-3">
                    <small>ML : Maternity Leave</small>
                </div>
                <div class="col-lg-3">
                    <small>H : Annual Holiday: OHW approved (13 days/year) </small>
                </div>
                <div class="col-lg-3">
                    <small>NPL : No paid Leave </small>
                </div>
                <div class="col-lg-3">
                    <small>A¼ : Two Hours Annual Leave </small>
                </div>
                <div class="col-lg-3">
                    <small>BL : Bereavement Leave</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-lg-6">
            <div class="print-code fs-7 fw-bold mb-2">Remarks: if any
                <div class="border" style="height: 125px;"></div>
            </div>

        </div>
        <div class="col-lg-6">
            <table class="table table-borderless table-bordered  mb-0">
                <thead>
                    <tr>
                        <th colspan="5" class="text-center">Summary of Annual Leave (In Hour)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="column">Leave</th>
                        <th scope="column">Carry over </th>
                        <th scope="column">Earned</th>
                        <th scope="column">Taken</th>
                        <th scope="column">Balance</th>
                    </tr>
                    <tr>
                        <th scope="row">Annual</th>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                    </tr>
                    <tr>
                        <th scope="row">Sick</th>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                    </tr>
                    <tr>
                        <th scope="row">Others</th>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                    </tr>
                    <tr>
                        <th scope="row">Total</th>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                        <td>32</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row last-row">
        <div class="col-sm-6 col-lg-4 mb-4">
            <div><strong>Submitted By:</strong></div>
            <div><strong>Name:</strong> Ram Krishna Shrestha </div>
            <div><strong>Signature:</strong> Ram Krishna Shrestha </div>
            <div><strong>Position:</strong> Ram Krishna Shrestha </div>
            <div><strong>Date:</strong> Ram Krishna Shrestha </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div><strong>Verified By:</strong></div>
            <div><strong>Name:</strong> Ram Krishna Shrestha </div>
            <div><strong>Signature:</strong> Ram Krishna Shrestha </div>
            <div><strong>Position:</strong> Ram Krishna Shrestha </div>
            <div><strong>Date:</strong> Ram Krishna Shrestha </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div><strong>Approved By:</strong></div>
            <div><strong>Name:</strong> Ram Krishna Shrestha </div>
            <div><strong>Signature:</strong> Ram Krishna Shrestha </div>
            <div><strong>Position:</strong> Ram Krishna Shrestha </div>
            <div><strong>Date:</strong> Ram Krishna Shrestha </div>
        </div>
    </div>



    </div>



@endsection
