<div class="card-body">
    <div class="p-1">
        <ul class="mb-0 list-unstyled list-py-2 text-dark">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section">
                        <i class="bi-strava dropdown-item-icon"></i>
                    </div>
                    <div class="d-content-section">
                        {{ $purchaseRequest->getPurchaseRequestNumber() }}
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="PR Number"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $purchaseRequest->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Purchase Requester"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $purchaseRequest->getBudgetVerifier() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Budget Verifier"></span>
            </li>
            @if ($purchaseRequest->reviewer_id)
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $purchaseRequest->getReviewerName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Finance Reviewer"></span>
                </li>
            @endif
            @if ($purchaseRequest->recommender_id)
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $purchaseRequest->getRecommender() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Recommender"></span>
                </li>
            @endif

            @if ($purchaseRequest->verifier_id)
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $purchaseRequest->getVerifierName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
                </li>
            @endif
            @if ($purchaseRequest->approver_id)
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $purchaseRequest->getApproverName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>
            @endif

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $purchaseRequest->getRequestDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Request Date"></span>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $purchaseRequest->getRequiredDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Required Date"></span>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-map dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $purchaseRequest->getDistrictNames() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="District Name"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-currency-dollar dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $purchaseRequest->total_amount !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Total Tentative Amount"></span>
            </li>
            @if ($purchaseRequest->procurementOfficers()->count())
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi bi-people dropdown-item-icon"></i></div>
                        <div class="d-content-section">{!! $purchaseRequest->procurementOfficers->implode('full_name', ', ') !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Procurement Officers"></span>
                </li>
            @endif

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Purpose</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $purchaseRequest->purpose !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>


            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Delivery Instructions</span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $purchaseRequest->delivery_instructions !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Delivery Instructions"></span>
            </li>

            @if ($purchaseRequest->modification_number)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Amendment Remarks</span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $purchaseRequest->modification_remarks !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Amendment Remarks"></span>
                </li>
            @endif

            @isset($purchaseRequest->closed_at)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Close Detail</span></li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $purchaseRequest->getClosedByName() }} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Closed By"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $purchaseRequest->close_remarks !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Close Remarks"></span>
                </li>
            @endisset


            @if (auth()->user()->can('print', $purchaseRequest))
                <li class="mt-3 position-relative">
                    <a href="{{ route('purchase.requests.print', $purchaseRequest->id) }}" target="_blank"
                        title="Print Purchase Request" class="btn btn-primary btn-sm"> <i
                            class="bi-printer me-1"></i> Print Purchase Request</a>
                </li>
            @endif
        </ul>
    </div>
</div>
