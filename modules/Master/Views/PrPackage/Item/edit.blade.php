<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Package Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.packages.items.update', [$packageItem->package_id, $packageItem->id]) !!}" method="post" enctype="multipart/form-data" id="packageItemForm"
    autocomplete="off">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="item_id">
                    <option value="">Select Item</option>
                    @foreach ($items as $item)
                        <option value="{!! $item->id !!}" @if ($packageItem->item_id == $item->id) selected @endif>
                            {{ $item->getItemName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="unit_id">
                    <option value="">Select Unit</option>
                    @foreach ($units as $unit)
                        <option value="{!! $unit->id !!}" @if ($packageItem->unit_id == $unit->id) selected @endif>
                            {{ $unit->getUnitName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Specification </label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="specification">{{ $packageItem->specification }}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Quantity </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" placeholder="Quantity"
                    value="{{ $packageItem->quantity }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit Price </label>
                </div>
            </div>
            <div class="col-lg-6">
                <input type="number" class="form-control" name="unit_price" value="{{ $packageItem->unit_price }}"
                    placeholder="Unit Price">
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control total_price" value="{{ $packageItem->total_price }}" readonly>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
