@extends('layouts.container')

@section('title', 'Pending Attendance')

@section('page_js')
    <script>
        $(function() {
            $('#navbarVerticalMenu').find('#pending-attendance-menu').addClass('active');

            var oTable = $('#pendingAttendanceTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('attendance.pending.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'employee_code',
                        name: 'employee_code'
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
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'month',
                        name: 'month'
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
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endsection

@section('page-content')

            <div class="pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="#" class="text-decoration-none text-dark">{{ __('label.attendance') }}</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless" id="pendingAttendanceTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Employee Name</th>
                                    <th>Employee Code</th>
                                    <th>Supervisor</th>
                                    <th>Duty Station</th>
                                    <th>{{ __('label.year') }}</th>
                                    <th>{{ __('label.month') }}</th>
                                    <th style="width:120px;">Status</th>
                                    <th style="width:95px;">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
@stop
