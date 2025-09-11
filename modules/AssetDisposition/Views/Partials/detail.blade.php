<div class="card">
<div class="card-body details-card">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi bi-building dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $dispositionRequest->getOfficeName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Office"></span>
            </li>
            {{-- <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi bi-box-fill dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $dispositionRequest->getAssetCode() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Asset"></span>
            </li> --}}
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $dispositionRequest->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>
            @if ($dispositionRequest->approver->id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $dispositionRequest->getApproverName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>
            @endif
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-geo-fill dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $dispositionRequest->getDispositionType() !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Disposition Type"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $dispositionRequest->getDispositionDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Disposition Date"></span>
            </li>
            @php
                $reason = $dispositionRequest->disposition_reason;
            @endphp
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Reason</span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section">
                        <div id="initial-reason">
                            {!! strlen($reason) > 100 ? substr($reason, 0, 100) . '...' : substr($reason, 0, 100) !!}
                        </div>
                        <div id="full-reason" style="display: none;">
                            {!! $reason !!}
                        </div>
                    </div>
                </div>
                <a href="#" class="stretched-link" id="reason-section" rel="tooltip" title="reason"></a>

            {{-- @if (auth()->user()->can('print', $dispositionRequest))
                <li class="position-relative mt-3">
                    <a href="{{ route('event.completion.print', $dispositionRequest->id) }}" target="_blank"
                        title="Print ECR" class="btn btn-primary btn-sm"> <i class="bi-printer me-1"></i>
                        Print ECR</a>
                </li>
            @endif --}}
        </ul>
    </div>
</div>
</div>
