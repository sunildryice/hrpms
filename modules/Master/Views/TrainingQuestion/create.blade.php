<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Training Question</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.training.questions.store') !!}" method="post"
      enctype="multipart/form-data" id="trainingQuestionForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Question </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="question" value="" placeholder="Training Question">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Entry By</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="type">
                    <option value="1">Requester</option>
                    <option value="3">Approver</option>
                    <option value="6">On Report</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Answer Type</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="select2 form-control" name="answer_type">
                    <option value="textarea">Textarea</option>
                    <option value="boolean">Boolean</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
