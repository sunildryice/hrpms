@extends('layouts.container')

@section('title', 'Report : Vehicle Movement')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            let start_date = '';
            let end_date = '';

            $('#navbarVerticalMenu').find('#vehicle-movement-report-menu').addClass('active');

            var oTable = $('#vehicleMovementReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.vehicle.movement.index') }}",
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
                        data: 'vehicle_request_number',
                        name: 'vehicle_request_number'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'vehicle_request_type',
                        name: 'vehicle_request_type'
                    },
                    {
                        data: 'hired_date_from',
                        name: 'hired_date_from'
                    },
                    {
                        data: 'hired_date_to',
                        name: 'hired_date_to'
                    },
                    {
                        data: 'travel_from',
                        name: 'travel_from'
                    },
                    {
                        data: 'travel_to',
                        name: 'travel_to'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'purpose_of_travel',
                        name: 'purpose_of_travel'
                    },
                    {
                        data: 'vehicle_type',
                        name: 'vehicle_type'
                    },
                    {
                        data: 'tentative_cost',
                        name: 'tentative_cost'
                    },
                    {
                        data: 'pickup_point',
                        name: 'pickup_point'
                    },
                    {
                        data: 'pickup_time',
                        name: 'pickup_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'request_approved_date',
                        name: 'request_approved_date'
                    },
                    {
                        data: 'vehicle_contractor',
                        name: 'vehicle_contractor'
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
                        data: 'tds',
                        name: 'tds'
                    },
                    {
                        data: 'net_payment',
                        name: 'net_payment'
                    },
                    {
                        data: 'payment_approved_date',
                        name: 'payment_approved_date'
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
                    end.setDate(end.getDate() + 1);
                    end_date = end.getTime();
                }

                if (start_date > end_date) {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    $('#error_message').show();
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') +
                    '/report/vehicle/movement/export?start_date=' + start_date + '&end_date=' + end_date
                    );

                oTable.ajax.reload();
            });

            $('[name=start_date]').datepicker({
                language: 'en-GB',
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).on('change', function (e) {
                $(this).datepicker('hide');
            });

            $('[name=end_date]').datepicker({
                language: 'en-GB',
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).on('change', function (e) {
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
                    <a href="{{ route('report.vehicle.movement.export') }}" id="btn_export" class="btn btn-primary btn-sm">
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
                    <table class="table" id="vehicleMovementReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Vehicle Request No.</th>
                                <th>Office</th>
                                <th>Vehicle Request Type</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Travel From</th>
                                <th>Travel To</th>
                                <th>User</th>
                                <th>Purpose Of Travel</th>
                                <th>Vehicle Type</th>
                                <th>Tentative Cost</th>
                                <th>Pick-up Point</th>
                                <th>Pick-up Time</th>
                                <th>End Time</th>
                                <th>Request Approval Date</th>
                                <th>Vehicle Contractor</th>
                                <th>Bill No.</th>
                                <th>Bill Date</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Total Amount</th>
                                <th>TDS</th>
                                <th>Net Payment</th>
                                <th>Payment Approved Date</th>
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
