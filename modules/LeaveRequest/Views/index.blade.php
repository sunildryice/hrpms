@extends('layouts.container')

@section('title', 'Leave Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#leave-requests-menu').addClass('active');

            var oTable = $('#leaveRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('leave.requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'leave_type',
                        name: 'leave_type'
                    },
                    {
                        data: 'request_days',
                        name: 'request_days'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date'
                    },
                    {
                        data: 'leave_number',
                        name: 'leave_number'
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
                        data: 'requester',
                        name: 'requester'
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
                        className: "sticky-col"
                    },
                ]
            });

            $('#leaveRequestTable').on('click', '.delete-record', function(e) {
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

            $('#leaveRequestTable').on('click', '.amend-leave-request', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.reload();
                }
                var confirmText = 'Amend this leave request'
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
                <a href="{{ route('leave.requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi-plus"></i> New Leave Request
                </a>
            </div>
        </div>

    </div>
    <div class="row g-3 mb-3">
        @foreach ($employeeLeaveBalances as $leaveBalance)
            <div class="col-md-2">
                <div class="card border shadow-sm rounded h-100">
                    <div class="card-header">
                        {{ $leaveBalance->leave_type_title }}
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted mb-1 small">Balance</p>
                        <h3 class="card-title mb-0">{{ $leaveBalance->balance }}</h3>
                    </div>

                </div>
            </div>
        @endforeach
    </div>


    <div class="card shadow-sm border rounded c-tabs-content active">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table" id="leaveRequestTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.leave-type') }}</th>
                            <th>{{ __('label.request-days') }}</th>
                            <th>{{ __('label.request-date') }}</th>
                            <th>{{ __('label.leave-number') }}</th>
                            <th>{{ __('label.start-date') }}</th>
                            <th>{{ __('label.end-date') }}</th>
                            <th>{{ __('label.requester') }}</th>
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
