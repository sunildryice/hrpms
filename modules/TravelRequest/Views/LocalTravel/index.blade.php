@extends('layouts.container')

@section('title', 'Local Travel Reimbursements')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#local-travel-reimbursements-menu').addClass('active');

            var oTable = $('#localTravelTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('local.travel.reimbursements.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'local_travel_number',
                        name: 'local_travel_number'
                    },
                    // {
                    //     data: 'travel_number',
                    //     name: 'travel_number'
                    // },
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
                        className:'sticky-col'
                    },
                ]
            });

            $('#localTravelTable').on('click', '.delete-record', function(e) {
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
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('local.travel.reimbursements.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Local Travel
                    </a>
                </div>
            </div>

        </div>
        <div class="card c-tabs-content active">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="localTravelTable">
                        <thead  class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.purpose') }}</th>
                                <th>{{ __('label.local-travel-number') }}</th>
                                {{-- <th>{{ __('label.travel-number') }}</th> --}}
                                <th>{{ __('label.requester') }}</th>
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
