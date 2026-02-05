<div class="card">
    <div class="card-header fw-bold">
        Lieu Leave Request Details
    </div>
    <div class="card-body">
        <div class="p-1">
            <ul class="mb-0 list-unstyled list-py-2 text-dark">
                <li class="pb-2">
                    <span class="card-subtitle text-uppercase text-primary">About</span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-hash dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">{{ $lieuLeaveRequest->getRequestId() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Request Number"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-person-bounding-box dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $lieuLeaveRequest->requester->full_name ?? '-' }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Requester"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-person-check dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $lieuLeaveRequest->approver->full_name ?? '-' }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section">
                            <i class="bi-calendar3-range dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $lieuLeaveRequest->leaveBalance->offDayWorkApprovedDate() }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Off Day Approved Day"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section">
                            <i class="bi-calendar3-range dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $lieuLeaveRequest->getStartDate() }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Duration"></span>
                </li>


                <li class="pt-4 pb-2">
                    <span class="card-subtitle text-uppercase text-primary">
                        Reason for Lieu Leave
                    </span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section">
                            <i class="bi-chat-dots dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {!! nl2br(e($lieuLeaveRequest->reason)) !!}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Reason"></span>
                </li>

                <li class="pt-4 pb-2">
                    <span class="card-subtitle text-uppercase text-primary">Status</span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-info-circle dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            <span class="{!! $lieuLeaveRequest->getStatusClass() !!}">
                                {!! $lieuLeaveRequest->getStatus() !!}
                            </span>
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Status"></span>
                </li>
            </ul>
        </div>
    </div>
</div>
