<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Good Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form
    action="{!! route('good.requests.items.update', [$goodRequestItem->good_request_id, $goodRequestItem->id]) !!}"
    method="post" enctype="multipart/form-data" id="goodRequestItemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" data-width="100%" name="item_name"
                value="{!! $goodRequestItem->item_name !!}"/>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="unit_id">
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" @if($unit->id == $goodRequestItem->unit_id) selected @endif>{{ $unit->getUnitName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Specification</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="specification" placeholder="Specification">{!! $goodRequestItem->specification !!}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Quantity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" data-width="100%" name="quantity" value="{{ $goodRequestItem->quantity }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
