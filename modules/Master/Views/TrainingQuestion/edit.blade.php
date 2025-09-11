<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Training Question</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.training.questions.update',$trainingQuestion->id) !!}" method="post"
      enctype="multipart/form-data" id="trainingQuestionForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Question </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="question" value="{!! $trainingQuestion->question !!}" placeholder="Training Question" />
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
                    <option value="3" @if($trainingQuestion->type == 3) selected @endif>Approver</option>
                    <option value="6" @if($trainingQuestion->type == 6) selected @endif>On Report</option>
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
                    <option value="boolean" @if($trainingQuestion->answer_type == 'boolean') selected @endif>Boolean</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
