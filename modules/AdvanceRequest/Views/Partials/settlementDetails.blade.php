<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Cash surplus/deficit</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-currency-dollar dropdown-item-icon"></i></div>
                        @php
                            $advanceAmount = $advanceSettlementRequest->advanceRequest?->getEstimatedAmount();
                            $expenditurePaid = $advanceSettlementRequest->settlementExpenses?->sum('net_amount');
                            $cashSurplusOrDeficit = $advanceAmount - $expenditurePaid;
                        @endphp
                    <div
                        class="d-content-section"> {{$cashSurplusOrDeficit}} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Cash surplus or deficit"></span>
            </li>

            <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Reason for over/underspending</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->reason_for_over_or_under_spending !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Reason for over/underspending"></span>
            </li>


            <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Remarks</span></li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->remarks !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
        </ul>
    </div>
</div>
