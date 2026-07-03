@extends('layouts.container')

@section('title', 'Events')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#event-index').addClass('active');

            var oTable = $('#eventTable').DataTable({
                scrollX: true,
                processing: true,
                serverside: true,
                ajax: "{{route('event.index')}}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'project_title',
                        name: 'project_title'
                    },
                    {
                        data: 'event_name',
                        name: 'event_name'
                    },
                    {
                        data: 'event_type',
                        name: 'event_type'
                    },
                    {
                        data: 'event_organized_by',
                        name: 'event_organized_by'
                    },
                    {
                        data: 'from_date',
                        name: 'from_date'
                    },
                    {
                        data: 'to_date',
                        name: 'to_date'
                    },
                    {
                        data: 'total_participants',
                        name: 'total_participants'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#eventTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeout: 5000
                    });
                    oTable.ajax.reload();
                };
                ajaxDeleteSweetAlert($url, successCallback);
            });

        });

    </script>
@endsection

@section('page-content')
<div class="m-content">
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    @can('manage-event')
                    <a href="{{ route('event.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Event
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <section>
            <div class="card shadow-sm border rounded c-tabs-content active" id="event-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="eventTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Project</th>
                                    <th>Event Name</th>
                                    <th>Event Type</th>
                                    <th>Organized By</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Total Participants</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

@stop
