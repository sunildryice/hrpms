<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Benefit/Deduction</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('payroll.batches.sheets.details.store', [$payrollSheet->payroll_batch_id, $payrollSheet->id]) !!}"
      method="post" enctype="multipart/form-data" id="benefitForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Earning/Deduction</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="payment_item_id">
                    <option value="">Select Payment Item</option>
                    @foreach($paymentItems as $paymentItem)
                        <option value="{{ $paymentItem->id }}">{{ $paymentItem->getPaymentItemWithType() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="amount" value="" placeholder="Amount">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
