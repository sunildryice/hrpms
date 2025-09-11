@extends('layouts.container')

@section('title', 'Assigned Assets')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assets-menu').addClass('active');

            var oTable = $('#assetTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('profile.assets.index') }}",
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {data: 'asset_number', name: 'asset_number'},
                    {data: 'item_name', name: 'item_name'},
                    {data: 'office', name: 'office'},
                    {data: 'department', name: 'department'},
                    {data: 'assigned_on', name: 'assigned_on'},
                    {data: 'condition', name: 'condition'},
                    {data: 'status', name: 'status'},
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
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('profile.show') }}" class="text-decoration-none">Profile</a>
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
                <div class="table-responsive">
                    <table class="table" id="assetTable">
                        <thead>
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.asset-number') }}</th>
                            <th>{{ __('label.item-name') }}</th>
                            <th>{{ __('label.office') }}</th>
                            <th>{{ __('label.department') }}</th>
                            <th>{{ __('label.assigned-on') }}</th>
                            <th>{{ __('label.condition') }}</th>
                            <th>Handover Status</th>
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
@stop
