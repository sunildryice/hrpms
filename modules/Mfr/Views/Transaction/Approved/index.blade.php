@extends('layouts.container')

@section('title', 'Approved Transactions')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-transactions').addClass('active');

            var oTable = $('#agreementTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('mfr.transaction.approved.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'partner_organization',
                        data: 'partner_organization',
                    },
                    {
                        data: 'release_amount',
                        data: 'release_amount',
                    },
                    {
                        data: 'expense_amount',
                        name: 'expense_amount'
                    },
                    {
                        data: 'reimbursed_amount',
                        name: 'reimbursed_amount'
                    },
                    {
                        data: 'questioned_cost',
                        name: 'questioned_cost'
                    },
                    {
                        data: 'requester',
                        name: 'requester'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "sticky-col"
                    },
                ]
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>



    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="agreementTable">
                    <thead class="bg-light">
                        <tr>
                                <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.transaction-date') }}</th>
                            <th>{{ __('label.partner-organization') }}</th>
                            <th>{{ __('label.advance-released') }}</th>
                            <th>{{ __('label.mfr-expenditure') }}</th>
                            <th>{{ __('label.expenditure-reimbursed') }}</th>
                            <th>{{ __('label.questioned-cost') }}</th>
                            <th>{{ __('label.requester') }}</th>
                        <th style="width:95px;">{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop
