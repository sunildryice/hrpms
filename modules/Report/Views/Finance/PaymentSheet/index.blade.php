@extends('layouts.container')

@section('title', 'Report : Payment Sheet')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            let start_date = '';
            let end_date = '';

            $('#navbarVerticalMenu').find('#payment-sheet-report-menu').addClass('active');

            var oTable = $('#paymentSheetReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.payment.sheet.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'payment_sheet_number',
                        name: 'payment_sheet_number'
                    },
                    {
                        data: 'vendor',
                        name: 'vendor'
                    },
                    {
                        data: 'vat_pan_number',
                        name: 'vat_pan_number'
                    },
                    {
                        data: 'bill_number',
                        name: 'bill_number'
                    },
                    {
                        data: 'bill_date',
                        name: 'bill_date'
                    },
                    {
                        data: 'purpose',
                        name: 'purpose'
                    },
                    {
                        data: 'bill_amount',
                        name: 'bill_amount'
                    },
                    {
                        data: 'less_tds',
                        name: 'less_tds'
                    },
                    {
                        data: 'net_payment',
                        name: 'net_payment'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'approved_date',
                        name: 'approved_date'
                    },
                    {
                        data: 'voucher_reference_number',
                        name: 'voucher_reference_number'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
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

                if (start_date > end_date) {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    '/report/payment/sheet/export?start_date=' + start_date + '&end_date=' + end_date);

                oTable.ajax.reload();
            });

            $('[name=start_date]').datepicker({
                language: 'en-GB',
                autoclose: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                $(this).datepicker('hide');
            });

            $('[name=end_date]').datepicker({
                language: 'en-GB',
                autoclose: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                $(this).datepicker('hide');
            });

            $('#btn_reset').on('click', function(e) {
                start_date = '';
                end_date = '';
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
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('report.payment.sheet.export') }}" id="btn_export" class="btn btn-primary btn-sm">
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
                    <div class="row mb-4" style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="start_date">From</label>
                            <input class="form-control" type="text" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="end_date">To</label>
                            <input class="form-control" type="text" name="end_date" id="end_date">
                        </div>
                        <div class="col">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="paymentSheetReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Payment Sheet No.</th>
                                <th>Vendor</th>
                                <th>PAN/VAT No.</th>
                                <th>Bill No.</th>
                                <th>Bill Date</th>
                                <th>Purpose</th>
                                <th>Bill Amount</th>
                                <th>Less TDS</th>
                                <th>Net Payment</th>
                                <th>Office</th>
                                <th>Approved Date</th>
                                <th>Voucher Reference No.</th>
                                <th>Payment Status</th>
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
