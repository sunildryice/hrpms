@extends('layouts.container')

@section('title', 'Construction')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#construction-index').addClass('active');

            var oTable = $('#constructionTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('construction.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'health_facility_name',
                        name: 'health_facility_name'
                    },
                    {
                        data: 'signed_date',
                        name: 'signed_date',
                        render: function (data, type, row) {
                            return new Date(data).toLocaleDateString('en-us', {
                                year: "numeric",
                                month: "short",
                                day: "numeric"
                            })
                        },
                    },
                    {
                        data: 'effective_date_from',
                        render: function (data, type, row) {
                            return new Date(data).toLocaleDateString('en-us', {
                                year: "numeric",
                                month: "short",
                                day: "numeric"});
                        },
                        name: 'effective_date_from',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'effective_date_to',
                        render: function (data, type, row) {
                            return new Date(data).toLocaleDateString('en-us', {
                                year: "numeric",
                                month: "short",
                                day: "numeric"});
                        },
                        name: 'effective_date_to',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'extension_date_to',
                        name: 'extension_date_to',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'physical_progress',
                        name: 'physical_progress'
                    },
                    {
                        data: 'cluster',
                        name: 'cluster'
                    },
                    {
                        data: 'district',
                        name: 'district'
                    },
                    {
                        data: 'locallevel',
                        name: 'locallevel'
                    },
                    {
                        data: 'created_on',
                        name: 'created_on'
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

            $('#constructionTable').on('click', '.delete-record', function(e) {
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

            $('#constructionTable').on('click', '.amend-purchase-request', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                var confirmText = 'Amend this purchase request'
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
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
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('construction.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Construction Track
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="constructionTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Health Facility Name</th>
                                <th>Signed Date</th>
                                <th>Effective From</th>
                                <th>Effective To</th>
                                <th>Extension To</th>
                                <th>Physical Progress %</th>
                                <th>Cluster</th>
                                <th>District</th>
                                <th>Municipality</th>
                                <th>Created On</th>
                                <th>Action</th>
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
