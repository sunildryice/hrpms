@extends('layouts.container')

@section('title', 'Timesheet')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#timesheets-index').addClass('active');

            var oTable = $('#TimeSheetTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('timesheet.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'timesheet_date',
                        name: 'timesheet_date'
                    },
                    {
                        data: 'hours_spent',
                        name: 'hours_spent'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
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


            $('#TimeSheetTable').on('click', '.cancel-record', function(e) {
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

            $('#TimeSheetTable').on('click', '.delete-record', function(e) {
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
            $('#TimeSheetTable').on('click', '.edit-record', function(e) {
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
                <div class="add-info justify-content-end">
                    <a href="" class="btn btn-primary btn-sm open-timesheet-modal-form" rel="tooltip"
                        title="Add TimeSheet">
                        <i class="bi-plus"></i> Add New</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="TimeSheetTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Timesheet Date</th>
                                <th>Hours Spent</th>
                                <th>{{ __('label.attachment') }}</th>
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
