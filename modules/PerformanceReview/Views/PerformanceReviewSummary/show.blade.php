@extends('layouts.container')
@section('title', 'Performance Review Summary Detail')
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#performance-summary-index').addClass('active');
        });
        $(document).ready(function() {
            $('#PerformanceReviewSummaryDetailTable').DataTable({
                processing: true,
                serverSide: true,
                scrollY: 500,
                scroller: true,
                scrollX: true,
                bPaginate: false,
                bInfo: true,
                bFilter: true,
                ajax: '{{ route('performance.summary.show', [$fiscalYear, $reviewType]) }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a class="text-decoration-none text-dark"
                            href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-decoration-none text-dark"
                            href="{{ route('performance.summary.index') }}">Performance Review Summary</a></li>
                    <li class="breadcrumb-item active">{{ $fiscalYearTitle }} - {{ $reviewTypeTitle }}
                    </li>
                </ol>
            </nav>
            <h4 class="m-0 mt-1 fs-6 text-uppercase fw-bold text-primary">
                Performance Review Summary - {{ $fiscalYearTitle }} - {{ $reviewTypeTitle }}
            </h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered" id="PerformanceReviewSummaryDetailTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.employee') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
