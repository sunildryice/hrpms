@extends('layouts.container')

@section('title', 'Event Completion Reports')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#event-completion-menu').addClass('active');

            var oTable = $('#eventCompletionTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('event.completion.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'venue',
                        name: 'venue'
                    },
                    {
                        data: 'district',
                        name: 'district'
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

            $('#event-completion-table').on('click', '.delete-record', function(e) {
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

            $('#event-completion-table').on('click', '.cancel-event-completion', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    oTable.ajax.reload();
                }
                var confirmText = 'Do you want to cancel this event completion ?';
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });

            $('#event-completion-table').on('click', '.amend-event-completion', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.reload();
                }
                var confirmText = 'Amend this event completion'
                ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
            });

            $('#event-completion-table').on('click', '.create-settlement', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    location.href = response.redirectUrl;
                }
                ajaxSweetAlert($url, 'POST', {}, 'Create Settlement', successCallback);
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
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Event Completion</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Event Completion</h4>
            </div>
            <div class="add-info justify-content-end">
                <a href="{!! route('event.completion.create') !!}" class="btn btn-primary btn-sm" rel="tooltip"
                    title="Add Event Completion">
                    <i class="bi-plus"></i> Add New</a>
            </div>
        </div>
    </div>

    <div class="card" id="event-completion-table">
        <div class="card-header fw-bold">Event Completion List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="eventCompletionTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:45px;">{{ __('label.sn') }}</th>
                            <th>{{ __('label.start-date') }}</th>
                            <th>{{ __('label.end-date') }}</th>
                            <th>{{ __('label.venue') }}</th>
                            <th>{{ __('label.district') }}</th>
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
