<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Inventory Category</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.inventory.categories.update',$inventoryCategory->id) !!}" method="post"
      enctype="multipart/form-data" id="inventoryCategoryForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Inventory Type </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="inventory_type_id">
                    <option value="">Select Inventory Type</option>
                    @foreach($inventoryTypes as $type)
                        <option value="{{ $type->id }}" @if($inventoryCategory->inventory_type_id == $type->id) selected="selected" @endif>{{ $type->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{!! __('label.inventory-category') !!} </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{{ $inventoryCategory->title }}" placeholder="{!! __('label.inventory-category') !!}">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">{!! __('label.description') !!}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="description" placeholder="{!! __('label.description') !!}">{{ $inventoryCategory->description }}</textarea>
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
