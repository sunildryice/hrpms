<div class="card">
    <div class="card-header fw-bold">
        Leave Request Details
    </div>
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
                            {{ $leaveRequest->getLeaveNumber() }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Leave Number"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $leaveRequest->getRequesterName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Leave Requester"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-info-circle dropdown-item-icon"></i></div>
                        <div class="d-content-section">
                            {!! $leaveRequest->getLeaveType() !!}
                            (Balance
                            : {!! $leaveRequest->requester->employee->getLeaveBalance(
                                $leaveRequest->fiscal_year_id,
                                $leaveRequest->leave_type_id,
                            ) !!})
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Leave Type"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $leaveRequest->getSubstitutes() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Substitutes"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $leaveRequest->getApproverName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-calendar3-range dropdown-item-icon"></i></div>
                        <div class="d-content-section">
                            {{ $leaveRequest->getStartDate() }}
                            - {{ $leaveRequest->getEndDate() }}
                            <span class="badge bg-primary">
                                {{ $leaveRequest->getLeaveDuration() }} {{ $leaveRequest->leaveType->getLeaveBasis() }}
                            </span>
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Leave Duration"></span>
                </li>

                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">List By Days</span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-calendar-event dropdown-item-icon"></i></div>

                        <div class="gap-1 d-content-section flex-grow-1">
                            @foreach ($leaveRequest->leaveDays as $leaveDay)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between" rel="tooltip"
                                        title="{{ $leaveDay->getLeaveMode() }} leave for {{ $leaveDay->getLeaveDate() }} ">
                                        <span>{{ $leaveDay->getLeaveDate() }}
                                        </span>
                                        <span>
                                            {{ date('D', strtotime($leaveDay->getLeaveDate())) }}</span>
                                        <span class="badge bg-light text-dark">{{ $leaveDay->getLeaveMode() }}</span>
                                    </div>
                                    @if ($leaveDay->leave_duration == 2)
                                        <div class="text-end">
                                            <i class="bi-clock dropdown-item-icon"></i>
                                            <span
                                                title="Start Time - End time of {{ $leaveDay->getLeaveDate() }}">{{ $leaveDay->getLeaveTime() }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>

                @if (file_exists('storage/' . $leaveRequest->attachment) && $leaveRequest->attachment != '')
                    <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Prescription</span>
                    </li>
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-start">
                            <div class="icon-section">
                                <i class="dropdown-item-icon"></i>
                            </div>
                            <div class="d-content-section">
                                {{-- <a href="{!! asset('storage/' . $leaveRequest->attachment) !!}" target="_blank" class="btn btn-outline-primary btn-sm"
                                    title="View Attachment">
                                    <div class="media">
                                        <img src="{{ url('storage/' . $leaveRequest->attachment) }}"
                                            style="width: 80px;">
                                    </div>
                                </a> --}}
                                @if ($leaveRequest->attachment && file_exists('storage/' . $leaveRequest->attachment))
                                    <a href="{!! asset('storage/' . $leaveRequest->attachment) !!}" target="_blank" class="fs-5"
                                        title="View Attachment">
                                        <i class="bi bi-file-earmark-medical"></i>
                                    </a>
                                @else
                                    <span class="text-muted"> (No attachment)</span>
                                @endif
                            </div>
                        </div>
                        {{-- <span class="stretched-link" rel="tooltip" title="Attachment"></span> --}}
                    </li>
                @endif
                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Reason for Leave</span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section">
                            <i class="bi-chat-dots dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section"> {!! $leaveRequest->remarks !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Remarks"></span>
                </li>
                @if (
                    $leaveRequest->approver_id == auth()->id() &&
                        isset($leaveRequest->verifier_id) &&
                        $leaveRequest->status_id == config('constant.VERIFIED_STATUS'))
                    <li class="pt-4"><span class="card-subtitle text-uppercase text-primary">HR Review</span></li>
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-start">
                            <div class="icon-section">
                                <i class="bi-person dropdown-item-icon"></i>
                            </div>
                            <div class="d-content-section"> {!! $leaveRequest->getReviewerName() !!}</div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
                    </li>
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-start">
                            <div class="icon-section">
                                <i class="bi-chat-dots dropdown-item-icon"></i>
                            </div>
                            <div class="d-content-section"> {!! $leaveRequest->review_remarks !!}</div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="HR Remarks"></span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

@if ($leaveRequest->parentLeaveRequest)
    <a class="mt-2 btn btn-sm btn-primary"
        href="{{ route('leave.requests.detail', $leaveRequest->parentLeaveRequest->id) }}" target="_blank"
        title="Leave Request">View Amended Leave Request</a>
@endif
