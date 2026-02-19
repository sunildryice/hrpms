@extends('layouts.container')

@section('title', 'Projects')
@php
    $active = 0;
    $label = 'Inactive ';
    if (array_key_exists('active', $requestData)) {
        $active = $requestData['active'] == 1 ? 0 : 1;
        $label = $requestData['active'] == 1 ? 'Inactive ' : 'Active';
    }
@endphp
@section('page_css')
    <style>
        .wrap-text {
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#project-index').addClass('active');

            var oTable = $('#projectTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('project.index', ['active=' . !$active]) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                        className: 'wrap-text'
                    },
                    {
                        data: 'short_name',
                        name: 'short_name'
                    },
                    {
                        data: 'team_lead_id',
                        name: 'team_lead_id'
                    },
                    {
                        data: 'focal_person_id',
                        name: 'focal_person_id'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'completion_date',
                        name: 'completion_date'
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


            $('#projectTable').on('click', '.cancel-record', function(e) {
                e.preventDefault();
                let url = $(this).attr('data-href');
                let number = $(this).attr('data-number');
                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    oTable.ajax.reload();
                };
                ajaxTextSweetAlert(url, 'POST', `Cancel ${number}?`, 'Remarks', 'log_remarks',
                    successCallback);
            })

            $('#projectTable').on('click', '.delete-record', function(e) {
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
    <div class="container-fluid">
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
                <a href="{!! route('project.index', ['active=' . $active]) !!}" class="btn btn-secondary btn-sm">
                    View {!! $label !!} Projects <i class="fa fa-lg fa-flip-horizontal"></i>
                </a>
                @can('manage-pms')
                    <div class="add-info justify-content-end">
                        <a href="{!! route('project.create') !!}" class="btn btn-primary btn-sm" rel="tooltip" title="Add Project">
                            <i class="bi-plus"></i> Add New</a>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="projectTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th class="wrap-text">{{ __('label.project') }}</th>
                                <th>{{ __('label.short-name') }}</th>
                                <th>{{ __('label.team-lead') }}</th>
                                <th>{{ __('label.focal-person') }}</th>
                                <th>{{ __('label.start-date') }}</th>
                                <th>{{ __('label.completion-date') }}</th>
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
