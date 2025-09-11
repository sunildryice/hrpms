@extends('layouts.container')

@section('title', 'Report : Fund Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            let start_date = '';
            let end_date = '';

            $('#navbarVerticalMenu').find('#fund-request-report-menu').addClass('active');

            var oTable = $('#fundRequestReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('report.fund.request.index') }}",
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
                        data: 'fund_request_number',
                        name: 'fund_request_number'
                    },
                    {
                        data: 'requested_by',
                        name: 'requested_by'
                    },
                    {
                        data: 'fiscal_year',
                        name: 'fiscal_year'
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'district',
                        name: 'district'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'requested_date',
                        name: 'requested_date'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
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
                    '/report/fund/request/export?start_date=' + start_date + '&end_date=' + end_date);

                oTable.ajax.reload();
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
                    <a href="{{ route('report.fund.request.export') }}" id="btn_export" class="btn btn-primary btn-sm">
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
                            <input class="form-control" type="date" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="end_date">To</label>
                            <input class="form-control" type="date" name="end_date" id="end_date">
                        </div>
                        <div class="col">
                            <button type="button" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table" id="fundRequestReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Fund Request No.</th>
                                <th>Requested By</th>
                                <th>Fiscal Year</th>
                                <th>Month</th>
                                <th>District</th>
                                <th>Project</th>
                                <th>Requested Date</th>
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
