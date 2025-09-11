@extends('layouts.container')

@section('title', 'Add Purchase Order')

@section('page_css')
    <style>

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background: white;
            z-index: 99;

        }

        .table-container {
            /* height: calc(100vh - 215px); */
            overflow: auto;
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
        var employeeId = '{{ auth()->user()->employee_id }}';

        document.addEventListener('DOMContentLoaded', function(e) {
            
             // table height calc
            const tableContainer = $('.table-container');
            const table = $('#purchaseRequestItemTable');
            const tableHeight = table[0].clientHeight;
            if(tableHeight > 682){
                tableContainer.css('height', 'calc(100vh - 215px)');
            }
            const purchaseReqItems = @json($purchaseRequest->purchaseRequestItems);
            var ltas = [];
            let lta = @json($lta);

            const form = document.getElementById('purchaseOrderAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    district_ids: {
                        validators: {
                            notEmpty: {
                                message: 'District is required',
                            },
                        },
                    },
                    supplier_id: {
                        validators: {
                            notEmpty: {
                                message: 'The supplier is required',
                            },
                        },
                    },
                    delivery_date: {
                        validators: {
                            notEmpty: {
                                message: 'The delivery date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Delivery date must be a valid date.'
                            }
                        },
                    },
                    currency_id: {
                        validators: {
                            notEmpty: {
                                message: 'Currency is required.'
                            }
                        }
                    }
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

            



            // $(form).on('change', '[name="supplier_id"]', function(e) {
            //     element = $(this);
            //     supplierId = element.val();
            //     var optionField = '<option value="">Select an LTA</option>';
            //     if (supplierId) {
            //         var url = '{{ route('api.lta.fetch', ':supplierId') }}';
            //         url = url.replace(':supplierId', supplierId);
            //         const successCallback = function(response) {
            //             ltas = response.ltas;
            //             if (ltas.length > 0) {
            //                 $.each(ltas, function(index, lta) {
            //                     optionField += '<option value="' + lta.id +
            //                         '"> Contract Number: ' + lta.contract_number + '</option>';
            //                 });
            //                 $(element).closest('form').find('[name="lta_contract_id"]').html(
            //                     optionField);
            //             }
            //         }
            //         const errorCallback = function(error) {
            //             console.log();
            //         }
            //         ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);

            //     }
            // }).on('change', '[name="lta_contract_id"]', function(e) {
            //     element = $(this);
            //     ltaId = element.val();
            //     if (ltaId) {
            //         const lta = ltas.find(lta => lta.id == ltaId);
            //         const ltaItems = lta.lta_items;

            //         $.each(ltaItems, function(index, ltaItem) {
            //             const item = purchaseReqItems.find(purchaseReqItem => purchaseReqItem
            //                 .item_id == ltaItem.item_id);
            //             if (item && $('#purchase-request-item-' + item.id).prop('checked')) {
            //                 $('#unit-price-' + item.id).val(ltaItem.unit_price).trigger(
            //                     'change');
            //             }
            //         });
            //     }
            // })

            if ($('[name="supplier_id"]').val()) {
                $(form).find('[name="supplier_id"]').trigger('change');
            }

            $('[name="delivery_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'))) }}',
                // startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
            }).on('change', function(e) {
                fv.revalidateField('delivery_date');
            });

            $(form).on('change', '[name="district_id"]', function(e) {
                fv.revalidateField('district_id');
            }).on('change', '[name="supplier_id"]', function(e) {
                fv.revalidateField('supplier_id');
            }).on('change', '.purchaseItem', function(e) {
                $(this).closest('form').find('button').attr('disabled', true);
                if ($(this).closest('form').find('.purchaseItem:checked').length >= 1) {
                    $(this).closest('form').find('button').attr('disabled', false);
                }
            }).on('click', '#purchaseItemCheckAll', function(e) {
                $('.purchaseItem').prop('checked', this.checked);
                if (this.checked) {
                    $(this).closest('form').find('button').attr('disabled', false);
                } else {
                    $(this).closest('form').find('button').attr('disabled', true);
                }
            }).on('click', '#vatApplicableCheckAll', function(e) {
                $('.vatApplicable').prop('checked', this.checked);
            });

            $('.order_amount').change(function(e) {
                calculateTotalPrice(this);
            });
            $('.order_quantity').change(function(e) {
                calculateTotalPrice(this);
            });

            if(lta){
                $('.item-header').html('Items (LTA Contract number: ' + lta.contract_number + ')' );
                $.each(lta.lta_items, function (index, ltaItem){
                    const item = purchaseReqItems.find(item => item.item_id == ltaItem.item_id);
                    if(item){
                        $(form).find('#unit-price-' + item.id).val(ltaItem.unit_price).trigger('change');
                        $(form).find('#purchase-request-item-' + item.id).prop('checked', true).trigger(
                                'change');
                    }
               })
            }
            //$('.delivery_date').datepicker({
            //     language: 'en-GB',
            //     autoHide: true,
            //     format: 'yyyy-mm-dd',
            //     startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
            // });
        });


        function calculateTotalPrice($element) {
            quantity = $($element).closest('tr').find('.order_quantity').val();
            unitPrice = $($element).closest('tr').find('.order_amount').val();
            $($element).closest('tr').find('.total_price').text(quantity * unitPrice);
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
                                class="text-decoration-none text-dark">Purchase
                                Request</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <form action="{{ route('purchase.requests.orders.combine.update', [$purchaseRequest->id, $purchaseOrder->id]) }}"
            id="purchaseOrderAddForm" method="post" enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationDistrict" class="form-label required-label">District </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php
                                $selectedDistrictId = $purchaseOrder
                                    ->districts()
                                    ->pluck('id')
                                    ->toArray();
                            @endphp
                            <select class="select2 form-control" name="district_ids[]" multiple disabled>
                                <option value="">Select a District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ in_array($district->id, $selectedDistrictId) ? 'selected' : '' }}>
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
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationSupplier" class="form-label required-label">Supplier </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php $selectedSupplierId = $purchaseOrder->supplier_id; @endphp
                            <select class="select2 form-control" name="supplier_id" disabled>
                                <option value="">Select a Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ $supplier->id == $selectedSupplierId ? 'selected' : '' }}>
                                        {{ $supplier->getSupplierNameandVAT() }}
                                    </option>
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
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationLta" class="form-label">LTA Contract </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select class="select2 form-control" name="lta_contract_id" readonly>
                                <option value="{{$lta?->id}}" selected>Contract Number: {{$lta?->contract_number}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="delivery_date" class="form-label required-label">Delivery Date</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="d-flex align-items-center">
                                <input type="text"
                                    class="form-control @if ($errors->has('delivery_date')) is-invalid @endif"
                                    name="delivery_date" id="delivery_date"
                                    value="{{ $purchaseOrder->delivery_date?->format('Y-m-d') }}" readonly>
                                @if ($errors->has('delivery_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="delivery_date">
                                            {{ $errors->first('delivery_date') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Delivery Location</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                      class="form-control @if($errors->has('delivery_location')) is-invalid @endif"
                                      name="delivery_location" value=" {{old('delivery_location') ?: $purchaseOrder->delivery_location }}" disabled/>
                            @if($errors->has('delivery_location'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div
                                        data-field="delivery_location">{!! $errors->first('delivery_location') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Delivery Instructions</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text"
                                      class="form-control @if($errors->has('delivery_instructions')) is-invalid @endif"
                                      name="delivery_instructions" disabled>{{ old('delivery_instructions')?: $purchaseOrder->delivery_instructions }}</textarea>
                            @if($errors->has('delivery_instructions'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div
                                        data-field="delivery_instructions">{!! $errors->first('delivery_instructions') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="currency_id" class="form-label required-label">Currency</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="d-flex align-items-center">
                                <select class="form-control @if ($errors->has('currency_id')) is-invalid @endif"
                                    name="currency_id" id="currency_id" disabled>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            @if ($purchaseOrder->currency_id == $currency->id) selected @endif>{{ $currency->getTitle() }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('currency_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="currency_id">
                                            {{ $errors->first('currency_id') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-header fw-bold">
                            Items
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-container">
                                <table class="table" id="purchaseRequestItemTable">
                                    <thead class="thead-light sticky-top">
                                        <tr>
                                            <th class="sticky-col first-col">
                                                <input type="checkbox" id="purchaseItemCheckAll" />
                                            </th>
                                            <th scope="col" class="sticky-col second-col">{{ __('label.item') }}</th>
                                            <th scope="col">{{ __('label.unit') }}</th>
                                            {{-- <th scope="col" style="width: 140px;">{{ __('label.delivery-date') }}</th> --}}
                                            <th scope="col">{{ __('label.po-quantity') }}</th>
                                            <th scope="col" style="width: 140px;">{{ __('label.po-amount') }}</th>
                                            <th scope="col">{{ __('label.total-amount') }}</th>
                                            <th scope="col"><input type="checkbox" id="vatApplicableCheckAll" />
                                                {{ __('label.vat-applicable') }}</th>
                                            <th scope="col" style="width: 500px;">{{ __('label.activity') }}</th>
                                            <th scope="col">{{ __('label.account') }}</th>
                                            <th scope="col">{{ __('label.donor') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchaseRequest->purchaseRequestItems as $purchaseRequestItem)
                                            @php
                                                $prItemQuantity = $purchaseRequestItem->quantity;
                                                $poItems = $purchaseRequestItem->purchaseOrderItems;
                                                $poItemQuantity = $poItems->sum('quantity');
                                                $remainingQuantity = $prItemQuantity - $poItemQuantity;
                                            @endphp
                                            @if ($remainingQuantity <= 0)
                                                @foreach ($poItems as $purchaseOrderItem)
                                                    <tr>
                                                        <td class="sticky-col first-col"></td>
                                                        <td class="sticky-col second-col">{{ $purchaseRequestItem->getItemName() }}</td>
                                                        <td>{{ $purchaseRequestItem->getUnitName() }}</td>
                                                        {{-- <td></td> --}}
                                                        <td>{{ $purchaseOrderItem->quantity }}</td>
                                                        <td>{{ $purchaseOrderItem->unit_price }}</td>
                                                        <td class="total_price">
                                                            {{ $purchaseOrderItem->total_price }}</td>
                                                        <td class="vat_amount">{{ $purchaseRequestItem->vat_amount }}</td>
                                                        <td class="activity_code">
                                                            {{ $purchaseRequestItem->getActivityCode() }}</td>
                                                        <td class="account_code">{{ $purchaseRequestItem->getAccountCode() }}
                                                        </td>
                                                        <td class="donor_code">{{ $purchaseRequestItem->getDonorCode() }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="sticky-col first-col">
                                                        <input type="checkbox" class="purchaseItem"
                                                            name="purchase_request_item_ids[{!! $purchaseRequestItem->id !!}]"
                                                            id="purchase-request-item-{{ $purchaseRequestItem->id }}"
                                                            value="{{ $purchaseRequestItem->id }}" />
                                                    </td>
                                                    <td class="sticky-col second-col">{{ $purchaseRequestItem->getItemName() }}</td>
                                                    <td>{{ $purchaseRequestItem->getUnitName() }}</td>
                                                    <td><input type="number" class="custom-input order_quantity"
                                                            name="order_quantity[{!! $purchaseRequestItem->id !!}]"
                                                            value="{{ $remainingQuantity }}" />
                                                    </td>
                                                    <td style="width: 15%"><input type="number"
                                                            class="custom-input order_amount"
                                                            name="unit_price[{!! $purchaseRequestItem->id !!}]"
                                                            id="unit-price-{{ $purchaseRequestItem->id }}"
                                                            value="0" />
                                                    </td>
                                                    <td class="total_price">{{ $purchaseRequestItem->total_price }}</td>
                                                    <td class="vat_applicale">
                                                        <input type="checkbox" class="vatApplicable"
                                                            name="vat_applicable[{!! $purchaseRequestItem->id !!}]"
                                                            value="{{ $purchaseRequestItem->id }}" />
                                                    </td>
                                                    <td class="activity_code">
                                                        {{ $purchaseRequestItem->getActivityCode() }}</td>
                                                    <td class="account_code">{{ $purchaseRequestItem->getAccountCode() }}
                                                    </td>
                                                    <td class="donor_code">{{ $purchaseRequestItem->getDonorCode() }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        @if ($errors->has('purchase_request_item_ids'))
                                            <tr>
                                                <td colspan="10" class="text-danger">
                                                    {!! $errors->first('purchase_request_item_ids') !!}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($errors->has('unit_price'))
                                            <tr>
                                                <td colspan="10" class="text-danger">
                                                    {!! $errors->first('unit_price') !!}
                                                </td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="justify-content-end d-flex gap-2">
                <button type="submit" name="btn" class="btn btn-success btn-sm" disabled="disabled">
                    Save
                </button>
                <a href="{!! route('approved.purchase.requests.show', $purchaseRequest->id) !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>
    </section>

@stop
