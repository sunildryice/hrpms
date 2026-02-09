@php
    // Prepare date range for the Gantt chart
    $allDates = collect();

    $project->activities->each(function ($theme) use ($allDates) {
        if ($theme->start_date) {
            $allDates->push($theme->start_date);
        }
        if ($theme->completion_date) {
            $allDates->push($theme->completion_date);
        }
        if ($theme->latest_extension?->extended_completion_date) {
            $allDates->push($theme->latest_extension->extended_completion_date);
        }

        $theme->activityChildren->each(function ($act) use ($allDates) {
            if ($act->start_date) {
                $allDates->push($act->start_date);
            }
            if ($act->completion_date) {
                $allDates->push($act->completion_date);
            }
            if ($act->latest_extension?->extended_completion_date) {
                $allDates->push($act->latest_extension->extended_completion_date);
            }
        });
    });

    if ($allDates->isEmpty()) {
        $minDate = now()->startOfYear();
        $maxDate = now()->endOfYear();
    } else {
        $minDate = $allDates->min()->startOfMonth();
        $maxDate = $allDates->max()->endOfMonth();
    }

    $months = [];
    $current = $minDate->copy();
    while ($current->lte($maxDate)) {
        $months[] = $current->format('M Y');
        $current->addMonthNoOverflow();
    }

    $weekLabels = ['I', 'II', 'III', 'IV'];
    $totalWeekColumns = count($months) * 4;
@endphp

