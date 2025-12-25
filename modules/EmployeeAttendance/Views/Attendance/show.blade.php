@extends('layouts.container')

@section('title', 'Attendance')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            // $('#navbarVerticalMenu').find('#attendance-index').addClass('active');


            var oTable = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('attendance.show', $employeeId) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    // {data: 'employee_name', name:'employee_name'},
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
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('click', '.amend-attendance', function() {
                let url = $(this).attr('data-href');
                let month = $(this).attr('data-month');
                let year = $(this).attr('data-year');
                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    oTable.ajax.reload();
                };
                ajaxTextSweetAlert(url, 'POST', `Amend attendance of ${month}, ${year}?`, 'Remarks',
                    'log_remarks', successCallback);
            });

            // Check In Today with Confirmation
            $(document).on('click', '.checkin-today-btn', function() {
                let date = $(this).data('date');
                let btn = $(this);

                Swal.fire({
                    title: 'Confirm Check In',
                    text: 'Are you sure you want to check in now?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#01aef0', 
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Check In',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="bi bi-hourglass-split"></i> Checking in...');

                        $.ajax({
                            url: "{{ route('attendance.checkin.today') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                date: date
                            },
                            success: function(response) {
                                toastr.success('Checked in at ' + response.time);
                                $('#today-attendance-action').html(`
                        <button class="btn btn-warning btn-sm checkout-today-btn" data-date="${date}">
                            <i class="bi bi-box-arrow-in-left"></i> Check Out
                        </button>
                    `);
                                oTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(
                                    '<i class="bi bi-box-arrow-in-right"></i> Check In Now'
                                );
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to check in');
                            }
                        });
                    }
                });
            });

            // Check Out Today with Confirmation
            $(document).on('click', '.checkout-today-btn', function() {
                let date = $(this).data('date');
                let btn = $(this);

                Swal.fire({
                    title: 'Confirm Check Out',
                    text: 'Are you sure you want to check out now?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Check Out',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="bi bi-hourglass-split"></i> Checking out...');

                        $.ajax({
                            url: "{{ route('attendance.checkout.today') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                date: date
                            },
                            success: function(response) {
                                toastr.success('Checked out at ' + response.time +
                                    ' on ' + response.worked_hours + ' hours worked'
                                );
                                $('#today-attendance-action').html(`
                                    <button class="btn btn-success btn-sm" disabled>
                                        <i class="bi bi-check-circle-fill"></i> Completed Today
                                    </button>
                                `);
                                oTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(
                                    '<i class="bi bi-box-arrow-in-left"></i> Check Out'
                                );
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to check out');
                            }
                        });
                    }
                });
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
                            <a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('profile.show') }}"
                                class="text-decoration-none text-dark">Profile</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>

            <div class="add-info justify-content-end">
                <div id="today-attendance-action">
                    @php
                        $today = now()->format('Y-m-d');
                        $employeeId = auth()->user()->employee->id;

                        $attendanceMaster = \Modules\EmployeeAttendance\Models\Attendance::where(
                            'employee_id',
                            $employeeId,
                        )
                            ->where('year', now()->year)
                            ->where('month', now()->month)
                            ->first();

                        $hasCheckIn = false;
                        $hasCheckOut = false;

                        if ($attendanceMaster) {
                            $todayDetail = $attendanceMaster
                                ->attendanceDetails()
                                ->where('attendance_date', $today)
                                ->first();

                            $hasCheckIn = $todayDetail && $todayDetail->checkin;
                            $hasCheckOut = $todayDetail && $todayDetail->checkout;
                        }
                    @endphp

                    @if ($hasCheckOut)
                        <button class="btn btn-success btn-sm" disabled>
                            <i class="bi bi-check-circle-fill"></i> Completed Today
                        </button>
                    @elseif ($hasCheckIn)
                        <button class="btn btn-warning btn-sm checkout-today-btn" data-date="{{ $today }}">
                            <i class="bi bi-box-arrow-in-left"></i> Check Out
                        </button>
                    @else
                        <button class="btn btn-primary btn-sm checkin-today-btn" data-date="{{ $today }}">
                            <i class="bi bi-box-arrow-in-right"></i> Check In
                        </button>
                    @endif
                </div>
            </div>

            {{-- @if (auth()->user()->can('add-attendance'))
            <div class="add-info justify-content-end">
                <a type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#createAttendanceModal">
                    <i class="bi-person-plus"></i> Add New Attendance
                </a>
            </div>
            @include('EmployeeAttendance::Attendance.create')
            @endif --}}
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="attendanceTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            {{-- <th>Employee Name</th> --}}
                            <th>{{ __('label.year') }}</th>
                            <th>{{ __('label.month') }}</th>
                            <th style="width:120px;">Process Status</th>
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
