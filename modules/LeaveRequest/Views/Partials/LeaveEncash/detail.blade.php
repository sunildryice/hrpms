<div class="card">
    <div class="card-header fw-bold">
        Leave Encash Details
    </div>
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
                            {{ $leaveEncash->getEncashNumber() }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Leave Number"></span>
                </li>
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi bi-person-circle dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $leaveEncash->getEmployeeName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Employee"></span>
                </li>

                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $leaveEncash->getRequesterName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Encashment Initiator"></span>
                </li>


                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-info-circle dropdown-item-icon"></i></div>
                        <div class="d-content-section">
                            {!! $leaveEncash->getLeaveType() !!}
                            (Balance
                            : {!! $leaveEncash->available_balance . ' ' . $leaveEncash->leaveType->getLeaveBasis() !!})
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Leave Type"></span>
                </li>



                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-info-circle dropdown-item-icon"></i></div>
                        <div class="d-content-section">
                            {!! $leaveEncash->encash_balance !!}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Encash Balance"></span>
                </li>

                @isset($leaveEncash->fiscal_year_id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi bi-calendar dropdown-item-icon"></i></div>
                        <div class="d-content-section">
                            {!! $leaveEncash->fiscalYear->title !!}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Fiscal Year"></span>
                </li>
                @endisset

                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi bi-calendar-event dropdown-item-icon"></i></div>
                    <div class="d-content-section">
                        {!! $leaveEncash->getRequestMonth() !!}
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Month"></span>
            </li>

                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-check-fill dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $leaveEncash->getReviewerName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
                </li>
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $leaveEncash->getApproverName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>


                @if (file_exists('storage/' . $leaveEncash->attachment) && $leaveEncash->attachment != '')
                    <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Attachment</span></li>
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="icon-section">
                                <i class="dropdown-item-icon"></i>
                            </div>
                            <div class="d-content-section">
                                <a href="{!! asset('storage/' . $leaveEncash->attachment) !!}" target="_blank" class="btn btn-outline-primary btn-sm"
                                    title="View Attachment">
                                    <div class="media">
                                        <img src="{{ url('storage/' . $leaveEncash->attachment) }}"
                                            style="width: 80px;">
                                    </div>
                                </a>
                            </div>
                        </div>
                        {{-- <span class="stretched-link" rel="tooltip" title="Attachment"></span> --}}
                    </li>
                @endif
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="icon-section">
                            <i class="bi-chat-dots dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section"> {!! $leaveEncash->remarks !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Remarks"></span>
                </li>
            </ul>
        </div>
    </div>
</div>

@if ($leaveEncash->parentLeaveRequest)
    <a class="btn btn-sm btn-primary mt-2"
        href="{{ route('leave.requests.detail', $leaveEncash->parentLeaveRequest->id) }}" target="_blank"
        title="Leave Request">View Amended Leave Request</a>
@endif
