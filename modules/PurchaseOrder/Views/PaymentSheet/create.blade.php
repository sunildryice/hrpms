<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Create New Payment Sheet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('approved.purchase.orders.payment.sheet.store', $purchaseOrder->id) }}" method="post"
    enctype="multipart/form-data" id="paymentSheetForm" autocomplete="off">
    <div class="modal-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="form-label required-label">{{ __('label.supplier') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" type="text" value="{{ $purchaseOrder->supplier->supplier_name }}"
                    disabled />
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="form-label">{{ __('label.vat-pan-no') }}</label>
                </div>
            </div>
            <div class="col-lg-9">

                <input class="form-control" name="vat_pan_number" value="{{ $purchaseOrder->supplier->vat_pan_number }}"
                    readonly />

            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label">Specification </label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="specification"></textarea>
            </div>
        </div>

        <input type="hidden" name="purchase_order_ids[]" value="{{ $purchaseOrder->id }}">
        {{-- <div class="mb-2 row"> --}}
        {{--     <div class="col-lg-3"> --}}
        {{--         <div class="d-flex align-items-start h-100"> --}}
        {{--             <label for="validationdd" class="form-label required-label">{{ __('label.purchase-order') }}</label> --}}
        {{--         </div> --}}
        {{--     </div> --}}
        {{--     <div class="col-lg-9"> --}}
        {{--         <select --}}
        {{--             class="select2 form-control purchase_order_id @if ($errors->has('purchase_order_ids')) is-invalid @endif" --}}
        {{--             name="purchase_order_ids[]" multiple="multiple"> --}}
        {{--             <option value="">Select Purchase Orders</option> --}}
        {{--             @foreach ($purchaseOrders as $key => $value) --}}
        {{--                 <option value="{{ $value->id }}" {{ $value->id == $purchaseOrder->id ? 'selected' : '' }}> --}}
        {{--                     {{ $value->getPurchaseOrderNumber() }} --}}
        {{--                 </option> --}}
        {{----}}
        {{--             @endforeach --}}
        {{--         </select> --}}
        {{--         @if ($errors->has('purchase_order_ids')) --}}
        {{--             <div class="fv-plugins-message-container invalid-feedback"> --}}
        {{--                 <div data-field="purchase_order_ids"> --}}
        {{--                     {!! $errors->first('purchase_order_ids') !!} --}}
        {{--                 </div> --}}
        {{--             </div> --}}
        {{--         @endif --}}
        {{--     </div> --}}
        {{-- </div> --}}

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="form-label">Bill</label>
                </div>
            </div>
            <div class="col-lg-6">
                <select name="payment_bill_id" class="select2 form-control" data-width="100%">
                    <option value="">Select a bill</option>
                    @foreach ($paymentBills as $bill)
                        <option value="{{ $bill->id }}">
                            {{ $bill->bill_number }}
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
                    <label for="tds_percentage" class="m-0">TDS Percentage</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control" name="tds_percentage" id="tds_percentage">
                    @foreach ($tdsPercentages as $tdsPercentage)
                        <option value="{{ $tdsPercentage }}">{{ $tdsPercentage }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label" for="purpose">Purpose</label>
                </div>
            </div>
            <div class="col-lg-6">

                <input class="form-control" type="text" name="purpose" id="purpose" value="{{ old('purpose') }}">

            </div>
        </div>


    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Create</button>
    </div>
    {!! csrf_field() !!}
</form>
