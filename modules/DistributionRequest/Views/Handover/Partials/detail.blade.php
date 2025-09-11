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
                        {{ $distributionHandover->getDistributionHandoverNumber() }}
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Distribution Handover Number"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-calendar dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $distributionHandover->date_of_handover }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Date of handover"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $distributionHandover->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Purchase Requester"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $distributionHandover->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $distributionHandover->getReceiverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Receiver"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-envelope dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $distributionHandover->to_name !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="To"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-badge-cc dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $distributionHandover->cc_name !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="CC"></span>
            </li>


            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Letter Body</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $distributionHandover->letter_body !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Letter Body"></span>
            </li>
            @isset($distributionHandover->received_date)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Receive Details</span></li>
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="icon-section"><i class="bi bi-calendar3-event"></i></div>
                        <div class="d-content-section"> {!! $distributionHandover->received_date->format('Y-m-d') !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Received Date"></span>
                </li>
                @isset($distributionHandover->handover_date)
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="icon-section"><i class="bi-arrows-fullscreen dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {!! $distributionHandover->handover_date?->format('Y-m-d') !!}</div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Handover Date"></span>
                    </li>
                @endisset
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $distributionHandover->receiver_remarks !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Receiver Remarks"></span>
                </li>
            @endisset
        </ul>
    </div>
</div>
