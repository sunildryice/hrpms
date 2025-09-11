@extends('layouts.container')

@section('title', 'Employees Attendance')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#attendance-index').addClass('active');

            var oTable = $('#attendanceTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('attendance.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_code',
                        name: 'employee_code'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'designation',
                        name: 'designation'
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className:"sticky-col"
                    },
                ]
            });

            let error = {!!$errors!!};
            // console.log(error);
            if (error.attendance_file) {
                $('#importAttendanceModal').modal('show');
            }

            // $('#importAttendanceModal').show();

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
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                                </li>
                                {{-- <li class="breadcrumb-item"><a href="#"
                                        class="text-decoration-none">{{ __('label.attendance') }}</a></li> --}}
                                <li class="breadcrumb-item" aria-current="page">Attendance</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                    @if (auth()->user()->can('import-attendance'))
                    @include('EmployeeAttendance::Attendance.import')
                    <div class="mb-2">
                        <a type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#importAttendanceModal">
                            <i class="bi-fingerprint"></i> Import
                        </a>
                    </div>
                @endif
                </div>
            </div>



            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless" id="attendanceTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Employee Code</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Supervisor</th>
                                    <th>Duty Station</th>
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
