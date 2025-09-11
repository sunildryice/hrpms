@extends('layouts.container')

@section('title', 'Assets On Store')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assets-store-menu').addClass('active');

            var assetsTable = $('#assetsTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('assets.store.index') }}",
                bFilter: true,
                bPaginate: true,
                bInfo: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'asset_number',
                        name: 'asset_number',
                        orderable: false,
                    },
                    {
                        data: 'assigned_user',
                        name: 'assigned_user',
                        orderable: false,
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                        orderable: false,
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'assigned_location',
                        name: 'assigned_location'
                    },
                    {
                        data: 'asset_condition',
                        name: 'asset_condition'
                    },
                    {
                        data: 'specification',
                        name: 'specification'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'sticky-col'
                    },
                ]
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="assetsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th scope="col">{{ __('label.asset-number') }}</th>
                                    <th scope="col">Assigned User</th>
                                    <th scope="col">{{ __('label.purchase-date') }}</th>
                                    <th scope="col">{{ __('label.item') }}</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">{{ __('label.condition') }}</th>
                                    <th scope="col">{{ __('label.specification') }}</th>
                                    <th>{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

@stop
