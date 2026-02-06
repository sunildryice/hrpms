@php
    // Prepare all data needed for header & table
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

    // Total number of week columns
    $totalWeekColumns = count($months) * 4;
@endphp

<table border="1">

    <!-- 1. Project Title -->
    <tr>
        <td colspan="{{ 10 + $totalWeekColumns }}">
            Project: {{ $project?->title ?? 'Project Activity Plan' }}<br>
            Period: [{{ $project?->start_date?->format('M d, Y') ?? '' }} to
            {{ $project?->completion_date?->format('M d, Y') ?? '' }}]
        </td>
    </tr>

    <!-- 2. Year Row (merged per year) -->
    <tr>
        <th rowspan="3">Activities</th>
        <th rowspan="3">Activity Type</th>
        <th rowspan="3">Output / Deliverables</th>
        <th rowspan="3">Budget Description</th>
        <th rowspan="3">Start Date</th>
        <th rowspan="3">Completion Date</th>
        <th rowspan="3">Members</th>
        <th rowspan="3">Completion status</th>
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
                        $yearGroups[] = [
                            'year' => $currentYear,
                            'colspan' => $colspanCount,
                        ];
                    }
                    $currentYear = $year;
                    $colspanCount = 1;
                } else {
                    $colspanCount++;
                }
            }

            // Last group
            if ($currentYear !== null) {
                $yearGroups[] = [
                    'year' => $currentYear,
                    'colspan' => $colspanCount,
                ];
            }
        @endphp

        @foreach ($yearGroups as $group)
            <td colspan="{{ $group['colspan'] * 4 }}">
                Year[{{ $group['year'] }}]
            </td>
        @endforeach
    </tr>

    <!-- 3. Month Row -->
    <tr>
        @foreach ($months as $monthLabel)
            @php
                $monthName = explode(' ', $monthLabel)[0];
            @endphp
            <td colspan="4">{{ $monthName }}</td>
        @endforeach
    </tr>

    <!-- 4. Week Labels Row -->
    <tr>
        @foreach ($months as $month)
            @foreach ($weekLabels as $week)
                <td style="padding: 4px 0;">{{ $week }}</td>
            @endforeach
        @endforeach
    </tr>


    <!-- === Main content - grouped by stage === -->
    @php
        $grouped = $project->activities->whereNull('parent_id')->groupBy('stage');
        $globalSn = 1;
    @endphp

    @forelse ($grouped as $stageName => $themesInStage)
        <!-- Stage row -->
        <tr style="background:#f0f4f8;">
            <td colspan="2">
                <strong>{{ json_decode($stageName)->title ?? 'No Stage' }}</strong>
            </td>
            <td colspan="{{ 8 + $totalWeekColumns }}"></td>
            @foreach ($months as $monthLabel)
                @foreach ($weekLabels as $_)
                    <td></td>
                @endforeach
            @endforeach
        </tr>

        @php $sn = $globalSn; @endphp

        @foreach ($themesInStage as $theme)
            <tr>
                <td>{{ $theme->title }}</td>
                <td>Activity Theme</td>
                <td>{{ $theme->deliverables ?? '' }}</td>
                <td>{{ $theme->budget_description ?? '' }}</td>

                @php
                    $start = $theme->start_date;
                    $end = $theme->latest_extension?->extended_completion_date ?? $theme->completion_date;
                @endphp

                <td>{{ $start->format('Y-m-d') }}</td>
                <td>{{ $theme->completion_date->format('Y-m-d') }}</td>

                <td>{{ $theme->memberNames() }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $theme->status ?? 'not started')) }}</td>
                <td>{{ $theme->latest_extension?->extended_completion_date?->format('Y-m-d') }}</td>
                <td>{{ $theme->latest_extension?->reason ?? '' }}</td>

                @foreach ($months as $monthLabel)
                    @foreach ($weekLabels as $weekIndex => $weekLabel)
                        @php
                            $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                            $monthEnd = $monthDate->copy()->endOfMonth();
                            $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                            $weekEnd = $weekStart->copy()->addDays(6)->min($monthEnd);

                            $active = $start && $end && $weekEnd->gte($start) && $weekStart->lte($end);
                        @endphp
                        <td>{{ $active ? '█' : '' }}</td>
                    @endforeach
                @endforeach
            </tr>

            @php $activitySn = 1; @endphp

            @forelse ($theme->activityChildren as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>Activity</td>
                    <td>{{ $activity->deliverables ?? '' }}</td>
                    <td>{{ $activity->budget_description ?? '' }}</td>

                    @php
                        $start = $activity->start_date;
                        $end = $activity->latest_extension?->extended_completion_date ?? $activity->completion_date;
                    @endphp

                    <td>{{ $start->format('Y-m-d') }}</td>
                    <td>{{ $activity->completion_date->format('Y-m-d') }}</td>

                    <td>{{ $activity->memberNames() }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $activity->status ?? 'not started')) }}</td>
                    <td>{{ $activity->latest_extension?->extended_completion_date?->format('Y-m-d') }}</td>
                    <td>{{ $activity->latest_extension?->reason ?? '' }}</td>

                    @foreach ($months as $monthLabel)
                        @foreach ($weekLabels as $weekIndex => $weekLabel)
                            @php
                                $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                                $monthEnd = $monthDate->copy()->endOfMonth();
                                $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                                $weekEnd = $weekStart->copy()->addDays(6)->min($monthEnd);

                                $active = $start && $end && $weekEnd->gte($start) && $weekStart->lte($end);
                            @endphp
                            <td>{{ $active ? '█' : '' }}</td>
                        @endforeach
                    @endforeach
                </tr>

                @php $subSn = 1; @endphp

                @forelse ($activity->children as $sub)
                    <tr>
                        <td>{{ $sub->title }}</td>
                        <td>Sub Activity</td>
                        <td>{{ $sub->deliverables ?? '' }}</td>
                        <td>{{ $sub->budget_description ?? '' }}</td>

                        @php
                            $start = $sub->start_date;
                            $end = $sub->latest_extension?->extended_completion_date ?? $sub->completion_date;
                        @endphp

                        <td>{{ $start->format('Y-m-d') }}</td>
                        <td>{{ $sub->completion_date->format('Y-m-d') }}</td>

                        <td>{{ $sub->memberNames() }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $sub->status ?? 'not started')) }}</td>
                        <td>{{ $sub->latest_extension?->extended_completion_date?->format('Y-m-d') }}</td>
                        <td>{{ $sub->latest_extension?->reason ?? '' }}</td>

                        @foreach ($months as $monthLabel)
                            @foreach ($weekLabels as $weekIndex => $weekLabel)
                                @php
                                    $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                                    $monthEnd = $monthDate->copy()->endOfMonth();
                                    $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                                    $weekEnd = $weekStart->copy()->addDays(6)->min($monthEnd);

                                    $active = $start && $end && $weekEnd->gte($start) && $weekStart->lte($end);
                                @endphp
                                <td>{{ $active ? '█' : '' }}</td>
                            @endforeach
                        @endforeach
                    </tr>
                    @php $subSn++; @endphp
                @empty
                @endforelse

                @php $activitySn++; @endphp
            @empty
            @endforelse

            @php $sn++; @endphp
        @endforeach

        @php $globalSn = $sn; @endphp
    @empty
        <tr>
            <td colspan="{{ 11 + count($months) * 4 }}" style="text-align:center; padding:20px;">
                No activities found
            </td>
        </tr>
    @endforelse

</table>
