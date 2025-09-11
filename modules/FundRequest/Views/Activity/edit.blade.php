<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Fund Request Activity</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('fund.requests.activities.update', [$fundRequestActivity->fund_request_id, $fundRequestActivity->id]) !!}" method="post"
      enctype="multipart/form-data" id="fundRequestActivityForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{!! __('label.activity-code') !!}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if($fundRequestActivity->activity_code_id == $activityCode->id) selected @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{!! __('label.estimated-fund') !!}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="estimated_amount" value="{{ $fundRequestActivity->estimated_amount }}" placeholder="Estimated Amount">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Budget Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="budget_amount" value="{{ $fundRequestActivity->budget_amount }}" placeholder="Budget Amount">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Project Target Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="project_target_unit" value="{{ $fundRequestActivity->project_target_unit }}" placeholder="Project Target Unit">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">DIP Target Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="dip_target_unit" value="{{ $fundRequestActivity->dip_target_unit }}" placeholder="DIP Target Unit">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remarks/Variance Note</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea type="text"
                    class="form-control"
                    name="justification_note">{{$fundRequestActivity->justification_note}}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
