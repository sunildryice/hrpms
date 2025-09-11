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
                        {{ $distributionRequest->getDistributionRequestNumber() }}
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Distribution Number"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $distributionRequest->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Purchase Requester"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $distributionRequest->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-code-slash dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $distributionRequest->getProjectCode() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Project Code"></span>
            </li>


            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-map dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $distributionRequest->getDistrictName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="District Name"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-hospital dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $distributionRequest->getHealthFacility() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Health Facility"></span>
            </li>


            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $distributionRequest->remarks !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
        </ul>
    </div>
</div>
