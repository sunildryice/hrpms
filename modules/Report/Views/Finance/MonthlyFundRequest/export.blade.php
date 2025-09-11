<table>
    <tr>
        <th colspan="2" style="text-align: center">Fund Request</th>
        <th colspan="7"></th>
    </tr>
    <tr>
        <th colspan="2" style="text-align: center">One Heart Worldwide</th>
        <th colspan="7"></th>
    </tr>

    <tr>
        <td colspan="9"></td>
    </tr>

    <tr>
        <td colspan="9">Year: {{$year}}</td>
    </tr>
    <tr>
        <td colspan="9">For the Month: {{date("F", mktime(0, 0, 0, $month, 10))}}</td>
    </tr>
    <tr>
        <td colspan="9">Fund Requested by: {{$requesterName}}</td>
    </tr>
    <tr>
        <td colspan="9">Fund Requested for (Office): {{$officeName}}</td>
    </tr>
    {{-- <tr>
        <td colspan="9">Fund Requested for (District): {{$districtName}}</td>
    </tr> --}}

    <tr>
        <td colspan="9"></td>
    </tr>

    <thead class="bg-light">
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Activity Name</th>
            <th>Estimated Fund</th>
            <th>Projected Target</th>
            <th>Budget</th>
            <th>DIP Target</th>
            <th>Budget Variance</th>
            <th>Target Variance</th>
            <th>Remarks/Variance notes</th>
        </tr>
    </thead>
    <tbody>
        @php
            $estimatedFundTotal     = 0;
            $projectedTargetTotal   = 0;
            $budgetTotal            = 0;
            $dipTargetTotal         = 0;
            $budgetVarianceTotal    = 0;
            $targetVarianceTotal    = 0;
        @endphp
        @foreach ($activityCodes as $key=>$activityCode)
            @php
                $estimatedFund          = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                            ->sum('estimated_amount');
                $estimatedFundTotal     += $estimatedFund;


                $projectedTarget        = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                            ->sum('project_target_unit');
                $projectedTargetTotal   += $projectedTarget;


                $budget                 = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                            ->sum('budget_amount'); 
                $budgetTotal            += $budget;


                $dipTarget              = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                            ->sum('dip_target_unit');
                $dipTargetTotal         += $dipTarget;


                $budgetVariance         = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                            ->sum('variance_budget_amount');   
                $budgetVarianceTotal    += $budgetVariance;


                $targetVariance         = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                                            ->sum('variance_target_unit');   
                $targetVarianceTotal    += $targetVariance;

            @endphp

            <tr>
                <td>{{++$key}}</td>
                <td>{{$activityCode->getActivityCodeWithDescription()}}</td>
                <td>{{$estimatedFund == 0 ? '' : $estimatedFund }}</td>
                <td>{{$projectedTarget == 0 ? '' : $projectedTarget }}</td>
                <td>{{$budget == 0 ? '' : $budget }}</td>
                <td>{{$dipTarget == 0 ? '' : $dipTarget }}</td>
                <td>{{$budgetVariance == 0 ? '' : $budgetVariance }}</td>
                <td>{{$targetVariance == 0 ? '' : $targetVariance }}</td>
                <td></td>
            </tr>
        @endforeach

        <tr>
            <th colspan="2">TOTAL FUND REQUIRED</th>
            <th>{{$estimatedFundTotal}}</th>
            <th>{{$projectedTargetTotal}}</th>
            <th>{{$budgetTotal}}</th>
            <th>{{$dipTargetTotal}}</th>
            <th>{{$budgetVarianceTotal}}</th>
            <th>{{$targetVarianceTotal}}</th>
            <th></th>
        </tr>

        <tr>
            <td colspan="2">Estimated Surplus/(Deficit) of Current Month</td>
            <th>{{$fundRequests->sum('estimated_surplus')}}</th>
            <th colspan="6"></th>
        </tr>

        <tr>
            <th colspan="2">Net Fund Required</th>
            <th>{{$fundRequests->sum('net_amount')}}</th>
            <th colspan="6"></th>
        </tr>

    </tbody>
</table>

