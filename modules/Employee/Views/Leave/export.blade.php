<table class="table table-bordered" id="employeeLeaveTable">
    <thead>
    <tr>
        <th></th>
        <th colspan="15" class="text-center">Employee Leave Report</th>
    </tr>
    <tr>
        <th></th>
        <th colspan="15">Employee Name: {{ $record->getFullName() }}</th>
    </tr>
    <tr>
        <th colspan="15"></th>
    </tr>
    <tr>
        <th rowspan="2">Month</th>
        @foreach($leaveTypes as $leaveType)
            @if($leaveType->leave_frequency == 2)
                <th colspan="5" class="text-center">{{ $leaveType->title }} ({{ $leaveType->getLeaveBasis() }})</th>
            @else
                <th rowspan="1" class="text-center">{{ $leaveType->title }} ({{ $leaveType->getLeaveBasis() }})</th>
            @endif
        @endforeach
    </tr>
    <tr>
        @foreach($leaveTypes as $leaveType)
            @if($leaveType->leave_frequency == 2)
                <th>Opening Balance</th>
                <th>Earned</th>
                <th>Taken</th>
                <th>Paid</th>
                <th>Balance</th>
            @else
                <th class="text-center">Taken</th>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($leaves->groupBy('reported_date') as $leaveGroups)
        <tr>
            <td>{{ $leaveGroups->first()->getReportedDateMonth() }}</td>
            @foreach($leaveTypes as $leaveType)
                @php
                    $selectedLeave = $leaveGroups->filter(function($leaveGroup) use ($leaveType){
                       return  $leaveType->id == $leaveGroup->leave_type_id;
                    })->first();
                @endphp
                @if($leaveType->leave_frequency == 2)
                    <td class="text-center">{{ $selectedLeave ?->opening_balance }}</td>
                    <td>{{ $selectedLeave ?->earned }}</td>
                    <td>{{ $selectedLeave ?->taken }}</td>
                    <td>{{ $selectedLeave ?->paid }}</td>
                    <td>{{ $selectedLeave ?->balance }}</td>
                @else
                    <td class="text-center">{{ $selectedLeave ?->taken}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
        <tr>
            <td>Total</td>
            @foreach($leaveTypes as $leaveType)
                @if($leaveType->leave_frequency == 2)
                    @php
                        $earnedTotal = $leaves->where('leave_type_id', $leaveType->id)->sum('earned');
                        $takenTotal = $leaves->where('leave_type_id', $leaveType->id)->sum('taken');
                    @endphp
                    <td></td>
                    <td>{{ $earnedTotal }}</td>
                    <td>{{ $takenTotal }}</td>
                    <td></td>
                    <td></td>
                @else
                    @php
                        $takenTotal = $leaves->where('leave_type_id', $leaveType->id)->sum('taken');
                    @endphp
                    <td class="text-center">{{$takenTotal}}</td>
                @endif
            @endforeach
        </tr>
    </tbody>
</table>