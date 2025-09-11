@extends('layouts.container')

@section('title', 'Attendance Detail')
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

            small {
                font-size: 0.675em;
            }

            .table tr th,
            .table tr td {
                padding: 0.25rem 0.35rem !important;
            }
        }
    </style>

@endsection

@section('page_js')
    <script type="text/javascript">
        $(function () {
            $('#navbarVerticalMenu').find('#attendance-index').addClass('active');
        });
    </script>
@endsection




@section('page-content')

        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}"
                                   class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('attendance.index')}}"
                                                           class="text-decoration-none text-dark">{{ __('label.attendance') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{route('attendance.view', $attendance->employee->id)}}"
                                    class="text-decoration-none text-dark">{{ $attendance->employee->getFullName() }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

    <!-- CSS only -->

    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
                <div class="print-code fs-7 fw-bold mb-2">
                    Staff Attendance Record
                </div>
                <div class="print-header-info mb-3">
                    <ul class="list-unstyled m-0 p-0">
                        <li><span
                                class="fw-bold me-2">Staff Name:</span><span>{{$attendance->employee->getFullName()}}</span>
                        </li>
                        <li><span
                                class="fw-bold me-2">Title:</span><span>{{$attendance->employee->latestTenure->getDesignationName()}}</span>
                        </li>
                        <li><span
                                class="fw-bold me-2">Duty station:</span><span>{{$attendance->employee->latestTenure->getDutyStation()}}</span>
                        </li>
                        <li><span
                                class="fw-bold me-2">Month:</span><span>{{date("F", mktime(0, 0, 0, $attendance->month, 10))}}</span>
                        </li>
                        <li><span class="fw-bold me-2">Year:</span><span>{{$attendance->year}}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col">
                <div class="print-header-info mb-3 mt-4" style="display: flex; flex-direction: column; align-items: center;">
                    <ul class="list-unstyled m-0 p-0">
                        <li><span class="fw-bold me-2">Reviewer:</span><span>{{ $attendance->getReviewer() }}</span></li>
                        <li><span class="fw-bold me-2">Approver:</span><span>{{ $attendance->getApprover() }}</span></li>
                    </ul>
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
                    @foreach ($dates as $date)
                        @if ($date->get('holiday'))
                            <th scope="column" class="holiday">{{$date->get('day_name')}}</th>
                        @else
                            <th scope="column">{{$date->get('day_name')}}</th>
                        @endif
                    @endforeach
                    <th scope="column">Total</th>
                    <th scope="column">Charge</th>
                </tr>

                <tr>
                    <th class="sticky-col first-col">Date</th>
                    @foreach ($dates as $date)
                        @if ($date->get('holiday'))
                            <th class="holiday">{{$date->get('day')}}</th>
                        @else
                            <th scope="column">{{$date->get('day')}}</th>
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
                            <th class="holiday">
                            @if ($date->get('is_annual_holiday'))
                                @if ($date->get('leave'))
                                     {{$date->get('in_travel') ? 'H / '.$date->get('leave')['leave_abbreviation'].'/ T' : 'H / '.$date->get('leave')['leave_abbreviation']}}
                                @else
                                    {{$date->get('in_travel') ? 'H / T' : 'H'}}
                                @endif
                            @else
                                @if ($date->get('leave'))
                                     {{$date->get('in_travel') ? 'X / '.$date->get('leave')['leave_abbreviation'].'/ T' : 'X / '.$date->get('leave')['leave_abbreviation']}}
                                @else
                                    {{$date->get('in_travel') ? 'X / T' : 'X'}}
                                @endif
                            @endif
                            </th>
                        @else
                            @if ($date->get('leave'))
                                <th>{{$date->get('leave')['leave_abbreviation']}}</th>
                            @else
                                @if ($date->get('check_in_time') && $date->get('check_out_time'))
                                    <th>{{$date->get('in_travel') ? 'T' : 'P'}}</th>
                                @else
                                    <th>{{$date->get('in_travel') ? 'T' : ''}}</th>
                                @endif
                            @endif
                        @endif
                    @endforeach
                    <th scope="column"></th>
                    <th scope="column"></th>
                </tr>
                <tr>
                    <th scope="row" class="sticky-col first-col">Time In (hh:mm)</th>
                    @foreach ($dates as $date)
                            <td>{{$date->get('check_in_time')}}</td>
                    @endforeach
                    <th scope="column"></th>
                    <th scope="column"></th>
                </tr>
                <tr>
                    <th scope="row" class="sticky-col first-col">Time Out (hh:mm)</th>
                    @foreach ($dates as $date)
                        <td>{{$date->get('check_out_time')}}</td>
                    @endforeach
                    <th scope="column"></th>
                    <th scope="column"></th>
                </tr>
                <tr>
                    <th scope="row" class="sticky-col first-col"> Hours Worked (hh.hh)</th>
                    @foreach ($dates as $date)
                        <td class="fw-bold">{{$date->get('worked_hours')}}</td>
                    @endforeach
                    <th scope="column">{{$total_worked_hours}}</th>
                    <th scope="column"></th>
                </tr>

                <tr>
                    <th scope="row" class="sticky-col first-col"><strong>Time Charge (hh.mm)</strong></th>
                </tr>

                @foreach ($donors as $donor)
                    @foreach ($donor_charges as $donor_charge)
                        @if ($donor_charge['donor_id'] == $donor->id)
                            <tr>
                                <th scope="row" class="sticky-col first-col text-wrap">{{$donor->description}}</th>
                                @foreach ($dates as $date)
                                    @php
                                        $donor_lists = $date->get('donor_list');
                                        $hour_charged = '';
                                        foreach($donor_lists as $donor_list) {
                                            if ($donor_list['donor_id'] == $donor->id) {
                                                $hour_charged = $donor_list['worked_hours'];
                                                break;
                                            }
                                        }
                                    @endphp
                                    <td>{{$hour_charged}}</td>
                                @endforeach

                                <th scope="column">{{$donor_charge['charged_hours']}}</th>
                                <th scope="column">{{$donor_charge['charged_percentage']}} %</th>
                            </tr>
                        @endif
                    @endforeach
                @endforeach

                <tr>
                    <th scope="row" class="sticky-col first-col">Unrestricted</th>
                    @foreach ($dates as $date)
                        <td>{{$date->get('unrestricted_hours')}}</td>
                    @endforeach
                    <th scope="column">{{$total_unrestricted_hours}}</th>
                    <th scope="column">{{$total_unrestricted_percentage}} %</th>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                <tr class="total">
                    <th scope="row" class="sticky-col first-col"><strong>Hours Charged (hh.mm)</strong></th>
                    @foreach ($dates as $date)
                        <td>{{$date->get('hours_charged')}}</td>
                    @endforeach
                    <th scope="column">{{$total_charged_hours}}</th>
                    <th scope="column">{{$total_charged_percentage}} %</th>
                </tr>
                </tfoot>

            </table>
        </div>
        @include('EmployeeAttendance::Partials.abbreviation')
    </div>
    <div class="row my-3">
        <div class="col-lg-6">
            <table class="table table-borderless table-bordered  mb-0">
                <thead>
                <tr>
                    <th colspan="5" class="text-center">Summary of Leave Detail</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="column">Leave</th>
                        <th scope="column">Carry over</th>
                        <th scope="column">Earned</th>
                        <th scope="column">Taken</th>
                        <th scope="column">Balance</th>
                    </tr>

                    @foreach($leaves as $leave)
                        <tr id="row_{!! $leave->id !!}">
                            <th scope="row" class="leave_type">
                                {{ $leave->getLeaveType() }} / {{ $leave->leaveType->getLeaveBasis() }}
                            </th>
                            @if ($leave->leaveType->maximum_carry_over > 0)
                                <td class="opening_balance">{{ $leave->opening_balance }}</td>
                                <td class="earned">{{ $leave->earned }}</td>
                                <td class="taken">{{ $leave->taken }}</td>
                                <td class="balance">{{ $leave->balance }}</td>
                            @else
                                <td class="opening_balance">-</td>
                                <td class="earned">-</td>
                                <td class="taken">{{ $leave->taken }}</td>
                                <td class="balance">-</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    @foreach($attendance->logs as $log)
                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                            <div width="40" height="40"
                                 class="rounded-circle mr-3 user-icon">
                                <i class="bi-person"></i>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-row align-items-center">
                                        <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                        <span
                                            class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                    </div>
                                    <small>{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="text-justify comment-text mb-0 mt-1">
                                    {{ $log->log_remarks }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
