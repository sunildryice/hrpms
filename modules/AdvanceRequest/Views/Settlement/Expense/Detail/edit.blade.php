<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Expense Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('advance.settlement.expense.details.update', [$expenseDetail->settlement_expense_id, $expenseDetail->id]) !!}" method="post"
      enctype="multipart/form-data" id="expenseDetailForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly value="{{ $expenseDetail->settlementExpense->narration }}"/>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Expense Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" value="{{ $expenseDetail->expense_date ? $expenseDetail->expense_date->format('Y-m-d') : '' }}"
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
                        <option value="{!! $expenseCategory->id !!}" @if($expenseCategory->id == $expenseDetail->expense_category_id) selected @endif>{{ $expenseCategory->title }}</option>
                    @endforeach
                </select> --}}

                <input class="form-control @if($errors->has('description')) is-invalid @endif" type="text" name="description" id="description" value="{{$expenseDetail->getDescription()}}">
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
                        <option value="{!! $expenseType->id !!}" @if($expenseType->id == $expenseDetail->expense_type_id) selected @endif>{{ $expenseType->title }}</option>
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
                <input type="text" class="form-control" name="bill_number"
                       value="{{ $expenseDetail->bill_number }}" placeholder="Bill Invoice No">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Gross amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="gross_amount"
                       value="{{ $expenseDetail->gross_amount }}" placeholder="Gross Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Less:Tax</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="tax_amount"
                       value="{{ $expenseDetail->tax_amount }}" placeholder="Less Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Net Amount Paid</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" readonly class="form-control" name="net_amount"
                       value="{{ $expenseDetail->net_amount }}" placeholder="Net Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Attachment</label>
                </div>
            </div>
            @php
                $col_class = $expenseDetail->attachment != NULL? 'col-lg-7': 'col-lg-9';
            @endphp
            <div class="{{$col_class}}">
                <input type="file" class="form-control @if($errors->has('attachment')) is-invalid @endif" id="validationdocument"
                    placeholder="" name="attachment">
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>

                @if($errors->has('attachment'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                    </div>
                @endif
            </div>
            @if(file_exists('storage/'.$expenseDetail->attachment) && $expenseDetail->attachment != '')
            <div class="col-lg-2">
                <div class="media">
                    <a href="{!! asset('storage/'.$expenseDetail->attachment) !!}" target="_blank" class="fs-5"
                    title="View Attachment"><i class="bi bi-file-earmark-medical"></i></a>
                    <a href = "javascript:;" data-href="{{route('advance.settlement.expense.details.attachment.delete', [$expenseDetail->id])}}"
                    id="delete-attachment" class="fs-5" title="Delete Attachment"><i class="bi-trash text-danger"></i></a>
                </div>
            </div>
            @endif
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
