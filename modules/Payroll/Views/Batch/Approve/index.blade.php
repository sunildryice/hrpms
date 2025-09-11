@extends('layouts.container')

@section('title', __('label.payroll-batches'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-payroll-batches-menu').addClass('active');

            var oTable = $('#payrollBatchTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('payroll.batches.approve.index') }}",
                columns: [{
                        data: 'fiscal_year',
                        name: 'fiscal_year',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'posted_date',
                        name: 'posted_date'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                ]
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#" class="text-decoration-none">{{ __('label.payroll') }}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="payrollBatchTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.fy') }}</th>
                                    <th scope="col">{{ __('label.pay-period') }}</th>
                                    <th scope="col">{{ __('label.posted-date') }}</th>
                                    <th scope="col">{{ __('label.description') }}</th>
                                    <th scope="col">{{ __('label.created-by') }}</th>
                                    <th scope="col">{{ __('label.status') }}</th>
                                    <th scope="col">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
