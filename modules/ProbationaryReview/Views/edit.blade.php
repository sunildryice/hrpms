<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Probation Review</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('probation.review.requests.update', $probationaryReview->id) !!}" method="post"
      enctype="multipart/form-data" id="probationReviewEditForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.employee') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="employee_id" class="select2 form-control" data-width="100%">
                    <option value="{!! $probationaryReview->employee_id !!}">{!! $probationaryReview->getEmployeeName() !!}</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.review-type') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="review_id" class="select2 form-control" data-width="100%">
                    <option value="">Select a Review Type</option>
                    @foreach($probationaryReviewTypes as $probationaryReviewType)
                        <option value="{{ $probationaryReviewType->id }}" @if($probationaryReview->review_id == $probationaryReviewType->id) selected @endif>
                            {{ $probationaryReviewType->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.review-date') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly
                    name="date" value="{{date('Y-m-d',strtotime($probationaryReview->date))}}"/>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.send-to') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="reviewer_id" class="select2 form-control" data-width="100%">
                    <option value="">Select a Reviewer</option>
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" @if($supervisor->id == $probationaryReview->reviewer_id) selected @endif>
                            {{ $supervisor->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.approver') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="approver_id" class="select2 form-control" data-width="100%">
                    <option value="">Select an Approver</option>
                    @foreach($approvers as $approver)
                        <option value="{{ $approver->id }}" @if($approver->id == $probationaryReview->approver_id) selected @endif>
                            {{ $approver->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationRemarks" class="form-label required-label">Remarks </label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea type="text"
                    class="form-control"
                    name="remarks">{{$probationaryReview->remarks}}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm next">Save</button>
        <button type="submit" name="btn" value="submit" class="btn btn-primary btn-sm">Submit</button>
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}

</form>