<table border="1">

    <!-- Project Title -->
    <tr>
        <td colspan="{{ 10 + $totalWeekColumns }}">
            Project: {{ $project?->title ?? 'Project Activity Plan' }}<br>
            Period: [{{ $project?->start_date?->format('M d, Y') ?? '' }}
            to
            {{ $project?->completion_date?->format('M d, Y') ?? '' }}]
        </td>
    </tr>

    <!-- Year Row -->
    <tr>
        <th rowspan="3">Activities</th>
        <th rowspan="3">Type</th>
        <th rowspan="3">Output / Deliverables</th>
        <th rowspan="3">Budget Description</th>
        <th rowspan="3">Start Date</th>
        <th rowspan="3">Completion Date</th>
        <th rowspan="3">Members</th>
        <th rowspan="3">Status</th>
        <th rowspan="3">Extended Deadline</th>
        <th rowspan="3">Remarks</th>

        @php
            $yearGroups = [];
            $currentYear = null;
            $colspanCount = 0;

            foreach ($months as $monthLabel) {
                $year = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->year;

                if ($year !== $currentYear) {
                    if ($currentYear !== null) {
                        $yearGroups[] = ['year' => $currentYear, 'colspan' => $colspanCount];
                    }
                    $currentYear = $year;
                    $colspanCount = 1;
                } else {
                    $colspanCount++;
                }
            }

            if ($currentYear !== null) {
                $yearGroups[] = ['year' => $currentYear, 'colspan' => $colspanCount];
            }
        @endphp

        @foreach ($yearGroups as $group)
            <td colspan="{{ $group['colspan'] * 4 }}">
                Year {{ $group['year'] }}
            </td>
        @endforeach
    </tr>

    <!-- Month Row -->
    <tr>
        @foreach ($months as $monthLabel)
            @php $monthName = explode(' ', $monthLabel)[0]; @endphp
            <td colspan="4">{{ $monthName }}</td>
        @endforeach
    </tr>

    <!-- Week Labels -->
    <tr>
        @foreach ($months as $month)
            @foreach ($weekLabels as $week)
                <td>{{ $week }}</td>
            @endforeach
        @endforeach
    </tr>

    <!-- === Content grouped by stage === -->
    @php
        $grouped = $project->activities->whereNull('parent_id')->groupBy('activity_stage_id');
        $globalSn = 1;
    @endphp

    @forelse ($grouped as $stageId => $themesInStage)
        @php
            $stage = \Modules\Project\Models\ActivityStage::find($stageId);
            $stageTitle = $stage ? $stage->title : 'No Stage';
        @endphp

        <!-- Stage Header -->
        <tr>
            <td colspan="2">{{ $stageTitle }}</td>
            <td colspan="{{ 8 + $totalWeekColumns }}"></td>
            @foreach ($months as $m)
                @foreach ($weekLabels as $_)
                    <td></td>
                @endforeach
            @endforeach
        </tr>

        @foreach ($themesInStage as $theme)
            @php
                // Gantt logic for THEME
                $statusValue = $theme->status ?? 'not_started';

                $plannedStart = $theme->start_date;
                $plannedEnd = $theme->completion_date;
                $extendedEnd = $theme->latest_extension?->extended_completion_date ?? $plannedEnd;

                $actualStart = $theme->actual_start_date;
                $actualCompletion = $theme->actual_completion_date;

                $ganttStart = $plannedStart;
                $ganttEnd = $extendedEnd;

                if ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::UnderProgress->value) {
                    $ganttStart = $actualStart ?? $plannedStart;
                    $ganttEnd = $extendedEnd;
                } elseif ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::NoRequired->value) {
                    $ganttStart = $actualStart ?? $plannedStart;
                    $ganttEnd = $actualCompletion ?? $extendedEnd;
                } elseif ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::Completed->value) {
                    $ganttStart = $actualStart ?? $plannedStart;
                    $ganttEnd = $actualCompletion ?? $extendedEnd;
                }

                if (!$ganttStart) {
                    $ganttStart = null;
                    $ganttEnd = null;
                }

                $statusLabel =
                    \Modules\Project\Models\Enums\ActivityStatus::tryFrom($statusValue)?->label() ?? 'Not Started';
            @endphp

            <tr>
                <td>{{ $theme->title }}</td>
                <td>Activity Theme</td>
                <td>{{ $theme->deliverables ?? '' }}</td>
                <td>{{ $theme->budget_description ?? '' }}</td>
                <td>{{ $theme->start_date?->format('Y-m-d') ?? '' }}</td>
                <td>{{ $theme->completion_date?->format('Y-m-d') ?? '' }}</td>
                <td>{{ $theme->memberNames() ?: '' }}</td>
                <td>{{ $statusLabel }}</td>
                <td>{{ $theme->latest_extension?->extended_completion_date?->format('Y-m-d') ?? '' }}</td>
                <td>{{ $theme->latest_extension?->reason ?? '' }}</td>

                @foreach ($months as $monthLabel)
                    @foreach ($weekLabels as $weekIndex => $weekLabel)
                        @php
                            $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                            $monthEnd = $monthDate->copy()->endOfMonth();
                            $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                            $weekEnd = $weekStart->copy()->addDays(6)->min($monthEnd);

                            $active =
                                $ganttStart && $ganttEnd && $weekEnd->gte($ganttStart) && $weekStart->lte($ganttEnd);
                        @endphp
                        <td>{{ $active ? '█' : '' }}</td>
                    @endforeach
                @endforeach
            </tr>

            <!-- Activities under this theme -->
            @foreach ($theme->activityChildren as $activity)
                @php
                    // Gantt logic for ACTIVITY
                    $statusValue = $activity->status ?? 'not_started';

                    $plannedStart = $activity->start_date;
                    $plannedEnd = $activity->completion_date;
                    $extendedEnd = $activity->latest_extension?->extended_completion_date ?? $plannedEnd;

                    $actualStart = $activity->actual_start_date;
                    $actualCompletion = $activity->actual_completion_date;

                    $ganttStart = $plannedStart;
                    $ganttEnd = $extendedEnd;

                    if ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::UnderProgress->value) {
                        $ganttStart = $actualStart ?? $plannedStart;
                        $ganttEnd = $extendedEnd;
                    } elseif ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::NoRequired->value) {
                        $ganttStart = $actualStart ?? $plannedStart;
                        $ganttEnd = $actualCompletion ?? $extendedEnd;
                    } elseif ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::Completed->value) {
                        $ganttStart = $actualStart ?? $plannedStart;
                        $ganttEnd = $actualCompletion ?? $extendedEnd;
                    }

                    if (!$ganttStart) {
                        $ganttStart = null;
                        $ganttEnd = null;
                    }

                    $statusLabel =
                        \Modules\Project\Models\Enums\ActivityStatus::tryFrom($statusValue)?->label() ?? 'Not Started';
                @endphp

                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>Activity</td>
                    <td>{{ $activity->deliverables ?? '' }}</td>
                    <td>{{ $activity->budget_description ?? '' }}</td>
                    <td>{{ $activity->start_date?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ $activity->completion_date?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ $activity->memberNames() ?: '' }}</td>
                    <td>{{ $statusLabel }}</td>
                    <td>{{ $activity->latest_extension?->extended_completion_date?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ $activity->latest_extension?->reason ?? '' }}</td>

                    @foreach ($months as $monthLabel)
                        @foreach ($weekLabels as $weekIndex => $weekLabel)
                            @php
                                $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                                $monthEnd = $monthDate->copy()->endOfMonth();
                                $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                                $weekEnd = $weekStart->copy()->addDays(6)->min($monthEnd);

                                $active =
                                    $ganttStart &&
                                    $ganttEnd &&
                                    $weekEnd->gte($ganttStart) &&
                                    $weekStart->lte($ganttEnd);
                            @endphp
                            <td>{{ $active ? '█' : '' }}</td>
                        @endforeach
                    @endforeach
                </tr>

                <!-- Sub-activities -->
                @foreach ($activity->children as $sub)
                    @php
                        // Gantt logic for SUB-ACTIVITY
                        $statusValue = $sub->status ?? 'not_started';

                        $plannedStart = $sub->start_date;
                        $plannedEnd = $sub->completion_date;
                        $extendedEnd = $sub->latest_extension?->extended_completion_date ?? $plannedEnd;

                        $actualStart = $sub->actual_start_date;
                        $actualCompletion = $sub->actual_completion_date;

                        $ganttStart = $plannedStart;
                        $ganttEnd = $extendedEnd;

                        if ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::UnderProgress->value) {
                            $ganttStart = $actualStart ?? $plannedStart;
                            $ganttEnd = $extendedEnd;
                        } elseif ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::NoRequired->value) {
                            $ganttStart = $actualStart ?? $plannedStart;
                            $ganttEnd = $actualCompletion ?? $extendedEnd;
                        } elseif ($statusValue === \Modules\Project\Models\Enums\ActivityStatus::Completed->value) {
                            $ganttStart = $actualStart ?? $plannedStart;
                            $ganttEnd = $actualCompletion ?? $extendedEnd;
                        }

                        if (!$ganttStart) {
                            $ganttStart = null;
                            $ganttEnd = null;
                        }

                        $statusLabel =
                            \Modules\Project\Models\Enums\ActivityStatus::tryFrom($statusValue)?->label() ??
                            'Not Started';
                    @endphp

                    <tr>
                        <td>{{ $sub->title }}</td>
                        <td>Sub Activity</td>
                        <td>{{ $sub->deliverables ?? '' }}</td>
                        <td>{{ $sub->budget_description ?? '' }}</td>
                        <td>{{ $sub->start_date?->format('Y-m-d') ?? '' }}</td>
                        <td>{{ $sub->completion_date?->format('Y-m-d') ?? '' }}</td>
                        <td>{{ $sub->memberNames() ?: '' }}</td>
                        <td>{{ $statusLabel }}</td>
                        <td>{{ $sub->latest_extension?->extended_completion_date?->format('Y-m-d') ?? '' }}</td>
                        <td>{{ $sub->latest_extension?->reason ?? '' }}</td>

                        @foreach ($months as $monthLabel)
                            @foreach ($weekLabels as $weekIndex => $weekLabel)
                                @php
                                    $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                                    $monthEnd = $monthDate->copy()->endOfMonth();
                                    $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                                    $weekEnd = $weekStart->copy()->addDays(6)->min($monthEnd);

                                    $active =
                                        $ganttStart &&
                                        $ganttEnd &&
                                        $weekEnd->gte($ganttStart) &&
                                        $weekStart->lte($ganttEnd);
                                @endphp
                                <td>{{ $active ? '█' : '' }}</td>
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        @endforeach

    @empty
        <tr>
            <td colspan="{{ 10 + $totalWeekColumns }}">
                No activities found for this project.
            </td>
        </tr>
    @endforelse

</table>
