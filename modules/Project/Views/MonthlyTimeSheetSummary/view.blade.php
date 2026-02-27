@extends('layouts.container')

@section('title', 'Employee Monthly Timesheet - ' . ($employee->getFullName() ?? 'N/A'))

@section('page_css')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-break: break-word;
            min-width: 160px;
            max-width: 340px;
        }

        .date-group-divider td {
            border-top: 2px solid #dee2e6 !important;
        }

        .project-group-divider td {
            border-top: 1px dashed #ced4da !important;
        }

        .activity-group-divider td {
            border-top: 1px dotted #adb5bd !important;
        }

        .absence-reason {
            color: #6c757d;
            font-style: italic;
        }
    </style>
@endsection

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#monthly-timesheets-summary-menu').addClass('active');
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="pb-3 mb-3 border-bottom">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('monthly-timesheet.summary.index') }}" class="text-decoration-none text-dark">
                            Monthly Timesheet Summary
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('monthly-timesheet.summary.show', [$year, $month]) }}"
                            class="text-decoration-none text-dark">
                            {{ $year }} {{ $monthlyTimeSheet->month_name ?? $month }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $employee->getFullName() ?? 'Employee' }}
                    </li>
                </ol>
            </nav>
            <h4 class="m-0 mt-2 fs-6 text-uppercase fw-bold text-primary">
                {{ $employee->getFullName() ?? 'Employee' }} - {{ $yearMonth }}
            </h4>
        </div>

        <!-- Main Content -->
        <div class="card shadow-sm border">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="employeeTimesheetTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width:50px">{{ __('label.sn') }}</th>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Activity</th>
                                <th>Description / Task</th>
                                <th class="text-end" style="width:90px">Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 1; @endphp
                            @forelse ($allDates as $dateKey => $day)
                                @php
                                    $items = $day['items'];
                                    $carbonDate = $day['carbon'];
                                    $isWeekend = $carbonDate->isWeekend();
                                    $dateClasses = $loop->first ? '' : 'date-group-divider';
                                @endphp

                                @if ($items->isEmpty())
                                    <tr class="{{ $dateClasses }}">
                                        <td class="text-center align-middle">{{ $sn++ }}</td>
                                        <td>{{ $carbonDate->format('d, M Y') }}</td>
                                        <td colspan="4" class="text-center fw-bold">
                                            {!! $day['reason'] !!}
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $dateRowspan = $items->count();
                                        $datePrinted = false;
                                        $projectGroups = $items->groupBy(
                                            fn($ts) => optional($ts->project)->id ?? 'unknown',
                                        );
                                    @endphp

                                    @foreach ($projectGroups as $projId => $projItems)
                                        @php
                                            $projPrinted = false;
                                            $projRowspan = $projItems->count();
                                            $activityGroups = $projItems->groupBy(
                                                fn($ts) => optional($ts->activity)->id ?? 'unknown',
                                            );
                                        @endphp

                                        @foreach ($activityGroups as $actId => $actItems)
                                            @php $actPrinted = false; @endphp

                                            @foreach ($actItems as $entry)
                                                <tr class="{{ !$datePrinted ? $dateClasses : '' }}">
                                                    @if (!$datePrinted)
                                                        <td rowspan="{{ $dateRowspan }}" class="text-center align-middle">
                                                            {{ $sn++ }}</td>
                                                        <td rowspan="{{ $dateRowspan }}" class="align-middle">
                                                            {{ $carbonDate->format('d, M Y') }}
                                                        </td>
                                                        @php $datePrinted = true; @endphp
                                                    @endif

                                                    @if (!$projPrinted)
                                                        <td rowspan="{{ $projRowspan }}" class="align-middle wrap-text">
                                                            {{ optional($entry->project)->short_name ?? '—' }}
                                                        </td>
                                                        @php $projPrinted = true; @endphp
                                                    @endif

                                                    @if (!$actPrinted)
                                                        <td rowspan="{{ $actItems->count() }}"
                                                            class="align-middle wrap-text">
                                                            {{ optional($entry->activity)->title ?? '—' }}
                                                        </td>
                                                        @php $actPrinted = true; @endphp
                                                    @endif

                                                    <td class="wrap-text">{{ $entry->description ?: '—' }}</td>
                                                    <td class="text-end align-middle fw-medium">
                                                        {{ number_format($entry->hours_spent ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No data available for this period
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
