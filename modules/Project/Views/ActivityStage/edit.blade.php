<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Activity Stage</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('activity-stage.update', $activityStage->id) !!}" method="post" enctype="multipart/form-data" id="activityStageEditForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Title</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title"
                    value="{{ old('title', $activityStage->title) }}" placeholder="Title">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="description" rows="4" placeholder="Description">{{ old('description', $activityStage->description) }}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Activate ? </label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                        name="activated" @if ($activityStage->activated_at) checked @endif>
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
</form>
