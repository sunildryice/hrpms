<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Assign Asset {{ $asset->getAssetNumber() }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('good.requests.direct.assign.store', $asset->id) }}" method="post" enctype="multipart/form-data"
    id="assetassignform" autocomplete="off">
    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="employee_id" class="form-label required-label">Employee</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="employee_id" id="employee_id" class="form-control select2">
                    <option value="">Select Employee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="room_number" class="form-label required-label">Date of Handover</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly="readonly" name="handover_date" value="" />
            </div>
        </div>

    


    </div>
    <div class="modal-footer">
        <button type="submit" name="submit_action" value="save" class="btn btn-outline-primary">Save</button>

        <button type="submit" name="submit_action" value="assign" class="btn btn-primary">Assign</button>
    </div>
    {!! csrf_field() !!}
</form>
