@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'GRN Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-grns-menu').addClass('active');
        });
        $(document).on('click', '.open-item-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('grnInventoryForm');
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
                        distribution_type_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Distribution type is required',
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
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        //
                        $('#grnItemTable').find('#grnItem_' + response.grnItem.id).html('');
                        if (response.itemCount == 0) {
                            $('#grnItemTable').find('#grnItemBulk').html('');
                        }
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $('[name="expiry_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                    startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
                });

                $('[name="purchase_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                    endDate: '{{ date('Y-m-d') }}',
                });
            });
        });

        $(document).on('click', '.open-item-bulk-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('grnInventoryBulkForm');
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
                        distribution_type_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Distribution type is required',
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
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        response.grnItemsIds.forEach(element => {
                            $('#grnItemTable').find('#grnItem_' + element).html('');
                        });
                        $('#grnItemTable').find('#grnItemBulk').html('');
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $('[name="expiry_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                    startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')) }}',
                });

                $('[name="purchase_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                    endDate: '{{ date('Y-m-d') }}',
                });
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="p-3 m-content">
        <div class="container-fluid">

            <div class="pb-3 mb-3 page-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('approved.grns.index') }}" class="text-decoration-none">Good
                                        Receive Notes</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header fw-bold">
                                GRN Details
                            </div>
                            @include('Grn::Partials.detail')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                GRN Items
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="grnItemTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{ __('label.sn') }}</th>
                                                        <th scope="col">{{ __('label.item') }}</th>
                                                        <th scope="col">{{ __('label.specification') }}</th>
                                                        <th scope="col">{{ __('label.unit') }}</th>
                                                        <th scope="col">{{ __('label.quantity') }}</th>
                                                        <th scope="col">{{ __('label.unit-price') }}</th>
                                                        <th scope="col">{{ __('label.amount') }}</th>
                                                        <th scope="col">{{ __('label.discount') }}</th>
                                                        <th scope="col">{{ __('label.vat-amount') }}</th>
                                                        <th scope="col">{{ __('label.total-amount') }}</th>
                                                        @if ($authUser->can('manage-inventory'))
                                                            <th scope="col" class="sticky-col">{{ __('label.action') }}
                                                            </th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($grn->grnItems as $index => $grnItem)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $grnItem->getItemName() }}</td>
                                                            <td>{{ $grnItem->grnitemable?->specification }}</td>
                                                            <td>{{ $grnItem->getUnitName() }}</td>
                                                            <td>{{ $grnItem->quantity }}</td>
                                                            <td>{{ $grnItem->unit_price }}</td>
                                                            <td>{{ $grnItem->total_price }}</td>
                                                            <td>{{ $grnItem->discount_amount }}</td>
                                                            <td>{{ $grnItem->vat_amount }}</td>
                                                            <td>{{ $grnItem->total_amount }}</td>
                                                            @if ($authUser->can('manage-inventory'))
                                                                <td id="grnItem_{{ $grnItem->id }}" class="sticky-col">
                                                                    @if ($authUser->can('createInventory', $grnItem))
                                                                        <a data-toggle="modal"
                                                                            class="btn btn-outline-primary btn-sm open-item-modal-form"
                                                                            rel="tooltip" title="Create Inventory"
                                                                            href="{{ route('approved.grns.items.inventory.create', [$grnItem->grn_id, $grnItem->id]) }}">
                                                                            <i class="bi bi-forward-fill"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    @if ($grn->grnItems->count())
                                                        <tr>
                                                            <td colspan="6">{!! __('label.total-amount') !!}</td>
                                                            <td>{{ $grn->sub_total }}</td>
                                                            <td>{{ $grn->discount_amount }}</td>
                                                            <td>{{ $grn->vat_amount }}</td>
                                                            <td>{{ $grn->total_amount }}</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="9">{!! __('label.tds-amount-less') !!}</td>
                                                            <td>{{ $grn->tds_amount }}</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="9">Net Payable Amount</td>
                                                            <td>{{ $grn->grn_amount }}</td>
                                                            @if ($authUser->can('manage-inventory'))
                                                                <td id="grnItemBulk" class="sticky-col">
                                                                    @if ($authUser->can('createBulkInventory', $grn))
                                                                        <a data-toggle="modal"
                                                                            class="btn btn-outline-primary btn-sm open-item-bulk-modal-form"
                                                                            rel="tooltip" title="Create Inventory in Bulk"
                                                                            href="{{ route('approved.grns.items.inventory.bulk.create', $grn->id) }}">
                                                                            <i class="bi bi-forward-fill"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('Grn::Partials.summary')
                    </div>
                </div>
        </div>
        </section>

    </div>
    </div>
@stop
