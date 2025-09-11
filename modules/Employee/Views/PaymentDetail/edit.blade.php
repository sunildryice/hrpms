<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Payment Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('employees.payments.masters.details.update',[$paymentDetail->payment_master_id, $paymentDetail->id]) !!}" method="post"
      enctype="multipart/form-data" id="paymentDetailForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Payment Item</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" disabled>
                    <option value="">Select Option</option>
                    @foreach($paymentItems as $paymentItem)
                        <option value="{!! $paymentItem->id !!}" @if($paymentItem->id == $paymentDetail->payment_item_id) selected="selected" @endif>{!! $paymentItem->getPaymentItemWithType() !!}</option>
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
                <input type="number" class="form-control" name="amount" value="{{ $paymentDetail->amount }}" placeholder="Amount">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
