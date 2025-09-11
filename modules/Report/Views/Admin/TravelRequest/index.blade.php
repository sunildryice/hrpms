@extends('layouts.container')

@section('title', 'Report : Travel Request')

@section('page_js')
    <script type="text/javascript">
        let start_date          = '';
        let end_date            = '';
        let employee            = '';
        let duty_station        = '';
        let purpose_of_travel   = '';

        $(document).ready(function () {

            $('#navbarVerticalMenu').find('#travel-request-report-menu').addClass('active');

            var oTable = $('#travelRequestReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.travel.request.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date        = start_date;
                        d.end_date          = end_date;
                        d.employee          = employee;
                        d.duty_station      = duty_station;
                        d.purpose_of_travel = purpose_of_travel;
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name:'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'travel_number', name: 'travel_number'},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'designation', name: 'designation'},
                    {data: 'duty_station', name: 'duty_station'},
                    {data: 'date_from', name: 'date_from'},
                    {data: 'date_to', name: 'date_to'},
                    {data: 'total_days', name: 'total_days'},
                    {data: 'mode_of_travel', name: 'mode_of_travel'},
                    {data: 'travel_location', name: 'travel_location'},
                    {data: 'purpose_of_travel', name: 'purpose_of_travel'},
                    {data: 'approved', name: 'approved'},
                    {data: 'amended', name: 'amended'},
                    {data: 'travel_claim_submitted_date', name: 'travel_claim_submitted_date'},
                    {data: 'travel_claim_approved_date', name: 'travel_claim_approved_date'},
                    {data: 'travel_claim_reimbursed_date', name: 'travel_claim_reimbursed_date'},
                ],
                scrollX: true
            });

            $('#btn_search').on('click', function (e) {
                if ($('#start_date').val()) {
                    let start = new Date($('#start_date').val());
                    start_date = start.getTime();
                }

                if ($('#end_date').val()) {
                    let end = new Date($('#end_date').val());
                    // end.setDate(end.getDate() + 1);
                    end_date = end.getTime();
                }

                if ($('#employee').val()) {
                    employee = $('#employee').val();
                }

                if ($('#duty_station').val()) {
                    duty_station = $('#duty_station').val();
                }
                if ($('#purpose_of_travel').val()) {
                    purpose_of_travel = $('#purpose_of_travel').val();
                }

                if (start_date > end_date)
                {
                    $('#error_message').html('\'From\' date cannot be greater than \'To\' date.');
                    return;
                } else {
                    $('#error_message').html('');
                    $('#error_message').hide();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') + '/report/travel/request/export?start_date=' + start_date + '&end_date=' + end_date
                + '&employee=' + employee + '&duty_station=' + duty_station + '&purpose_of_travel=' + purpose_of_travel);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function (e) {
                start_date          = '';
                end_date            = '';
                employee            = '';
                duty_station        = '';
                purpose_of_travel   = '';
                $('#employee').val('').trigger('change');
                $('#duty_station').val('').trigger('change');
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
                    <a href="{{ route('report.travel.request.export') }}" id="btn_export" class="btn btn-primary btn-sm">
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
                    <div class="row mb-4"  style="align-items: flex-end">
                        <div class="col-md-2">
                            <label class="form-label" for="start_date">From</label>
                            <input class="form-control" type="text" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="end_date">To</label>
                            <input class="form-control" type="text" name="end_date" id="end_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="employee">Employee</label>
                            <select class="form-control select2" name="employee" id="employee">
                                <option value="" onclick="resetValue('employee')">Select employee...</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}">{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="duty_station">Duty Station</label>
                            <select class="form-control select2" name="duty_station" id="duty_station">
                                <option value="" onclick="resetValue('duty_station')">Select duty station...</option>
                                @foreach ($dutyStations as $dutyStation)
                                    <option value="{{ $dutyStation->id }}">{{ $dutyStation->getDistrictName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="purpose_of_travel">Purpose of Travel</label>
                            <input class="form-control" type="text" name="purpose_of_travel" id="purpose_of_travel">
                        </div>
                        <div class="col">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="travelRequestReportTable">
                        <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>Travel No.</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Duty Station</th>
                            <th>Date From</th>
                            <th>Date To</th>
                            <th>Total Days</th>
                            <th>Mode of Travel</th>
                            <th>Travel Location</th>
                            <th>Purpose of Travel</th>
                            <th>Approved</th>
                            <th>Amended</th>
                            <th>Travel Claim Submitted Date</th>
                            <th>Travel Claim Approved Date</th>
                            <th>Travel Claim Reimbursed Date</th>
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
