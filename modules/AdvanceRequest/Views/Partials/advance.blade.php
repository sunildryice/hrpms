<div class="card-body">
    <div class="p-1">
        <ul class="mb-0 list-unstyled list-py-2 text-dark">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    {{-- <div class="icon-section"><i
                            class="bi-person-bounding-box dropdown-item-icon"></i></div> --}}
                    <div class="d-content-section text-primary fw-bold">{{ $advanceRequest->getAdvanceRequestNumber() }}
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Advance Request Number"></span>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $advanceRequest->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $advanceRequest->getVerifierName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Verifier"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $advanceRequest->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $advanceRequest->getRequestDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Request Date"></span>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $advanceRequest->getRequiredDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Required Date"></span>
            </li>


            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Purpose</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $advanceRequest->purpose !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>

            {{-- <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">District</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i
                            class="dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $advanceRequest->district->district_name !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="District"></span>
            </li> --}}

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Office</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i class="dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $advanceRequest->requestForOffice->getOfficeName() !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Office"></span>
            </li>
            @isset($advanceRequest->closed_at)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Close Detail</span></li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $advanceRequest->getClosedByName() }} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Closed By"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-calendar3-range dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $advanceRequest->getClosedDate() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Closed Date"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $advanceRequest->close_remarks !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Close Remarks"></span>
                </li>
            @endisset
            @isset($advanceRequest->paid_at)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Payment Detail</span></li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $advanceRequest->getPaidByName() }} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Paid By"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-calendar3-range dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $advanceRequest->pay_date !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Paid Date"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $advanceRequest->payment_remarks !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Payment Remarks"></span>
                </li>
            @endisset
        </ul>
    </div>
</div>
