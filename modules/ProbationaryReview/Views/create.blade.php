<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Probation Review</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('probation.review.requests.store') !!}" method="post"
      enctype="multipart/form-data" id="probationReviewAddForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.employee') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="employee_id" class="select2 form-control" data-width="100%">
                    <option value="">Select a Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
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
                        <option value="{{ $probationaryReviewType->id }}">
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
                    name="date" value=""/>
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
                    name="remarks"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
    </div>
    {!! csrf_field() !!}
</form>

<script>
    $(".select2").select2({
        dropdownParent: $('.modal'),
        width: '100%',
        dropdownAutoWidth: true
    });
</script>
