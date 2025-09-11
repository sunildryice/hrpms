@extends('layouts.container')
@section('title', 'Add Item')

@section('page_css')
    <style>
        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background: white;
            z-index: 99;

        }

        .first-col {
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
            const error = @json($errors->all());
            console.log(error)
            $(form).on('change', '.grnItem', function(e) {
                $(this).closest('form').find('button').attr('disabled', true);
                if ($(this).closest('form').find('.grnItem:checked').length >= 1) {
                    $(this).closest('form').find('button').attr('disabled', false);
                }
            });

            $('.unit_price').change(function(e) {
                calculateTotalPrice(this);
            });
            $('.received_quantity').change(function(e) {
                calculateTotalPrice(this);
            });
            $(form).on('change', '.grnItem', function(e) {
                $(this).closest('form').find('button').attr('disabled', true);
                if ($(this).closest('form').find('.grnItem:checked').length >= 1) {
                    $(this).closest('form').find('button').attr('disabled', false);
                }
            }).on('click', '#vatApplicableCheckAll', function(e) {
                $('.vatApplicable').prop('checked', this.checked);
            });

        function calculateTotalPrice($element) {
            quantity = $($element).closest('tr').find('.received_quantity').val();
            unitPrice = $($element).closest('tr').find('.unit_price').val();
            $($element).closest('tr').find('.total_amount').text(quantity * unitPrice);
        }

        $("#allcheck").on("click", function() {
            var isChecked = $(this).prop("checked");

            $(".grnItem").prop("checked", isChecked);
                if (isChecked) {
                    $(this).closest('form').find('button').attr('disabled', false);
                } else {
                    $(this).closest('form').find('button').attr('disabled', true);
                }
        });

       
        })
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
                            <a href="{{ route('grns.index') }}"
                                class="text-decoration-none text-dark">GRN</a>
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
            <form action="{{ route('grns.items.add.store', [$grn->id]) }}" id="grnAddForm"
                method="post" enctype="multipart/form-data" autocomplete="off">
                @csrf
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
                                        <th scope="col"><input type="checkbox" id="vatApplicableCheckAll" />{{ __('label.vat-applicable') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td class="sticky-col first-col">
                                                <input type="checkbox" class="grnItem"
                                                    name="purchase_request_item_ids[{!! $item->id !!}]"
                                                    value="{{ $item->id }}" />
                                            </td>
                                            <td class="sticky-col second-col">{{ $item->getItemName() }}</td>
                                            <td>{{ $item->getUnitName() }}</td>
                                            <td>{{ $item->specification }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                {{ $item->quantity - $item->grnItems->sum('quantity') }}
                                            </td>
                                            <td>
                                                <input type="number" class="form-control received_quantity"
                                                    name="received_quantity[{!! $item->id !!}]" />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control unit_price"
                                                    name="unit_price[{!! $item->id !!}]"
                                                    value="{{ $item->unit_price }}" step="any">
                                            </td>
                                            <td class="total_amount"></td>
                                            </td>
                                            <td >
                                                <input type="checkbox" name="vat_applicable[{!! $item->id !!}]" @if($item->vat_amount > 0) checked @endif
                                                class="vatApplicable" value="{{ $item->id }}" />
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
                    <a href="{!! route('grns.edit',[$grn->id]) !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>

        </div>
    </section>

@stop
