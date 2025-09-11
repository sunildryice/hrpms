<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Exit Question</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.exit.questions.store') !!}" method="post"
      enctype="multipart/form-data" id="exitQuestionForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Exit Question</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="question" value="" placeholder="Exit Question">
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
                    <option value="selectBox">SelectBox</option>
                </select>
            </div>
        </div>

        <div class="container1">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="m-0">Options</label>
                    </div>
                </div>
                <div class="col-lg-6">
                    <input class="form-control" name="options[]" multiple="multiple" />
                </div>
                <div class="col-lg-3">
                    <button type="button" class="btn btn-success add_form_field" id="addButton">+</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
