<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Payment Sheet Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('payment.sheets.details.update', [$paymentSheetDetail->payment_sheet_id, $paymentSheetDetail->id]) !!}" method="post"
      enctype="multipart/form-data" id="paymentSheetDetailForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Bill No</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input class="form-control" type="text" name="payment_bill_id_readonly" value="{{$paymentBill->getBillNumber()}}" readonly>
                <input class="form-control" type="text" name="payment_bill_id" value="{{$paymentBill->id}}" hidden>
            </div>
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Bill Amount</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="number" class="form-control" name="bill_amount" readonly
                       value="{{ $paymentBill->bill_amount }}" data-bill-amount="{{ $paymentBill->bill_amount }}"
                       data-vat-applicable="{{ $paymentBill->bill_amount != $paymentBill->total_amount }}" placeholder="Bill Amount">
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
                        <option value="{!! $activityCode->id !!}"
                                @if($activityCode->id == $paymentSheetDetail->activity_code_id) selected @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>
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
                    @foreach($accountCodes as $accountCode)
                        <option value="{!! $accountCode->id !!}"
                                @if($accountCode->id == $paymentSheetDetail->account_code_id) selected @endif>{{ $accountCode->getAccountCodeWithDescription() }}</option>
                    @endforeach
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
                        <option value="{!! $donorCode->id !!}"
                                @if($donorCode->id == $paymentSheetDetail->donor_code_id) selected @endif>{{ $donorCode->getDonorCodeWithDescription() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

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
                        <option value="{!! $office->id !!}"
                                @if($office->id == $paymentSheetDetail->charged_office_id) selected @endif>{{ $office->getOfficeName() }}</option>
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
                <input type="number" class="form-control" name="percentage" step="1"
                       value="{{ $paymentSheetDetail->percentage }}" placeholder="Percentage">
                <input type="number" class="form-control" name="left_percentage" value='{{$leftPercentage}}' hidden>
            </div>
        </div> --}}



        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="total_amount"
                       value="{{ $paymentSheetDetail->total_amount }}" placeholder="Amount">

                <input type="number" class="form-control" name="left_amount" value='{{$leftAmount}}' hidden>
            </div>
        </div>
        <?php $vatFlag = $paymentSheetDetail->paymentBill->vat_flag ?>
        <?php $tdsFlag = (config('constant.TDS_PERCENTAGE') == $paymentSheetDetail->tds_percentage) ?>

        <div class="row mb-2 vatBlock" style="display: @if($vatFlag) flex; @else none; @endif">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">VAT Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="vat_amount" readonly
                       value="{{ $paymentSheetDetail->vat_amount }}" placeholder="VAT Amount">
            </div>
        </div>
        <div class="row mb-2 vatBlock" style="display: @if($vatFlag) flex; @else none; @endif">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Amount with VAT</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="amount_with_vat" readonly
                       value="{{ $paymentSheetDetail->amount_with_vat }}" placeholder="Amount with VAT">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="tds_percentage" class="m-0">TDS Percentage</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="tds_percentage" id="tds_percentage" @if($vatFlag) readonly="readonly" @endif>
                    @foreach ($tdsPercentages as $tdsPercentage)
                        <option value="{{$tdsPercentage}}" {{ floatval($paymentSheetDetail->tds_percentage) == floatval($tdsPercentage) ? 'selected' : ''}}>{{$tdsPercentage}}</option>
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
                <input type="number" class="form-control" name="tds_amount" readonly
                       value="{{ $paymentSheetDetail->tds_amount }}" placeholder="TDS Amount">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Net Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="net_amount" readonly
                       value="{{ $paymentSheetDetail->net_amount }}" placeholder="Net Amount">
            </div>
        </div>


        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="description"
                       value="{{ $paymentSheetDetail->description }}" placeholder="Description">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
