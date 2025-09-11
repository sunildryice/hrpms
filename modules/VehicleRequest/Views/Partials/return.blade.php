@if(isset($vehicleRequest) && isset($vehicleRequest->latestReturnLog) && $vehicleRequest->status_id == config('constant.RETURNED_STATUS'))
    @php
        $log = $vehicleRequest->latestReturnLog;
    @endphp
    <div class="card pt-3 pb-3 mt-3">
        <div class="card-header">Return Details</div>
        <div class="card-body">
            <div class="p-1">
                <ul class="list-unstyled list-py-2 text-dark mb-0">
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                            <div class="d-content-section">{{ $log->getCreatedBy() }}</div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Returner"></span>
                    </li>
                    {{-- <li class="position-relative">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="icon-section">
                                <i class="bi-strava dropdown-item-icon"></i>
                            </div>
                            <div class="d-content-section">
                                <span class="badge bg-primary c-badge">{{ $log->createdBy->employee->latestTenure->getDesignationName() }}</span>
                                
                            </div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Designation"></span>
                    </li> --}}
                 
                    <li class="pt-3"><span class="card-subtitle text-uppercase text-danger">Return Remarks</span></li>
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {!! $log->log_remarks !!} </div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Reason"></span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
@endif