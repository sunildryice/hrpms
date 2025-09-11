@extends('layouts.container')

@section('title', 'Create Direct Dispatch Bulk')

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#direct-dispatch-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {

            const itemValidator = {
                validators: {
                    notEmpty: {
                        message: 'Item is required'
                    }
                }
            }

            const quantityValidator = {
                validators: {
                    notEmpty: {
                        message: ' '
                    },
                    callback: {
                        message: ' ',
                        callback: function(input) {
                            let availableQuantity = $(input.element).closest('tr').find(
                                '[data-name="dispatch_item.available_quantity"]').val()
                            let assignedQuantity = parseFloat(input.value);
                            return assignedQuantity <= availableQuantity;
                        }
                    }
                }
            }


            let fcall = true;

            let rowIndex = 0;

            let inventoryItems = @json($inventoryItems);


            const form = document.getElementById('assetDispositionEditForm');

            $(form).find('[name="handover_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('handover_date');
            });

            const fv = FormValidation.formValidation(form, {

                fields: {
                    office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office is required',
                            }
                        }
                    },
                    purpose: {
                        validators: {
                            notEmpty: {
                                message: 'Asset is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is required',
                            },
                        }
                    },
                    handover_date: {
                        validators: {
                            notEmpty: {
                                message: 'Date of handover is required',
                            },
                        },
                    },

                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            {{-- if(filed == )  --}}
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            fv.addField('dispatch_item[assigned_inventory_item_id][0]', itemValidator)
                .addField('dispatch_item[assigned_quantity][0]', quantityValidator)

            $(form).on('change', '[name="office_id"]', function(e) {
                fv.revalidateField('office_id');
            });

            const removeRow = function(rowIndex) {
                const row = form.querySelector('[data-row-index="' + rowIndex + '"]');

                fv.removeField('dispatch_item[assigned_inventory_item_id][' + rowIndex + ']')
                    .removeField('dispatch_item[assigned_quantity][' + rowIndex + ']')

                row.parentNode.removeChild(row);
                updateItems();
            };

            const template = document.getElementById('template');

            $(document).on('click', '.removeButton', function(e) {
                const index = $(this).closest('tr').attr('data-row-index');
                removeRow(index);
            });

            document.getElementById('addButton').addEventListener('click', function() {
                rowIndex++;
                const clone = template.cloneNode(true);
                clone.removeAttribute('id');

                clone.style.display = 'block';
                clone.setAttribute('data-row-index', rowIndex);
                clone.removeAttribute('style');

                template.before(clone);

                $(clone.querySelector('[data-name="dispatch_item.assigned_inventory_item_id"]')).select2();

                clone
                    .querySelector('[data-name="dispatch_item.assigned_inventory_item_id"]')
                    .setAttribute('name', 'dispatch_item[assigned_inventory_item_id][' + rowIndex + ']');
                clone
                    .querySelector('[data-name="dispatch_item.assigned_unit"]')
                    .setAttribute('name', 'dispatch_item[assigned_unit][' + rowIndex + ']');

                clone
                    .querySelector('[data-name="dispatch_item.available_quantity"]')
                    .setAttribute('name', 'dispatch_item[available_quantity][' + rowIndex + ']')

                clone
                    .querySelector('[data-name="dispatch_item.assigned_quantity"]')
                    .setAttribute('name', 'dispatch_item[assigned_quantity][' + rowIndex + ']');

                if (!fcall) {
                    fv.addField('dispatch_item[assigned_inventory_item_id][' + rowIndex + ']',
                            itemValidator)
                        .addField('dispatch_item[assigned_quantity][' + rowIndex + ']', {
                            validators: {
                                notEmpty: {
                                    message: ' '
                                },
                                callback: {
                                    message: ' ',
                                    callback: function(input) {
                                        let availableQuantity = parseFloat(clone.querySelector(
                                            '[data-name="dispatch_item.available_quantity"]'
                                        ).value);
                                        let assignedQuantity = parseFloat(input.value);
                                        return assignedQuantity <= availableQuantity;
                                    }
                                }
                            }
                        })
                    updateItems();

                }

                const removeBtn = clone.querySelector('.js-remove-button');
                removeBtn.setAttribute('data-row-index', rowIndex);
                removeBtn.addEventListener('click', function(e) {
                    const index = e.target.getAttribute('data-row-index');
                    removeRow(index);
                });

                if (fcall) {
                    clone.remove();
                    fcall = false
                    $('#addButton').trigger('click');
                }


            });


            const updateItems = () => {
                let field = '<option value="">Select Item</option>';

                let selectedItems = Array.from(document.querySelectorAll(
                        '[data-name="dispatch_item.assigned_inventory_item_id"]'))
                    .map(select => select.value)
                    .filter(value => value !== '');

                let filteredItems = inventoryItems.filter(item => !selectedItems.includes((item.id)
                    .toString()));
                filteredItems.forEach(item => {
                    field += '<option value="' + item.id + '">' +
                        item.item_name + ' | Batch: ' + item.batch_number + '</option>';
                })
                document.querySelectorAll('[data-name="dispatch_item.assigned_inventory_item_id"]').forEach(
                    select => {
                        if (!select.value) {
                            select.innerHTML = '';
                            select.innerHTML = field;
                        }
                    });
            }

            $('#item-table-body').on('change', '.item-select', function() {
                const item_id = $(this).val();
                const rowIndex = $(this).closest('tr').attr('data-row-index');
                const item = inventoryItems.find(item => item.id == item_id);
                const unit = item.unit.title;
                const available_quantity = item.quantity - item.assigned_quantity;
                $(this).closest('tr').find('[data-name="dispatch_item.assigned_unit"]').val(unit);
                $(this).closest('tr').find('[data-name="dispatch_item.available_quantity"]').val(
                    available_quantity);
                updateItems();
            });

        });
    </script>

