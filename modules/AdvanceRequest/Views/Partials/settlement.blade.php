<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span
                    class="card-subtitle text-uppercase text-primary">About</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-sort-numeric-up dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{{ $advanceSettlementRequest->advanceRequest->getAdvanceRequestNumber() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="Advance Request Number"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{{ $advanceSettlementRequest->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="Requester"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->getReviewerName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->getCompletionDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Completion Date"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->advanceRequest->getApprovedDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Advance Issue Date"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->getApprovedDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Settlement Date"></span>
            </li>


            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-currency-dollar dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->advance_amount !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Advance Amount"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-currency-dollar dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->settlementExpenses->sum('gross_amount') !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Expense Gross Amount"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-currency-dollar dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->settlementExpenses->sum('tax_amount') !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="TAX Amount"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-currency-dollar dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->settlementExpenses->sum('net_amount') !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Expense Net Amount"></span>
            </li>

            <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Purpose</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->advanceRequest->purpose !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
            <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Office</span></li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $advanceSettlementRequest->advanceRequest->requestForOffice->getOfficeName() !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
        </ul>
    </div>
</div>
<div class="card-footer" style="display: flex; flex-direction: row; justify-content: center;">
    <a class="btn btn-sm btn-primary" href="{{route('advance.requests.show', $advanceRequest->id)}}">
        View Advance Request
    </a>
</div>
