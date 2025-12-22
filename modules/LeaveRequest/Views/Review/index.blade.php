@extends('layouts.container')

@section('title', 'Review Leave Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#review-leave-requests-menu').addClass('active');

            var oTable = $('#leaveRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('review.leave.requests.index') }}",
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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item  aria-current="page">Review Leave Requests</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase text-primary fw-bold">Review Leave Requests</h4>
            </div>
        </div>

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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@stop
