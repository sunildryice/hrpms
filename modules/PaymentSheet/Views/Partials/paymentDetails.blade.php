<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $paymentSheet->getPayerName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Payer"></span>
            </li>
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Payment Date</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $paymentSheet->getPaymentDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Payment Date"></span>
            </li>

            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Payment Remarks</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $paymentSheet->payment_remarks !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Remarks"></span>
            </li>
        </ul>
    </div>
</div>
