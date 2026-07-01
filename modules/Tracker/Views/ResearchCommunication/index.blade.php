@extends('layouts.container')

@section('title', 'Research Uptake & Communication')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#research-communication-index').addClass('active');

            var oTable = $('#researchCommunicationTable').DataTable({
                scrollX: true,
                processing: true,
                serverside: true,
                ajax: "{{route('research-communication.index')}}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type_of_publication',
                        name: 'type_of_publication'
                    },
                    {
                        data: 'publication_title',
                        name: 'publication_title'
                    },
                    {
                        data: 'date_of_publication',
                        name: 'date_of_publication'
                    },
                    {
                        data: 'project_title',
                        name: 'project_title'
                    },
                    {
                        data: 'post_title',
                        name: 'post_title'
                    },
                    {
                        data: 'posted_in',
                        name: 'posted_in'
                    },
                    {
                        data: 'date_posted',
                        name: 'date_posted'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#researchCommunicationTable').on('click', '.delete-record', function(e) {
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
                    @can('manage-research-communication')
                    <a href="{{ route('research-communication.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Record
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <section>
            <div class="card shadow-sm border rounded c-tabs-content active" id="research-communication-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="researchCommunicationTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th>Type of Publication</th>
                                    <th>Publication Title</th>
                                    <th>Publication Date</th>
                                    <th>Project</th>
                                    <th>Post Title</th>
                                    <th>Posted In</th>
                                    <th>Date Posted</th>
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
