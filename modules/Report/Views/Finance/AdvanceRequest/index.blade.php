@extends('layouts.container')

@section('title', 'Report : Advance Request & Settlement')

@section('page_js')
    <script type="text/javascript">
        let start_date  = '';
        let end_date    = '';
        let requester   = '';
        let office      = '';
        let status      = '';

        $(document).ready(function() {

            $('#navbarVerticalMenu').find('#advance-request-report-menu').addClass('active');

            var oTable = $('#advanceRequestReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.advance.request.index') }}",
                    type: 'POST',
                    data: function(d) {
                        d.start_date    = start_date;
                        d.end_date      = end_date;
                        d.requester     = requester;
                        d.office        = office;
                        d.status        = status;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'cash_advance_number',
                        name: 'cash_advance_number'
                    },
                    {
                        data: 'requester',
                        name: 'requester'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'purpose',
                        name: 'purpose'
                    },
                    {
                        data: 'advance_amount',
                        name: 'advance_amount'
                    },
                    {
                        data: 'advance_released_date',
                        name: 'advance_released_date'
                    },
                    {
                        data: 'program_completion_date',
                        name: 'program_completion_date'
                    },
                    {
                        data: 'settlement_date',
                        name: 'settlement_date'
                    },
                    {
                        data: 'settled_amount',
                        name: 'settled_amount'
                    },
                    {
                        data: 'balance',
                        name: 'balance'
                    },
                    {
                        data: 'status',
                        name: 'status'
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

                if ($('#requester').val()) {
                    requester = $('#requester').val();
                }

                if ($('#office').val()) {
                    office = $('#office').val();
                }
                if ($('#status').val()) {
                    status = $('#status').val();
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
                    '/report/advance/request/export?start_date=' + start_date + '&end_date=' + end_date
                    + '&requester=' + requester + '&office=' + office + '&status=' + status);

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
                start_date  = '';
                end_date    = '';
                requester   = '';
                office      = '';
                status      = '';
                $('#requester').val('').trigger('change');
                $('#office').val('').trigger('change');
            });
        });

        function resetValue(name){
            let value = null;
            eval(name + "=" + value + ";");
        }

    </script>
@endsection
@section('page-content')

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
                    <a href="{{ route('report.advance.request.export') }}" id="btn_export" class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
        <div class="card" id="employee-table" style="overflow: auto;">
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
                            <label class="form-label" for="requester">Requester</label>
                            <select class="form-control select2" name="requester" id="requester">
                                <option value="" onclick="resetValue('requester')">Select requester...</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}">{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
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
                            <label class="form-label" for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="" onclick="resetValue('status')">Select status</option>
                                <option value="settled">Settled</option>
                                <option value="due">Due</option>
                                <option value="overdue">Overdue</option>
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
                    <table class="table" id="advanceRequestReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Cash Advance No.</th>
                                <th>Requester</th>
                                <th>Office</th>
                                <th>Purpose</th>
                                <th>Advance Amount</th>
                                <th>Advance Released Date</th>
                                <th>Program Completion Date</th>
                                <th>Settlement Date</th>
                                <th>Settled Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@stop
