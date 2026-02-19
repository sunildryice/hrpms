@extends('layouts.container')

@section('title', 'Attendance')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#self-attendance').addClass('active');

            var oTable = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('attendance.show', $employeeId) }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
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

            $(document).on('click', '.amend-attendance', function () {
                let url = $(this).attr('data-href');
                let month = $(this).attr('data-month');
                let year = $(this).attr('data-year');
                let successCallback = function (response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    oTable.ajax.reload();
                };
                ajaxTextSweetAlert(url, 'POST', `Amend attendance of ${month}, ${year}?`, 'Remarks',
                    'log_remarks', successCallback);
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
