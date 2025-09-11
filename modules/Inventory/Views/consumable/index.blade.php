@extends('layouts.container')

@section('title', 'Office Use')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#inventories-consumable-menu').addClass('active');
            let item_type = '';
            var oTable = $('#inventoryTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('inventories.office.use.index') }}",
                    type: 'POST',
                    data: function(d){
                        console.log(item_type)
                        d.item_type = item_type
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'batch_number',
                        name: 'batch_number'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'vat_amount',
                        name: 'vat_amount'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'available_quantity',
                        name: 'available_quantity'
                    },
                    {
                        data: 'specification',
                        name: 'specification',
                        orderable: false
                    },
                    {
                        data: 'execution_type',
                        name: 'execution_type',
                        orderable: false
                    },
                    {
                        data: 'activity_code',
                        name: 'activity_code',
                        orderable: false
                    },
                    {
                        data: 'account_code',
                        name: 'account_code',
                        orderable: false
                    },
                    {
                        data: 'donor_code',
                        name: 'donor_code',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'sticky-col'
                    },
                ]
            });

            $('#inventoryTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $('#item_type').on('change', function() {
                item_type = $(this).val();
                oTable.ajax.reload();
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{--                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <form action="{{ route('inventories.office.use.index') }}" method="POST" id="filterForm">
                    @csrf
                    <div class="row mb-4">
                        <div class="col col-2  mb-6">
                            <label class="form-label" for="item_type">Item Type</label>
                            <select class="form-control select2" name="item_type" id="item_type">
                                @foreach ($inventoryTypes as $invType)
                                    <option value="{{ $invType->id }}">
                                        {{ $invType->title }}</option>
                                @endforeach
                            </select>
                        </div>
                  
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="inventoryTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th scope="col">{{ __('label.purchase-date') }}</th>
                                <th scope="col">{{ __('label.supplier') }}</th>
                                <th scope="col">{{ __('label.item') }}</th>
                                <th scope="col">Batch No.</th>
                                <th scope="col">{{ __('label.quantity') }}</th>
                                <th scope="col">{{ __('label.unit-price') }}</th>
                                <th scope="col">{{ __('label.total-price') }}</th>
                                <th scope="col">{{ __('label.vat-amount') }}</th>
                                <th scope="col">{{ __('label.total-amount') }}</th>
                                <th scope="col">{{ __('label.available-quantity') }}</th>
                                <th scope="col">{{ __('label.description') }}</th>
                                <th scope="col">{{ __('label.execution-type') }}</th>
                                <th scope="col">{{ __('label.activity-code') }}</th>
                                <th scope="col">{{ __('label.account-code') }}</th>
                                <th scope="col">{{ __('label.donor-code') }}</th>
                                <th>{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
@stop
