<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Asset</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('inventories.assets.update', $asset->id) }}" method="POST"
      enctype="multipart/form-data" id="assetEditForm" autocomplete="off">
      @csrf
      @method('PUT')

    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="serial_number" class="m-0">Serial Number</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="serial_number" id="serial_number" value="{{ $asset->serial_number }}" placeholder="Serial Number">
            </div>
        </div>

        @can('manage-asset-logistic')
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="room_number" class="m-0 required-label">Room Number</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="room_number" id="room_number" value="{{ $asset->room_number }}" >
                </div>
            </div>
        @endcan

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="remarks" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="remarks" id="remarks" rows="2" placeholder="Remarks">{{ $asset->remarks }}</textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
