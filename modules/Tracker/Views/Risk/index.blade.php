@extends('layouts.container')

@section('title', 'Risk Tracker')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#risk-index').addClass('active');

            var oTable = $('#riskTable').DataTable({
                scrollX: true,
                processing: true,
                serverside: true,
                ajax: "{{route('risk.index')}}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'project_title',
                        name: 'project_title'
                    },
                    {
                        data: 'date_added',
                        name: 'date_added'
                    },
                    {
                        data: 'risk_name',
                        name: 'risk_name'
                    },
                    {
                        data: 'risk_status',
                        name: 'risk_status'
                    },
                    {
                        data: 'risk_type',
                        name: 'risk_type'
                    },
                    {
                        data: 'risk_rating',
                        name: 'risk_rating'
                    },
                    {
                        data: 'risk_owner_names',
                        name: 'risk_owner'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#riskTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeout: 5000
                    });
                    oTable.ajax.reload();
                };
                ajaxDeleteSweetAlert($url, successCallback);
            });

        });

    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    @can('manage-risk')
                    <a href="{{ route('risk.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Risk
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <section>
            <div class="card shadow-sm border rounded c-tabs-content active" id="risk-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="riskTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Project</th>
                                    <th>Date Added</th>
                                    <th>Risk Name</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Rating</th>
                                    <th>Risk Owner</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

@stop
