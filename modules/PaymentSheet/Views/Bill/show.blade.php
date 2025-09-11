<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Show Bill Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.supplier') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control" value="{{ $paymentBill->getSupplierName() }}" readonly>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.bill-category') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control" name="office_code" value="{{ $paymentBill->getCategoryName() }}"
                   readonly>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.bill-date') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control" name="phone_number" value="{{ $paymentBill->getBillDate() }}"
                   readonly>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.bill-number') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control" name="fax_number" value="{{ $paymentBill->getBillNumber() }}"
                   readonly>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.bill-amount') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="tel" class="form-control" name="email_address" value="{{ $paymentBill->bill_amount }}"
                   readonly>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.vat-amount') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control" name="account_number" value="{{ $paymentBill->vat_amount }}"
                   readonly>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.total-amount') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" class="form-control" name="bank_name" value="{{ $paymentBill->total_amount }}" readonly>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label for="" class="m-0">{{ __('label.remarks') }}</label>
            </div>
        </div>
        <div class="col-lg-9">
            <textarea type="text" class="form-control" readonly>{{ $paymentBill->remarks }}</textarea>
        </div>
    </div>
</div>
