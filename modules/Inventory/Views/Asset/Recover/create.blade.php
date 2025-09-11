<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Recover Asset: {{$asset->getAssetNumber()}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('assets.recover.store', $asset->id) }}" method="POST" enctype="multipart/form-data"
    id="assetEditForm" autocomplete="off">
    @csrf

    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="assigned_office_id">
                    <option value="">Select New Office</option>
                    @foreach ($offices as $office)
                        <option value="{!! $office->id !!}" @if($office->id == $asset->assigned_office_id) selected @endif>{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="room_number" class="m-0">Room Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="room_number" id="room_number"
                    value="{{ $asset->room_number }}">
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Recover</button>
    </div>
</form>
