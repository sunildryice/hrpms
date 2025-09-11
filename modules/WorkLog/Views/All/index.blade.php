@extends('layouts.container')

@section('title', 'Work Log')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        var oTable = $('#workPlanMonthlyLogTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('all.monthly.work.logs.index') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
                {
                    data: 'year_month',
                    name: 'year_month'
                },
                {
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'planned',
                    name: 'planned'
                },
                {
                    data: 'completed',
                    name: 'completed'
                },
                {
                    data: 'summary',
                    name: 'summary'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ]
        });

        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#all-work-logs-menu').addClass('active');
        });
    </script>
@endsection

@section('page-content')

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">All Monthly Worklogs</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Monthly Worklog</h4>
                </div>
                <div class="add-info justify-content-end">
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless" id="workPlanMonthlyLogTable">
                            <thead class="bg-light">
                            <tr>
                                <th style="width:45px;">SN</th>
                                <th style="width:15%;">{{ __('label.year-month') }}</th>
                                <th>{{ __('label.name') }}</th>
                                <th>{{ __('label.planned') }}</th>
                                <th>{{ __('label.completed') }}</th>
                                <th>{{ __('label.summary') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th style="width: 140px;">{{ __('label.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
@stop