@endsection

@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('good.requests.direct.dispatch.index') }}"
                                class="text-decoration-none text-dark">Direct Dispatch Requests</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <form action="{{ route('direct.dispatch.bulk.store') }}" id="assetDispositionEditForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationOffice" class="form-label required-label">Office
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="office_id" class="select2 form-control" data-width="100%">
                                <option value="">Select an Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}"
                                        {{ $office->id == old('office_id') ? 'selected' : '' }}>
                                        {{ $office->office_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('office_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="office_id">
                                        {!! $errors->first('office_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationOffice" class="form-label required-label">Date of Handover
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" readonly="readonly" name="handover_date"
                                value="" />
                            @if ($errors->has('handover_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="handover_date">
                                        {!! $errors->first('handover_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="purpose" class="form-label required-label">Purpose</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea class="form-control" name="purpose" id="purpose" rows="2" placeholder="Purpose"></textarea>
                        </div>
                        @if ($errors->has('purpose'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div data-field="purpose">
                                    {!! $errors->first('purpose') !!}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="purpose" class="form-label ">Employees</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select class="select2 form-control" name="employee_ids[]" multiple>
                                <option value="">Select Employees</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->getFullName() }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationApprover" class="form-label required-label">Send
                                    to </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="approver_id"
                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select an Approver</option>
                                @foreach ($approvers as $approver)
                                    <option value="{{ $approver->id }}"
                                        {{ $approver->id == old('approver_id') ? 'selected' : '' }}>
                                        {{ $approver->getFullName() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('approver_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="approver_id">{!! $errors->first('approver_id') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="gap-2 m-2 justify-content-end d-flex" id="submitRequest">
                        {{-- <button type="submit" value="save" class="btn btn-primary btn-sm" name="btn" --}}
                        {{--     data-submit="save">Update</button> --}}
                    </div>
                    <div class="mb-2 row">
                        <div class="table-responsive">
                            <table id="item-table" class="table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="" style="width: 30%">Inventory Item</th>
                                        <th class="">Unit</th>
                                        <th class="">Available Qty</th>
                                        <th class="">Quantity</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="item-table-body">
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <select class="form-control select2 item-select" data-width="100%"
                                                        name="dispatch_item[assigned_inventory_item_id][0]"
                                                        data-name="dispatch_item.assigned_inventory_item_id">
                                                        <option value="">Select Item</option>
                                                        @foreach ($inventoryItems as $item)
                                                            <option value="{!! $item->id !!}">
                                                                {{ "{$item->getItemName()} | Batch: {$item->batch_number}" }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('dispatch_item[assigned_inventory_item_id][0]'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="dispatch_item[assigned_inventory_item_id][0]">
                                                                {!! $errors->first('dispatch_item[assigned_inventory_item_id][0]') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" name="dispatch_item[assigned_unit][0]"
                                                        data-name="dispatch_item.assigned_unit" class="form-control"
                                                        readonly>
                                                    @if ($errors->has('dispatch_item[assigned_unit][0]'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="dispatch_item[assigned_unit][0]">
                                                                {!! $errors->first('dispatch_item[assigned_unit][0]') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" name="dispatch_item[available_quantity][0]"
                                                        data-name="dispatch_item.available_quantity"
                                                        class="form-control available-qty" readonly value="0">
                                                    @if ($errors->has('dispatch_item[available_quantity][0]'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="dispatch_item[available_quantity][0]">
                                                                {!! $errors->first('dispatch_item[available_quantity][0]') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" name="dispatch_item[assigned_quantity][0]"
                                                        class="form-control">
                                                    @if ($errors->has('dispatch_item[assigned_quantity][0]'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="dispatch_item[assigned_quantity][0]">
                                                                {!! $errors->first('dispatch_item[assigned_quantity][0]') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-primary btn-block" id="addButton"
                                                rel="tooltip" title="Add Row">
                                                +
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- @endforelse --}}

                                    <!-- Template -->
                                    <tr id="template" style="display: none">
                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <select class="form-control select2 item-select" data-width="100%"
                                                        data-name="dispatch_item.assigned_inventory_item_id">
                                                        <option value="">Select Item</option>
                                                        {{-- @foreach ($assets as $asset)
                                                    <option value="{!! $asset->id !!}">
                                                        {{ $asset->getAssetNumber() }}</option>
                                                                                            @endforeach --}}
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" data-name="dispatch_item.assigned_unit"
                                                        class="form-control" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" data-name="dispatch_item.available_quantity"
                                                        class="form-control" value="0" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" data-name="dispatch_item.assigned_quantity"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-block js-remove-button"
                                                id="removeButton" rel="tooltip" title="Delete Row"> -
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($errors->has('assigned_quantity'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div data-field="assigned_quantity">
                                {!! $errors->first('assigned_quantity') !!}
                            </div>
                        </div>
                    @endif

                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                        {{-- <button type="submit" value="save" class="btn btn-primary btn-sm" name="btn" --}}
                        {{--     data-submit="save">Save</button> --}}
                        <button type="submit" value="submit" class="btn btn-success btn-sm" name="btn"
                            data-submit="submit">Submit</button>
                        <a href="{!! route('good.requests.direct.dispatch.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
            </form>



    </section>

@stop
