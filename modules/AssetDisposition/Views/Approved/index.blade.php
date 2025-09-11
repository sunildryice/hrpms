@extends('layouts.container')

@section('title', 'Approved Asset Disposition')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-asset-disposition-menu').addClass('active');

            var oTable = $('#assetDispositionTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.asset.disposition.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'disposition_type',
                        name: 'disposition_type'
                    },
                    {
                        data: 'office_name',
                    },
                    {
                        data: 'disposition_date',
                        name: 'disposition_date'
                    },
                    {
                        data: 'assets',
                        name: 'assets'
                    },
                    {
                        data: 'requester',
                        name: 'requester',
                    },
                    {
                        data: 'approver',
                        name: 'approver',
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
                        <li class="breadcrumb-item" aria-current="page">Approved Asset Disposition</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Approved Asset Disposition</h4>
            </div>
        </div>
    </div>

    <div class="card" id="asset-disposition-index">
        <div class="card-header fw-bold">Approved Asset Disposition List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="assetDispositionTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:45px;">{{ __('label.sn') }}</th>
                            <th>Disposition Type</th>
                            <th>Office</th>
                            <th>Disposition Date</th>
                            <th>Assets</th>
                            <th>Requester</th>
                            <th>Approver</th>
                            <th>{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop
