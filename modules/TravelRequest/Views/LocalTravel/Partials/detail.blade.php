<div class="card-body">
    <div class="p-1">
        <ul class="mb-0 list-unstyled list-py-2 text-dark">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-text-center dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $localTravel->title !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Purpose"></span>
            </li>
            @if ($localTravel->requester->employee_id != $localTravel->employee_id)
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-lines-fill dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $localTravel->getEmployeeName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Traveller"></span>
                </li>
            @endif
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $localTravel->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-strava dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $localTravel->getTravelRequestNumber() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Travel Request"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $localTravel->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $localTravel->remarks !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
        </ul>
    </div>
</div>
