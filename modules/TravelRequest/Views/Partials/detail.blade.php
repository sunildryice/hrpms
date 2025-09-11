<div class="card-body">
    <div class="p-1">
        <ul class="mb-0 list-unstyled list-py-2 text-dark">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section">
                        <i class="bi-strava dropdown-item-icon"></i>
                    </div>
                    <div class="d-content-section">
                        {{ $travelRequest->getTravelRequestNumber() }}
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Travel Number"></span>
            </li>
            @if ($travelRequest->requester->employee_id != $travelRequest->employee_id)
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-lines-fill dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $travelRequest->getEmployeeName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Traveller"></span>
                </li>
            @endif
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $travelRequest->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $travelRequest->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-pin-map dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travelRequest->getTravelType() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Travel Type"></a>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travelRequest->getAccompanyingStaffs() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Accompanying Staffs"></a>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-dash-square dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travelRequest->getProjectCode() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Project"></a>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-dash-square dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travelRequest->purpose_of_travel }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Purpose of Travel"></a>
            </li>

            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travelRequest->getSubstitutes() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Substitutes"></a>
            </li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travelRequest->getDepartureDate() }} -
                        {{ $travelRequest->getReturnDate() }} <span
                            class="badge bg-primary">{{ $travelRequest->getTotalDays() }}
                            Days</span></div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Travel Duration"></a>
            </li>

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $travelRequest->remarks }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
            </li>

            @if ($travelRequest->received_advance_amount && $travelRequest->advance_received_at)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Advance</span></li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-cash dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $travelRequest->formattedReceivedAmount() }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Advance Amount"></a>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-calendar dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $travelRequest->getAdvanceReceivedDate() }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Advance Received At"></a>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-person dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $travelRequest->getFinanceUserName() }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Finance Employee"></a>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $travelRequest->finance_remarks }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
                </li>
            @endif


            @isset($travelRequest->cancel_remarks)
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-danger">Cancel Remarks</span></li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $travelRequest->cancel_remarks }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
                </li>
            @endisset
            @if ($travelRequest->travelReport && !request()->is('travel/reports/*'))
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Travel Report</span>
                </li>
                <li class="position-relative">
                    <a href="{{ route('travel.reports.show', $travelRequest->travelReport->id) }}" target="_blank"
                        title="Travel Report" class="text-white text-decoration-none badge bg-primary">View Travel
                        Report</a>
                </li>
            @endif
            @if (!request()->is('travel/requests/*'))
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Travel Request</span>
                </li>
                <li class="position-relative">
                    <a href="{{ route('travel.requests.view', $travelRequest->id) }}" target="_blank"
                        title="Travel Request" class="text-white text-decoration-none badge bg-primary">View Travel
                        Request</a>
                </li>
            @endif
        </ul>
    </div>
</div>
