@extends('layouts.container')

@section('title', 'Add Items')

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

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('purchaseOrderAddForm');

             // table height calc
            const tableContainer = $('.table-container');
            const table = $('#purchaseRequestItemTable');
            const tableHeight = table[0].clientHeight;
            if(tableHeight > 682){
                tableContainer.css('height', 'calc(100vh - 215px)');
            }

            const purchaseReqItems = @json($purchaseRequest->purchaseRequestItems);
            const lta = @json($lta);

            $(form).on('change', '.purchaseItem', function (e) {
                $(this).closest('form').find('button').attr('disabled', true);
                if ($(this).closest('form').find('.purchaseItem:checked').length >= 1) {
                    $(this).closest('form').find('button').attr('disabled', false);
                }
            }).on('click', '#purchaseItemCheckAll', function (e) {
                $('.purchaseItem').prop('checked',this.checked);
                if (this.checked) {
                    $(this).closest('form').find('button').attr('disabled', false);
                } else {
                    $(this).closest('form').find('button').attr('disabled', true);
                }
            }).on('click', '#vatApplicableCheckAll', function (e) {
                $('.vatApplicable').prop('checked',this.checked);
            });

            $('.order_amount').change(function (e) {
                calculateTotalPrice(this);
            });
            $('.order_quantity').change(function (e) {
                calculateTotalPrice(this);
            });

            if(lta){
                $('.item-header').html('Items (LTA Contract number: ' + lta.contract_number + ')' );
            $.each(purchaseReqItems, function(index, purchaseReqItem) {
                const ltaItem = lta.lta_items.find(ltaItem => ltaItem.item_id == purchaseReqItem.item_id);
                if (ltaItem) {
                    $(form).find('#unit-price-' + purchaseReqItem.id).val(ltaItem.unit_price).trigger(
                        'change');
                    $(form).find('#purchase-request-item-' + purchaseReqItem.id).prop('checked', true).trigger(
                        'change');
                }
            });
            }
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
                    <form action="{{ route('purchase.orders.items.storeItem',[ $purchaseRequest->id, $purchaseOrder->id]) }}"
                          id="purchaseOrderAddForm" method="post"
                          enctype="multipart/form-data" autocomplete="off">
                          @csrf
                          @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header fw-bold item-header">
                                        Items
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-container">
                                            <table class="table" id="purchaseRequestItemTable">
                                                <thead class="thead-light sticky-top">
                                                <tr>
                                                    <th class="sticky-col first-col">
                                                        <input type="checkbox" id="purchaseItemCheckAll"/>
                                                    </th>
                                                    <th scope="col" class="sticky-col second-col">{{ __('label.item') }}</th>
                                                    <th scope="col">{{ __('label.unit') }}</th>
                                                    {{-- <th scope="col" style="width: 140px;">{{ __('label.delivery-date') }}</th> --}}
                                                    <th scope="col">{{ __('label.po-quantity') }}</th>
                                                    <th scope="col" style="width: 140px;">{{ __('label.unit-price') }}</th>
                                                    <th scope="col">{{ __('label.total-amount') }}</th>
                                                    <th scope="col"><input type="checkbox" id="vatApplicableCheckAll" /> {{ __('label.vat-applicable') }}</th>
                                                    <th scope="col" style="width: 500px;">{{ __('label.activity') }}</th>
                                                    <th scope="col">{{ __('label.account') }}</th>
                                                    <th scope="col">{{ __('label.donor') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($purchaseRequest->purchaseRequestItems as $purchaseRequestItem)
                                                @php
                                                    $prItemQuantity = $purchaseRequestItem->quantity;
                                                    $poItems = $purchaseRequestItem->purchaseOrderItems;
                                                    $poItemQuantity = $poItems->sum('quantity');
                                                    $remainingQuantity = $prItemQuantity - $poItemQuantity;
                                                @endphp
                                                    @if($remainingQuantity <= 0)
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
                                                                       value="{{ $purchaseRequestItem->id }}"/>
                                                            </td>
                                                            <td class="sticky-col second-col">{{ $purchaseRequestItem->getItemName() }}</td>
                                                            <td>{{ $purchaseRequestItem->getUnitName() }}</td>
                                                            <td><input type="number"
                                                                       class="custom-input order_quantity"
                                                                       name="order_quantity[{!! $purchaseRequestItem->id !!}]"
                                                                       value="{{ $remainingQuantity }}"/>
                                                            </td>
                                                            <td style="width: 15%"><input type="number"
                                                                                          class="custom-input order_amount"
                                                                                          name="unit_price[{!! $purchaseRequestItem->id !!}]"
                                                                                            id="unit-price-{{ $purchaseRequestItem->id }}"
                                                                                          value="0"/>
                                                            </td>
                                                            <td class="total_price">{{ $purchaseRequestItem->total_price }}</td>
                                                            <td class="vat_applicale">
                                                                <input type="checkbox" class="vatApplicable"
                                                                       name="vat_applicable[{!! $purchaseRequestItem->id !!}]"
                                                                       value="{{ $purchaseRequestItem->id }}"/>
                                                            </td>
                                                            <td class="activity_code">{{ $purchaseRequestItem->getActivityCode() }}</td>
                                                            <td class="account_code">{{ $purchaseRequestItem->getAccountCode() }}</td>
                                                            <td class="donor_code">{{ $purchaseRequestItem->getDonorCode() }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach

                                                @if($errors->has('purchase_request_item_ids'))
                                                    <tr>
                                                        <td colspan="10" class="text-danger">
                                                            {!! $errors->first('purchase_request_item_ids') !!}
                                                        </td>
                                                    </tr>
                                                @endif

                                                @if($errors->has('unit_price'))
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
                         
                            <a href="{!! route('purchase.orders.edit', $purchaseOrder->id) !!}"
                               class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
            </section>

@stop
