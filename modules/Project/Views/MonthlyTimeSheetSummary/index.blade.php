@extends('layouts.container')
@section('title', 'Monthly Timesheet Summary')
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#monthly-timesheets-summary-menu').addClass('active');
        });
        $(document).ready(function() {
            $('#MonthlyTimeSheetSummaryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('monthly-timesheet.summary.index') }}',
                columns: [{
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'not_submitted',
                        name: 'not_submitted'
                    },
                    {
                        data: 'submitted',
                        name: 'submitted'
                    },
                    {
                        data: 'approved',
                        name: 'approved'
                    },
                    {
                        data: 'returned',
                        name: 'returned'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
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
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm border rounded">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="MonthlyTimeSheetSummaryTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.year') }}</th>
                                <th>{{ __('label.month') }}</th>
                                <th>Not Submitted</th>
                                <th>Submitted</th>
                                <th>Approved</th>
                                <th>Returned</th>
                                <th>{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
