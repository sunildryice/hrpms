@extends('layouts.container')

@section('title', 'Report : Maintenance Request')

@section('page_js')
    <script type="text/javascript">
        let start_date = '';
        let end_date = '';
        let rm_number = '';
        let office = '';
        let item = '';

        $(document).ready(function() {

            $('#navbarVerticalMenu').find('#maintenance-request-report-menu').addClass('active');

            var oTable = $('#maintenanceRequestReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: "{{ route('report.maintenance.request.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.rm_number = rm_number;
                        d.office = office;
                        d.item = item;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'rm_number',
                        name: 'rm_number'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'requested_date',
                        name: 'requested_date'
                    },
                    {
                        data: 'requested_by',
                        name: 'requested_by'
                    },
                    {
                        data: 'item_equipment_to_repair',
                        name: 'item_equipment_to_repair'
                    },
                    {
                        data: 'assets_code',
                        name: 'assets_code'
                    },
                    {
                        data: 'problem_service_for',
                        name: 'problem_service_for'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'total_tentative_cost',
                        name: 'total_tentative_cost'
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
                        name: 'dob'
                    },
                    {
                        data: 'remarks',
                        name: 'remarkste'
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

                if ($('#rm_number').val()) {
                    rm_number = $('#rm_number').val();
                }

                if ($('#office').val()) {
                    office = $('#office').val();
                }

                if ($('#item').val()) {
                    item = $('#item').val();
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
                    '/report/maintenance/request/export?start_date=' + start_date + '&end_date=' +
                    end_date +
                    '&rm_number=' + rm_number + '&office=' + office + '&item=' + item);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function(e) {
                start_date = '';
                end_date = '';
                rm_number = '';
                office = '';
                item = '';
                $('#office').val('').trigger('change');
                $('#item').val('').trigger('change');
            });
        });

        $('[name=start_date]').datepicker({
            language: 'en-GB',
            autohide: true,
            format: 'yyyy-mm-dd'
        });

        $('[name=end_date]').datepicker({
            language: 'en-GB',
            autohide: true,
            format: 'yyyy-mm-dd'
        });

        function resetValue(name) {
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
                    <a href="{{ route('report.maintenance.request.export') }}" id="btn_export"
                        class="btn btn-primary btn-sm">
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

                        <div class="col-md-2">
                            <label class="form-label" for="rm_number">RM No.</label>
                            <input class="form-control" type="text" name="rm_number" id="rm_number">
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
                            <label class="form-label" for="item">Item/Equipment to Repair</label>
                            <select class="form-control select2" name="item" id="item">
                                <option value="" selected onclick="resetValue('item')">Select item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="maintenanceRequestReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>RM No.</th>
                                <th>Office</th>
                                <th>Requested Date</th>
                                <th>Requested By</th>
                                <th>Item/Equipment to Repair</th>
                                <th>Assets Code</th>
                                <th>Problem/Service For</th>
                                <th>Qty</th>
                                <th>Total Tentative Cost</th>
                                <th>Project</th>
                                <th>Activity Code</th>
                                <th>Account Code</th>
                                <th>Donor Code</th>
                                <th>Remarks</th>
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
