@extends('layouts.container')

@section('title', 'Add GRN')

@section('page_css')
    <style>

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background: white;
            z-index: 99;

        }

        .first-col {
            width: 40px;
            left: 0px;
        }

        .second-col {
            min-width: 300px;
            max-width: 350px;
            white-space: pre-line;
            left: 25px;
        }
        
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('grnAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    received_date: {
                        validators: {
                            notEmpty: {
                                message: 'Received date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
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

            $('.received_date').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ $purchaseRequest->request_date->format('Y-m-d') }}',
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function() {
                fv.revalidateField('received_date');
            });

            $(form).on('change', '.grnItem', function(e) {
                $(this).closest('form').find('button').attr('disabled', true);
                if ($(this).closest('form').find('.grnItem:checked').length >= 1) {
                    $(this).closest('form').find('button').attr('disabled', false);
                }
            }).on('click', '#allcheck', function(e) {
                $('.grnItem').prop('checked', this.checked);
                if (this.checked) {
                    $(this).closest('form').find('button').attr('disabled', false);
                } else {
                    $(this).closest('form').find('button').attr('disabled', true);
                }
            });

            $('.unit_price').change(function(e) {
                calculateTotalPrice(this);
            });
            $('.received_quantity').change(function(e) {
                calculateTotalPrice(this);
            });
        });

        function calculateTotalPrice($element) {
            quantity = $($element).closest('tr').find('.received_quantity').val();
            unitPrice = $($element).closest('tr').find('.unit_price').val();
            $($element).closest('tr').find('.total_amount').text(quantity * unitPrice);
        }

    </script>
@endsection
@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('approved.purchase.requests.index') }}"
                                class="text-decoration-none text-dark">Purchase Request</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div>
            <form action="{{ route('approved.purchase.requests.grns.store', $purchaseRequest->id) }}" id="grnAddForm"
                method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationSupplier" class="form-label required-label">Received Date</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input readonly class="form-control received_date" name="received_date"
                                    value="{!! old('received_date') !!}" />
                                @if ($errors->has('received_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="received_date">
                                            {!! $errors->first('received_date') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationInvoiceNumber" class="form-label">Invoice Number (If Any)</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input class="form-control" name="invoice_number" type="text"
                                    value="{!! old('invoice_number') !!}" />
                                @if ($errors->has('invoice_number'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="invoice_number">
                                            {!! $errors->first('invoice_number') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="supplier_id" class="form-label required-label">Supplier</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select class="form-control select2" name="supplier_id" id="supplier_id">
                                    <option value="">Select supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->getSupplierName() }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('supplier'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="supplier">
                                            {!! $errors->first('supplier') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationReceivedNote" class="form-label">Received Note</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea class="form-control" name="received_note" rows="3">{!! old('received_note') !!}</textarea>
                                @if ($errors->has('received_note'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="received_note">
                                            {!! $errors->first('received_note') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">Items</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="purchaseRequestItemTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="sticky-col first-col"><input type="checkbox" id="allcheck"></th>
                                        <th scope="col" class="sticky-col second-col">{{ __('label.item') }}</th>
                                        <th scope="col">{{ __('label.unit') }}</th>
                                        <th scope="col">{{ __('label.specification') }}</th>
                                        <th scope="col">{{ __('label.quantity-ordered') }}</th>
                                        <th scope="col">{{ __('label.quantity-remaining') }}</th>
                                        <th scope="col">{{ __('label.quantity-received') }}</th>
                                        <th scope="col">GRN Amount</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">VAT Applicable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseRequest->purchaseRequestItems as $purchaseRequestItem)
                                        <tr>
                                            <td class="sticky-col first-col">
                                                <input type="checkbox" class="grnItem"
                                                    name="purchase_request_item_ids[{!! $purchaseRequestItem->id !!}]"
                                                    value="{{ $purchaseRequestItem->id }}" />
                                            </td>
                                            <td class="sticky-col second-col">{{ $purchaseRequestItem->getItemName() }}</td>
                                            <td>{{ $purchaseRequestItem->getUnitName() }}</td>
                                            <td>{{ $purchaseRequestItem->specification }}</td>
                                            <td>{{ $purchaseRequestItem->quantity }}</td>
                                            <td>
                                                {{ $purchaseRequestItem->quantity - $purchaseRequestItem->grnItems->sum('quantity') }}
                                            </td>
                                            <td>
                                                <input type="number" class="form-control received_quantity"
                                                    name="received_quantity[{!! $purchaseRequestItem->id !!}]" />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control unit_price"
                                                    name="unit_price[{!! $purchaseRequestItem->id !!}]"
                                                    value="{{ $purchaseRequestItem->unit_price }}">
                                            </td>
                                            <td class="total_amount"></td>
                                            </td>
                                            <td class="vat_applicale">
                                                <input type="checkbox" name="vat_applicable[{!! $purchaseRequestItem->id !!}]"
                                                    value="{{ $purchaseRequestItem->id }}" />
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($errors->has('purchase_request_item_ids'))
                                        <tr>
                                            <td colspan="10" class="text-danger">
                                                {!! $errors->first('purchase_request_item_ids') !!}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" class="btn btn-primary btn-sm" disabled="disabled">
                        Save
                    </button>
                    <a href="{!! route('approved.purchase.orders.show', $purchaseRequest->id) !!}" class="btn btn-danger btn-sm">Cancel</a>
                    {!! csrf_field() !!}
                </div>
            </form>

            {{-- <span>
                        @if ($errors)
                            @dump($errors)
                        @endif
                    </span> --}}
        </div>
    </section>

@stop
