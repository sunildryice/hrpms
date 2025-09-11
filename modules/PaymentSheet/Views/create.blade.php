@extends('layouts.container')

@section('title', 'Add New Payment Sheet')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#payment-sheets-menu').addClass('active');
            const form = document.getElementById('paymentSheetAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    supplier_id: {
                        validators: {
                            notEmpty: {
                                message: 'Supplier is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="supplier_id"]', function(e) {
                $element = $(this);
                $(form).find('[name="vat_pan_number"]').val($('[name="supplier_id"] option:selected').data(
                    'vat'));
                $element = $(this);
                var supplierId = $element.val();
                var htmlToReplace = '<option value="">Select Purchase Order</option>';
                if (supplierId) {
                    var url = baseUrl + '/api/suppliers/' + supplierId;
                    var successCallback = function(response) {
                        response.purchaseOrders.forEach(function(purchaseOrder) {
                            htmlToReplace += '<option value="' + purchaseOrder.id + '">' +
                                purchaseOrder.prefix + '-' + purchaseOrder.order_number +
                                '</option>';
                        });
                        $($element).closest('form').find(".purchase_order_id").html(htmlToReplace)
                            .trigger('change');
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find(".purchase_order_id").html(htmlToReplace);
                }
                fv.revalidateField('supplier_id');
            });
        });
    </script>
@endsection
@section('page-content')


    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('payment.sheets.index') }}" class="text-decoration-none text-dark">Payment
                                Sheets</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <form action="{{ route('payment.sheets.store') }}" id="paymentSheetAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <div class="mb-2 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd"
                                    class="form-label required-label">{{ __('label.supplier') }}</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <select class="select2 form-control @if ($errors->has('supplier_id')) is-invalid @endif"
                                name="supplier_id">
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" data-vat="{{ $supplier->vat_pan_number }}">
                                        {{ $supplier->getSupplierName() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('supplier_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="supplier_id">
                                        {!! $errors->first('supplier_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label">{{ __('label.vat-pan-no') }}</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <input class="form-control" name="vat_pan_number" value="{{ old('vat_pan_number') }}"
                                readonly />
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label">{{ __('label.purchase-order') }}</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <select
                                class="select2 form-control purchase_order_id @if ($errors->has('purchase_order_ids')) is-invalid @endif"
                                name="purchase_order_ids[]" multiple="multiple">
                                <option value="">Select Purchase Orders</option>
                            </select>
                            @if ($errors->has('purchase_order_ids'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="purchase_order_ids">
                                        {!! $errors->first('purchase_order_ids') !!}
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- <div class="col-lg-2"> --}}
                        {{--     <div class="d-flex align-items-start h-100"> --}}
                        {{--         <label for="validationdd" class="form-label">District</label> --}}
                        {{--     </div> --}}
                        {{-- </div> --}}
                        {{-- <div class="col-lg-4"> --}}
                        {{--     <select name="district_id" class="select2 form-control" data-width="100%"> --}}
                        {{--         <option value="">Select a District</option> --}}
                        {{--         @foreach ($districts as $district) --}}
                        {{--             <option value="{{ $district->id }}" --}}
                        {{--                 {{ $district->id == old('district_id')? "selected":"" }}> --}}
                        {{--                 {{ $district->getDistrictName() }} --}}
                        {{--             </option> --}}
                        {{--         @endforeach --}}
                        {{--     </select> --}}
                        {{--     @if ($errors->has('district_id')) --}}
                        {{--         <div class="fv-plugins-message-container invalid-feedback"> --}}
                        {{--             <div data-field="district_id"> --}}
                        {{--                 {!! $errors->first('district_id') !!} --}}
                        {{--             </div> --}}
                        {{--         </div> --}}
                        {{--     @endif --}}
                        {{-- </div> --}}
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label class="form-label" for="purpose">Purpose</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <input class="form-control" type="text" name="purpose" id="purpose"
                                value="{{ old('purpose') }}">
                        </div>
                    </div>

                    {!! csrf_field() !!}
                </div>
                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                    </button>
                    <a href="{!! route('payment.sheets.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>



@stop
