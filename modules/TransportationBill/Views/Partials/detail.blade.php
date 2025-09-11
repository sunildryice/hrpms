<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span
                    class="card-subtitle text-uppercase text-primary">About</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{{ $transportationBill->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="Requester"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $transportationBill->getBillDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Bill Date"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-truck dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $transportationBill->shipper_name . ' '. $transportationBill->shipper_address !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Shipper"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-receipt dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $transportationBill->consignee_name . ' '. $transportationBill->consignee_address !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Consignee"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $transportationBill->getReceiverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Receiver"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $transportationBill->getAlternateReceiverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Alternate Receiver"></span>
            </li>

            <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Remarks</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $transportationBill->remarks !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>

            <li class="pt-4 pb-2"><span
                    class="card-subtitle text-uppercase text-primary">Special Instruction</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $transportationBill->instruction !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Instruction"></span>
            </li>
        </ul>
    </div>
</div>
