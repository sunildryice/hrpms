@extends('layouts.container')

@section('title', 'Lieu Leave Requests')


@section('page_css')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
            word-break: break-word;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {

            $('#navbarVerticalMenu')
                .find('#lieu-leave-requests-index')
                .addClass('active');

            if ($.fn.DataTable.isDataTable('#lieuLeaveRequestTable')) {
                $('#lieuLeaveRequestTable').DataTable().destroy();
            }

            var oTable = $('#lieuLeaveRequestTable').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: "{{ route('lieu.leave.requests.index') }}",
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
                        data: 'requester',
                        name: 'project',
                        className: 'wrap-text'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date'
                    },
                    {
                        data: 'leave_date',
                        name: 'leave_date'
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
                    }
                ]
            });

            // Delete Lieu Leave Request
            $('#lieuLeaveRequestTable').on('click', '.delete-record', function(e) {
                e.preventDefault();

                let $object = $(this);
                let url = $object.data('href');

                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload(null, false);
                };

                ajaxDeleteSweetAlert(url, successCallback);
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
                <a href="{{ route('lieu.leave.requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi-plus"></i> New Lieu Leave Request
                </a>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <div class="card border shadow-sm rounded h-100">
                <div class="card-header">
                    Lieu Leave Balances of {{ date('F') }}
                </div>

                <div class="card-body text-center">
                    <div class="row">

                        <div class="col-3">
                            <b class="text-muted d-block">Applied Leave</b>
                            <h5 class="my-3">{{ $appliedLeaveofMonth }}</h5>
                        </div>
                        <div class="col-3">
                            <b class="text-muted d-block">Lieu Balance </b>
                            <h5 class="my-3">{{ $lieuLeaveBalance }}</h5>
                        </div>
                        <div class="col-3">
                            <b class="text-muted d-block">Available Status </b>
                            <p class="my-3">{{ $availableBalanceofMonthStatus }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="card shadow-sm border rounded c-tabs-content active">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="lieuLeaveRequestTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.request-id') }}</th>
                            <th>{{ __('label.requester') }}</th>
                            <th>{{ __('label.request-date') }}</th>
                            <th>{{ __('label.leave-date') }}</th>
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
