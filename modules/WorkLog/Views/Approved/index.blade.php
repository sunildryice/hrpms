@extends('layouts.container')

@section('title', 'Approved Worklog')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-work-logs-menu').addClass('active');
        });
        var oTable = $('#workPlanApproveTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('approved.monthly.work.logs.index') }}",
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
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Approve Worklog</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Approve Worklog</h4>
            </div>
        </div>
    </div>
    <div class="card" id="worklogTable">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="workPlanApproveTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:45px;">SN</th>
                            <th class="" style="width:15%;">{{ __('label.year-month') }}</th>
                            <th>{{ __('label.employee') }}</th>
                            <th class="">{{ __('label.planned') }}</th>
                            <th>{{ __('label.completed') }}</th>
                            <th class="">{{ __('label.summary') }}</th>
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

@stop
