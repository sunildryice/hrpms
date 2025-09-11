@extends('layouts.container')

@section('title', 'Lta Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#lta-menu').addClass('active');
        });

        var oTable = $('#ltaItemsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('lta.items.index', $lta->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'item',
                    name: 'item',
                    className: "item-column"
                },
                {
                    data: 'unit',
                    name: 'unit'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'specification',
                    name: 'specification'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ],
            // drawCallback: function() {
            //     let table = this[0];
            //     let footer = table.getElementsByTagName('tfoot')[0];
            //     if (!footer) {
            //         footer = document.createElement("tfoot");
            //         table.appendChild(footer);
            //     }

            //     let estimated_amount = this.api().column(4).data().reduce(function(a, b) {
            //         return parseFloat(a) + parseFloat(b);
            //     }, 0);

            //     estimated_amount = new Intl.NumberFormat('en-US').format(estimated_amount);

            //     footer.innerHTML = '';
            //     footer.innerHTML = `<tr>
        //                             <td colspan='4'>Total Tentative Amount</td>
        //                             <td colspan='6'>${estimated_amount}</td>
        //                         </tr>`;
            // },
        });

        $('#ltaItemsTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 3000
                });
                // submitPackageForm();
                if (response.ltaItemCount) {
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
                const form = document.getElementById('ltaItemFrom');
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
                        item_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Item is required',
                                },
                            },
                        },
                        unit_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Unit is required',
                                },
                            },
                        },
                        unit_price: {
                            validators: {
                                notEmpty: {
                                    message: 'Unit price is required',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0.01',
                                    min: 0.01,
                                },
                            },
                        },
                        specification: {
                            validators: {
                                notEmpty: {
                                    message: 'Specification is required',
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
                            timeOut: 3000
                        });
                        // submitPackageForm();
                        if (response.packageItemCount) {
                            $('.open-forward-modal-form').show();
                        } else {
                            $('.open-forward-modal-form').hide();
                        }
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('input', '[name="unit_price"]', function(e) {
                    calculateTotalPrice(this);
                }).on('input', '[name="quantity"]', function(e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="vat_applicable"]', function(e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="item_id"]', function(e) {
                    $element = $(this);
                    var itemId = $element.val();
                    var htmlToReplace = '<option value="">Select Unit</option>';
                    $($element).closest('form').find('[name="unit_id"]').html(
                        htmlToReplace);
                    if (itemId) {
                        var url = baseUrl + '/api/master/items/' + itemId;
                        var successCallback = function(response) {
                            response.units.forEach(function(unit) {
                                htmlToReplace += '<option value="' + unit
                                    .id +
                                    '" selected="selected">' + unit.title +
                                    '</option>';
                            });
                            $($element).closest('form').find('[name="unit_id"]')
                                .html(
                                    htmlToReplace).trigger('change');
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback,
                            errorCallback);
                    }
                    fv.revalidateField('item_id');
                }).on('change', '[name="unit_id"]', function(e) {
                    fv.revalidateField('unit_id');
                }).on('change', '[name="activity_code_id"]', function(e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' +
                            activityCodeId;
                        var successCallback = function(response) {
                            response.accountCodes.forEach(function(accountCode) {
                                htmlToReplace += '<option value="' +
                                    accountCode.id +
                                    '">' + accountCode.title + ' ' +
                                    accountCode
                                    .description + '</option>';
                            });
                            $($element).closest('form').find(
                                '[name="account_code_id"]').html(
                                htmlToReplace).trigger('change');
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback,
                            errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]')
                            .html(
                                htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="account_code_id"]', function(e) {
                    fv.revalidateField('account_code_id');
                });

                function calculateTotalPrice($element) {
                    quantity = parseFloat($($element).closest('form').find('[name="quantity"]').val());
                    unitPrice = parseFloat($($element).closest('form').find('[name="unit_price"]').val());
                    unitPrice = isNaN(unitPrice) ? 0 : unitPrice;
                    quantity = isNaN(quantity) ? 0 : quantity;
                    billAmount = unitPrice * quantity;
                    $($element).closest('form').find('[name="total_price"]').val(billAmount);
                    vatFlag = $($element).closest('form').find('[name="vat_applicable"]').prop('checked');
                    vatAmount = vatFlag ? parseFloat(billAmount * vatPercentage / 100) : 0;
                    console.log(vatPercentage)
                    $($element).closest('form').find('[name="vat_amount"]').val(vatAmount);
                    $($element).closest('form').find('[name="total_amount"]').val(vatAmount + billAmount);
                }


            });
        });
    </script>
@endsection
@section('page_css')
    <style>
        .item-column {
            max-width: 500px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>
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
                                    <a href="{{ route('lta.index') }}" class="text-decoration-none">LTA Contracts</a>
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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Lta Details
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="display table table-bordered table-condensed">
                                                <tr>
                                                    <td class="gray-bg">Supplier</td>
                                                    <td>{{ $lta->getSupplierName() }}</td>
                                                    <td class="gray-bg">VAT/PAN No.</td>
                                                    <td>{{ $lta->getVATPANNo() }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Contract Number</td>
                                                    <td>{{ $lta->contract_number }}</td>
                                                    <td class="gray-bg">Contract Date</td>
                                                    <td>{{ $lta->getContractDate() }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Description</td>
                                                    <td colspan="3">{{ $lta->description }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Start Date</td>
                                                    <td>{{ $lta->getStartDate() }}</td>
                                                    <td class="gray-bg">End Date</td>
                                                    <td>{{ $lta->getEndDate() }}</td>
                                                </tr>
                                                <tr>

                                                    <td class="gray-bg">Focal Person</td>
                                                    <td>{{ $lta->getFocalPersonName() }}</td>
                                                    <td class="gray-bg">Contract Attachment</td>
                                                    <td>
                                                        @if (file_exists('storage/' . $lta->attachment) && $lta->attachment != '')
                                                            <a href="{!! asset('storage/' . $lta->attachment) !!}" target="_blank" class="fs-5"
                                                                title="View Attachment">
                                                                <i class="bi bi-file-earmark-medical"></i>
                                                            </a>
                                                        @else
                                                            File does not exists.
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="gray-bg">Remarks</td>
                                                    <td colspan="3">{{ $lta->remarks }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                <div class="d-flex align-items-center add-info justify-content-between">
                                    <span>LTA Items</span>
                                    {{-- @if ($authUser->can('update', $package)) --}}
                                    <button data-toggle="modal" class="btn btn-primary btn-sm open-item-modal-form"
                                        href="{!! route('lta.items.create', $lta->id) !!}"><i class="bi-plus"></i> Add New Item
                                    </button>
                                    {{-- @endif --}}
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="ltaItemsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('label.item') }}</th>
                                                <th scope="col">{{ __('label.unit') }}</th>
                                                <th scope="col">Unit Rate</th>
                                                <th scope="col">{{ __('label.specification') }}</th>
                                                <th style="width: 150px">{{ __('label.action') }}</th>
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
            </section>
        </div>
    </div>
@stop
