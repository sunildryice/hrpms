@extends('layouts.container')

@section('title', 'Edit Distribution Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#distribution-requests-menu').addClass('active');
            const form = document.getElementById('distributionRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'District is required',
                            },
                        },
                    },
                    office_id: {
                        validators: {
                            notEmpty: {
                                message: 'The office is required',
                            },
                        },
                    },
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The project code is required',
                            },
                        },
                    },
                    health_facility_id: {
                        validators: {
                            notEmpty: {
                                message: 'The health facility is required',
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

            $(form).on('change', '[name="district_id"]', function (e) {
                fv.revalidateField('district_id');
            }).on('change', '[name="project_code_id"]', function (e) {
                fv.revalidateField('project_code_id');
            }).on('change', '[name="office_id"]', function (e) {
                fv.revalidateField('office_id');
            });
        });

        var oTable = $('#distributionRequestItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('distribution.requests.items.index', $distributionRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'item_name', name: 'item_name'},
                {data: 'unit', name: 'unit'},
                {data: 'quantity', name: 'quantity'},
                {data: 'unit_price', name: 'unit_price'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'vat_amount', name: 'vat_amount'},
                {data: 'net_amount', name: 'net_amount'},
                {data: 'activity', name: 'activity'},
                {data: 'account', name: 'account'},
                {data: 'donor', name: 'donor'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className:'sticky-col'},
            ]
        });

        $('#distributionRequestItemTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {timeOut: 5000});
                $('#distributionRequestItemTable').find('#net_amount').text(response.distributionRequest.total_amount);
                $('#distributionRequestItemTable').find('#vat_amount').text(response.total_vat);
                $('#distributionRequestItemTable').find('#total_amount').text(response.total_amount);
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-item-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('distributionRequestItemForm');
                $(form).find(".select2").each(function () {
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
                        inventory_item_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Item name is required',
                                },
                            },
                        },
                        activity_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Activity code is required',
                                },
                            },
                        },
                        account_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Account code is required',
                                },
                            },
                        },
                        quantity: {
                            validators: {
                                notEmpty: {
                                    message: 'Quantity is required',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 1',
                                    min: 1,
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
                }).on('core.form.valid', function (event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                      toastr.success(response.message, 'Success', {timeOut: 5000});
                      $('#distributionRequestItemTable').find('#net_amount').text(response.distributionRequest.total_amount);
                      $('#distributionRequestItemTable').find('#vat_amount').text(response.total_vat);
                      $('#distributionRequestItemTable').find('#total_amount').text(response.total_amount);
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change', '[name="inventory_item_id"]', function (e) {
                    $element = $(this);
                    var itemId = $element.val();
                    var htmlToReplace = '<option value="">Select Unit</option>';
                    $($element).closest('form').find('[name="unit_id"]').html(htmlToReplace);
                    if (itemId) {
                        var url = baseUrl + '/api/inventory/items/' + itemId;
                        var successCallback = function (response) {
                            //console.log(response)
                            $($element).closest('form').find('[name="unit"]').val(response.unit);
                            $($element).closest('form').find('[name="unit_id"]').val(response.inventoryItem.unit_id);
                            $($element).closest('form').find('[name="specification"]').val(response.inventoryItem.specification);
                            $($element).closest('form').find('[name="expiry_date"]').val(response.inventoryItem.expiry_date);
                            $($element).closest('form').find('[name="unit_price"]').val(response.inventoryItem.unit_price).trigger('change');
                            $($element).closest('form').find('[name="available_quantity"]').val(response.availableQuantity);

                            $($element).closest('form').find('[name="activity_code_id"]').val(response.inventoryItem.activity_code_id).trigger('change');
                            $($element).closest('form').find('[name="donor_code_id"]').val(response.inventoryItem.donor_code_id).trigger('change');

                            // Async request using ajax
                            if (response.inventoryItem.activity_code_id) {
                                var htmlToReplace = '<option value="">Select Account Code</option>';
                                $.ajax({url: baseUrl+'/api/master/activity-codes/'+response.inventoryItem.activity_code_id, async: true, success: function (output) {
                                    output.accountCodes.forEach(function (accountCode) {
                                        htmlToReplace += '<option value="' + accountCode.id + '">' + accountCode.title + ' ' + accountCode.description + '</option>';
                                    });
                                    $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace).trigger('change');
                                    $($element).closest('form').find('[name="account_code_id"]').val(response.inventoryItem.account_code_id).trigger('change');
                                }});
                            }

                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    }
                    fv.revalidateField('inventory_item_id');
                }).on('change', '[name="activity_code_id"]', function (e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function (response) {
                            response.accountCodes.forEach(function (accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id + '">' + accountCode.title + ' ' + accountCode.description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace).trigger('change');
                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                }).on('change', '[name="account_code_id"]', function (e) {
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="unit_price"]', function (e) {
                    calculateTotalPrice(this);
                }).on('change', '[name="quantity"]', function (e) {
                    calculateTotalPrice(this);
                });

                function calculateTotalPrice($element) {
                    quantity = $($element).closest('form').find('[name="quantity"]').val();
                    unitPrice = $($element).closest('form').find('[name="unit_price"]').val();
                    $($element).closest('form').find('.total_price').val(quantity * unitPrice);
                }
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
                                    <a href="{{ route('distribution.requests.index') }}" class="text-decoration-none">Distribution
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <form action="{{ route('distribution.requests.update', $distributionRequest->id) }}"
                                  id="distributionRequestEditForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype"
                                                       class="form-label required-label">District</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedDistrictId = $distributionRequest->district_id; @endphp
                                            <select class="select2 form-control" name="district_id">
                                                <option value="">Select a District</option>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                        {{ $district->id == $selectedDistrictId ? "selected":"" }}>
                                                        {{ $district->getDistrictName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('district_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="district_id">
                                                        {!! $errors->first('district_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype"
                                                       class="form-label required-label">Office</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedOfficeId = old('office_id') ?: $distributionRequest->office_id; @endphp
                                            <select name="office_id" class="select2 form-control"
                                                    data-width="100%">
                                                <option value="">Select an Office</option>
                                                @foreach($offices as $office)
                                                    <option value="{{ $office->id }}"
                                                            data-distribution="{{ $office->id }}"
                                                        {{ $office->id == $selectedOfficeId ? "selected":"" }}>
                                                        {{ $office->getOfficeName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('office_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="office_id">
                                                        {!! $errors->first('office_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype"
                                                       class="form-label required-label">Project</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedProjectId = old('project_code_id') ?: $distributionRequest->project_code_id; @endphp
                                            <select name="project_code_id" class="select2 form-control"
                                                    data-width="100%">
                                                <option value="">Select a Project</option>
                                                @foreach($projectCodes as $project)
                                                    <option value="{{ $project->id }}"
                                                            data-distribution="{{ $project->id }}"
                                                        {{ $project->id == $selectedProjectId ? "selected":"" }}>
                                                        {{ $project->getProjectCodeWithDescription() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('project_code_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="project_code_id">
                                                        {!! $errors->first('project_code_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationHealthFacility" class="form-label required-label">Health
                                                    Facility</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select class="form-control select2 @if($errors->has('health_facility_id')) is-invalid @endif" name="health_facility_id" id="health_facility_id">
                                                <option value="">Select a health facility</option>
                                                @foreach ($healthFacilities as $healthFacility)
                                                    <option value="{{ $healthFacility->id }}" {{ $distributionRequest->health_facility_id == $healthFacility->id ? 'selected' : ''}}>{{ $healthFacility->title }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('health_facility_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="health_facility_id">{!! $errors->first('health_facility_id') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="m-0">Remarks</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text"
                                                      class="form-control @if($errors->has('remarks')) is-invalid @endif"
                                                      name="remarks">{{ old('remarks') ?: $distributionRequest->remarks }}</textarea>
                                            @if($errors->has('remarks'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Send
                                                    To</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedApproverId = old('approver_id') ?: $distributionRequest->approver_id; @endphp
                                            <select name="approver_id" class="select2 form-control
                                                @if($errors->has('approver_id')) is-invalid @endif" data-width="100%">
                                                <option value="">Select an Approver</option>
                                                @foreach($supervisors as $approver)
                                                    <option
                                                        value="{{ $approver->id }}" {{$approver->id == $selectedApproverId ? "selected":""}}>{{ $approver->full_name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('approver_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="approver_id">
                                                        {!! $errors->first('approver_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                    {!! method_field('PUT') !!}
                                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                                            Update
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Distribution Request Items
                                        </div>
                                        <div class="p-2">
                                            <div class="d-flex align-items-center add-info justify-content-end">
                                                @if ($authUser->can('update', $distributionRequest))
                                                    <button data-toggle="modal"
                                                            class="btn btn-primary btn-sm open-item-modal-form"
                                                            href="{!! route('distribution.requests.items.create', $distributionRequest->id) !!}"
                                                    ><i class="bi-plus"></i> Add New Item
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table" id="distributionRequestItemTable">
                                                            <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">{{ __('label.item-name') }}</th>
                                                                <th scope="col">{{ __('label.unit') }}</th>
                                                                <th scope="col">{{ __('label.quantity') }}</th>
                                                                <th scope="col">{{ __('label.rate') }}</th>
                                                                <th scope="col">{{ __('label.amount') }}</th>
                                                                <th scope="col">{{ __('label.vat-amount') }}</th>
                                                                <th scope="col">{{ __('label.total-amount') }}</th>
                                                                <th scope="col">{{ __('label.activity') }}</th>
                                                                <th scope="col">{{ __('label.account') }}</th>
                                                                <th scope="col">{{ __('label.donor') }}</th>
                                                                <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            @php
                                                              $distrubutionItems = $distributionRequest->distributionRequestItems()->select('total_amount', 'vat_amount')->get();
                                                            @endphp
                                                            <tfoot>
                                                            <tr>
                                                                <td colspan="4">Total Amount</td>
                                                                <td id="total_amount">{{ $distrubutionItems->sum('total_amount')  }}</td>
                                                                <td id="vat_amount">{{ $distrubutionItems ->sum('vat_amount') }}</td>
                                                                <td id="net_amount">{{ $distributionRequest->total_amount }}</td>
                                                                <td colspan="3"></td>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('distribution.requests.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
