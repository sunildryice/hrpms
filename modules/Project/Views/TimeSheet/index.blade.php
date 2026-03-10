@extends('layouts.container')

@section('title', 'Timesheet')

@section('page_css')
    <style>
        .wrap-text {
            min-width: 200px;
            max-width: 400px;
            word-break: break-word;
            white-space: pre-wrap;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#timesheets-index').addClass('active');


            var oTable = $('#TimeSheetTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('timesheet.index') }}",
                order: [[0, 'desc']],
                columns: [{
                        data: 'timesheet_date_display',
                        name: 'timesheet_date_display',
                        render: function(data, type, row, meta) {
                            // Hide duplicate dates except for the first row in each group
                            if (type === 'display') {
                                var api = meta.settings;
                                var rowIdx = meta.row;
                                var prevDate = '';
                                if (rowIdx > 0) {
                                    prevDate = api.json.data[rowIdx - 1].timesheet_date_display;
                                }
                                if (prevDate === data) {
                                    return '';
                                }
                            }
                            return data;
                        }
                    },
                    {
                        data: 'project_id',
                        name: 'project_id'
                    },
                    {
                        data: 'activity_id',
                        name: 'activity_id',
                        className: 'wrap-text'
                    },
                    {
                        data: 'hours_spent',
                        name: 'hours_spent'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        className: 'wrap-text'
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
                ],
                rowGroup: {
                    dataSrc: 'timesheet_date'
                },
                drawCallback: function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var lastDate = null;
                    var rowspan = 1;
                    var dateColumnIndex = 0; // DATE column is first
                    api.column(dateColumnIndex, {
                        page: 'current'
                    }).data().each(function(date, i) {
                        if (lastDate === date) {
                            $(rows).eq(i).find('td').eq(dateColumnIndex).remove();
                            rowspan++;
                        } else {
                            if (rowspan > 1) {
                                $(rows).eq(i - rowspan).find('td').eq(dateColumnIndex).attr(
                                    'rowspan', rowspan);
                            }
                            lastDate = date;
                            rowspan = 1;
                        }
                    });
                    // Handle last group
                    if (rowspan > 1) {
                        $(rows).eq(api.column(dateColumnIndex, {
                            page: 'current'
                        }).data().length - rowspan).find('td').eq(dateColumnIndex).attr('rowspan',
                            rowspan);
                    }
                }
            });


            $(document).on('click', '.open-timesheet-modal-form', function(e) {
                e.preventDefault();
                $('#timeSheetModal').find('.modal-content').html('');
                $('#timeSheetModal').modal('show')
                    .find('.modal-content')
                    .load($(this).attr('href') || $(this).data('href'), function(response, status) {
                        if (status === 'error') {
                            toastr.error('Could not load form');
                            return;
                        }

                        var $form = $('#timeSheetModal').find('#TimeSheetForm');
                        if ($form.length) {
                            // select2 for dropdowns
                            $form.find('.select2').select2({
                                dropdownParent: $('#timeSheetModal'),
                                width: '100%'
                            });

                            // datepicker initialization (same as create)
                            $form.find('[name="timesheet_date"]').datepicker({
                                language: 'en-GB',
                                autoHide: true,
                                format: 'yyyy-mm-dd',
                                zIndex: 2048,
                                endDate: new Date(),
                                todayHighlight: true,
                                todayBtn: 'true'
                            });
                        }
                    });
            });

            // handle create/edit form submission via AJAX
            $(document).on('submit', '#TimeSheetForm', function(e) {
                e.preventDefault();
                var form = this;
                var url = $(form).attr('action');
                var method = $(form).attr('method') || 'POST';
                var formData = new FormData(form);

                // send regardless of PUT override (server handles method override)
                ajaxSubmitFormData(url, method, formData, function(response) {
                    $('#timeSheetModal').modal('hide');
                    toastr.success(response.message || 'Saved successfully');
                    oTable.ajax.reload();
                });
            });

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
                    <a href="{{ route('timesheet.create') }}" class="btn btn-primary btn-sm" rel="tooltip"
                        title="Add TimeSheet">
                        <i class="bi-plus"></i> Add New
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="TimeSheetTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.date') }}</th>
                                <th>{{ __('label.project') }}</th>
                                <th class="wrap-text">{{ __('label.activity') }}</th>
                                <th>Hours Spent</th>
                                <th class="wrap-text">{{ __('label.description') }}</th>
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

    {{-- modal for timesheet forms --}}
    <div class="modal fade" id="timeSheetModal" tabindex="-1" aria-labelledby="timeSheetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{-- content loaded dynamically --}}
            </div>
        </div>
    </div>
@stop
