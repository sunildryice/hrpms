<table border="1">
    <!-- Title -->
    <tr>
        <td colspan="12">
            {{ $project->title ?? 'Project' }} — Activity Plan
        </td>
    </tr>

    <!-- Headers -->
    <tr>
        <th>Activities</th>
        <th>Activity Type</th>
        <th>Output / Deliverables</th>
        <th>Timeline</th>
        <th>Members</th>
        <th>Status</th>
        <th>Extended Deadline</th>
        <th>Remarks</th>
        <th>Days left</th>

        @php
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

            $minDate = $allDates->min() ? $allDates->min()->startOfMonth() : now()->startOfYear();
            $maxDate = $allDates->max() ? $allDates->max()->endOfMonth() : now()->endOfYear();

            $months = [];
            $current = $minDate->copy();
            while ($current->lte($maxDate)) {
                $months[] = $current->format('M Y');
                $current->addMonthNoOverflow();
            }

            $weekLabels = ['I', 'II', 'III', 'IV'];
        @endphp

        @foreach ($months as $monthLabel)
            <th colspan="4">{{ $monthLabel }}</th>
        @endforeach
    </tr>

    <!-- Week labels -->
    <tr>
        <td colspan="9"></td>
        @foreach ($months as $month)
            @foreach ($weekLabels as $week)
                <th style="font-size:10px; text-align:center;">{{ $week }}</th>
            @endforeach
        @endforeach
    </tr>

    <!-- === Main content - grouped by stage === -->
    @php
        $grouped = $project->activities->whereNull('parent_id')->groupBy('stage');
        $globalSn = 1;
    @endphp

    @forelse ($grouped as $stageName => $themesInStage)
        <!-- Stage row – light background (will be styled in AfterSheet too) -->
        <tr class="stage-row">
            <td colspan="2">
                <strong>{{ json_decode($stageName)->title ?? 'No Stage' }}</strong>
            </td>
            <td colspan="7"></td>
            @foreach ($months as $monthLabel)
                @foreach ($weekLabels as $_)
                    <td></td>
                @endforeach
            @endforeach
        </tr>

        @php
            $rowType = 'theme';
            $sn = $globalSn;
        @endphp

        @foreach ($themesInStage as $theme)
            <tr class="theme-row">
                <td>{{ $theme->title }}</td>
                <td>Activity Theme</td>
                <td>{{ $theme->deliverables ?? '' }}</td>

                @php
                    $start = $theme->start_date;
                    $end = $theme->latest_extension?->extended_completion_date ?? $theme->completion_date;
                @endphp

                <td>
                    @if ($start && $end)
                        {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }}
                    @endif
                </td>
                <td>{{ $theme->memberNames() }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $theme->status ?? 'not started')) }}</td>
                <td>{{ $theme->latest_extension?->extended_completion_date?->format('Y-m-d') }}</td>
                <td>{{ $theme->latest_extension?->reason ?? '' }}</td>
                <td>
                    @if ($theme->completion_date)
                        {{ now()->diffInDays($theme->completion_date, false) }}
                    @endif
                </td>

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
                <tr class="activity-row">
                    <td>{{ $activity->title }}</td>
                    <td>Activity</td>
                    <td>{{ $activity->deliverables ?? '' }}</td>

                    @php
                        $start = $activity->start_date;
                        $end = $activity->latest_extension?->extended_completion_date ?? $activity->completion_date;
                    @endphp

                    <td>
                        @if ($start && $end)
                            {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }}
                        @endif
                    </td>
                    <td>{{ $activity->memberNames() }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $activity->status ?? 'not started')) }}</td>
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

                        @php
                            $start = $sub->start_date;
                            $end = $sub->latest_extension?->extended_completion_date ?? $sub->completion_date;
                        @endphp

                        <td>
                            @if ($start && $end)
                                {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }}
                            @endif
                        </td>
                        <td>{{ $sub->memberNames() }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $sub->status ?? 'not started')) }}</td>
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
            <td colspan="12" style="text-align:center;">No activities found</td>
        </tr>
    @endforelse
</table>
