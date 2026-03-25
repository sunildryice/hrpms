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

            $('#navbarVerticalMenu').find('#local-travel-request-report-menu').addClass('active');

            var oTable = $('#localTravelRequestReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.local.travel.request.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.employee          = employee;
                        d.purpose_of_travel = purpose_of_travel;
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name:'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'local_travel_number', name: 'local_travel_number'},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'designation', name: 'designation'},
                    {data: 'duty_station', name: 'duty_station'},
                    {data: 'title', name: 'title'},
                    {data: 'approved', name: 'approved'},
                ],
                scrollX: true
            });

            $('#btn_search').on('click', function (e) {
                if ($('#employee').val()) {
                    employee = $('#employee').val();
                }
                if ($('#purpose_of_travel').val()) {
                    purpose_of_travel = $('#purpose_of_travel').val();
                }

                $('#btn_export').attr('href', '');
                $('#btn_export').attr('href', $('#btn_export').attr('href') + '/report/local/travel/request/export?employee=' + employee + '&purpose_of_travel=' + purpose_of_travel);

                oTable.ajax.reload();
            });

            $('#btn_reset').on('click', function (e) {
                employee            = '';
                purpose_of_travel   = '';
                $('#employee').val('').trigger('change');
                $('#duty_station').val('').trigger('change');
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
                    <table class="table" id="localTravelRequestReportTable">
                        <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>Travel No.</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Duty Station</th>
                            <th>Purpose of Travel</th>
                            <th>Approved</th>
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
