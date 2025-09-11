@extends('layouts.container')

@section('title', 'Attendance Worklogs')
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
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#attendance-index').addClass('active');

        });
    </script>
@endsection




@section('page-content')


    <div class="pb-3 mb-3 border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('profile.show') }}"
                                class="text-decoration-none text-dark">Profile</a></li>
                        <li class="breadcrumb-item"><a href="{{ session()->previousUrl() }}"
                                class="text-decoration-none text-dark">{{ __('label.attendance') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    <button href="#" onclick="document.getElementById('donor-filter-form').submit();"
                        class="btn btn-outline-primary"><i class="bi bi-printer"></i> Print</button>
                </div>
            </div>
        </div>
    </div>


    <!-- CSS only -->

    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-2 print-code fs-7 fw-bold">
                   Timesheet Worklog
                </div>
                <div class="mb-3 print-header-info">
                    <ul class="p-0 m-0 list-unstyled">
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
                <div class="mt-4 mb-3 print-header-info"
                    style="display: flex; flex-direction: column; align-items: center;">
                    <ul class="p-0 m-0 list-unstyled">
                        <li><span class="fw-bold me-2">Reviewer:</span><span>{{ $attendance->getReviewer() }}</span></li>
                        <li><span class="fw-bold me-2">Approver:</span><span>{{ $attendance->getApprover() }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="print-body">
        @include('EmployeeAttendance::Partials.sub-worklogs', [
            'attendance' => $attendance,
            'enabledDonors' => $enabledDonors,
        ])
    </div>

@endsection
