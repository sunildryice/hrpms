@extends('layouts.container')

@section('title', 'Travel Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-report-menu').addClass('active');
        });

        var oTable = $('#travelReportTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.reports.index') }}",
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
                    data: 'travel_number',
                    name: 'travel_number'
                },
                {
                    data: 'departure_date',
                    name: 'departure_date'
                },
                {
                    data: 'return_date',
                    name: 'return_date'
                },
                {
                    data: 'final_destination',
                    name: 'final_destination'
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

        $('#travelReportTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $($object).closest('tr').remove();
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Travel Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Travel Report</h4>
            </div>
        </div>

    </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderedless" id="travelReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.requester') }}</th>
                                <th>{{ __('label.travel-number') }}</th>
                                <th>{{ __('label.from-date') }}</th>
                                <th>{{ __('label.to-date') }}</th>
                                <th>{{ __('label.destination') }}</th>
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
