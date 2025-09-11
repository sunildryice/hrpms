 <div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Construction Progress</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('construction.progress.update', [$construction->id,$constructionProgress->id]) !!}" method="post"
      enctype="multipart/form-data" id="constructionAddForm" autocomplete="off">
    <div class="modal-body">
         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Date of Report</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('report_date')) is-invalid @endif" name="report_date" value="{{$constructionProgress->report_date->format('Y-m-d')}}" placeholder="Report date">
               @if($errors->has('report_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="report_date">
                            {!! $errors->first('report_date') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="work_start_date" class="form-label required-label">Work Start Date</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('work_start_date')) is-invalid @endif" name="work_start_date" value="{{$constructionProgress->work_start_date}}" placeholder="Work start date">
                @if($errors->has('work_start_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="work_start_date">
                            {!! $errors->first('work_start_date') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="work_completion_date" class="form-label required-label">Work Completion Date</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('work_completion_date')) is-invalid @endif" name="work_completion_date" value="{{$constructionProgress->work_completion_date}}" placeholder="Work completion date">
                @if($errors->has('work_completion_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="work_completion_date">
                            {!! $errors->first('work_completion_date') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div> --}}
         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Work Progress</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('progress_percentage')) is-invalid @endif" name="progress_percentage" value="{{$constructionProgress->progress_percentage}}" placeholder="Progress percentage">
               @if($errors->has('progress_percentage'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="progress_percentage">
                            {!! $errors->first('progress_percentage') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Estimate</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('estimate')) is-invalid @endif" name="estimate" value="{{$constructionProgress->estimate}}" placeholder="Estimate">
               @if($errors->has('estimate'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="estimate">
                            {!! $errors->first('estimate') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>


        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control @if($errors->has('remarks')) is-invalid @endif" name="remarks" placeholder="Remarks">{{$constructionProgress->remarks}}</textarea>
                @if($errors->has('remarks'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="remarks">
                            {!! $errors->first('remarks') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>


    </div>
    <div class="modal-footer">
        <button type="submit" name="btn" value="update" class="btn btn-primary">Update</button>
        {{-- <a href="{!! route('construction.index') !!}" class="btn btn-danger btn-sm">Cancel</a> --}}
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
