<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Project Code</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.project.codes.store') !!}" method="post"
      enctype="multipart/form-data" id="projectCodeAddForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Project Code </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="" placeholder="Project Code">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Short Name </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="short_name" value="" placeholder="Acronym">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="description" value="" placeholder="Description">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
