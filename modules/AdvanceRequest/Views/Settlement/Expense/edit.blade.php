<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Edit Expense for Advance Settlement</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('advance.settlement.expense.update', [$advanceSettlement->id, $settlementExpense->id]) !!}" method="post" enctype="multipart/form-data" id="settlementExpenseForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="narration"
                    value="{{ $settlementExpense->narration }}" />
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="form-label required-label">District</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="district_id" class="select2 form-control" data-width="100%">
                    <option value="">Select a District</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district->id }}" @if ($settlementExpense->district_id == $district->id) selected @endif>
                            {{ $district->getDistrictName() }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('district_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="district_id">
                            {!! $errors->first('district_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="m-0">Location</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text"
                    class="form-control
                    @if ($errors->has('location')) is-invalid @endif"
                    name="location" value="{{ $settlementExpense->location }}" />
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Activity Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach ($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}" @if ($settlementExpense->activity_code_id == $activityCode->id) selected @endif>
                            {{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Account Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="account_code_id">
                    <option value="">Select Account Code</option>
                    @foreach ($accountCodes as $accountCode)
                        <option value="{!! $accountCode->id !!}" @if ($settlementExpense->account_code_id == $accountCode->id) selected @endif>
                            {{ $accountCode->getAccountCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Donor Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id">
                    <option value="">Select Donor Code</option>
                    @foreach ($donorCodes as $donorCode)
                        <option value="{!! $donorCode->id !!}" @if ($settlementExpense->donor_code_id == $donorCode->id) selected @endif>
                            {{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
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
