<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add New Construction Progress</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('construction.progress.store', $construction->id) !!}" method="post" id="constructionAddForm" autocomplete="off" enctype="multipart/form-data">
    <div class="modal-body">
         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Date of Report</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('report_date')) is-invalid @endif" name="report_date" value="" placeholder="Report date">
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
               <input type="text" class="form-control @if($errors->has('work_start_date')) is-invalid @endif" name="work_start_date" value="" placeholder="Work start date">
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
               <input type="text" class="form-control @if($errors->has('work_completion_date')) is-invalid @endif" name="work_completion_date" value="" placeholder="Work completion date">
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
                    <label for="progress_percentage" class="form-label required-label">Work Progress</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('progress_percentage')) is-invalid @endif" name="progress_percentage" value="" placeholder="Progress percentage">
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
                    <label for="estimate" class="m-0">Estimate</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control @if($errors->has('estimate')) is-invalid @endif" name="estimate" value="" placeholder="Estimate">
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
                    <label for="remarks" class="form-label required-label">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control @if($errors->has('remarks')) is-invalid @endif" name="remarks" placeholder="Remarks"></textarea>
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
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
