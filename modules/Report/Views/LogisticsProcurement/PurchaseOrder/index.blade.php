@extends('layouts.container')

@section('title', 'Report : Purchase Order')

@section('page_js')
    <script type="text/javascript">
        let start_date  = '';
        let end_date    = '';
        let po_number   = '';
        let office      = '';
        let vendor      = '';
        let item        = '';

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#purchase-order-report-menu').addClass('active');
            var oTable = $('#purchaseOrderReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.purchase.order.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date    = start_date;
                        d.end_date      = end_date;
                        d.po_number     = po_number;
                        d.office        = office;
                        d.vendor        = vendor;
                        d.item          = item;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'purchase_order_number',
                        name: 'purchase_order_number'
                    },
                    {
                        data: 'purchase_order_date',
                        name: 'purchase_order_date'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendor_name'
                    },
                    {
                        data: 'items',
                        name: 'items'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'rate',
                        name: 'rate'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'vat',
                        name: 'vat'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'activity_code',
                        name: 'activity_code'
                    },
                    {
                        data: 'account_code',
                        name: 'account_code'
                    },
                    {
                        data: 'donor_code',
                        name: 'donor_code'
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date'
                    },
                    {
                        data: 'purchase_request_number',
                        name: 'purchase_request_number'
                    },
                    {
                        data: 'bill_number',
                        name: 'bill_number'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'requested_final_remarks',
                        name: 'requested_final_remarks'
                    },
                ],
                scrollX: true
            });

            $('#btn_search').on('click', function(e) {
                if ($('#start_date').val()) {
                    let start = new Date($('#start_date').val());
                    start_date = start.getTime();
                }

                if ($('#end_date').val()) {
                    let end = new Date($('#end_date').val());
                    end_date = end.getTime();
                }

                if ($('#po_number').val()) {
                    po_number = $('#po_number').val();
                }

                if ($('#office').val()) {
                    office = $('#office').val();
                }

                if ($('#vendor').val()) {
                    vendor = $('#vendor').val();
                }

                if ($('#item').val()) {
                    item = $('#item').val();
                }

                if (start_date > end_date) {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    '/report/purchase/order/export?start_date=' + start_date + '&end_date=' + end_date +
                    '&office=' + office + '&po_number=' + po_number + '&vendor=' + vendor + '&item=' + item);

                oTable.ajax.reload();
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autohide: true,
                format: 'yyyy-mm-dd'
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autohide: true,
                format: 'yyyy-mm-dd'
            });

            $('#btn_reset').on('click', function(e) {
                start_date  = '';
                end_date    = '';
                po_number   = '';
                office      = '';
                vendor      = '';
                item        = '';
                $('#office').val('').trigger('change');
                $('#vendor').val('').trigger('change');
                $('#item').val('').trigger('change');
                $("input[type=text]").removeAttr('value');
            });
        });

        function resetValue(name){
            let value = null;
            eval(name + "=" + value + ";");
        }
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
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('report.purchase.order.export') }}" id="btn_export" class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form>
                    <div class="row mb-2" style="align-items: flex-end">
                        <div class="col-md-2 mb-2">
                            <label class="form-label" for="start_date">From</label>
                            <input class="form-control" type="text" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="end_date">To</label>
                            <input class="form-control" type="text" name="end_date" id="end_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="po_number">PO No.</label>
                            <input class="form-control" type="text" name="po_number" id="po_number">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="office">Office</label>
                            <select class="form-control select2" name="office" id="office">
                                <option value="" selected onclick="resetValue('office')">Select Office...</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="vendor">Vendor</label>
                            <select class="form-control select2" name="vendor" id="vendor">
                                <option value="" selected onclick="resetValue('vendor')">Select vendor...</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->getSupplierName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="item">Items</label>
                            <select class="form-control select2" name="item" id="item">
                                <option value="" selected onclick="resetValue('item')">Select item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col" style="display: flex; justify-content: end; margin-top: 10px">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="purchaseOrderReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>PO No.</th>
                                <th>PO Date</th>
                                <th>Office</th>
                                <th>Vendor Name</th>
                                <th>Items</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Total Amount</th>
                                <th>Project</th>
                                <th>Activity Code</th>
                                <th>Account Code</th>
                                <th>Donor Code</th>
                                <th>Delivery Date</th>
                                <th>PR No.</th>
                                <th>Bill No.</th>
                                <th>Payment Status</th>
                                <th>Requested Final Remarks</th>
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
