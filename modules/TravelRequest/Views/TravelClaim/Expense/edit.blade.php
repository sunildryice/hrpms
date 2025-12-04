<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Expense</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('travel.claims.expenses.update', [$travelExpense->travel_claim_id, $travelExpense->id]) !!}" method="post" enctype="multipart/form-data" id="travelExpenseForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach ($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if ($travelExpense->activity_code_id == $activityCode->id) selected @endif>
                            {{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Donor Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach ($donorCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if ($travelExpense->donor_code_id == $activityCode->id) selected @endif>{{ $activityCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Expense Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="expense_date" onfocus="this.blur()"
                    placeholder="yyyy-mm-dd"
                    value="{{ $travelExpense->expense_date ? $travelExpense->expense_date->format('Y-m-d') : '' }}" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="expense_description"
                    value="{{ $travelExpense->expense_description }}" placeholder="Description">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Expense Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="expense_amount"
                    value="{{ $travelExpense->expense_amount }}" placeholder="Expense Amount">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Invoice / Bill Number</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="invoice_bill_number"
                    value="{{ $travelExpense->invoice_bill_number }}" placeholder="Invoice / Bill Number">
            </div>
        </div>
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Charging Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="office_id">
                    <option value="">Select Charging Office</option>
                    @foreach ($offices as $office)
                        <option value="{!! $office->id !!}" @if ($travelExpense->office_id == $office->id) selected @endif>{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Attachment </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control" name="attachment">
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                @if (file_exists('storage/' . $travelExpense->attachment) && $travelExpense->attachment != '')
                    <div class="media">
                        <a href="{!! asset('storage/' . $travelExpense->attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
                            <i class="bi bi-file-earmark-medical"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
</form>
