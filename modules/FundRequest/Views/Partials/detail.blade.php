<div class="card-body">
    <div class="p-1">
        <ul class="mb-0 list-unstyled list-py-2 text-dark">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span>
                @if ($fundRequest->status_id == config('constant.CANCELLED_STATUS'))
                    <span class="text-danger">(Cancelled)<span>
                @endif

            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $fundRequest->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $fundRequest->checker->getFullName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Checker"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $fundRequest->certifier->getFullName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Certifier"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $fundRequest->getReviewerName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $fundRequest->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-code-slash dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $fundRequest->getProjectCode() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Project Code"></span>
            </li>


            {{-- <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i
                            class="bi-map dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{!! $fundRequest->getDistrictName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="District Name"></span>
            </li> --}}

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi bi-calendar dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $fundRequest->getMonthName() . ' ' . $fundRequest->getFiscalYear() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Period"></span>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi bi-building dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $fundRequest->getRequestForOfficeName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Office Name (Requested for)"></span>
            </li>


            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi bi-file-earmark-medical dropdown-item-icon"></i></div>
                    <div class="d-content-section">
                        Attachment
                        @if (file_exists('storage/' . $fundRequest->attachment) && $fundRequest->attachment != '')
                            <a href="{!! asset('storage/' . $fundRequest->attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
                                <i class="bi bi-file-earmark-medical"></i>
                            </a>
                        @endif
                    </div>
                </div>
                {{-- <span class="stretched-link" rel="tooltip" title="Attachment"></span> --}}
            </li>


            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $fundRequest->remarks !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
        </ul>
    </div>
</div>
