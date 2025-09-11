<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Purchase Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form
    action="{!! route('purchase.orders.items.update', [$purchaseOrderItem->purchase_order_id, $purchaseOrderItem->id]) !!}"
    method="post"
    enctype="multipart/form-data" id="purchaseOrderItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Item</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $purchaseOrderItem->getItemName() }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $purchaseOrderItem->getUnitName() }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Specification </label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control"
                          name="specification">{{ $purchaseOrderItem->specification }}</textarea>
            </div>
        </div>

        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Delivery Date </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="delivery_date" placeholder="Delivery Date" readonly
                       value="{{ $purchaseOrderItem->delivery_date ? $purchaseOrderItem->delivery_date->format('Y-m-d') : '' }}">
            </div>
        </div> --}}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Quantity </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" value="{{ $purchaseOrderItem->quantity }}"
                       placeholder="Quantity">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit Price </label>
                </div>
            </div>
            <div class="col-lg-6">
                <input type="number" class="form-control" name="unit_price" value="{{ $purchaseOrderItem->unit_price }}"
                       placeholder="Unit Price">
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control total_price" value="{{ $purchaseOrderItem->total_price }}"
                       readonly>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Activity Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $purchaseOrderItem->getActivityCode() }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Account Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $purchaseOrderItem->getAccountCode() }}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Donor Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" disabled value="{{ $purchaseOrderItem->getDonorCode() }}">
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
                       name="vat_applicable" @if($purchaseOrderItem->vat_amount != 0) checked @endif>
                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
