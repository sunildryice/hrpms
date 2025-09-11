@extends('layouts.container')

@section('title', 'Attendance')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#attendance-index').addClass('active');

            var attendanceTable = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('attendance.view', $employeeId) }}",
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

            $('#attendanceTable').on('click', '.delete-record', function (e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function (response) {
                    toastr.success(response.message, 'Success', {timeOut: 5000});
                    attendanceTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
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
                                <li class="breadcrumb-item"><a href="{{route('attendance.index')}}"
                                        class="text-decoration-none text-dark">{{ __('label.attendance') }}</a></li>
                                <li class="breadcrumb-item" aria-current="page">{{$employeeName}}</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
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
