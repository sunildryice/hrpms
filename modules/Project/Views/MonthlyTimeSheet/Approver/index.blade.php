@extends('layouts.container')
@section('title', 'Approve Monthly Timesheets')
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-monthly-timesheets-menu').addClass('active');
            var oTable = $('#MonthlyTimeSheetTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('approve.monthly-timesheet.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'month_name',
                        name: 'month_name'
                    },
                    {
                        data: 'total_hours',
                        name: 'total_hours'
                    },
                    {
                        data: 'projects',
                        name: 'projects',
                    },
                    {
                        data: 'status',
                        name: 'status',
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
            $('#MonthlyTimeSheetTable').on('click', '.cancel-record', function(e) {
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
            $('#MonthlyTimeSheetTable').on('click', '.delete-record', function(e) {
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
            // Open add/edit forms in modal (fallback if server doesn't emit .open-modal-form)
            $('#MonthlyTimeSheetTable').on('click', '.edit-record', function(e) {
                e.preventDefault();
                var href = $(this).attr('href') || $(this).attr('data-href');
                if (!href) return;
                $('#openModal').modal('show').find('.modal-content').load(href);
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
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="monthly-timesheet-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="MonthlyTimeSheetTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Month</th>
                                <th>Total Hours</th>
                                <th>Projects</th>
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
    </div>
@stop