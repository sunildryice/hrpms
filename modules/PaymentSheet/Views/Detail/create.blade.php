<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add New Payment Sheet Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('payment.sheets.details.store', $paymentSheet->id) !!}" method="post"
      enctype="multipart/form-data" id="paymentSheetDetailForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Bill No</label>
                </div>
            </div>
            <div class="col-lg-3">
                <select class="form-control select2" data-width="100%" name="payment_bill_id">
                    <option value="">Select Bill No</option>
                    @foreach($paymentBills as $paymentBill)
                        <option value="{!! $paymentBill->id !!}" data-bill-amount="{{ $paymentBill->bill_amount }}"
                        data-vat-applicable="{{ $paymentBill->bill_amount != $paymentBill->total_amount }}">{{ $paymentBill->getBillNumber() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Bill Amount</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="bill_amount" readonly placeholder="Bill Amount">
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remaining Amount</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control" value="0" name="left_amount" readonly placeholder="Remaining Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Activity Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="activity_code_id">
                    <option value="">Select Activity Code</option>
                    @foreach($activityCodes as $activityCode)
                        <option value="{!! $activityCode->id !!}">{{ $activityCode->getActivityCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Account Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="account_code_id">
                    <option value="">Select Account Code</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Donor Code</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="donor_code_id">
                    <option value="">Select Donor Code</option>
                    @foreach($donorCodes as $donorCode)
                        <option value="{!! $donorCode->id !!}">{{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Processed by (Office)</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="processed_by_office_id">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{!! $office->id !!}">{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}
        <input type="hidden" name="processed_by_office_id" value="{{auth()->user()->getCurrentOffice()?->id}}">


        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Charged to (Office)</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="charged_office_id">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{!! $office->id !!}">{{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Percentage</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="percentage" step="1" placeholder="Percentage">
                <input type="number" class="form-control" name="left_percentage" value="" hidden="hidden">
            </div>
        </div> --}}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="total_amount" placeholder="Amount">
            </div>
        </div>
        <div class="row mb-2 vatBlock">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">VAT Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="vat_amount" readonly placeholder="VAT Amount">
            </div>
        </div>
        <div class="row mb-2 vatBlock">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Amount with VAT</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="amount_with_vat" readonly placeholder="Amount with VAT">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="tds_percentage" class="m-0">TDS Percentage</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="tds_percentage" id="tds_percentage">
                    @foreach ($tdsPercentages as $tdsPercentage)
                        <option value="{{$tdsPercentage}}">{{$tdsPercentage}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">TDS Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="tds_amount" readonly placeholder="TDS Amount">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Net Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="net_amount" readonly placeholder="Net Amount">
            </div>
        </div>


        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="description" value="" placeholder="Description">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
