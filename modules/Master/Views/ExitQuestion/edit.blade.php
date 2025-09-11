<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Exit Question</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.exit.questions.update',$exitQuestion->id) !!}" method="post"
      enctype="multipart/form-data" id="exitQuestionForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Exit Question</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="question" value="{!! $exitQuestion->question !!}"
                       placeholder="Exit Question"/>
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
                    <option value="boolean" @if($exitQuestion->answer_type == 'boolean') selected @endif>Boolean
                    </option>
                    <option value="selectBox" @if($exitQuestion->answer_type == 'selectBox') selected @endif>SelectBox
                    </option>
                </select>
            </div>
        </div>
        @php $options = collect(json_decode($exitQuestion->options)); @endphp
        <div class="container1">
            @forelse($options as $index=>$option)
                <div class="row mb-2">
                    @if($loop->iteration == 1)
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start h-100">
                            <label for="" class="m-0">Options</label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <input class="form-control" name="options[]" value="{{ $option }}" multiple="multiple"/>
                    </div>
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-success add_form_field">+</button>
                    </div>
                        @else
                        <div class="col-lg-3"></div>
                        <div class="col-lg-6">
                            <input class="form-control" name="options[]" value="{{ $option }}" multiple="multiple"/>
                        </div>
                        <div class="col-lg-3">
                            <button type="button" class="btn btn-warning delete">-</button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start h-100">
                            <label for="" class="m-0">Options</label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <input class="form-control" name="options[]" multiple="multiple"/>
                    </div>
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-success add_form_field">+</button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
