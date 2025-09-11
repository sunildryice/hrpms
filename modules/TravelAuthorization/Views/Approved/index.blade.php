@extends('layouts.container')

@section('title', 'Approved Travel Authorization Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-ta-request-menu').addClass('active');

            var oTable = $('#travelRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.ta.requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'request_number',
                        name: 'request_number'
                    },
                    {
                        data: 'requester',
                        name: 'requester'
                    },
                    {
                        data: 'submitted_date',
                        name: 'submitted_date'
                    },
                    {
                        data: 'officials',
                        name: 'officials'
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
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
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
                <table class="table" id="travelRequestTable">
                    <thead>
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.travel-number') }}</th>
                            <th>{{ __('label.requester') }}</th>
                            <th>{{ __('label.submit-date') }}</th>
                            <th>{{ __('label.officials') }}</th>
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
