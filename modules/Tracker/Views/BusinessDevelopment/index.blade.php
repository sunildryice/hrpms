@extends('layouts.container')

@section('title', 'Business Development')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#business-development-index').addClass('active');

            var oTable = $('#businessDevelopmentTable').DataTable({
                scrollX: true,
                processing: true,
                serverside: true,
                ajax: "{{route('business-development.index')}}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'thematic_area',
                        name: 'thematic_area'
                    },
                    {
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'call_name',
                        name: 'call_name'
                    },
                    {
                        data: 'url',
                        name: 'url'
                    },
                    {
                        data: 'funding_agency',
                        name: 'funding_agency'
                    },
                    {
                        data: 'partnership_type',
                        name: 'partnership_type'
                    },
                    {
                        data: 'project_type',
                        name: 'project_type'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'result',
                        name: 'result'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#businessDevelopmentTable').on('click', '.delete-record', function(e) {
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
<div class="m-content">
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
                    @can('manage-business-development')
                    <a href="{{ route('business-development.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Business Development
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <section>
            <div class="card shadow-sm border rounded c-tabs-content active" id="business-development-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="businessDevelopmentTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Thematic Area</th>
                                    <th>Project Name</th>
                                    <th>Date</th>
                                    <th>Call Name</th>
                                    <th>URL</th>
                                    <th>Funding Agency</th>
                                    <th>Partnership Type</th>
                                    <th>Project Type</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                    <th>Remarks</th>
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
