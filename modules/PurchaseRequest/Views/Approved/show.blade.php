@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Purchase Request Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-purchase-requests-menu').addClass('active');

            var oTable = $('#purchaseOrderTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.purchase.requests.orders.index', $purchaseRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'order_number',
                        name: 'order_number'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'order_date',
                        name: 'order_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });

            $('#purchaseOrderTable').on('click', '.delete-record', function(e) {
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

            var grnTable = $('#grnTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.purchase.requests.grns.index', $purchaseRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'request_number',
                        name: 'request_number'
                    },
                    {
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'received_date',
                        name: 'received_date'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'grn_amount',
                        name: 'grn_amount'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'sticky-col'
                    },
                ]
            });

            $('#grnTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    grnTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-order-modal-form', function(e) {
                console.log('modal open');
                e.preventDefault();
                $('#purchaseOrderCombineModal').find('.modal-content').html('');
                $('#purchaseOrderCombineModal').modal('show').find('.modal-content').load($(this).attr(
                        'href'),
                    function() {
                        const form = document.getElementById('purchaseOrderCombineForm');
                        $(form).find(".select2").each(function() {
                            $(this)
                                .wrap("<div class=\"position-relative\"></div>")
                                .select2({
                                    dropdownParent: $(this).parent(),
                                    width: '100%',
                                    dropdownAutoWidth: true
                                });
                        });
                        const fv = FormValidation.formValidation(form, {
                                fields: {
                                    purchase_order_id: {
                                        validators: {
                                            notEmpty: {
                                                message: 'Purchase Order is required.',
                                            },
                                        },
                                    },
                                },
                                plugins: {
                                    trigger: new FormValidation.plugins.Trigger(),
                                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                                    submitButton: new FormValidation.plugins.SubmitButton(),
                                    icon: new FormValidation.plugins.Icon({
                                        valid: 'bi bi-check2-square',
                                        invalid: 'bi bi-x-lg',
                                        validating: 'bi bi-arrow-repeat',
                                    }),
                                },
                            })
                            .on('core.form.valid', function(event) {
                                form.submit();
                            });
                    });
            });

            var grnTable = $('#poGrnTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('approved.purchase.requests.orders.grns.index', $purchaseRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'request_number',
                        name: 'request_number'
                    },
                    {
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'received_date',
                        name: 'received_date'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'grn_amount',
                        name: 'grn_amount'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'sticky-col'
                    },
                ]
            });
        });
    </script>
@endsection
@section('page-content')


    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('approved.purchase.requests.index') }}"
                                class="text-decoration-none text-dark">Purchase
                                Requests</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Purchase Request Details
                    </div>
                    @include('PurchaseRequest::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Purchase Request Items
                    </div>
                    <div class="card-body">
                        @include('PurchaseRequest::Partials.pr-items')
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        <div class="d-flex align-items-center add-info justify-content-between">
                            <span> {{ __('label.purchase-orders') }}</span>
                            @if ($remainingItemsCount > 0)
                                <div class="ml-auto">
                                    <a class="btn btn-primary btn-sm text-capitalize" href="{!! route('approved.purchase.requests.orders.create', $purchaseRequest->id) !!}">
                                        <i class="bi-plus"></i> Add New Order
                                    </a>
                                    &emsp;
                                    <button class="btn btn-primary btn-sm text-capitalize open-order-modal-form"
                                        href="{!! route('purchase.requests.orders.combine.create', $purchaseRequest->id) !!}">
                                        <i class="bi-plus"></i> Add to Other Existing Orders
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="purchaseOrderTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.order-number') }}</th>
                                        <th scope="col">{{ __('label.supplier') }}</th>
                                        <th scope="col">{{ __('label.order-date') }}</th>
                                        <th scope="col">{{ __('label.status') }}</th>
                                        <th style="width: 150px">{{ __('label.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold">
                        <div class="d-flex align-items-center add-info justify-content-between">
                            <span> {{ __('label.grn') }}</span>
                            <a class="btn btn-primary btn-sm text-capitalize" href="{!! route('approved.purchase.requests.grns.create', $purchaseRequest->id) !!}">
                                <i class="bi-plus"></i> Add New GRN
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="grnTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.request-number') }}</th>
                                        <th scope="col">{{ __('label.grn-number') }}</th>
                                        <th scope="col">{{ __('label.invoice-number') }}</th>
                                        <th scope="col">{{ __('label.grn-date') }}</th>
                                        <th scope="col">{{ __('label.supplier') }}</th>
                                        <th scope="col">{{ __('label.grn-amount') }}</th>
                                        <th scope="col">{{ __('label.status') }}</th>
                                        <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if(true)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <div class="d-flex align-items-center add-info justify-content-between">
                                <span>Purchase Order {{ __('label.grn') }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="poGrnTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">{{ __('label.order-number') }}</th>
                                            <th scope="col">{{ __('label.grn-number') }}</th>
                                            <th scope="col">{{ __('label.invoice-number') }}</th>
                                            <th scope="col">{{ __('label.grn-date') }}</th>
                                            <th scope="col">{{ __('label.supplier') }}</th>
                                            <th scope="col">{{ __('label.grn-amount') }}</th>
                                            <th scope="col">{{ __('label.status') }}</th>
                                            <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @include('Attachment::list', [
                    'modelType' => 'Modules\PurchaseRequest\Models\PurchaseRequest',
                    'modelId' => $purchaseRequest->id,
                ])

                <div class="card">
                    <div class="card-header fw-bold">
                        Purchase Process
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="c-b">
                                @foreach ($purchaseRequest->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person-circle fs-5"></i>
                                        </div>
                                        <div class="w-100">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                <div
                                                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                    <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                    <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                </div>
                                                <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <p class="text-justify comment-text mb-0 mt-1">
                                                {{ $log->log_remarks }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <div class="modal fade" id="purchaseOrderCombineModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="purchaseOrderCombineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
@stop
