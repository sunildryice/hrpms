@extends('layouts.container')

@section('title', 'Review Attendance')
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
            $('#navbarVerticalMenu').find('#attendance-review-index').addClass('active');
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
                        <li class="breadcrumb-item"><a href="#"
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

    <section>
        <div class="card">
            <div class="card-header fw-bold">
                Attendance Review Process
            </div>
            <form action="{{ route('attendance.review.store', $attendance->id) }}" id="attendanceReviewProcessForm"
                method="post" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            @foreach ($attendance->logs as $log)
                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                        <i class="bi-person"></i>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex flex-row align-items-center">
                                                <span class="me-2">{{ $log->createdBy->getFullName() }}</span>
                                                <span class="badge bg-primary c-badge">
                                                    {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                </span>
                                            </div>
                                            <small
                                                title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <p class="text-justify comment-text mb-0 mt-1">
                                            {{ $log->log_remarks }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-6">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="status_id" class="form-label required-label">Status </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="status_id" class="select2 form-control" data-width="100%">
                                        <option value="">Select Status</option>
                                        <option value="{{ config('constant.RETURNED_STATUS') }}">Return to Employee
                                        </option>
                                        <option value="{{ config('constant.VERIFIED_STATUS') }}">Verify</option>
                                    </select>
                                    @if ($errors->has('status_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="status_id">
                                                {!! $errors->first('status_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="log_remarks" class="form-label required-label">Remarks </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                    @if ($errors->has('log_remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {!! csrf_field() !!}
                        </div>
                    </div>
                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                        Submit
                    </button>
                    <a href="{!! route('attendance.review.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>


    </div>

@endsection
