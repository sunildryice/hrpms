@extends('layouts.container')

@section('title', 'Review Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-review-index').addClass('active');

            var oTable = $('#performanceReviewTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('performance.review.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'fiscal_year',
                        name: 'fiscal_year'
                    },
                    {
                        data: 'review_type',
                        name: 'review_type'
                    },
                    {
                        data: 'review_from',
                        name: 'review_from',
                    },
                    {
                        data: 'review_to',
                        name: 'review_to'
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
                    }
                ]
            });

            $('#performanceReviewTable').on('click', '.delete-record', function(e) {
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

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('performance.review.index') }}"
                                    class="text-decoration-none text-dark">Performance Review</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                {{-- <div class="add-info justify-content-end">
                    <a href="{{ route('performance.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Performance Review
                    </a>
                </div> --}}
            </div>
        </div>
        <div class="card" id="performance-review-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="performanceReviewTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Employee Name</th>
                                <th>Fiscal Year</th>
                                <th>Review Type</th>
                                <th>Review From</th>
                                <th>Review To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

@stop
