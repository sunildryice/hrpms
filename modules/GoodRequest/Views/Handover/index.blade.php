@extends('layouts.container')

@section('title', 'Review Good Requests')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#review-good-requests-menu').addClass('active');

            var oTable = $('#goodRequestTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('review.good.requests.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name:'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'request_number', name: 'request_number'},
                    {data: 'item_name', name: 'item_name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'unit', name: 'unit'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
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
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <table class="table" id="goodRequestTable">
                    <thead class="bg-light">
                    <tr>
                        <th>{{ __('label.sn') }}</th>
                        <th>{{ __('label.request-number') }}</th>
                        <th>{{ __('label.item-name') }}</th>
                        <th>{{ __('label.quantity') }}</th>
                        <th>{{ __('label.unit') }}</th>
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
