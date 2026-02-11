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
    <script>
        $(document).ready(function() {
            const detailForm = document.getElementById('attendanceProcessForm');
            const fv = FormValidation.formValidation(detailForm, {
                fields: {
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Hr reviewer is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            })
        });
    </script>
@endsection

@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('profile.show') }}"
                                class="text-decoration-none text-dark">Profile</a>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.show', $attendance->employee->id) }}"
                                class="text-decoration-none text-dark">{{ __('label.attendance') }}</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 col-lg-8">
                    <div class="mb-3 print-code fs-6 fw-bold">
                        Staff Attendance Record
                    </div>
                    <div class="mb-3 print-header-info">
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            <li><span class="fw-bold me-2">Staff
                                    Name:</span><span>{{ $attendance->employee->getFullName() }}</span>
                            </li>
                            <li><span
                                    class="fw-bold me-2">Title:</span><span>{{ $attendance->employee->latestTenure->getDesignationName() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Duty
                                    station:</span><span>{{ $attendance->employee->latestTenure->getDutyStation() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <ul class="mt-4 list-unstyled fs-7">
                        <li class="mb-2 d-flex justify-content-end align-items-center">
                            <span
                                class="fw-bold me-2">Month:</span><span>{{ date('F', mktime(0, 0, 0, $attendance->month, 10)) }}</span>
                        </li>
                        <li class="d-flex justify-content-end align-items-center">
                            <span class="fw-bold me-2">Year:</span><span>{{ $attendance->year }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="wrapper table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Time In (hh:mm)</th>
                            <th>Time Out (hh:mm)</th>
                            <th>Hours Worked (hh.mm)</th>
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
                                    {{ $date->get('worked_hours') ?: '00.00' }}
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
    </div>


    @if (auth()->user()->can('submit', $attendance))
        @php
            $attendanceMonth = $attendance->month; 
            $attendanceYear = $attendance->year;

            $firstDayOfMonth = Carbon\Carbon::createFromDate($attendanceYear, $attendanceMonth, 1);
            $lastDayOfMonth = $firstDayOfMonth->endOfMonth()->format('Y-m-d');
            $today = now()->format('Y-m-d');

            $canShowForm = $today >= $lastDayOfMonth;
        @endphp
        @if ($canShowForm)
            <section>
                <div class="card">
                    <div class="card-header fw-bold">
                        Attendance Process
                    </div>
                    <form action="{{ route('attendance.submit') }}" id="attendanceProcessForm" method="post"
                        enctype="multipart/form-data" autocomplete="off">

                        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    @if ($attendance->status_id == config('constant.RETURNED_STATUS'))
                                        <section>
                                            <div class="m-2 col-lg-6">
                                                <div class="p-3 mb-2 border row">
                                                    <div>
                                                        <div class="d-flex align-items-start h-100">
                                                            <span class="fw-bold"
                                                                style="text-decoration: underline">Remarks:</span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <span>{{ $attendance->getLatestRemark() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    @endif
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-2 row">
                                        <div class="col-lg-5">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="status_id" class="m-0">Remarks</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <textarea class="form-control" name="remarks" id="remarks" rows="2">{{ $attendance->remarks ?: old('remarks') }}</textarea>
                                            @if ($errors->has('remarks'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="remarks">
                                                        {!! $errors->first('remarks') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-5">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="approver_id" class="form-label required-label">Select
                                                    Approver</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <select name="approver_id" id="approver_id" class="select2 form-control"
                                                data-width="100%">
                                                <option value="">Select Approver</option>
                                                @foreach ($approvers as $reviewer)
                                                    <option value="{{ $reviewer->id }}">{{ $reviewer->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('reviewer_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="reviewer_id">
                                                        {!! $errors->first('reviewer_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-lg-5">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="status_id" class="form-label required-label">Submit to HR for
                                                    verification </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <select name="reviewer_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select HR</option>
                                                @foreach ($reviewers as $reviewer)
                                                    <option value="{{ $reviewer->id }}">{{ $reviewer->getFullName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('reviewer_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="reviewer_id">
                                                        {!! $errors->first('reviewer_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                            </div>
                        </div>
                        <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm"
                                {{ $editable ? '' : 'disabled' }}>
                                Submit
                            </button>
                            <a href="{!! route('attendance.detail.show', $attendance->id) !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>
        @endif
    @endif
@endsection
