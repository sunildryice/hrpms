@extends('layouts.container')

@section('title', 'Distribution Item Details')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#inventories-distribution-menu').addClass('active');

            var assetTable = $('#assetTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventories.assets.index', [$inventory->id]) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'asset_number',
                        name: 'asset_number',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'purchase_date',
                    //     name: 'purchase_date',
                    //     orderable: false,
                    //     searchable: false
                    // },
                    {
                        data: 'serial_number',
                        name: 'serial_number',
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'model_number',
                        name: 'model_number'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ]
            });


        $(document).on('click', '.open-asset-edit-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('assetEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        // title: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'The attachment title is required.',
                        //         },
                        //     },
                        // },
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
                    let data = new FormData(form);
                    let url  = form.getAttribute('action');
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        assetTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });


        $(document).on('click', '.open-direct-dispatch-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('directDispatchForm');
                $(form).find(".select2").select2({
                    dropdownParent: $('.modal'),
                    width: '100%',
                    dropdownAutoWidth: true
                });
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        purpose: {
                            validators: {
                                notEmpty: {
                                    message: 'Purpose is required',
                                },
                            },
                        },
                        quantity: {
                            validators: {
                                notEmpty: {
                                    message: 'Quantity is required',
                                },
                                digits: {
                                    message: 'Quantity can only be numbers',
                                }
                            },
                        },
                        office_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select an office',
                                }
                            }
                        },
                        approver_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select an approver',
                                }
                            }
                        }
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
                        // oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
            });
        });
    });
    </script>
@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="container-fluid">

            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{!! route('inventories.distribution.index') !!}" class="text-decoration-none">Distribution Items</a>
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
                                Inventory Details
                            </div>
                            <div class="card-body">
                                <div class="p-1">
                                    <ul class="list-unstyled list-py-2 text-dark mb-0">
                                        <li class="pb-2"><span
                                                class="card-subtitle text-uppercase text-primary">About</span></li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-truck dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getSupplierName() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Supplier"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-calendar-date dropdown-item-icon"></i></div>
                                                <div class="d-content-section">{{ $inventory->getPurchaseDate() }}</div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Purchase Date"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-calendar-date dropdown-item-icon"></i></div>
                                                <div class="d-content-section">{{ $inventory->getDistributionType() }}</div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Distribution Type"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><small class="text-muted">NPR</small></div>
                                                <div class="d-content-section"> {!! $inventory->getUnitPrice() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Unit Price"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><small class="text-muted">NPR</small></div>
                                                <div class="d-content-section"> {!! $inventory->getTotalPrice() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Total Price"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><small class="text-muted">NPR</small></div>
                                                <div class="d-content-section"> {!! $inventory->getVatAmount() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="VAT Amount"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><small class="text-muted">NPR</small></div>
                                                <div class="d-content-section"> {!! $inventory->getTotalAmount() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Total Amount"></span>
                                        </li>

                                        {{-- <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-activity dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getActivityCode() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Activity Code"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-123 dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getAccountCode() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Account Code"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-currency-dollar dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getDonorCode() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Donor Code"></span>
                                        </li> --}}
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-gear-wide-connected dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getExecutionType() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Execution Type"></span>
                                        </li>

                                        @if ($inventory->specification)
                                            <li class="pt-4 pb-2"><span
                                                    class="card-subtitle text-uppercase text-primary">Specification</span>
                                            </li>
                                            <li class="position-relative">
                                                <div class="d-flex gap-2 align-items-start">
                                                    <div class="icon-section"><i
                                                            class="bi-chat-dots dropdown-item-icon"></i></div>
                                                    <div class="d-content-section"> {!! $inventory->specification !!}</div>
                                                </div>
                                                <span class="stretched-link" rel="tooltip" title="Specification"></span>
                                            </li>
                                        @endif

                                        @if ($inventory->getAvailableQuantity() > 0 &&
                                        $inventory->getDistributionType() == 'Office Use' &&
                                        $inventory->getConsumableType() == 'Consumable')
                                            <li class="pt-4 pb-2 d-flex flex-row justify-content-start">
                                                <a data-toggle="modal" class="btn btn-sm btn-outline-primary open-direct-dispatch-modal-form"
                                                href="{{route('good.requests.direct.dispatch.create', $inventory->id)}}">
                                                    Direct Dispatch <i class="bi bi-arrow-up-right-circle"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Assets
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="assetTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{ __('label.sn') }}</th>
                                                        <th scope="col">{{ __('label.asset-number') }}</th>
                                                        {{-- <th scope="col">{{ __('label.purchase-date') }}</th> --}}
                                                        <th scope="col">{{ __('label.serial-number') }}</th>
                                                        <th scope="col">{{ __('label.item') }}</th>
                                                        <th scope="col">{{ __('label.model-number') }}</th>
                                                        <th scope="col">{{ __('label.brand') }}</th>
                                                        <th scope="col">{{ __('label.specification') }}</th>
                                                        <th>{{ __('label.action') }}</th>
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
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
