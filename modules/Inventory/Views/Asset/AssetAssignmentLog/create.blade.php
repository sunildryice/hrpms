<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Asset Assignment Log</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('asset.assignment.logs.store') !!}" method="post"
      enctype="multipart/form-data" id="assetAssignmentLogForm" autocomplete="off">
      @csrf
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="assign_id" class="m-0">Asset Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" id="assign_asset_id" name="assign_asset_id" value="{{ $asset->id }}" hidden>
                <input class="form-control" type="text" id="assign_id" name="assign_id" value="{{ $asset->getAssetNumber() }}" readonly>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="assigned_user_id" class="form-label required-label">Assigned To</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="assigned_user_id" id="assigned_user_id">
                    <option value="">Select Assigned Employee</option>
                    @foreach ($employees as $employee)
                        @if ($employee->user)
                            <option value="{{ $employee->user->id }}">{{ $employee->getFullName() }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="assigned_on" class="form-label required-label">Assigned On</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input class="form-control" type="text" id="assigned_on" name="assigned_on" value="{{now()->format('Y-m-d')}}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="status" class="form-label required-label">Asset Status</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="status" id="status">
                    <option value="">Select Asset Status</option>
                    @foreach ($assetStatuses as $assetStatus)
                        @if ($employee->user)
                            <option value="{{ $assetStatus->id }}">{{ $assetStatus->getStatus() }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
