@extends('layouts.container')
@section('title', 'Monthly Timesheet Summary Detail')
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#monthly-timesheets-summary-menu').addClass('active');
        });
        $(document).ready(function() {
            $('#MonthlyTimeSheetSummaryDetailTable').DataTable({
                processing: true,
                serverSide: true,
                scrollY: 500,
                scroller: true,
                scrollX: true,
                bPaginate: false,
                bInfo: true,
                bFilter: true,
                ajax: '{{ route('monthly-timesheet.summary.show', [$year, $month]) }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'requester_name',
                        name: 'requester_name'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_name'
                    },
                ],
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="pb-3 mb-3 border-bottom">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a class="text-decoration-none text-dark"
                            href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-decoration-none text-dark"
                            href="{{ route('monthly-timesheet.summary.index') }}">Monthly Timesheet Summary</a></li>
                    <li class="breadcrumb-item active">{{ $year }} {{ $month }}</li>
                </ol>
            </nav>
            <h4 class="m-0 mt-1 fs-6 text-uppercase fw-bold text-primary">
                Monthly Timesheet Summary - {{ $year }} {{ $month }}
            </h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered" id="MonthlyTimeSheetSummaryDetailTable">
                    <thead class="bg-light">
                        <tr>
                            <th>SN</th>
                            <th>Employee</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
