@extends('layouts.container-report')

@section('title', 'Timesheet Worklog')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
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
            font-weight: 600;
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
            border-bottom-width: 0px;
            text-align: justify;
            vertical-align: top;
        }
    </style>
@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>
    <!-- CSS only -->
    <section class="p-3 bg-white print-info" id="print-info">

        <div class="mb-3 text-center print-title fw-bold translate-middle">
            {{-- <div class="fs-5"> One Heart Worldwide</div> --}}
            {{-- <div class="fs-8">{{ $requester->getOfficeName() }}</div> --}}
            <div class="fs-5"> Timesheet Worklog</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="my-3 print-header-info">
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            <li><strong>Organization Name: </strong> One Heart Worldwide </li>
                            <li><strong>Name of Staff: </strong> {{ $attendance->getRequester() }} </li>
                            <li><strong>Designation: </strong> {{ $requester->getDesignationName() }}</li>
                            <li><strong>Duty Station: </strong> {{ $requester->latestTenure?->office?->getOfficeName() }}</li>
                            <li><strong>Supervisor Name: </strong> {{ $requester->latestTenure?->supervisor?->getFullName() }}</li>
                            <li><strong>Year: </strong> {{ $attendance->year }}</li>
                            <li><strong>Month: </strong> {{ date('F', mktime(0, 0, 0, $attendance->month, 10)) }}</li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
        @php
            $logData = $logData->groupBy('attendanceDetail.attendance_date');
            $totalHours = 0;
        @endphp
        <div class="mt-4 print-body">
            <table class="table border">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th width="35%">@lang('label.activity-desc')</th>
                        <th>Project Name</th>
                        <th>Funding Source</th>
                        <th>Hours Worked</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logData as $key => $logs)
                        @php
                            $date = \Carbon\Carbon::parse($key);
                            $rowCount = $logs->count();
                        @endphp
                        @foreach ($logs as $index => $log)
                            @php
                                $workedHours = $log->getWorkedHours();
                                $totalHours += $workedHours;
                            @endphp
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ $rowCount }}">{{ $date->format('Y-m-d') }}</td>
                                    <td rowspan="{{ $rowCount }}">{{ $date->format('l') }}</td>
                                @endif
                                <td>{{ $log['activities'] }}</td>
                                <td>{{ $log->project->getShortName() }}</td>
                                <td>{{ $log->donor->description }}</td>
                                <td class="text-end">{{ $workedHours }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end">Total Hours</td>
                        <td class="text-end">{{ $totalHours }}</td>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                @if ($attendance->status_id != config('constant.CREATED_STATUS'))
                    <div class="col-lg-6">Submitted by: {{ $attendance->getRequester() }}</div>
                @else
                    <div class="col-lg-6"></div>
                @endif
                <div class="col-lg-6">Approved by:
                    @if ($attendance->status_id == config('constant.APPROVED_STATUS'))
                        {{ $attendance->getApprover() }}
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
