 <div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Transaction</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('construction.installment.store', $construction->id) !!}" method="post"
      enctype="multipart/form-data" id="constructionInstallForm" autocomplete="off">
    <div class="modal-body">

          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="advance_release_date" class="form-label required-label">Transaction Date</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="advance_release_date" value="" placeholder="Transaction Date">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="transaction_type_id" class="form-label required-label">Transaction Type</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="transaction_type_id" id="transaction_type_id">
                    <option value="">Select transaction type</option>
                    @foreach ($transactionTypes as $transactionType)
                        <option value="{{ $transactionType->id }}">{{ $transactionType->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="amount" value="" placeholder="Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
              <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
            </div>
        </div>


    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
