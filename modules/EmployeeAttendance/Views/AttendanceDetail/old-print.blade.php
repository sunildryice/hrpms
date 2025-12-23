@extends('layouts.container-report')

@section('title', 'Staff Attendance Print')
@section('page_css')
    <style>
        .table tr th,
        .table tr td {
            font-size: 9px;
            padding: 0.35rem 0.15rem;
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

        .print-header-info,
        .last-row {
            font-size: 0.7rem;
        }


        @media print {
            @page {
                size: auto
            }

            small {
                font-size: 0.675em;
            }

            .table tr th,
            .table tr td {
                font-size: 8px;
                padding: 0.25rem 0.25rem !important;
            }
        }
    </style>

@endsection

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#attendance-approve-index').addClass('active');
        });
    </script>
@endsection




@section('page-content')

    <!-- CSS only -->

    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
                <div class="print-code fs-7 fw-bold mb-2">
                    Staff Attendance Record
                </div>
                <div class="print-header-info mb-3">
                    <ul class="list-unstyled m-0 p-0">
                        <li><span class="fw-bold me-2">Staff
                                Name:</span><span>{{ $attendance->employee->getFullName() }}</span></li>
                        <li><span
                                class="fw-bold me-2">Title:</span><span>{{ $attendance->employee->latestTenure->getDesignationName() }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Duty
                                station:</span><span>{{ $attendance->employee->latestTenure->getOfficeName() }}</span></li>
                        <li><span
                                class="fw-bold me-2">Month:</span><span>{{ date('F', mktime(0, 0, 0, $attendance->month, 10)) }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Year:</span><span>{{ $attendance->year }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end brand-logo flex-grow-1">
                    <div class="d-flex flex-column justify-content-end float-right">
                        <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="print-body">
        <div class="wrapper mb-2">
            <table class="table table-borderless table-bordered  border mb-0 ">
                <thead>
                    <tr>
                        <th class="sticky-col first-col" scope="row">Days</th>
                        @foreach ($dates as $date)
                            @if ($date->get('holiday'))
                                <th scope="column" class="holiday">{{ $date->get('day_name') }}</th>
                            @else
                                <th scope="column">{{ $date->get('day_name') }}</th>
                            @endif
                        @endforeach
                        <th scope="column">Total</th>
                        <th scope="column">Charge</th>
                    </tr>

                    <tr>
                        <th class="sticky-col first-col">Date</th>
                        @foreach ($dates as $date)
                            @if ($date->get('holiday'))
                                <th class="holiday">{{ $date->get('day') }}</th>
                            @else
                                <th scope="column">{{ $date->get('day') }}</th>
                            @endif
                        @endforeach
                        <th scope="column">hh.mm</th>
                        <th scope="column">%</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Attendance</th>
                        @foreach ($dates as $date)
                            @if ($date->get('holiday'))
                                @if ($date->get('is_annual_holiday'))
                                    @if ($date->get('leave'))
                                        {{-- <th class="holiday">H / {{$date->get('leave')['leave_abbreviation']}}</th> --}}
                                        <th class="holiday">
                                            {{ $date->get('in_travel') ? 'H / ' . $date->get('leave')['leave_abbreviation'] . '/ T' : 'H / ' . $date->get('leave')['leave_abbreviation'] }}
                                        </th>
                                    @else
                                        <th class="holiday">{{ $date->get('in_travel') ? 'H / T' : 'H' }}</th>
                                    @endif
                                @else
                                    @if ($date->get('leave'))
                                        <th class="holiday">
                                            {{ $date->get('in_travel') ? 'X / ' . $date->get('leave')['leave_abbreviation'] . '/ T' : 'X / ' . $date->get('leave')['leave_abbreviation'] }}
                                        </th>
                                    @else
                                        <th class="holiday">{{ $date->get('in_travel') ? 'X / T' : 'X' }}</th>
                                    @endif
                                @endif
                            @else
                                @if ($date->get('leave'))
                                    <th>{{ $date->get('leave')['leave_abbreviation'] }}</th>
                                @else
                                    @if ($date->get('check_in_time') && $date->get('check_out_time'))
                                        <th>{{ $date->get('in_travel') ? 'T' : 'P' }}</th>
                                    @else
                                        <th>{{ $date->get('in_travel') ? 'T' : '' }}</th>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                        <th scope="column"></th>
                        <th scope="column"></th>
                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Time In (hh:mm) </th>
                        @foreach ($dates as $date)
                            <td>{{ $date->get('check_in_time') }}</td>
                        @endforeach
                        <th scope="column"></th>
                        <th scope="column"></th>
                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col">Time Out (hh:mm)</th>
                        @foreach ($dates as $date)
                            <td>{{ $date->get('check_out_time') }}</td>
                        @endforeach
                        <th scope="column"></th>
                        <th scope="column"></th>
                    </tr>
                    <tr>
                        <th scope="row" class="sticky-col first-col"> Hours Worked (hh.hh) </th>
                        @foreach ($dates as $date)
                            <td class="fw-bold">{{ $date->get('worked_hours') }}</td>
                        @endforeach
                        <th scope="column">{{ $total_worked_hours }}</th>
                        <th scope="column"></th>
                    </tr>

                    <tr>
                        <th scope="row" class="sticky-col first-col"><strong>Time Charge (hh.mm)</strong> </th>
                    </tr>

                    @foreach ($donors as $donor)
                        @foreach ($donor_charges as $donor_charge)
                            @if ($donor_charge['donor_id'] == $donor->id)
                                <tr>
                                    <th scope="row" class="sticky-col first-col text-wrap">{{ $donor->description }}
                                    </th>
                                    @foreach ($dates as $date)
                                        @php
                                            $donor_lists = $date->get('donor_list');
                                            $hour_charged = '';
                                            foreach ($donor_lists as $donor_list) {
                                                if ($donor_list['donor_id'] == $donor->id) {
                                                    $hour_charged = $donor_list['worked_hours'];
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <td>{{ $hour_charged }}</td>
                                    @endforeach

                                    <th scope="column">{{ $donor_charge['charged_hours'] }}</th>
                                    <th scope="column">{{ $donor_charge['charged_percentage'] }} %</th>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach

                    <tr>
                        <th scope="row" class="sticky-col first-col">
                            {{ $unrestrictedDonor->description ?: 'Unrestricted' }}</th>
                        @foreach ($dates as $date)
                            <td>{{ $date->get('unrestricted_hours') }}</td>
                        @endforeach
                        <th scope="column">{{ $total_unrestricted_hours }}</th>
                        <th scope="column">{{ $total_unrestricted_percentage }} %</th>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                    <tr class="total">
                        <th scope="row" class="sticky-col first-col"> <strong>Hours Charged (hh.mm)</strong> </th>
                        @foreach ($dates as $date)
                            <td>{{ $date->get('hours_charged') }}</td>
                        @endforeach
                        <th scope="column">{{ $total_charged_hours }}</th>
                        <th scope="column">{{ $total_charged_percentage }} %</th>
                    </tr>
                </tfoot>

            </table>
        </div>
        @include('EmployeeAttendance::Partials.abbreviation')
    </div>



    <div class="row my-2">
        <div class="col-lg-6">
            <table class="table table-sm table-bordered  mb-0" style="font-size: 0.65rem">
                <thead>
                    <tr>
                        <th colspan="5" style="font-size: inherit" class="text-center">Summary of Leave Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="column" style="text-align:center; font-size: inherit">Leave</th>
                        <th scope="column" style="text-align:center; font-size: inherit">Carry over </th>
                        <th scope="column" style="text-align:center; font-size: inherit">Earned</th>
                        <th scope="column" style="text-align:center; font-size: inherit">Taken</th>
                        <th scope="column" style="text-align:center; font-size: inherit">Balance</th>
                    </tr>

                    @foreach ($leaves as $leave)
                        <tr id="row_{!! $leave->id !!}" style="margin: 0px; padding: 0px;">
                            <th scope="row" class="leave_type" style="font-size: inherit">{{ $leave->getLeaveType() }} /
                                {{ $leave->leaveType->getLeaveBasis() }}</th>
                            @if ($leave->leaveType->maximum_carry_over > 0)
                                <td class="opening_balance">{{ $leave->opening_balance }}</td>
                                <td class="earned" style="text-align:center; font-size: inherit">{{ $leave->earned }}</td>
                                <td class="taken" style="text-align:center; font-size: inherit">{{ $leave->taken }}</td>
                                <td class="balance" style="text-align:center; font-size: inherit">{{ $leave->balance }}
                                </td>
                            @else
                                <td class="opening_balance" style="text-align:center; font-size: inherit">-</td>
                                <td class="earned" style="text-align:center; font-size: inherit">-</td>
                                <td class="taken" style="text-align:center; font-size: inherit">{{ $leave->taken }}</td>
                                <td class="balance" style="text-align:center; font-size: inherit">-</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($attendance->remarks)
            <div class="col-lg-6 last-row">
                <div class="card">
                    <div class="card-body">
                        <strong>Employee Remarks: </strong>
                        <span>{{ $attendance->remarks }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row last-row">
        <div class="col-sm-6 col-lg-4">
            <div><strong>Submitted By:</strong></div>
            <div><strong>Name:</strong> {{ $attendance->getRequester() }} </div>
            <div><strong>Signature:</strong> </div>
            <div><strong>Position:</strong> {{ $attendance->getRequesterDesignation() }} </div>
            <div><strong>Date:</strong> {{ $attendance->getSubmittedDate() }} </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div><strong>Verified By:</strong></div>
            <div><strong>Name:</strong> {{ $attendance->getReviewer() }} </div>
            <div><strong>Signature:</strong> </div>
            <div><strong>Position:</strong> {{ $attendance->getReviewerDesignation() }} </div>
            <div><strong>Date:</strong> {{ $attendance->getReviewedDate() }} </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div><strong>Approved By:</strong></div>
            <div><strong>Name:</strong> {{ $attendance->getApprover() }} </div>
            <div><strong>Signature:</strong> </div>
            <div><strong>Position:</strong> {{ $attendance->getApproverDesignation() }} </div>
            <div><strong>Date:</strong> {{ $attendance->getApprovedDate() }} </div>
        </div>
    </div>



    </div>


    <script>
        window.onload = print;
    </script>

@endsection
