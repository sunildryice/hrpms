@extends('layouts.container')

@section('title', 'Transportation Bills')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#transportation-bills-menu').addClass('active');

            var oTable = $('#transportationBillTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('transportation.bills.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'bill_date',
                        name: 'bill_date'
                    },
                    {
                        data: 'shipper_name',
                        name: 'shipper_name'
                    },
                    {
                        data: 'shipper_address',
                        name: 'shipper_address'
                    },
                    {
                        data: 'consignee_name',
                        name: 'consignee_name'
                    },
                    {
                        data: 'consignee_address',
                        name: 'consignee_address'
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
                        searchable: false
                    },
                ]
            });

            $('#transportationBillTable').on('click', '.delete-record', function(e) {
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
        });
    </script>
@endsection
@section('page-content')

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
                <div class="add-info justify-content-end">
                    <a href="{{ route('transportation.bills.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi-plus"></i> New Transportation Bill
                    </a>
                </div>
            </div>
        </div>
        <div class="card" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="transportationBillTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.bill-date') }}</th>
                                <th>{{ __('label.shipper') }}</th>
                                <th>{{ __('label.shipper-address') }}</th>
                                <th>{{ __('label.consignee') }}</th>
                                <th>{{ __('label.consignee-address') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="table-responsive">

                    </div>
                </div>
            </div>
        </div>
        @stop
