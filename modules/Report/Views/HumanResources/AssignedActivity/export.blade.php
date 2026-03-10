<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center">Assigned Activity Report</th>
        </tr>
        <tr style="font-weight:bold; background:#f2f2f2;">
            <th>S.N.</th>
            <th>Project</th>
            <th>Parent Activity</th>
            <th>Activity</th>
            <th>Stage</th>
            <th>Activity Level</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activities as $index => $act)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $act->project_title }}</td>
                <td>{{ $act->parent_title ?: '-' }}</td>
                <td>{{ $act->title }}</td>
                <td>{{ $act->stage_title ?: '-' }}</td>
                <td>{{ ucfirst($act->activity_level) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
