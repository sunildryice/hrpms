<table class="table table-bordered" id="leaveRequestReportTable">
    <thead>
    <tr>
        <th colspan="17" style="text-align: center">Leave Requests Report</th>
    </tr>
    <tr>
        <th>{{ __('label.sn') }}</th>
        <th>Staff Name</th>
        <th>Office</th>
        <th>Leave Number</th>
        <th>Leave Type</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Request Date</th>
        <th>Request Days/Hours</th>
    </tr>
    </thead>
    <tbody>
    @foreach($leaveRequests as $index=>$leaveRequest)
        <tr>
            <td>{{ $index+1 }}</td>
            <td>{{ $leaveRequest->getRequesterName() }}</td>
            <td>{{ $leaveRequest->getOfficeName() }}</td>
            <td>{{ $leaveRequest->getLeaveNumber() }}</td>
            <td>{{ $leaveRequest->getLeaveType() }}</td>
            <td>{{ $leaveRequest->getStartDate() }}</td>
            <td>{{ $leaveRequest->getEndDate() }}</td>
            <td>{{ $leaveRequest->getRequestDate() }}</td>
            <td>{{ $leaveRequest->getLeaveDuration() . ' ' . $leaveRequest->leaveType->getLeaveBasis(); }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
