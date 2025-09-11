<div class="text-white modal-header bg-primary">

    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Add Transaction</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('mfr.transaction.store', $agreement->id) !!}" method="post" enctype="multipart/form-data" id="transactionForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdistrict" class="form-label required-label">Type
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="transaction_type" class="select2 form-control" data-width="100%">
                    <option value="">Select a Type</option>
                    <option value="1" {{ '1' == old('transaction_type') ? 'selected' : '' }}>
                        Fund Release
                    </option>
                    <option value="2" {{ '2' == old('transaction_type') ? 'selected' : '' }}>
                        Fund Release/MFR Approval
                    </option>
                </select>
                @if ($errors->has('transaction_type'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="transaction_type">
                            {!! $errors->first('transaction_type') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationpd" class="form-label required-label">Transaction Date
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly name="transaction_date"
                    value="{{ old('transaction_date') }}" />
                @if ($errors->has('transaction_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="transaction_date">{!! $errors->first('transaction_date') !!}
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
