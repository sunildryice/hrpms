<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.items.update',$item->id) !!}" method="post"
      enctype="multipart/form-data" id="itemForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Category </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="inventory_category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @if($item->inventory_category_id == $category->id) selected @endif>{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{!! $item->title !!}" placeholder="Item Name" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="item_code" value="{!! $item->item_code !!}" placeholder="Item Code" />
            </div>
        </div>
        @php $selectedUnits = $item->units->pluck('id')->toArray(); @endphp
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Units</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="units[]" multiple="multiple">
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" @if(in_array($unit->id, $selectedUnits)) selected="selected" @endif>{{ $unit->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
<script>
    $(".select2").select2({
        dropdownParent: $('.modal'),
        width: '100%',
        dropdownAutoWidth: true
    });
</script>
