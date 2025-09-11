<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add New Expense Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('advance.settlement.expense.details.store', $settlementExpense->id) !!}" method="post"
      enctype="multipart/form-data" id="expenseDetailForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly value="{{ $settlementExpense->narration }}"/>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Expense Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control"
                       name="expense_date" data-toggle="datepicker"/>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                {{-- <select class="form-control select2"
                        data-width="100%" name="expense_category_id">
                    <option value="">Select Expense Category</option>
                    @foreach($expenseCategories as $expenseCategory)
                        <option value="{!! $expenseCategory->id !!}">{{ $expenseCategory->title }}</option>
                    @endforeach
                </select> --}}

                <input class="form-control @if($errors->has('description')) is-invalid @endif" type="text" name="description" id="description">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Expense Type</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2"
                        data-width="100%" name="expense_type_id">
                    <option value="">Select Expense Type</option>
                    @foreach($expenseTypes as $expenseType)
                        <option value="{!! $expenseType->id !!}">{{ $expenseType->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Bill Invoice No</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="bill_number" value="" placeholder="Bill Invoice No">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Gross amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="gross_amount" value="" placeholder="Gross Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Less:Tax</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="tax_amount" value="" placeholder="Less Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Net Amount Paid</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" readonly class="form-control" name="net_amount" value="" placeholder="Net Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control @if($errors->has('attachment')) is-invalid @endif" id="validationdocument" value="{{ old('attachment') }}"
                    placeholder="" name="attachment">
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                @if($errors->has('attachment'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
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
