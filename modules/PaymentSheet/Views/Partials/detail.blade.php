<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $paymentSheet->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $paymentSheet->getVerifierName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Verifier"></span>
            </li>
            @isset($paymentSheet->recommender_id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $paymentSheet->recommender->getFullName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Recommender"></span>
                </li>
            @endisset

            @if ($paymentSheet->reviewer_id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $paymentSheet->getReviewerName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
                </li>
            @endif
            @if ($paymentSheet->approver_id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $paymentSheet->getApproverName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>
            @endif
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-paperclip dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $paymentSheet->getPaymentSheetNumber() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Sheet Number"></span>
            </li>
            {{-- @if ($paymentSheet->district_id) --}}
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-geo-fill"></i></div>
                        <div class="d-content-section"> {!! $paymentSheet->getDistricts() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="District"></span>
                </li>
            {{-- @endif --}}

            {{--            <li class="position-relative"> --}}
            {{--                <div class="d-flex gap-2 align-items-center"> --}}
            {{--                    <div class="icon-section"><i --}}
            {{--                            class="bi-truck dropdown-item-icon"></i></div> --}}
            {{--                    <div --}}
            {{--                        class="d-content-section"> {!! $paymentSheet->getSupplierName() !!} </div> --}}
            {{--                </div> --}}
            {{--                <span class="stretched-link" rel="tooltip" title="Supplier"></span> --}}
            {{--            </li> --}}
            {{--            <li class="position-relative"> --}}
            {{--                <div class="d-flex gap-2 align-items-center"> --}}
            {{--                    <div class="icon-section"><i --}}
            {{--                            class="bi-receipt dropdown-item-icon"></i></div> --}}
            {{--                    <div --}}
            {{--                        class="d-content-section"> {!! $paymentSheet->getSupplierVatPanNumber() !!} </div> --}}
            {{--                </div> --}}
            {{--                <span class="stretched-link" rel="tooltip" title="Supplier's VAT/PAN"></span> --}}
            {{--            </li> --}}

            {{--            <li class="position-relative"> --}}
            {{--                <div class="d-flex gap-2 align-items-center"> --}}
            {{--                    <div class="icon-section"><i --}}
            {{--                            class="bi-person dropdown-item-icon"></i></div> --}}
            {{--                    <div --}}
            {{--                        class="d-content-section"> {!! $paymentSheet->supplier->contact_person_name !!} </div> --}}
            {{--                </div> --}}
            {{--                <span class="stretched-link" rel="tooltip" title="Contact Person Name"></span> --}}
            {{--            </li> --}}
            {{--            <li class="position-relative"> --}}
            {{--                <div class="d-flex gap-2 align-items-center"> --}}
            {{--                    <div class="icon-section"><i --}}
            {{--                            class="bi-phone dropdown-item-icon"></i></div> --}}
            {{--                    <div --}}
            {{--                        class="d-content-section"> {!! $paymentSheet->supplier->contact_number !!} </div> --}}
            {{--                </div> --}}
            {{--                <span class="stretched-link" rel="tooltip" title="Contact Number"></span> --}}
            {{--            </li> --}}

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $paymentSheet->getPurpose() !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Purpose of payment sheet"></span>
            </li>

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Supplier Details</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-truck dropdown-item-icon"></i></div>
                    <div class="d-content-section">
                        Supplier Name: {!! $paymentSheet->getSupplierName() !!} <br />
                        VAT/PAN Number: {!! $paymentSheet->getSupplierVatPanNumber() !!} <br />
                        Contact Person: {!! $paymentSheet->supplier->contact_person_name !!} <br />
                        Contact Number: {!! $paymentSheet->supplier->contact_number !!} <br />
                        Account Name: {!! $paymentSheet->supplier->account_name !!} <br />
                        Account Number: {!! $paymentSheet->supplier->account_number !!} <br />
                        Bank Name: {!! $paymentSheet->supplier->bank_name !!} <br />
                    </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Supplier Details"></span>
            </li>

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $paymentSheet->remarks !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
        </ul>
    </div>
</div>
