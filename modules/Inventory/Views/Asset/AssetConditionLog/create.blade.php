<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Asset Condition Log</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('asset.condition.logs.store') !!}" method="post"
      enctype="multipart/form-data" id="assetConditionLogForm" autocomplete="off">
      @csrf
    <div class="modal-body">

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="asset_id" class="m-0">Asset Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" id="asset_id" name="asset_id" value="{{ $asset->id }}" hidden>
                <input class="form-control" type="text" id="assign_id" name="assign_id" value="{{ $asset->getAssetNumber() }}" readonly>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="condition_id" class="form-label required-label">Condition</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="condition_id" id="condition_id">
                    <option value="">Select Asset Condition</option>
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition->id }}">{{ $condition->getTitle() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="description" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="description" id="description" rows="2" placeholder="Asset Condition Description"></textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
