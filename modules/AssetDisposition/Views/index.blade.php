@extends('layouts.container')

@section('title', 'Asset Disposition')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#asset-disposition-menu').addClass('active');

            var oTable = $('#assetDispositionTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('asset.disposition.index') }}",
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

          $('#asset-disposition-index').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $('#asset-disposition-index').on('click', '.cancel-asset-disposition', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                var confirmText = 'Do you want to cancel this Asset Disposition ?';
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });

            $('#asset-disposition-index').on('click', '.amend-asset-disposition', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.reload();
                }
                var confirmText = 'Amend this Asset Disposition'
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });

            $('#asset-disposition-index').on('click', '.create-settlement', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.href = response.redirectUrl;
                }
                ajaxSweetAlert($url, 'POST', {}, 'Create Settlement', successCallback);
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
                        <li class="breadcrumb-item" aria-current="page">Asset Disposition</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Asset Disposition</h4>
            </div>
            <div class="add-info justify-content-end">
                <a href="{!! route('asset.disposition.create') !!}" class="btn btn-primary btn-sm" rel="tooltip"
                    title="Add Asset Disposition">
                    <i class="bi-plus"></i> Add New</a>
            </div>
        </div>
    </div>

    <div class="card" id="asset-disposition-index">
        <div class="card-header fw-bold">Asset Disposition List</div>
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
                            <th>{{ __('label.status') }}</th>
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
