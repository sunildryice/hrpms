<table>
    <thead>
        <tr>
            <th colspan="9" style="text-align: center">Work From Home Report</th>
        </tr>
        <tr>
            <th>S.N.</th>
            <th>Staff Name</th>
            <th>Office</th>
            <th>WFH Number</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Request Date</th>
            <th>Total Days</th>
            <th>Projects</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($workFromHomes as $index => $wfh)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $wfh->getRequesterName() }}</td>
                <td>{{ $wfh->getOfficeName() ?? '-' }}</td>
                <td>{{ $wfh->getRequestId() }}</td>
                <td>{{ $wfh->getStartDate() }}</td>
                <td>{{ $wfh->getEndDate() }}</td>
                <td>{{ $wfh->getRequestDate() }}</td>
                <td>{{ $wfh->getTotalDays() }} day{{ $wfh->getTotalDays() > 1 ? 's' : '' }}</td>
                <td>{{ implode(', ', $wfh->getProjectNames()) ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
