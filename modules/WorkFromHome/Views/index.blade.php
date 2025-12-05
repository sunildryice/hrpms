@extends('layouts.container')

@section('title', 'Work From Home Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#wfh-requests-index').addClass('active');

            if ($.fn.DataTable.isDataTable('#wfhRequestTable')) {
                $('#wfhRequestTable').DataTable().destroy();
            }

            var oTable = $('#wfhRequestTable').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('wfh.requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'request_id',
                        name: 'request_id',
                        searchable: true
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
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

            $('#wfhRequestTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    $($object).closest('tr').remove();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $('#wfhRequestTable').on('click', '.amend-wfh-request', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.reload();
                }
                var confirmText = 'Amend this work from home request';
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 ">
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
                <a href="{{ route('wfh.requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi-plus"></i> New Work From Home Request
                </a>
            </div>
        </div>

    </div>
    <div class="card shadow-sm border rounded c-tabs-content active">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="wfhRequestTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.request-id') }}</th>
                            <th>{{ __('label.project-name') }}</th>
                            <th>{{ __('label.request-date') }}</th>
                            <th>{{ __('label.start-date') }}</th>
                            <th>{{ __('label.end-date') }}</th>
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
