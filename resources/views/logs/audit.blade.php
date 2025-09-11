@extends('layouts.container')

@section('title', 'Audit Logs')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#audit-logs-menu').addClass('active');

            var oTable = $('#auditLogTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('audit.logs.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'user', name: 'user'},
                    {data: 'ip_address', name: 'ip_address'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'description', name: 'description'},
                    {data: 'action', name: 'action'},
                ]
            });

            $(document).on('click', '.open-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){});
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active">
            <div class="card-body">
                <table class="table" id="auditLogTable">
                    <thead>
                    <tr>
                        <th>{{ __('label.sn') }}</th>
                        <th scope="col">{{ __('label.name') }}</th>
                        <th scope="col">{{ __('label.ip-address') }}</th>
                        <th scope="col">{{ __('label.datetime') }}</th>
                        <th scope="col">{{ __('label.description') }}</th>
                        <th scope="col">{{ __('label.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@stop
