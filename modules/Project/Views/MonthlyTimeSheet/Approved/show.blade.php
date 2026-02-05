@extends('layouts.container')

@section('title', 'Approved Monthly TimeSheet')

@section('page_css')
    <style>
        .recommend-col {
            max-width: 350px;
            white-space: pre-line;
        }
    </style>

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-monthly-timesheets-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="">
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <div class="card border shadow-sm rounded h-100">
                        <div class="card-header">
                            Timesheet Summary of {{ $yearMonthFormatted }} (Status:
                            {{ $timeSheet->status->name ?? $timeSheet->status_id }})
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-3">
                                    <b class="text-muted d-block">Projects</b>
                                    <h5 class="my-3">{{ $stats['projects'] ?? 0 }}</h5>
                                </div>
                                <div class="col-3">
                                    <b class="text-muted d-block">Activities</b>
                                    <h5 class="my-3">{{ $stats['activities'] ?? 0 }}</h5>
                                </div>
                                <div class="col-3">
                                    <b class="text-muted d-block">Tasks</b>
                                    <h5 class="my-3">{{ $stats['tasks'] ?? 0 }}</h5>
                                </div>
                                <div class="col-3">
                                    <b class="text-muted d-block">Total Hours</b>
                                    <h5 class="my-3">
                                        {{ isset($stats['hours']) ? number_format($stats['hours'], 2) : number_format(0, 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border rounded c-tabs-content active" id="monthly-timesheet-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="MonthlyTimeSheetTable">
                        <style>
                            /* Visual separators for readability */
                            #MonthlyTimeSheetTable tr.date-group-divider td {
                                border-top: 2px solid #dee2e6;
                            }

                            #MonthlyTimeSheetTable tr.project-group-divider td {
                                border-top: 1px dashed #e9ecef;
                            }

                            #MonthlyTimeSheetTable tr.activity-group-divider td {
                                border-top: 1px dotted #f0f0f0;
                            }
                        </style>
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Activities</th>
                                <th>Tasks</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sn = 1; @endphp
                            @foreach ($allDates as $date => $itemsByDate)
                                @php
                                    // If no data for this date, show empty row
                                    if ($itemsByDate->isEmpty()) {
                                        echo '<tr' . ($loop->first ? '' : ' class="date-group-divider"') . '>';
                                        echo '<td>' . $sn++ . '</td>';
                                        echo '<td>' . \Carbon\Carbon::parse($date)->format('d, M Y') . '</td>';
                                        echo '<td colspan="4" class="text-center text-muted">No timesheet entries</td>';
                                        echo '</tr>';
                                        continue;
                                    }
                                    $dateRowCount = $itemsByDate->count();
                                    $datePrinted = false;
                                    $projectGroups = collect($itemsByDate)->groupBy(function ($ts) {
                                        return optional($ts->project)->id ?? 'unknown';
                                    });
                                    $dateLoop = $loop; // capture outer loop for first/last detection
                                @endphp
                                @foreach ($projectGroups as $projectId => $projectItems)
                                    @php
                                        $projectPrinted = false;
                                        $projectLoop = $loop;
                                        $activityGroups = collect($projectItems)->groupBy(function ($ts) {
                                            return optional($ts->activity)->id ?? ($ts->activity_id ?? 'unknown');
                                        });
                                    @endphp
                                    @foreach ($activityGroups as $activityId => $activityItems)
                                        @php
                                            $activityPrinted = false;
                                            $activityLoop = $loop;
                                        @endphp
                                        @foreach ($activityItems as $item)
                                            @php
                                                $rowClasses = [];
                                                if (!$datePrinted && !$dateLoop->first) {
                                                    $rowClasses[] = 'date-group-divider';
                                                }
                                                if (!$projectPrinted && !$projectLoop->first) {
                                                    $rowClasses[] = 'project-group-divider';
                                                }
                                                if (!$activityPrinted && !$activityLoop->first) {
                                                    $rowClasses[] = 'activity-group-divider';
                                                }
                                            @endphp
                                            <tr class="{{ implode(' ', $rowClasses) }}">
                                                @if (!$datePrinted)
                                                    <td rowspan="{{ $dateRowCount }}">{{ $sn++ }}</td>
                                                    <td rowspan="{{ $dateRowCount }}">
                                                        {{ \Carbon\Carbon::parse($item->timesheet_date)->format('d,M Y') }}
                                                    </td>
                                                    @php $datePrinted = true; @endphp
                                                @endif
                                                @if (!$projectPrinted)
                                                    <td rowspan="{{ $projectItems->count() }}">
                                                        {{ optional($item->project)->short_name }}</td>
                                                    @php $projectPrinted = true; @endphp
                                                @endif
                                                @if (!$activityPrinted)
                                                    <td rowspan="{{ $activityItems->count() }}">
                                                        {{ optional($item->activity)->title ?? 'N/A' }}</td>
                                                    @php $activityPrinted = true; @endphp
                                                @endif
                                                <td>{{ $item->description }}</td>
                                                <td>{{ is_numeric($item->hours_spent) ? number_format($item->hours_spent, 2) : $item->hours_spent }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-top">
            </div>
        </div>
    </div>
@stop
