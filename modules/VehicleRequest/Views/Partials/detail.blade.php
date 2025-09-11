<div class="card-body">
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Request Number">
                <i class="bi-strava"></i>
                <span class="fw-bold col-auto"> Vehicle Request No. :</span>
                <span>{{ $vehicleRequest->getVehicleRequestNumber() }}</span>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Request Type">
                <i class="bi-info-circle"></i>
                <span class="fw-bold col-auto"> Vehicle Request Type :</span>
                <span>{{ $vehicleRequest->getVehicleRequestType() }}</span>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Purpose of Travel">
                <i class="bi-activity"></i>
                <span class="fw-bold col-auto"> Purpose of Travel :</span>
                <span>{{ $vehicleRequest->purpose_of_travel }}</span>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Vehicle Types">
                <i class="bi-truck"></i>
                <span class="fw-bold col-auto"> Vehicle Types :</span>
                <span>{!! $vehicleRequest->getVehicleTypes() !!}</span>
            </div>
        </div>
        @if($vehicleRequest->vehicle_request_type_id == 1)
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Office">
                    <i class="bi-bank"></i><span class="fw-bold col-auto"> Office :</span>
                    <span> {{ $vehicleRequest->getOfficeName() }} </span>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Travel Duration">
                    <i class="bi-calendar-range"></i><span class="fw-bold col-auto"> Travel Duration :</span>
                    <span> {{ $vehicleRequest->start_datetime }} - {{ $vehicleRequest->end_datetime }}
                        <span class="badge bg-primary">{{ $vehicleRequest->getDifferenceInDays() }} days</span>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Travel Locations">
                    <i class="bi-pin-map"></i><span class="fw-bold col-auto"> Travel Locations :</span>
                    <span>{{ $vehicleRequest->travel_from }} - {{ $vehicleRequest->destination }}</span>
                </div>
            </div>
            @if($vehicleRequest->status_id == config('constant.ASSIGNED_STATUS'))
                <div class="col-lg-6 mb-2">
                    <div class="d-flex align-items-start gap-2" rel="tooltip" title="Assigned Vehicle">
                        <i class="bi-calendar-range"></i><span class="fw-bold col-auto"> Assigned Vehicle :</span>
                        <span> {{ $vehicleRequest->getAssignedVehicleNumber() }}
                    </div>
                </div>
                <div class="col-lg-12 mb-2">
                    <div class="d-flex align-items-start gap-2" rel="tooltip" title="Assigned Remarks">
                        <i class="bi-command"></i><span class="fw-bold col-auto"> Assigned Remarks :</span>
                        <span> {{ $vehicleRequest->assigned_remarks }}
                    </div>
                </div>
            @endif
        @else
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Travel Duration">
                    <i class="bi-calendar-range"></i><span class="fw-bold col-auto"> Travel Duration :</span>
                    <span> {{ $vehicleRequest->start_datetime->format('j M, Y') }} - {{ $vehicleRequest->end_datetime->format('j M, Y') }}
                        <span class="badge bg-primary">{{ $vehicleRequest->getDifferenceInDays() }} days</span>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Travel Locations">
                    <i class="bi-pin-map"></i><span class="fw-bold col-auto"> Travel Locations :</span>
                    <span>{{ $vehicleRequest->travel_from }} - {{ $vehicleRequest->destination }}</span>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Pickup">
                    <i class="bi-pin-map"></i><span class="fw-bold col-auto"> Pickup :</span>
                    <span>{{ $vehicleRequest->pickup_time }} / {{ $vehicleRequest->pickup_place }}</span>
                </div>
            </div>

            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Tentative Cost (in NPR)">
                    <i class="bi-currency-dollar"></i><span class="fw-bold col-auto"> Tentative Cost (in NPR) :</span>
                    <span>{{ $vehicleRequest->tentative_cost }}</span>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Extra Travel from DHQ (in KM)">
                    <i class="bi-map"></i><span class="fw-bold col-auto"> Extra Travel from DHQ (in KM) :</span>
                    <span>{{ $vehicleRequest->extra_travel }}</span>
                </div>
            </div>

            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Districts">
                    <i class="bi-map"></i><span class="fw-bold col-auto"> Districts :</span>
                    <span>{!! $vehicleRequest->getDistricts() !!}</span>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Donor Code">
                    <i class="bi-currency-dollar"></i><span class="fw-bold col-auto"> Donor Code :</span>
                    <span> {{ $vehicleRequest->getDonorCode() }}</span>
                </div>
            </div>

            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Activity Code">
                    <i class="bi-activity"></i><span class="fw-bold col-auto"> Activity Code :</span>
                    <span> {{ $vehicleRequest->getActivityCode() }}</span>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Account Code">
                    <i class="bi-123"></i><span class="fw-bold col-auto"> Account Code :</span>
                    <span> {{ $vehicleRequest->getAccountCode() }}</span>
                </div>
            </div>
        @endif

        <div class="col-lg-12 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Accompanying Staff">
                <i class="bi-people"></i><span class="fw-bold col-auto"> Accompanying Staff :</span>
                <span> {!! $vehicleRequest->getAccompanyingStaffs() !!}</span>
            </div>
        </div>
        <div class="col-lg-12 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Remarks">
                <i class="bi-chat-dots"></i><span class="fw-bold col-auto"> Remarks :</span>
                <span> {{ $vehicleRequest->remarks }}</span>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Requester">
                <i class="bi-person"></i><span class="fw-bold col-auto"> Requester :</span>
                <span> {{ $vehicleRequest->getRequesterName() }}</span>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Approver">
                <i class="bi-person"></i><span class="fw-bold col-auto"> Approver :</span>
                <span> {{ $vehicleRequest->getApproverName() }}</span>
            </div>
        </div>
        @isset($vehicleRequest->closed_at)
            <div class="col-lg-12 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Closed By">
                    <i class="bi-person"></i><span class="fw-bold col-auto"> Closed By :</span>
                    <span> {!! $vehicleRequest->getClosedByName() !!}</span>
                </div>
            </div>
            <div class="col-lg-12 mb-2">
                <div class="d-flex align-items-start gap-2" rel="tooltip" title="Close Remarks">
                    <i class="bi-chat-dots"></i><span class="fw-bold col-auto"> Close Remarks :</span>
                    <span> {!! $vehicleRequest->close_remarks !!}</span>
                </div>
            </div>
        @endisset
    </div>
</div>

