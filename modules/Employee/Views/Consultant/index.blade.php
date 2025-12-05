@extends('layouts.container')

@section('title', 'Consultants')
@php
    $active = 0;
    $label = 'Inactive ';
    if (array_key_exists('active', $requestData)) {
        $active = $requestData['active'] == 1 ? 0 : 1;
        $label = $requestData['active'] == 1 ? 'Inactive ' : 'Active';
    }
@endphp
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#consultant-menu').addClass('active');

            var oTable = $('#employeeTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('consultant.index', ['active=' . !$active]) }}",
                columns: [{
                    data: 'employee_code',
                    name: 'employee_code'
                },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'official_email_address',
                        name: 'official_email_address'
                    },
                    {
                        data: 'position',
                        name: 'position'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'supervisor',
                        name: 'supervisor'
                    },
                    {
                        data: 'duty_station',
                        name: 'duty_station'
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
                        className: 'sticky-col'
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
                            <a href="{!! route('dashboard.index') !!}"
                               class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#"
                                                       class="text-decoration-none text-dark">{{ __('label.human-resource') }}</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            @can('manage-employee')
                <div class="add-info d-flex flex-wrap gap-2">
                    <a href="{!! route('consultant.create') !!}" class="btn btn-primary btn-sm">
                        <i class="bi-person-plus"></i> Add New
                    </a>
                    <a href="{!! route('consultant.index', ['active=' . $active]) !!}" class="btn btn-secondary btn-sm">
                        View {!! $label !!} <i class="fa fa-lg fa-flip-horizontal"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="employeeTable">
                    <thead class="bg-light">
                    <tr>
                        <th class="" style="width:120px;">Consultant Code</th>
                        <th>{{ __('label.name') }}</th>
                        <th>{{ __('label.email-address') }}</th>
                        <th>{{ __('label.position') }}</th>
                        <th>{{ __('label.department') }}</th>
                        <th>{{ __('label.supervisor') }}</th>
                        <th>Duty station</th>
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
