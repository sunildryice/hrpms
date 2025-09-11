@extends('layouts.container')

@section('title', 'Fund Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#fund-requests-menu').addClass('active');

            var oTable = $('#fundRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('fund.requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'request_for_office',
                        name: 'request_for_office'
                    },
                    {
                        data: 'requester',
                        name: 'requester'
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount'
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

            $('#fundRequestTable').on('click', '.delete-record', function(e) {
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

            $('#fundRequestTable').on('click', '.cancel-record', function(e) {
                e.preventDefault();
                let url = $(this).attr('data-href');
                let number = $(this).attr('data-number');
                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    oTable.ajax.reload();
                };
                ajaxTextSweetAlert(url, 'POST', `Cancel ${number}?`, 'Remarks', 'log_remarks',
                    successCallback);
            })

            $('#fundRequestTable').on('click', '.replicate-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var url = $object.attr('data-href');
                var desc = $object.attr('data-description');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                ajaxSweetAlert(url, 'POST', {}, 'Replicate ' + desc, successCallback);
            });

            $('#fundRequestTable').on('click', '.amend-fund-request', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    window.location.href = response.redirectUrl;
                }
                var confirmText = 'Amend this Fund request'
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('fund.requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Fund Request
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="rounded border shadow-sm card c-tabs-content active" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="fundRequestTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.year') }}</th>
                                <th>{{ __('label.month') }}</th>
                                <th>{{ __('label.office') }}</th>
                                <th>{{ __('label.request-for') }}</th>
                                <th>{{ __('label.requester') }}</th>
                                <th>{{ __('label.amount') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th style="width: 150px;">{{ __('label.action') }}</th>
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
