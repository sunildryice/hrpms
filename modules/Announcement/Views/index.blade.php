@extends('layouts.container')

@section('title', 'Announcement')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#announcement-index').addClass('active');

            var oTable = $('#announcementTable').DataTable({
                scrollX: true,
                processing: true,
                serverside: true,
                ajax: "{{route('announcement.index')}}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'announcement_number',
                        name: 'announcement_number'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'published_date',
                        name: 'published_date'
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#announcementTable').on('click', '.delete-record', function(e) {
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
<div class="m-content p-3">
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
                    <a href="{{ route('announcement.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Announcement
                    </a>
                </div>
            </div>
        </div>

        <section>
            <div class="card shadow-sm border rounded c-tabs-content active" id="announcement-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="announcementTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Announcement No.</th>
                                    <th>Title</th>
                                    <th>Published Date</th>
                                    <th>Expiry Date</th>
                                    <th>Created By</th>
                                    <th>Attachment</th>
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
