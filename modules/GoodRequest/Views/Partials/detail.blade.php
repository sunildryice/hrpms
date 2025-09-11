<div class="card-body">
    @if($goodRequest)
        <div class="p-1">
            <ul class="list-unstyled list-py-2 text-dark mb-0">
                <li class="pb-2"><span
                        class="card-subtitle text-uppercase text-primary">About</span></li>
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-person-bounding-box dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section">{{ $goodRequest->getRequesterName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip"
                          title="Requester"></span>
                </li>

                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-person-badge dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {!! $goodRequest->getReviewerName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
                </li>

                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-person-badge dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {!! $goodRequest->getApproverName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>

                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-person-badge dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {!! $goodRequest->getLogisticOfficerName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Assignor"></span>
                </li>

                @isset($goodRequest->receiver_id)
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="icon-section"><i
                                    class="bi-person-badge dropdown-item-icon"></i></div>
                            <div
                                class="d-content-section"> {!! $goodRequest->getReceiverName() !!} </div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Receiver"></span>
                    </li>
                @endisset

                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-building dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {!! $goodRequest->office->getOfficeName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Office"></span>
                </li>

                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-calendar-event dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {!! $goodRequest->handover_date?->format('Y-m-d') !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Date of Handover"></span>
                </li>


                <li class="pt-4 pb-2"><span
                        class="card-subtitle text-uppercase text-primary">Purpose</span></li>
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="icon-section"><i
                                class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $goodRequest->purpose !!}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Purpose"></span>
                </li>
                @if(isset($goodRequest->receiver_note, $goodRequest->received_at))
                    <li class="pt-4 pb-2"><span
                            class="card-subtitle text-uppercase text-primary">Receiver Note</span></li>
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="icon-section"><i
                                    class="bi-calendar dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {!! $goodRequest->received_at?->toFormattedDateString() !!}</div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Received At"></span>
                    </li>
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="icon-section"><i
                                    class="bi-chat-dots dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {!! $goodRequest->receiver_note !!}</div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Receiver Note"></span>
                    </li>
                @endif
            </ul>
        </div>
    @endif
</div>
