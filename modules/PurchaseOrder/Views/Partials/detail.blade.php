@php
    //$prIds = $purchaseOrder->purchaseRequests->map(function ($pr){
    //    return $pr->getPurchaseRequestNumber();
    //})->implode(', ');

@endphp
<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span
                    class="card-subtitle text-uppercase text-primary">About</span></li>
            <li class="position-relative">
                <div class="gap-2 d-flex align-items-center">
                    <div class="icon-section"><i class="bi-strava dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $purchaseOrder->getPurchaseOrderNumber() }}</div>
                    @if ($purchaseOrder->status_id == config('constant.CANCELLED_STATUS'))
                        <span class="text-danger">(Cancelled)<span>
                    @endif
                </div>
                <span class="stretched-link" rel="tooltip"
                        title="PO Number"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-receipt dropdown-item-icon"></i></div>
                    {{-- <div class="d-content-section">{{ $prIds }}</div> --}}
                    <div class="d-content-section">
                      @foreach( $purchaseOrder->purchaseRequests as $pr)
                        <a href="{{ route('purchase.requests.show', $pr->id) }}" class="text-primary text-decoration-none" rel="tooltip" title="Purchase Request">
                          {{ $pr->getPurchaseRequestNumber() }}</a>
                        @if(!$loop->last && $pr->getPurchaseRequestNumber())
                          ,
                        @endif
                       @endforeach
                    </div>
                </div>
                {{-- <span class="stretched-link" rel="tooltip" --}}
                {{--         title="PR Number"></span> --}}
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{{ $purchaseOrder->getCreatedBy() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="Created By"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $purchaseOrder->getReviewerName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $purchaseOrder->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-truck-flatbed dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $purchaseOrder->getSupplierName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Supplier"></span>
            </li>

            @isset($purchaseOrder->lta_contract_id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi bi-file-earmark-text dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {!! $purchaseOrder->getContractNumber() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Contract Number"></span>
                </li>
            @endisset

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $purchaseOrder->getOrderDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Order Date"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $purchaseOrder->getDeliveryDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Delivery Date"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-map dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{!! $purchaseOrder->getDistrictNames() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="District Names"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi bi-cash dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{!! $purchaseOrder->getCurrency() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Currency"></span>
            </li>
            <li class="pt-4 pb-2"><span
                class="card-subtitle text-uppercase text-primary">Delivery Instruction</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-geo-alt dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $purchaseOrder->delivery_location !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Delivery Location"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $purchaseOrder->delivery_instructions !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Delivery Instructions"></span>
            </li>
        </ul>
    </div>
</div>
