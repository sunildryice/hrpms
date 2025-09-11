@extends('layouts.container')

@section('title', 'Edit Purchase Request')

@section('page_js')
    <script type="text/javascript">
        var oTable = $('#purchaseRequestItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('purchase.requests.items.special.index', $purchaseRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'item',
                    name: 'item'
                },
                {
                    data: 'unit',
                    name: 'unit'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                // {data: 'district', name: 'district'},
                {
                    data: 'office',
                    name: 'office'
                },
                {
                    data: 'activity',
                    name: 'activity'
                },
                {
                    data: 'account',
                    name: 'account'
                },
                {
                    data: 'donor',
                    name: 'donor'
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

        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#special-purchase-requests-menu').addClass('active');
            const form = document.getElementById('purchaseRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                // fields: {
                //     required_date: {
                //         validators: {
                //             notEmpty: {
                //                 message: 'The required date is required',
                //             },
                //         },
                //     },
                // },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="district_id"]', function(e) {
                fv.revalidateField('district_id');
            });

            $('[name="required_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}',
            }).on('change', function(e) {
                fv.revalidateField('start_date');
            });
        });

        $('#purchaseRequestItemTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.purchaseItemCount) {
                    $('.open-forward-modal-form').show();
                } else {
                    $('.open-forward-modal-form').hide();
                }
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-item-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('purchaseRequestItemForm');
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
                        // item_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Item is required',
                        //         },
                        //     },
                        // },
                        // unit_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Unit is required',
                        //         },
                        //     },
                        // },
                        // quantity: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Quantity is required',
                        //         },
                        //         greaterThan: {
                        //             message: 'The value must be greater than or equal to 0.01',
                        //             min: 0.01,
                        //         },
                        //     },
                        // },
                        // unit_price: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Unit price is required',
                        //         },
                        //         greaterThan: {
                        //             message: 'The value must be greater than or equal to 0.01',
                        //             min: 0.01,
                        //         },
                        //     },
                        // },
                        // specification: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Specification is required',
                        //         },
                        //     },
                        // },
                        // district_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'District is required',
                        //         },
                        //     },
                        // },
                        office_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Office is required',
                                },
                            },
                        },
                        // activity_code_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Activity code is required',
                        //         },
                        //     },
                        // },
                        // account_code_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Account code is required',
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
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });

                        if (response.purchaseItemCount) {
                            $('.open-forward-modal-form').show();
                        } else {
                            $('.open-forward-modal-form').hide();
                        }
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="unit_price"]', function(e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="quantity"]', function(e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="item_id"]', function(e) {
                    $element = $(this);
                    var itemId = $element.val();
                    var htmlToReplace = '<option value="">Select Unit</option>';
                    $($element).closest('form').find('[name="unit_id"]').html(htmlToReplace);
                    if (itemId) {
                        var url = baseUrl + '/api/master/items/' + itemId;
                        var successCallback = function(response) {
                            response.units.forEach(function(unit) {
                                htmlToReplace += '<option value="' + unit.id +
                                    '" selected="selected">' + unit.title + '</option>';
                            });
                            $($element).closest('form').find('[name="unit_id"]').html(
                                htmlToReplace).trigger('change');
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    }
                    fv.revalidateField('item_id');
                }).on('change', '[name="activity_code_id"]', function(e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function(response) {
                            response.accountCodes.forEach(function(accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id +
                                    '">' + accountCode.title + ' ' + accountCode
                                    .description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(
                                htmlToReplace).trigger('change');
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(
                            htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="account_code_id"]', function(e) {
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="unit_id"]', function(e) {
                    fv.revalidateField('unit_id');
                });

                function calculateTotalPrice($element) {
                    quantity = $($element).closest('form').find('[name="quantity"]').val();
                    unitPrice = $($element).closest('form').find('[name="unit_price"]').val();
                    $($element).closest('form').find('.total_price').val(quantity * unitPrice);
                }
            });
        });

        $(document).on('click', '.open-forward-modal-form', function(e) {
            e.preventDefault();
            $('#purchaseRequestForwardModal').find('.modal-content').html('');
            $('#purchaseRequestForwardModal').modal('show').find('.modal-content').load($(this).attr('href'),
                function() {
                    const form = document.getElementById('forwardForm');
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
                            // reviewer_id: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'The reviewer is required.',
                            //         },
                            //     },
                            // },
                            // approver_id: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'The approver is required.',
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
                        $url = fv.form.action;
                        $form = fv.form;
                        data = $($form).serialize();
                        var successCallback = function(response) {
                            $('#purchaseRequestForwardModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            window.location.href = "{!! route('purchase.requests.index') !!}";
                        }
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });
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
                            <a href="{{ route('purchase.requests.index') }}" class="text-decoration-none text-dark">Purchase
                                Request</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <form action="{{ route('purchase.requests.special.update', $purchaseRequest->id) }}" id="purchaseRequestEditForm"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label required-label">Required
                                    Date </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                            @if ($errors->has('required_date')) is-invalid @endif"
                                readonly name="required_date"
                                value="{{ old('required_date') ?: $purchaseRequest->required_date->format('Y-m-d') }}" />
                            @if ($errors->has('required_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="required_date">{!! $errors->first('required_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Purpose </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('purpose')) is-invalid @endif" name="purpose"
                                readonly>{{ old('purpose') ?: $purchaseRequest->purpose }}</textarea>
                            @if ($errors->has('purpose'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="purpose">{!! $errors->first('purpose') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Delivery
                                    Instructions </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('delivery_instructions')) is-invalid @endif"
                                name="delivery_instructions" readonly>{{ old('delivery_instructions') ?: $purchaseRequest->delivery_instructions }}</textarea>
                            @if ($errors->has('delivery_instructions'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="delivery_instructions">{!! $errors->first('delivery_instructions') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Districts</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input class="form-control" type="text" value="{{ $purchaseRequest->getDistrictNames() }}"
                                disabled>
                        </div>
                    </div>

                    @if ($purchaseRequest->modification_number)
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationRemarks" class="m-0">Amendment Remarks</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea type="text" class="form-control @if ($errors->has('modification_remarks')) is-invalid @endif"
                                    name="modification_remarks" readonly>{{ old('modification_remarks') ?: $purchaseRequest->modification_remarks }}</textarea>
                                @if ($errors->has('modification_remarks'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="modification_remarks">{!! $errors->first('modification_remarks') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {!! csrf_field() !!}
                    {!! method_field('PUT') !!}
                    {{-- <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm" disabled>
                                        Update
                                    </button>
                    </div> --}}
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span>Items</span>
                        @if ($authUser->can('update', $purchaseRequest))
                            <button data-toggle="modal" class="btn btn-primary btn-sm open-item-modal-form"
                                href="{!! route('purchase.requests.items.create', $purchaseRequest->id) !!}" disabled><i class="bi-plus"></i> Add New Item
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="purchaseRequestItemTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.item') }}</th>
                                    <th scope="col">{{ __('label.unit') }}</th>
                                    <th scope="col">{{ __('label.quantity') }}</th>
                                    <th scope="col">{{ __('label.estimated-rate') }}</th>
                                    <th scope="col">{{ __('label.estimated-amount') }}</th>
                                    {{-- <th scope="col">{{ __('label.district') }}</th> --}}
                                    <th scope="col">{{ __('label.office') }}</th>
                                    <th scope="col">{{ __('label.activity') }}</th>
                                    <th scope="col">{{ __('label.account') }}</th>
                                    <th scope="col">{{ __('label.donor') }}</th>
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
                <div class="card-header fw-bold">Notes</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-9">
                            <ol type="i">
                                <li>Approval limits apply to thresholds specified in OHW
                                    policy.
                                </li>
                                <li>If the estimated price varies >10%, to the actual
                                    price, additional approval required.
                                </li>
                                <li>Please use separate PR for separate activity as can
                                    as possible.
                                </li>
                                <li>If PR for different districts use additional sheet
                                    to separate districts.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- @if ($purchaseRequest->status_id == config('constant.RETURNED_STATUS'))
                            <div class="card-body mt-0 pt-0">
                                <div class="card">
                                    <div class="card-header fw-bold">
                                        Remarks
                                    </div>
                                    <div class="card-body">
                                        {{ $purchaseRequest->latestLog->log_remarks }}
                                    </div>
                                </div>
                            </div>
                        @endif --}}

            <div class="justify-content-end d-flex gap-2">
                {{-- <button data-toggle="modal"
                                    @if (!$authUser->can('submit', $purchaseRequest)) style="display:none;"
                                    @endif
                                    class="btn btn-primary btn-sm open-forward-modal-form"
                                    href="{!! route('purchase.requests.forward.create', $purchaseRequest->id) !!}"
                            ><i class="bi-forward"></i> Forward
                            </button> --}}

                <a href="{!! route('approved.purchase.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>
    </section>

    <div class="modal fade" id="purchaseRequestForwardModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="purchaseRequestForwardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

@stop
