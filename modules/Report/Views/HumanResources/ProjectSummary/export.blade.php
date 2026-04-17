<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center">Project Summary Report</th>
        </tr>
        <tr>
            <th>S.N.</th>
            <th>Project</th>
            <th>Total</th>
            <th>Completed</th>
            <th>Under Progress</th>
            <th>Not Started</th>
            <th>Not Required</th>
            <th>Start Date</th>
            <th>Completion Date</th>
            <th>Team Lead</th>
            <th>Focal Person</th>
            <th>Status</th>
            <th>Primary Funder</th>
            <th>Contracting Agency</th>
            <th>Budget (USD)</th>
            <th>Working Areas (Districts)</th>
            <th>Project Theme</th>
            <th>Approaches</th>
            <th>Sector</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($projects as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->short_name ?? $p->title }}</td>
                <td>{{ $p->total_activities ?? 0 }}</td>
                <td>{{ $p->completed_count ?? 0 }}</td>
                <td>{{ $p->under_progress_count ?? 0 }}</td>
                <td>{{ $p->not_started_count ?? 0 }}</td>
                <td>{{ $p->no_required_count ?? 0 }}</td>
                <td>{{ $p->formatted_start_date ?: '-' }}</td>
                <td>{{ $p->formatted_completion_date ?: '-' }}</td>
                <td>{{ optional($p->teamLead)->full_name ?? '-' }}</td>
                <td>{{ optional($p->focalPerson)->full_name ?? '-' }}</td>
                <td>{{ $p->getActiveStatus() }}</td>
                <td>{{ $p->primary_funder ?: '-' }}</td>
                <td>{{ $p->contracting_agency ?: '-' }}</td>
                <td>{{ $p->budget_usd ? number_format($p->budget_usd, 2) : '-' }}</td>
                <td>{{ $p->districts->pluck('district_name')->join(', ') ?: '-' }}</td>
                <td>{{ optional($p->projectTheme)->title ?? '-' }}</td>
                <td>{{ $p->approaches->pluck('title')->join(', ') ?: '-' }}</td>
                <td>{{ optional($p->sector)->title ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="19" style="text-align: center; padding: 15px;">
                    No project summary records found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
