<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add New PackageItem</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.packages.items.store', $package->id) !!}" method="post" enctype="multipart/form-data" id="packageItemForm"
    autocomplete="off">
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
                        <option value="{!! $item->id !!}">{{ $item->getItemName() }}</option>
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
                <textarea rows="5" class="form-control" name="specification"></textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Quantity </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="quantity" value="" placeholder="Quantity">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit Price </label>
                </div>
            </div>
            <div class="col-lg-6">
                <input type="number" class="form-control" name="unit_price" value="" placeholder="Unit Price">
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control total_price" value="0" readonly>
            </div>
        </div>



    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
