@extends('layouts.container')

@section('title', 'Memo')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#memo-menu').addClass('active');
        });

        var oTable = $('#memoTable').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('memo.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'memo_number',
                    name: 'memo_number'
                },
                {
                    data: 'memo_date',
                    name: 'memo_date'
                },
                {
                    data: 'subject',
                    name: 'subject'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'requester',
                    name: 'requester'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'sticky-col'
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

        $('#memoTable').on('click', '.amend-record', function(e) {
            e.preventDefault();
            let number = $(this).attr('data-number');
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                oTable.ajax.reload();
            }
            var confirmText = `Amend ${number}?`;
            ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
        });

        $('#memoTable').on('click', '.delete-record', function(e) {
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
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                    class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Memo</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Memo</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <a class="btn btn-primary btn-sm" href="{!! route('memo.create') !!}" rel="tooltip" title="Memo">
                        <i class="bi-plus"></i>Add New
                    </a>
                </div>
            </div>

        </div>
        <section class="registration">
            <div class="card shadow-sm border rounded c-tabs-content active" id="memo-table">
                {{-- <div class="card-header fw-bold">
                    <h3 class="m-0 fs-6">Meeting Hall Booking List</h3>
                </div> --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless" id="memoTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width:45px;"></th>
                                    <th>{{ __('label.memo-number') }}</th>
                                    <th>{{ __('label.date') }}</th>
                                    <th>{{ __('label.subject') }}</th>
                                    <th>{{ __('label.attachment') }}</th>
                                    <th>{{ __('label.requester') }}</th>
                                    <th class="sticky-col">{{ __('label.status') }}</th>
                                    <th style="width: 164px;">{{ __('label.action') }}</th>
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


@stop
