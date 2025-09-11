@extends('layouts.container')

@section('title', 'Advance Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('[href="#navbarAdvanceRequest"]').addClass('active').attr('aria-expanded', 'true');
            $('#navbarVerticalMenu').find('#navbarAdvanceRequest').addClass('show');
            $('#navbarVerticalMenu').find('#advance-requests-menu').addClass('active');

            var oTable = $('#advanceRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('advance.requests.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'advance_number', name: 'advance_number'},
                    {data: 'project_code', name: 'project_code'},
                    {data: 'required_date', name: 'required_date'},
                    {data: 'estimated_amount', name: 'estimated_amount'},
                    {data: 'requester', name: 'requester'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className:'sticky-col'},
                ]
            });

            $('#advanceRequestTable').on('click', '.delete-record', function(e) {
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

            $('#advanceRequestTable').on('click', '.amend-purchase-request', function(e) {
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

        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
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
                    <a href="{{ route('advance.requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Advance Request
                    </a>
                </div>
            </div>

        </div>
        <div class="card" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                <table class="table" id="advanceRequestTable">
                    <thead class="bg-light">
                    <tr>
                        <th>{{ __('label.sn') }}</th>
                        <th>{!! __('label.advance-number') !!}</th>
                        <th>{!! __('label.project') !!}</th>
                        <th>{!! __('label.required-date') !!}</th>
                        <th>{!! __('label.amount') !!}</th>
                        <th>{!! __('label.requester') !!}</th>
                        <th>{!! __('label.status') !!}</th>
                        <th>{!! __('label.action') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            </div>
        </div>
@stop
