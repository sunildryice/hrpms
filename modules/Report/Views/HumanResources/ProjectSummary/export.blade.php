<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center">Project Summary Report</th>
        </tr>
        <tr>
            <th>S.N.</th>
            <th>Project</th>
            <th>Activities</th>
            <th>Completed</th>
            <th>Under Progress</th>
            <th>Not Started</th>
            <th>No Longer Req.</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($projects as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->title }}</td>
                <td>{{ $p->total_activities }}</td>
                <td>{{ $p->completed_count }}</td>
                <td>{{ $p->under_progress_count }}</td>
                <td>{{ $p->not_started_count }}</td>
                <td>{{ $p->no_required_count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
