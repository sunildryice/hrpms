<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span
                    class="card-subtitle text-uppercase text-primary">About</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-strava dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{{ $grn->getGrnNumber() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="GRN Number"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-receipt dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{!! $grn->getGrnableNumber()!!}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="PR/PO Number"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{{ $grn->getCreatedBy() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="Created By"></span>
            </li>

{{--            <li class="position-relative">--}}
{{--                <div class="d-flex gap-2 align-items-center">--}}
{{--                    <div class="icon-section"><i--}}
{{--                            class="bi-person-badge dropdown-item-icon"></i></div>--}}
{{--                    <div--}}
{{--                        class="d-content-section"> {!! $grn->getApproverName() !!} </div>--}}
{{--                </div>--}}
{{--                <span class="stretched-link" rel="tooltip" title="Approver"></span>--}}
{{--            </li>--}}

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-truck-flatbed dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $grn->getSupplierName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Supplier"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $grn->getReceivedDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Received Date"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-map dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{!! $grn->grnable?->getDistrictNames() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Districts"></span>
            </li>

            <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Received Note</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $grn->received_note !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Received Note"></span>
            </li>
        </ul>
    </div>
</div>
