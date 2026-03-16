@extends('layouts.container')

@section('title', 'Assigned Vehicle Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assigned-vehicle-requests-menu').addClass('active');

            var oTable = $('#vehicleRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('assigned.vehicle.requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'requester',
                        name: 'requester'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'request_number',
                        name: 'request_number'
                    },
                    {
                        data: 'start_datetime',
                        name: 'start_datetime'
                    },
                    {
                        data: 'end_datetime',
                        name: 'end_datetime'
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
                        searchable: false
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>

        </div>



        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="vehicleRequestTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.requester') }}</th>
                                <th>{{ __('label.office') }}</th>
                                <th>{{ __('label.request-number') }}</th>
                                <th>{{ __('label.start-date') }}</th>
                                <th>{{ __('label.end-date') }}</th>
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
