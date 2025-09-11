<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Probationary Question</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.probationary.questions.update',$probationaryQuestion->id) !!}" method="post"
      enctype="multipart/form-data" id="probationaryQuestionForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Question </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="question" value="{!! $probationaryQuestion->question !!}" placeholder="Probationary Question" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
