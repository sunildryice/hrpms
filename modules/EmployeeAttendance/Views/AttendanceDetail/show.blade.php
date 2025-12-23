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

        .print-header-info,
        .last-row {
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
        $(function() {
            // $('#navbarVerticalMenu').find('#attendance-index').addClass('active');
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
                        <li class="breadcrumb-item"><a href="{{ route('profile.show') }}"
                                class="text-decoration-none text-dark">Profile</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.show', $attendance->employee->id) }}"
                                class="text-decoration-none text-dark">{{ __('label.attendance') }}</a></li>
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
                        <li><span class="fw-bold me-2">Staff
                                Name:</span><span>{{ $attendance->employee->getFullName() }}</span></li>
                        <li><span
                                class="fw-bold me-2">Title:</span><span>{{ $attendance->employee->latestTenure->getDesignationName() }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Duty
                                station:</span><span>{{ $attendance->employee->latestTenure->getDutyStation() }}</span></li>
                        <li><span
                                class="fw-bold me-2">Month:</span><span>{{ date('F', mktime(0, 0, 0, $attendance->month, 10)) }}</span>
                        </li>
                        <li><span class="fw-bold me-2">Year:</span><span>{{ $attendance->year }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col">
                <div class="print-header-info mb-3 mt-4"
                    style="display: flex; flex-direction: column; align-items: center;">
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
                            if (!$hasCheckIn && !$hasCheckOut && !$isHoliday && !$hasLeave) {
                                $remarkParts[] = 'Absent';
                            }

                            $remark = implode(' / ', array_filter($remarkParts));
                            $remark = $remark ?: ($hasCheckIn && $hasCheckOut ? 'Present' : '');
                        @endphp
                        <tr data-date="{{ $date->get('date') }}">
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
    <div class="row my-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    @foreach ($attendance->logs as $log)
                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                            <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                <i class="bi-person"></i>
                            </div>
                            <div class="w-100">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                    <div
                                        class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                        <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                        <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                        </span>
                                    </div>
                                    <small
                                        title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                                {{-- <span class="{{$log->getStatusClass()}}">{{$log->getStatus()}}</span> --}}
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

    </div>
@endsection
