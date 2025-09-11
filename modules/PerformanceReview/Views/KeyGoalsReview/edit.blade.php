<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Key Goal</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('performance.keygoal.updateOne', $keyGoal->id) !!}" method="post" enctype="multipart/form-data" id="keyGoalEditForm" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationTitle" class="form-label required-label">Title</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <textarea class="form-control" style="width:100%" name="title" id="key_goal_title_edit" rows="2"
                                            placeholder="Key goal">{{$keyGoal->title}}</textarea>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="btn" value="save">Save</button>
    </div>
</form>
