@extends('layouts.container')

@section('title', 'HR Events')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#hr-event-index').addClass('active');

            var oTable = $('#hrEventTable').DataTable({
                scrollX: true,
                processing: true,
                serverside: true,
                ajax: "{{route('hr-event.index')}}",
                columnDefs: [
                    {
                        targets: [3, 4],
                        render: function(data, type, row) {
                            if (row.event_type === 'Orientation') return '-';
                            return data ?? '-';
                        }
                    },
                    {
                        targets: [5],
                        render: function(data, type, row) {
                            if (row.event_type === 'Recruitment') return '-';
                            return data ?? '-';
                        }
                    }
                ],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'event_date',
                        name: 'event_date'
                    },
                    {
                        data: 'event_type',
                        name: 'event_type'
                    },
                    {
                        data: 'vacancy_for_positions',
                        name: 'vacancy_for_positions'
                    },
                    {
                        data: 'project_title',
                        name: 'project_title'
                    },
                    {
                        data: 'orientation_title',
                        name: 'orientation_title'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#hrEventTable').on('click', '.delete-record', function(e) {
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
                    @can('manage-hr-event')
                    <a href="{{ route('hr-event.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New HR Event
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <section>
            <div class="card shadow-sm border rounded c-tabs-content active" id="hr-event-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="hrEventTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Event Date</th>
                                    <th>Event Type</th>
                                    <th>Vacancy For Positions</th>
                                    <th>Project</th>
                                    <th>Orientation Title</th>
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
