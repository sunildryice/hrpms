<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Leave Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.leave.types.update', $leaveType->id) !!}" method="post"
      enctype="multipart/form-data" id="leaveTypeForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Name of leave </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{!! $leaveType->title !!}"
                       placeholder="Name of Leave">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Short Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="short_description" value="{!! $leaveType->short_description !!}" placeholder="Description">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Paid/Unpaid</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="paid">
                    <option value="1">Paid</option>
                    <option value="0" @if($leaveType->paid == 0) selected @endif>UnPaid</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Types</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="mt-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <span class="filter-items d-flex gap-2 mb-2" data-id="value">
                                <span class="filter-checks">
                                    <input type="checkbox" name="applicable_to_all" class="f-check-input"
                                           @if($leaveType->applicable_to_all) checked @endif>
                                </span>
                                <span class="filter-body">
                                    Applicable to all staff
                                </span>
                            </span>
                        </div>
                        <div class="col-lg-4">
                            <span class="filter-items d-flex gap-2 mb-2" data-id="value">
                                <span class="filter-checks">
                                    <input type="checkbox" name="include_weekends" class="f-check-input"
                                           @if($leaveType->include_weekends) checked @endif>
                                </span>
                                <span class="filter-body">
                                    Includes Weekends
                                </span>
                            </span>
                        </div>
                        <div class="col-lg-4">
                            <span class="filter-items d-flex gap-2 mb-2" data-id="value">
                                <span class="filter-checks">
                                    <input type="checkbox" name="female" class="f-check-input"
                                           @if($leaveType->female) checked @endif>
                                </span>
                                <span class="filter-body">
                                    Female Only
                                </span>
                            </span>
                        </div>
                        <div class="col-lg-4">
                            <span class="filter-items d-flex gap-2 mb-2 align-items-center" data-id="value">
                                <span class="filter-checks">
                                    <input type="checkbox" name="male" class="f-check-input"
                                           @if($leaveType->male) checked @endif>
                                </span>
                                <span class="filter-body">
                                    Male Only
                                </span>
                            </span>
                        </div>
                        <div class="col-lg-4">
                            <span class="filter-items d-flex gap-2 mb-2 align-items-center" data-id="value">
                                <span class="filter-checks">
                                    <input type="checkbox" name="encashment" class="f-check-input"
                                           @if($leaveType->encashment) checked @endif>
                                </span>
                                <span class="filter-body">
                                    Encashment ?
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Balance (In Days)</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-8">
                        <input type="number" min="1" class="form-control" name="number_of_days" value="{{ $leaveType->number_of_days }}" placeholder="Balance"/>
                    </div>
                    <div class="col-lg-4">
                        <select class="form-control select2" data-width="100%" name="leave_frequency">
                            <option value="">Select Month/Year *</option>
                            <option value="1" @if($leaveType->leave_frequency == 1) selected @endif>Yearly</option>
                            <option value="2" @if($leaveType->leave_frequency == 2) selected @endif>Monthly</option>
                            <option value="3" @if($leaveType->leave_frequency == 3) selected @endif>Event Based</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Leave Basis</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4">
                        <select class="form-control" data-width="100%" name="leave_basis">
                            <option value="1" @if($leaveType->leave_basis == 1) selected @endif>Day</option>
                            <option value="2" @if($leaveType->leave_basis == 2) selected @endif>Hour</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Maximum carry over (Per Year in Days)</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" min="0" class="form-control" name="maximum_carry_over" value="{{ $leaveType->maximum_carry_over }}"
                       placeholder="Maximum carry over">

            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="m-0">Status</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input form-control" type="checkbox" role="switch"
                           id="flexSwitchCheckChecked" @if($leaveType->activated_at) checked @endif name="active">
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
