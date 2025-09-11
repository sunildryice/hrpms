@isset($travelClaim)
<div class="card">
    @php
        $itineraries = $travelClaim->itineraries()->with(['travelRequestItinerary.activityCode', 'travelRequestItinerary.donorCode', 'office'])
                                ->select(['travel_claim_itineraries.*', 'i2.activity_code_id', 'i2.donor_code_id'])
                                ->join('travel_request_itineraries as i2','i2.id','=', 'travel_claim_itineraries.travel_itinerary_id')
                                ->get();

        $itineraries = $itineraries->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])->flatten(2)
            ->map(function($itinerary){
                $dsa = collect();
                foreach($itinerary as $index => $value){
                    if($index == 0){
                        $dsa = $value;
                        continue;
                    }
                    $dsa->total_amount += $value->total_amount;
                }
                $dsa->subledger = 'DSA';
                return $dsa;
            });

        $expenses = $travelClaim->expenses()->with(['activityCode', 'donorCode', 'office'])->get();
        $expenses = $expenses->groupBy(['activity_code_id', 'donor_code_id', 'office_id'])->flatten(2)
            ->map(function($expense){
                $travel = collect();
                foreach($expense as $index => $value){
                    if($index == 0){
                        $travel = $value;
                        continue;
                    }
                    $travel->expense_amount += $value->expense_amount;
                }
                $travel->subledger = 'Travel';
            return $travel;
        });

        $summaries = $expenses->merge($itineraries)->groupBy('activity_code_id')->flatten(1);
    @endphp
    <div class="card-header fw-bold">
        Summary of Travel Claim
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="summaryTable">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">{{ __('label.activity') }}</th>
                        <th scope="col">Subledger</th>
                        <th scope="col">{{ __('label.donor') }}</th>
                        <th scope="col">Charging Office</th>
                        <th scope="col">{{ __('label.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                     @foreach ($summaries as $summary)
                     <tr>
                         <td>{{ $summary->getActivityTitle() }}</td>
                         <td>{{ $summary->subledger }}</td>
                         <td>{{ $summary->getDonorDescription() }}</td>
                         <td>{{ $summary->office->office_name }}</td>
                         <td>{{ $summary->getAmount() }}</td>
                     </tr>
                     @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">{{ __('label.total-amount') }}</td>
                        <td id="total_expense_amount">
                            {{ $travelClaim->total_expense_amount + $travelClaim->total_itinerary_amount }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endisset
