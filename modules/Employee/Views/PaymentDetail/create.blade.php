<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Payment Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('employees.payments.masters.details.store', $paymentMaster->id) !!}" method="post"
      enctype="multipart/form-data" id="paymentDetailForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Payment Item</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="payment_item_id">
                    <option value="">Select Option</option>
                    @foreach($paymentItems as $paymentItem)
                        <option value="{!! $paymentItem->id !!}">{!! $paymentItem->getPaymentItemWithType() !!}</option>
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
                <input type="number" class="form-control" name="amount" placeholder="Amount">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
