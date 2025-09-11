@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Assign Good Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assign-good-requests-menu').addClass('active');
        });

        $(document).on('click', '.open-item-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const itemForm = document.getElementById('goodRequestAssignItemForm');
                $(itemForm).find(".select2").each(function() {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });
                const fv = FormValidation.formValidation(itemForm, {
                    fields: {
                        assigned_inventory_item_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Item is required',
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
                        itemTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(itemForm).on('change', '[name="assigned_inventory_item_id"]', function(e) {
                    $element = $(this);
                    var itemId = $element.val();
                    var assetHtmlToReplace = '';
                    if (itemId) {
                        var url = baseUrl + '/api/inventory/items/' + itemId;
                        var successCallback = function(response) {
                            response.assets.forEach(function(asset) {
                                assetHtmlToReplace += '<option value="' + asset.id +
                                    '">' + asset.prefix + '/' + String(asset
                                        .asset_number).padStart(3, 0) + '/' + asset
                                    .year + '</option>';
                            });
                            $($element).closest('form').find('.assigned_assets').html(
                                assetHtmlToReplace);
                            if (response.consumable) {
                                $(itemForm).find('#consumableBlock').show();
                                $(itemForm).find('#nonConsumableBlock').hide();
                            } else {
                                $(itemForm).find('#consumableBlock').hide();
                                $(itemForm).find('#nonConsumableBlock').show();
                            }
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    }
                    fv.revalidateField('assigned_inventory_item_id');
                });

            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('goodRequestAssignForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
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

            $(form).on('change', '.assigned_inventory_item_id', function(e) {
                $element = $(this);
                var itemId = $element.val();
                var assetHtmlToReplace = '';
                if (itemId) {
                    //var url = baseUrl + '/api/inventory/items/' + itemId;
                    let url = "{{ route('api.inventory.items.show', ['item' => ':item']) }}"
                    url = url.replace(':item', itemId);
                    var successCallback = function(response) {
                        response.assets.forEach(function(asset) {
                            assetHtmlToReplace += '<option value="' + asset.id + '">' + asset
                                .asset_code + '</option>';
                        });

                        // console.log(response.availableQuantity);
                        $($element).closest('tr').find('.availableQuantity').html(response
                            .availableQuantity);

                        $($element).closest('tr').find('.assigned_assets').html(assetHtmlToReplace);
                        if (response.consumable) {
                            $($element).closest('tr').find('.consumableBlock').show();
                            $($element).closest('tr').find('.nonConsumableBlock').hide();
                        } else {
                            $($element).closest('tr').find('.consumableBlock').hide();
                            $($element).closest('tr').find('.nonConsumableBlock').show();
                        }
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                }
            });

            $('#goodRequestItemTable').on('change', '.quantity-input', function(e) {
                let data = $(form).serialize();
                var url = "{{ route('validate.inventory.item.assign') }}";
                var successCallback = function(response) {
                    console.log(response)
                }
                var errorCallback = function(error) {
                    toastr.warning(error.responseJSON.message, 'Warning', {
                        timeOut: 2000
                    });
                }
                ajaxNativeSubmit(url, 'GET', data, 'json', successCallback, errorCallback);
            });

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
            }).on('change', '[name="assigned_item_id"]', function(e) {
                $element = $(this);
                var itemId = $element.val();
                var htmlToReplace = '<option value="">Select Unit</option>';
                var assetHtmlToReplace = '';
                $($element).closest('form').find('[name="assigned_unit_id"]').html(htmlToReplace);
                if (itemId) {
                    var url = baseUrl + '/api/master/items/' + itemId;
                    var successCallback = function(response) {
                        response.units.forEach(function(unit) {
                            htmlToReplace += '<option value="' + unit.id + '">' + unit.title +
                                '</option>';
                        });
                        response.assets.forEach(function(asset) {
                            assetHtmlToReplace += '<option value="' + asset.id + '">' + asset
                                .prefix + '-' + asset.asset_number + '</option>';
                        });
                        $($element).closest('form').find('[name="assigned_unit_id"]').html(
                            htmlToReplace).trigger('change');
                        $($element).closest('form').find('.assigned_assets').html(assetHtmlToReplace);
                        if (response.consumable) {
                            $(form).find('#consumableBlock').show();
                            $(form).find('#nonConsumableBlock').hide();
                        } else {
                            $(form).find('#consumableBlock').hide();
                            $(form).find('#nonConsumableBlock').show();
                        }
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                }
                fv.revalidateField('assigned_item_id');
                fv.revalidateField('assigned_unit_id');
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
                                    <a href="{{ route('assign.good.requests.index') }}" class="text-decoration-none">Good
                                        Requests</a>
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
                                Good Request Details
                            </div>
                            @include('GoodRequest::Partials.detail')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <form action="{{ route('assign.good.requests.store', $goodRequest->id) }}"
                            id="goodRequestAssignForm" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Good Request Items
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                <table class="table" id="goodRequestItemTable">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col">{{ __('label.item-name') }}</th>
                                                            <th scope="col">{{ __('label.unit') }}</th>
                                                            <th scope="col">{{ __('label.quantity') }}</th>
                                                            <th scope="col">{{ __('label.specification') }}</th>
                                                            <th scope="col">Assigned Item</th>
                                                            <th scope="col">Available</th>
                                                            <th scope="col">Assigned Asset/Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($goodRequest->goodRequestItems as $goodRequestItem)
                                                            <tr>
                                                                <td>{{ $goodRequestItem->item_name }}</td>
                                                                <td>{{ $goodRequestItem->getUnit() }}</td>
                                                                <td>{{ $goodRequestItem->quantity }}</td>
                                                                <td>{{ $goodRequestItem->specification }}</td>
                                                                <td>
                                                                    <select
                                                                        class="select2 form-control assigned_inventory_item_id"
                                                                        name="assigned_inventory_item_id[{{ $goodRequestItem->id }}]">
                                                                        <option value="">Select Item</option>
                                                                        @foreach ($inventoryItems as $inventoryItem)
                                                                            <option value="{{ $inventoryItem->id }}"
                                                                                data-consumable="{{ $inventoryItem->getConsumableFlag() }}">
                                                                                {{ $inventoryItem->getItemName() . ' | Batch: ' . $inventoryItem->batch_number }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td class="availableQuantity">

                                                                </td>
                                                                <td>
                                                                    <div class="consumableBlock" style="display: none;">
                                                                        <input
                                                                            name="assigned_quantity[{{ $goodRequestItem->id }}]"
                                                                            class="form-control quantity-input"
                                                                            value="">
                                                                    </div>
                                                                    <div class="nonConsumableBlock" style="display: none;">
                                                                        <select
                                                                            name="assigned_asset_ids[{{ $goodRequestItem->id }}][]"
                                                                            class="select2 form-control assigned_assets"
                                                                            multiple>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 rounded border shadow-sm card">
                                <div class="card-header fw-bold">
                                    Good Request Process
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            @foreach ($goodRequest->logs as $log)
                                                <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                                    <div width="40" height="40"
                                                        class="mr-3 rounded-circle user-icon">
                                                        <i class="bi-person-circle fs-5"></i>
                                                    </div>
                                                    <div class="w-100">
                                                        <div
                                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                            <div
                                                                class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                                <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                                <span
                                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                            </div>
                                                            <small
                                                                title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                        </div>
                                                        <p class="mt-1 mb-0 text-justify comment-text">
                                                            {{ $log->log_remarks }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-2 row">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="status_id"
                                                            class="form-label required-label">Status</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="status_id" class="select2 form-control" data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="{{ config('constant.RETURNED_STATUS') }}"
                                                            @if (old('status_id') == config('constant.RETURNED_STATUS')) selected @endif>
                                                            Return to Requester
                                                        </option>
                                                        <option value="{{ config('constant.REJECTED_STATUS') }}"
                                                            @if (old('status_id') == config('constant.REJECTED_STATUS')) selected @endif>
                                                            Reject
                                                        </option>
                                                        <option value="{{ config('constant.ASSIGNED_STATUS') }}"
                                                            @if (old('status_id') == config('constant.ASSIGNED_STATUS')) selected @endif>
                                                            Assign
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('status_id'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="status_id">
                                                                {!! $errors->first('status_id') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mb-2 row">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationRemarks"
                                                            class="form-label required-label">Remarks</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                    @if ($errors->has('log_remarks'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('assign.good.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
