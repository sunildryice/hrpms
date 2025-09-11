<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Permission</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('privilege.permissions.store') !!}" method="post"
      enctype="multipart/form-data" id="permissionForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Parent Permission</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="parent_id">
                    <option value="">Parent Itself</option>
                    @foreach($permissions as $permission)
                        <option value="{{ $permission->id }}">{{ $permission->permission_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Permission Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="permission_name" value="" placeholder="Permission Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Guard Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="guard_name" value="" placeholder="Guard Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Active ? </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                           name="active" checked >
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
