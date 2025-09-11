<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit GRN Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form
    action="{!! route('grns.items.from.order.update', [$grnItem->grn_id, $grnItem->id]) !!}"
    method="post"
    enctype="multipart/form-data" id="grnItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $grnItem->getItemName() }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $grnItem->getUnitName() }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Quantity Requested</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" disabled value="{{ $grnItem->grnitemable->quantity }}"
                       placeholder="{{ __('label.quantity-ordered') }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.quantity-received') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" value="{{ $grnItem->quantity }}"
                       placeholder="{{ __('label.quantity-received') }}">
            </div>
        </div>
         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit Price </label>
                </div>
            </div>
            <div class="col-lg-6">
                <input type="number" class="form-control" name="unit_price" value="{{$grnItem->unit_price}}" placeholder="Unit Price">
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control total_price" value="{{$grnItem->total_price}}" readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Discount (If Any)</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="discount_amount" value="{{ $grnItem->discount_amount }}"
                       placeholder="Discount (If Any)">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">VAT Applicable</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                       name="vat_applicable" @if($grnItem->vat_amount != 0) checked @endif>
                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" value="order" name="btn" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
