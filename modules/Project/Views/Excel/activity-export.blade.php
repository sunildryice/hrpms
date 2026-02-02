<table border="1">
    <!-- Title -->
    <tr>
        <td colspan="13">
            {{ $project->title ?? $project->name ?? 'Project' }} — Activity Plan
        </td>
    </tr>

    <!-- Main header row -->
    <tr>
        <th>SN</th>
        <th>Activities</th>
        <th>Type</th>
        <th>Output / Deliverables</th>
        <th>Timeline</th>
        <th>Members</th>
        <th>Status</th>
        <th>Extended Deadline</th>
        <th>Remarks</th>
        <th>Days left</th>

        <!-- Dynamic year-month-week columns -->
        @php
            // Find min and max dates from all activities
            $allDates = collect();
            $project->activities->each(function ($theme) use ($allDates) {
                if ($theme->start_date) $allDates->push($theme->start_date);
                if ($theme->completion_date) $allDates->push($theme->completion_date);
                if ($theme->latest_extension) {
                    if ($theme->latest_extension->extended_completion_date) {
                        $allDates->push($theme->latest_extension->extended_completion_date);
                    }
                }
                $theme->activityChildren->each(function ($act) use ($allDates) {
                    if ($act->start_date) $allDates->push($act->start_date);
                    if ($act->completion_date) $allDates->push($act->completion_date);
                    if ($act->latest_extension && $act->latest_extension->extended_completion_date) {
                        $allDates->push($act->latest_extension->extended_completion_date);
                    }
                });
            });

            $minDate = $allDates->min() ? $allDates->min()->startOfMonth() : now()->startOfYear();
            $maxDate = $allDates->max() ? $allDates->max()->endOfMonth() : now()->endOfYear();

            // Generate months between min and max
            $months = [];
            $current = $minDate->copy();
            while ($current->lte($maxDate)) {
                $months[] = $current->format('M Y');
                $current->addMonthNoOverflow();
            }

            // Prepare week labels: I, II, III, IV repeated for each month
            $weekLabels = ['I', 'II', 'III', 'IV'];
        @endphp

        @foreach ($months as $monthLabel)
            <th colspan="4">{{ $monthLabel }}</th>
        @endforeach
    </tr>

    <!-- Week row (I, II, III, IV for each month) -->
    <tr>
        <td colspan="10"></td> <!-- empty cells for left columns -->
        @foreach ($months as $month)
            @foreach ($weekLabels as $week)
                <th style="font-size:10px; text-align:center;">{{ $week }}</th>
            @endforeach
        @endforeach
    </tr>

    <!-- Data rows -->
    @php $sn = 1; @endphp

    @forelse ($project->activities->whereNull('parent_id') as $theme)
        <tr>
            <td>{{ $sn }}</td>
            <td>{{ $theme->title }}</td>
            <td>Activity Theme</td>
            <td>{{ $theme->deliverables ?? '' }}</td>

            @php
                $start = $theme->start_date;
                $end   = $theme->latest_extension?->extended_completion_date ?? $theme->completion_date;
            @endphp

            <td>
                @if($start && $end)
                    {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }}
                @endif
            </td>
            <td>{{ $theme->memberNames() }}</td>
            <td>{{ $theme->status ?? 'Not Started' }}</td>
            <td>{{ $theme->latest_extension?->extended_completion_date?->format('Y-m-d') }}</td>
            <td>{{ $theme->latest_extension?->reason ?? '' }}</td>
            <td>
                @if ($theme->completion_date)
                    {{ now()->diffInDays($theme->completion_date, false) }}
                @endif
            </td>

            <!-- One cell per week slot (4 per month) -->
            @foreach ($months as $monthLabel)
                @foreach ($weekLabels as $weekIndex => $weekLabel)
                    @php
                        $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                        $monthEnd  = $monthDate->copy()->endOfMonth();

                        // Approximate week start/end within month
                        $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                        $weekEnd   = $weekStart->copy()->addDays(6)->min($monthEnd);

                        $active = $start && $end &&
                                  $weekEnd->gte($start) &&
                                  $weekStart->lte($end);
                    @endphp
                    <td style="font-family:Consolas; text-align:center;">
                        {{ $active ? '██' : '' }}
                    </td>
                @endforeach
            @endforeach
        </tr>

        @php $activitySn = 1; @endphp
        @forelse ($theme->activityChildren as $activity)
            <tr>
                <td>{{ $sn . '.' . $activitySn }}</td>
                <td>{{ $activity->title }}</td>
                <td>Activity</td>
                <td>{{ $activity->deliverables ?? '' }}</td>

                @php
                    $start = $activity->start_date;
                    $end   = $activity->latest_extension?->extended_completion_date ?? $activity->completion_date;
                @endphp

                <td>
                    @if($start && $end)
                        {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }}
                    @endif
                </td>
                <td>{{ $activity->memberNames() }}</td>
                <td>{{ $activity->status ?? 'Not Started' }}</td>
                <td>{{ $activity->latest_extension?->extended_completion_date?->format('Y-m-d') }}</td>
                <td>{{ $activity->latest_extension?->reason ?? '' }}</td>
                <td>
                    @if ($activity->completion_date)
                        {{ now()->diffInDays($activity->completion_date, false) }}
                    @endif
                </td>

                @foreach ($months as $monthLabel)
                    @foreach ($weekLabels as $weekIndex => $weekLabel)
                        @php
                            $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                            $monthEnd  = $monthDate->copy()->endOfMonth();

                            $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                            $weekEnd   = $weekStart->copy()->addDays(6)->min($monthEnd);

                            $active = $start && $end &&
                                      $weekEnd->gte($start) &&
                                      $weekStart->lte($end);
                        @endphp
                        <td style="font-family:Consolas; text-align:center;">
                            {{ $active ? '██' : '' }}
                        </td>
                    @endforeach
                @endforeach
            </tr>

            @php $subSn = 1; @endphp
            @forelse ($activity->children as $sub)
                <tr>
                    <td>{{ $sn . '.' . $activitySn . '.' . $subSn }}</td>
                    <td>{{ $sub->title }}</td>
                    <td>Sub_activity</td>
                    <td>{{ $sub->deliverables ?? '' }}</td>

                    @php
                        $start = $sub->start_date;
                        $end   = $sub->latest_extension?->extended_completion_date ?? $sub->completion_date;
                    @endphp

                    <td>
                        @if($start && $end)
                            {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }}
                        @endif
                    </td>
                    <td>{{ $sub->memberNames() }}</td>
                    <td>{{ $sub->status ?? 'Not Started' }}</td>
                    <td>{{ $sub->latest_extension?->extended_completion_date?->format('Y-m-d') }}</td>
                    <td>{{ $sub->latest_extension?->reason ?? '' }}</td>
                    <td>
                        @if ($sub->completion_date)
                            {{ now()->diffInDays($sub->completion_date, false) }}
                        @endif
                    </td>

                    @foreach ($months as $monthLabel)
                        @foreach ($weekLabels as $weekIndex => $weekLabel)
                            @php
                                $monthDate = \Carbon\Carbon::createFromFormat('M Y', $monthLabel)->startOfMonth();
                                $monthEnd  = $monthDate->copy()->endOfMonth();

                                $weekStart = $monthDate->copy()->addWeeks($weekIndex);
                                $weekEnd   = $weekStart->copy()->addDays(6)->min($monthEnd);

                                $active = $start && $end &&
                                          $weekEnd->gte($start) &&
                                          $weekStart->lte($end);
                            @endphp
                            <td style="font-family:Consolas; text-align:center;">
                                {{ $active ? '██' : '' }}
                            </td>
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
    @empty
        <tr>
            <td colspan="{{ 10 + count($weekCells) }}" style="text-align:center;">
                No activities found
            </td>
        </tr>
    @endforelse
</table>