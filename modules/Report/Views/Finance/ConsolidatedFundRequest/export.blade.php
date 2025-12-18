<table class="table">
    <thead class="bg-light">
        <tr>
            <th colspan="4" style="text-align: center">Consolidated Fund Request</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center">HERD International</th>
        </tr>
        <tr>
            <th>Year</th>
            <th>For the Month:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$year}}</td>
            <td>{{date("F", mktime(0, 0, 0, $month, 10))}}</td>
        </tr>
    </tbody>
</table>
<table class="table" id="consolidatedFundRequestReportTable">
    <thead class="bg-light">
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Activity Name</th>
            @foreach ($offices as $office)
                <th>Fund for: {{$office->getOfficeName()}}</th>
            @endforeach
            <th>Total Amount (Rs.)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activityCodes as $key=>$activityCode)
            <tr>
                <td>{{++$key}}</td>
                {{-- <td>{{$activityCode->getActivityCodeWithDescription()}}</td> --}}
                <td>{{$activityCode->getActivityCode()}}</td>
                @php
                    $fundTotal = 0;
                @endphp
                @foreach ($offices as $office)
                    @php
                        $fund = $fundRequestActivities->where('activity_code_id', '=', $activityCode->id)
                                        ->where('fundRequest.request_for_office_id', $office->id)
                                        ->sum('estimated_amount');

                        $fundTotal += $fund;
                    @endphp
                    <td>{{$fund == 0 ? '' : $fund}}</td>
                @endforeach
                <td>{{$fundTotal == 0 ? '' : $fundTotal}}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="2">TOTAL FUND REQUIRED</th>
            @php
                $activityFundTotal = 0;
            @endphp
            @foreach ($offices as $office)
                @php
                    $officeFundRequired = $fundRequestActivities->where('fundRequest.request_for_office_id', $office->id)
                                        ->sum('estimated_amount');
                    $activityFundTotal += $officeFundRequired;
                @endphp
                <th>{{$officeFundRequired == 0 ? '' : $officeFundRequired}}</th>
            @endforeach
            <th>{{$activityFundTotal == 0 ? '' : $activityFundTotal}}</th>
        </tr>

        <tr>
            <td colspan="2">Estimated Surplus/(Deficit) of Current Month</td>
            @php
                $activitySurplusDeficitTotal = 0;
            @endphp
            @foreach ($offices as $office)
                @php
                    $officeSurplusDeficit = $fundRequests->where('request_for_office_id', $office->id)
                                                        ->sum('estimated_surplus');
                    $activitySurplusDeficitTotal += $officeSurplusDeficit;
                @endphp
                <td>{{$officeSurplusDeficit == 0 ? '' : $officeSurplusDeficit}}</td>
            @endforeach
            <td>{{$activitySurplusDeficitTotal == 0 ? '' : $activitySurplusDeficitTotal}}</td>
        </tr>

        <tr>
            <th colspan="2">Net Fund Required</th>
            @php
                $activityNetFundRequiredTotal = 0;
            @endphp
            @foreach ($offices as $office)
                @php
                    $officeNetFundRequired = $fundRequests->where('request_for_office_id', $office->id)
                                                        ->sum('net_amount');
                    $activityNetFundRequiredTotal += $officeNetFundRequired;
                @endphp
                <th>{{$officeNetFundRequired == 0 ? '' : $officeNetFundRequired}}</th>
            @endforeach
            <th>{{$activityNetFundRequiredTotal == 0 ? '' : $activityNetFundRequiredTotal}}</th>
        </tr>

        <tr>
            <th colspan="2">Requester</th>
            @foreach ($offices as $office)
                @php
                    $requesters = $fundRequests->where('request_for_office_id', $office->id)
                                    ->map( fn($fundRequest) => $fundRequest->getRequesterName())
                                    ->implode(", ");
                @endphp
                <td>{{ $requesters }}</td>
            @endforeach
            <td></td>
        </tr>
        
        <tr>
            <th colspan="2">Approver</th>
            @foreach ($offices as $office)
                @php
                    $approvers = $fundRequests->where('request_for_office_id', $office->id)
                                    ->map( fn($fundRequest) => $fundRequest->getApproverName())
                                    ->implode(", ");
                @endphp
                <td>{{ $approvers }}</td>
            @endforeach
            <td></td>
        </tr>

        <tr>
            <th colspan="2">Approved On</th>
            @foreach ($offices as $office)
                @php
                    $approvedDate = $fundRequests->where('request_for_office_id', $office->id)
                                    ->map( fn($fundRequest) => $fundRequest->approvedLog?->created_at?->toFormattedDateString())
                                    ->implode(", ");
                @endphp
                <td>{{ $approvedDate }}</td>
            @endforeach
            <td></td>
        </tr>
    </tbody>
</table>