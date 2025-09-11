<table class="table table-bordered" id="leaveRequestReportTable">
    <thead>
        <tr>
            <th colspan="17" style="text-align: center">Leave Record Summary Report of {{$fiscalYear}} {{$month != null ? 'for the M/O '.$month : 'for all Months'}}</th>
        </tr>
        <tr>
            <th colspan="17"></th>
        </tr>
    <tr>
        <th rowspan="2">{{ __('label.sn') }}</th>
        <th rowspan="2">Staff Name</th>
        <th rowspan="2">Staff Type</th>
        @foreach($leaveTypes as $leaveType)
            @if($leaveType->maximum_carry_over > 0)
                <th colspan="5" class="text-center">{{ $leaveType->title }} ({{ $leaveType->getLeaveBasis() }})</th>
            @else
                <th colspan="1" class="text-center">{{ $leaveType->title }} ({{ $leaveType->getLeaveBasis() }})</th>
            @endif
        @endforeach
        <th rowspan="2" class="text-center">Total Leave Balance (Days)</th>
        <th rowspan="2" class="text-center">Remarks</th>
    </tr>
    <tr>
        @foreach($leaveTypes as $leaveType)
            @if($leaveType->maximum_carry_over > 0)
                <th>Carryover (@if(request()->get('month')) Last Month @else Last Year @endif)</th>
                <th>Earned</th>
                <th>Taken</th>
                <th>Paid</th>
                <th>Balance</th>
            @else
                <th>Taken</th>
            @endif
            
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($filteredEmployees as $index=>$employee)
        @php $totalBalance = 0; @endphp
        <tr>
            <td>{{ $index+1 }}</td>
            <td>{{ $employee->getFullName() }}</td>
            <td>{{ $employee->getEmployeeType() }}</td>
            @foreach($leaveTypes as $leaveType)
                @php $employeeLeaves = $leaves->filter(function($leave) use ($employee, $leaveType){
                        return $leave->employee_id == $employee->id && $leave->leave_type_id == $leaveType->id;
                    })->sortBy('reported_date');
                    $balance = $employeeLeaves->count() ? $employeeLeaves->last()->balance : 0;
                @endphp
                @if($leaveType->maximum_carry_over > 0)
                    @php
                        $totalBalance += $leaveType->getLeaveBasis() == 'Hour' ? round($balance/8,2): $balance;
                    @endphp
                    <td>{{ $employeeLeaves->count() ? $employeeLeaves->first()->opening_balance : '-' }}</td>
                    <td>{{ $employeeLeaves->sum('earned') }}</td>
                    <td>{{ $employeeLeaves->sum('taken') }}</td>
                    <td>{{ $employeeLeaves->sum('paid') }}</td>
                    <td>{{ $balance ?: '-' }}</td>
                @else
                    <td>{{ $employeeLeaves->sum('taken') }}</td>
                @endif
            @endforeach
            <td>{{ round($totalBalance,2) }}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>