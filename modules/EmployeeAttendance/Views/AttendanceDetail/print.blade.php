@extends('layouts.container-report')

@section('title', 'Staff Attendance Print')
@section('page_css')
    <style>
        .table,
        .table td,
        .table th {
            white-space: nowrap !important;
        }
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

        .print-header-info,
        .last-row {
            font-size: 0.65rem;
        }

        /* Highlight today row - subtle light-dark effect */
        .today-row {
            background-color: #f5f5f5 !important;
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
                padding: 0.25rem 0.35rem !important;
            }

            .today-row {
                background-color: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
        <div class="wrapper table-responsive mb-3">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Time In (hh:mm)</th>
                        <th>Time Out (hh:mm)</th>
                        <th>Hours Worked (hh.hh)</th>
                        <th style="width: 40%;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dates as $date)
                        @php
                            $currentDate = $date->get('date');
                            $isToday = $currentDate === now()->format('Y-m-d');
                            $isFuture = \Carbon\Carbon::parse($currentDate)->isFuture();

                            $isHoliday = $date->get('holiday');
                            $isWeekend = $date->get('is_weekend');
                            $hasLeave = $date->has('leave');
                            $inTravel = $date->get('in_travel');
                            $hasCheckIn = $date->get('check_in_time');
                            $hasCheckOut = $date->get('check_out_time');

                            $remarkParts = [];
                            if ($isHoliday) {
                                $remarkParts[] = $date->get('is_annual_holiday') ? 'Holiday' : 'Weekend';
                            }
                            if ($inTravel) {
                                $remarkParts[] = 'Travel';
                            }
                            if ($hasLeave) {
                                $remarkParts[] = $date->get('leave')['leave_abbreviation'];
                            }

                            // Only show "Absent" for past dates or today (not future dates)
                            if (
                                !$isFuture &&
                                !$hasCheckIn &&
                                !$hasCheckOut &&
                                !$isHoliday &&
                                !$hasLeave &&
                                !$inTravel
                            ) {
                                $remarkParts[] = 'Absent';
                            }

                            $remark = implode(' / ', array_filter($remarkParts));
                            $remark = $remark ?: ($hasCheckIn && $hasCheckOut ? 'Present' : '');
                        @endphp
                        <tr data-date="{{ $currentDate }}" class="{{ $isToday ? 'today-row' : '' }}">
                            <td class="text-center fw-bold {{ $isHoliday ? 'holiday' : '' }}">
                                {{ $date->get('date') }}<br>
                                <small class="text-muted">{{ $date->get('day_name') }}</small>
                            </td>
                            <td class="text-center {{ $hasCheckIn ? 'present' : '' }}">
                                {{ $date->get('check_in_time') ?: '-' }}
                            </td>
                            <td class="text-center {{ $hasCheckOut ? 'present' : '' }}">
                                {{ $date->get('check_out_time') ?: '-' }}
                            </td>
                            <td class="text-center fw-bold">
                                {{ $date->get('worked_hours') ?: '0.00' }}
                            </td>
                            <td>
                                @if ($remark)
                                    <span
                                        class="{{ $isHoliday ? 'holiday' : ($hasLeave ? 'leave' : ($inTravel ? 'travel' : 'absent')) }}">
                                        {{ $remark }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total Hours Worked:</th>
                        <th class="text-center">{{ $total_worked_hours }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
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
