<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Permission</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('privilege.permissions.update', $permission->id) !!}" method="post"
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
                    <option value="">Select Parent</option>
                    @foreach($permissions as $dropdown)
                        <option value="{{ $dropdown->id }}" @if($dropdown->id == $permission->parent_id) selected @endif>
                            {{ $dropdown->permission_name }}</option>
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
                <input type="text" class="form-control" name="permission_name"
                    value="{{ $permission->permission_name }}" placeholder="Permission Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Guard Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="guard_name" value="{{ $permission->guard_name }}"
                    placeholder="Guard Name">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Active ? </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class=" form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                        name="active" @if($permission->activated_at) checked @endif>
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>