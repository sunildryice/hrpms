<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section">
                        <i class="bi-strava dropdown-item-icon"></i>
                    </div>
                    <div class="d-content-section">
                        {{ $travel->getTravelAuthorizationNumber() }}
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Travel Number"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-building dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $travel->office->getOfficeName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Office"></span>
            </li>
             <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $travel->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>
            @isset($travel->recommender_id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $travel->getRecommenderName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Recommender"></span>
                </li>
            @endisset
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $travel->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Objectives</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $travel->objectives !!}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Objectives"></a>
            </li>
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Outcomes</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travel->outcomes }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Outcomes"></a>
            </li>
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travel->remarks }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
            </li>

            @isset($travel->cancel_remarks)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-danger">Cancel Remarks</span></li>
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $travel->cancel_remarks }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
                </li>
            @endisset
            @if ($travel->travelReport && !request()->is('travel/reports/*'))
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Travel Report</span></li>
                <li class="position-relative">
                    <a href="{{ route('ta.reports.show', $travel->travelReport->id) }}" target="_blank"
                        title="Travel Report" class="text-decoration-none badge bg-primary text-white">View Travel
                        Report</a>
                </li>
            @endif
            {{-- @if (!request()->is('travel/requests/*'))
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Travel Request</span>
                </li>
                <li class="position-relative">
                    <a href="{{ route('ta.requests.view', $travel->id) }}" target="_blank"
                        title="Travel Request" class="text-decoration-none badge bg-primary text-white">View Travel
                        Request</a>
                </li>
            @endif --}}
        </ul>
    </div>
</div>
