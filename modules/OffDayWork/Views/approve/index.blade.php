@extends('layouts.container')

@section('title', 'Approve Requests')

@section('page_css')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
            word-break: break-word;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu')
                .find('#approve-off-day-work-approve')
                .addClass('active');

            if ($.fn.DataTable.isDataTable('#OffDayWorkTable')) {
                $('#OffDayWorkTable').DataTable().destroy();
            }

            var oTable = $('#OffDayWorkTable').DataTable({
                responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: "{{ route('approve.off.day.work.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'request_id',
                        name: 'request_id'
                    },
                    {
                        data: 'employee',
                        name: 'employee'
                    },
                    {
                        data: 'project',
                        name: 'project',
                        className: 'wrap-text'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date'
                    },
                    {
                        data: 'date',
                        name: 'date'
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

        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 ">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>

    </div>
    <div class="card shadow-sm border rounded c-tabs-content active">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="OffDayWorkTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.request-id') }}</th>
                            <th>{{ __('label.employee-name') }}</th>
                            <th>{{ __('label.project-name') }}</th>
                            <th>{{ __('label.request-date') }}</th>
                            <th>{{ __('label.date') }}</th>
                            <th style="width: 140px;">{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
@stop
