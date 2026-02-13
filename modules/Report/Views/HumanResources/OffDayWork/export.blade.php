<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center">Off Day Work Report</th>
        </tr>
        <tr>
            <th>S.N.</th>
            <th>Staff Name</th>
            <th>Office</th>
            <th>ODW Number</th>
            {{-- <th>Off Day Date</th> --}}
            <th>Request Date</th>
            <th>Projects</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($offDayWorks as $index => $odw)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $odw->getRequesterName() }}</td>
                <td>{{ $odw->getOfficeName() ?? '-' }}</td>
                <td>{{ $odw->getRequestId() }}</td>
                {{-- <td>{{ $odw->getOffDayWorkDate() }}</td> --}}
                <td>{{ $odw->getRequestDate() }}</td>
                <td>{{ implode(', ', $odw->getProjectNames()) ?: '-' }}</td>
                <td>{{ $odw->reason ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
