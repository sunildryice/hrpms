<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Edit Inventory Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('inventories.update', $inventory->id) }}" method="POST" enctype="multipart/form-data"
    id="assetEditForm" autocomplete="off">
    @csrf
    @method('PUT')

    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="specification" class="m-0">Specification</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="specification" value="{{ $inventory->specification }}">
            </div>
        </div>
        @can('manage-inventory-finance')
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="voucher_number" class="m-0">Voucher Number</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="voucher_number" id="voucher_number"
                        value="{{ $inventory->voucher_number }}">
                </div>
            </div>
        @endcan

        @if ($canEditPrice)
            <div class="mb-2 row">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="voucher_number" class="m-0">Unit Price</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="unit_price" id="unit_price"
                        value="{{ $inventory->unit_price }}">
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="vat_applicable" id="vat_applicable"
                            @if ($inventory->vat_amount > 0) checked @endif>
                        <label class="form-check-label" for="vat_applicable">
                            Vat Applicable
                        </label>
                    </div>
                </div>
            </div>
        @endif

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
