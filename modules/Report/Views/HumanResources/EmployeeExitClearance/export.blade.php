<table>
    <thead>
        <tr>
            <th colspan="10" style="text-align: center">Staff Exiting Report</th>
        </tr>
        <tr>
            <th>Exit Ref No.</th>
            <th>Employee Name</th>
            <th>Designation</th>
            <th>Duty Station</th>
            <th>Joined Date</th>
            <th>Resigned Date</th>
            <th>Resigned Year</th>
            <th>Last Working Date</th>
            <th>Clearance Report</th>
            <th>Assets Handover</th>
            <th>Exit Handover</th>
            <th>Exit Interview</th>
            <th>Approved Date</th>
            <th>Insurance Premium Refund</th>
            <th>Final Payment</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($exitHandoverNotes as $exitHandoverNote)
            <tr>
                <td></td>
                <td>{{ $exitHandoverNote->employee->getFullName() }}</td>
                <td>{{ $exitHandoverNote->employee->latestTenure->getDesignationName() }}</td>
                <td>{{ $exitHandoverNote->employee->latestTenure->getDutyStation() }}</td>
                <td>{{ $exitHandoverNote->employee->firstTenure->getJoinedDate() }}</td>
                <td>{{ $exitHandoverNote->getResignationDate() }}</td>
                <td>{{ $exitHandoverNote->getResignationYear() }}</td>
                <td>{{ $exitHandoverNote->getLastDutyDate() }}</td>
                <td></td>
                <td></td>
                <td>{{ $exitHandoverNote->getStatus() }}</td>
                {{-- <td>{{ $exitHandoverNote->exitInterview->status_id == config('constant.CREATED_STATUS') ? 'Pending' : 'Submitted' }}</td> --}}
                <td>{{ $exitHandoverNote->exitInterview->getStatus() }}</td>
                <td>{{ $exitHandoverNote->exitInterview->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at->toFormattedDateString() }}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>