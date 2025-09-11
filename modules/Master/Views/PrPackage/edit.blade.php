@extends('layouts.container')

@section('title', 'Edit Purchase Request Package')

@section('page_js')
    <script type="text/javascript">
        var oTable = $('#packageItemsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master.packages.items.index', $package->id) }}",
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
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ],
            drawCallback: function() {
                let table = this[0];
                let footer = table.getElementsByTagName('tfoot')[0];
                if (!footer) {
                    footer = document.createElement("tfoot");
                    table.appendChild(footer);
                }

                let estimated_amount = this.api().column(4).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                estimated_amount = new Intl.NumberFormat('en-US').format(estimated_amount);

                footer.innerHTML = '';
                footer.innerHTML = `<tr>
                                        <td colspan='4'>Total Tentative Amount</td>
                                        <td colspan='6'>${estimated_amount}</td>
                                    </tr>`;
            },
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#packages-menu').addClass('active');

            function submitPackageForm() {
                var packageUrl = packagefv.form.action;
                var packageData = $(packagefv.form).serialize();
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 1000
                    });
                }
                var errorCallback = function(response) {
                    toastr.warning(resposne.message, 'Warning', {
                        timeOut: 1000
                    })
                }
                ajaxSubmit(packageUrl, 'PUT', packageData, successCallback);
            }
            const packageForm = document.getElementById('packageAddForm');
            const packagefv = FormValidation.formValidation(packageForm, {
                fields: {
                    package_name: {
                        validators: {
                            notEmpty: {
                                message: 'The package name is required',
                            },
                        },
                    },
                },
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

            $('#packageItemsTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
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
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-item-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('packageItemForm');
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
                            quantity: {
                                validators: {
                                    notEmpty: {
                                        message: 'Quantity is required',
                                    },
                                    greaterThan: {
                                        message: 'The value must be greater than or equal to 0.01',
                                        min: 0.01,
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
                        quantity = $($element).closest('form').find('[name="quantity"]').val();
                        unitPrice = $($element).closest('form').find('[name="unit_price"]').val();
                        $($element).closest('form').find('.total_price').val(quantity * unitPrice);
                    }


                });
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

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('master.packages.index') }}" class="text-decoration-none text-dark">Purchase
                                Request Package</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <form action="{{ route('master.packages.update', $package->id) }}" id="packageAddForm" method="post"
            enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">


                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationPackageName" class="form-label required-label">Package
                                    Name:</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control @if ($errors->has('package_name')) is-invalid @endif"
                                name="package_name" value="{{ $package->package_name }}">
                            @if ($errors->has('package_name'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="package_name">{!! $errors->first('package_name') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationDescription" class="form-label">Package Description</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('package_description')) is-invalid @endif"
                                name="package_description">{{ $package->package_description }}</textarea>
                            @if ($errors->has('package_description'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="package_description">{!! $errors->first('package_description') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                        Update
                    </button>
                    <a href="{!! route('master.packages.index') !!}" class="btn btn-danger btn-sm">Cancel</a>

                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span>Items</span>
                        {{-- @if ($authUser->can('update', $package)) --}}
                        <button data-toggle="modal" class="btn btn-primary btn-sm open-item-modal-form"
                            href="{!! route('master.packages.items.create', $package->id) !!}"><i class="bi-plus"></i> Add New Item
                        </button>
                        {{-- @endif --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="packageItemsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.item') }}</th>
                                    <th scope="col">{{ __('label.unit') }}</th>
                                    <th scope="col">{{ __('label.quantity') }}</th>
                                    <th scope="col">{{ __('label.estimated-rate') }}</th>
                                    <th scope="col">{{ __('label.estimated-amount') }}</th>
                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- <div class="justify-content-end d-flex gap-2" id="submitRequest">
                <button type="submit" name="btn" value="submit" class="btn btn-primary btn-sm submit-record"
                    style="">
                    Save
                </button>
                <a href="{!! route('master.packages.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div> --}}
        </form>
    </section>


@stop
