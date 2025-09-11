@extends('layouts.container')

@section('title', 'Edit Asset Disposition Request')

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#asset-disposition-menu').addClass('active');
            const errors = @json($errors->all());
            console.log(errors);
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            // $("#substitutes").select2({
            //     width: '100%',
            //     dropdownAutoWidth: true
            // });
            const assetValidators = {
                validators: {
                    notEmpty: {
                        message: 'Asset is required'
                    }
                }
            }

            const reasonValidators = {
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            }

            let fcall = true;

            let rowIndex = {{ $disposeAssets->count() - 1 }};

            let assets = @json($assets);




            const form = document.getElementById('assetDispositionEditForm');
            const fv = FormValidation.formValidation(form, {

                fields: {
                    office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office is required',
                            }
                        }
                    },
                    asset_id: {
                        validators: {
                            notEmpty: {
                                message: 'Asset is required',
                            },
                        },
                    },
                    disposition_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'The disposition type is required',
                            },
                        },
                    },
                    disposition_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Program date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is required',
                            },
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const submitButton = $('[name="btn"]:focus').data('submit');
                            return (field.startsWith('asset_dispose') || field == 'approver_id') && submitButton == 'save';
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            fv.addField('asset_dispose[asset][0]', assetValidators)
                .addField('asset_dispose[reason][0]',
                    reasonValidators)

            $(form.querySelector('[name="disposition_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('disposition_date');
            });


            $(form).on('change', '[name="asset_id"]', function(e) {
                fv.revalidateField('asset_id');
            });
            $(form).on('change', '[name="office_id"]', function(e) {
                fv.revalidateField('office_id');
            });
            $(form).on('change', '[name="disposition_type_id"]', function(e) {
                fv.revalidateField('disposition_type_id');
            });

            const removeRow = function(rowIndex) {
                const row = form.querySelector('[data-row-index="' + rowIndex + '"]');

                fv.removeField('asset_dispose[asset][' + rowIndex + ']')
                    .removeField('asset_dispose[reason][' + rowIndex + ']')

                row.parentNode.removeChild(row);
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

                $(clone.querySelector('[data-name="asset_dispose.asset"]')).select2();

                clone
                    .querySelector('[data-name="asset_dispose.asset"]')
                    .setAttribute('name', 'asset_dispose[asset][' + rowIndex + ']');
                clone
                    .querySelector('[data-name="asset_dispose.reason"]')
                    .setAttribute('name', 'asset_dispose[reason][' + rowIndex + ']');

                if (!fcall) {
                    fv.addField('asset_dispose[asset][' + rowIndex + ']', assetValidators)
                        .addField('asset_dispose[reason][' + rowIndex + ']',
                            reasonValidators)
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

                updateAssets(rowIndex);

            });

            const updateAssets = (rowIndex) => {
                let field = '<option value="">Select Asset</option>';
                assets = assets.filter(asset => {
                    let found = false;
                    for (let i = 0; i < rowIndex; i++) {
                        const assetId = $(form.querySelector('[name="asset_dispose[asset][' + i +
                            ']"]')).val();
                        if (assetId == asset.id) {
                            found = true;
                            break;
                        }
                    }
                    return !found;
                });
                assets.forEach(asset => {
                    field += '<option value="' + asset.id + '">' +
                        `${asset.prefix}/${asset.asset_number}/${asset.year}` + '</option>';
                });
                $(form.querySelector('[name="asset_dispose[asset][' + rowIndex + ']"]')).html(field);
            }

            $('#clearButton').click(function() {
                const field = form.querySelector('[name="asset_dispose[asset][0]"]');
                const reason = form.querySelector('[name="asset_dispose[reason][0]"]');
                field.value = '';
                reason.value = '';
            });

            @foreach ($dispositionRequest->disposeAssets as $index => $asset)
                fv.addField('asset_dispose[asset][' + {{ $index }} + ']',
                        assetValidators)
                    .addField('asset_dispose[reason][' + {{ $index }} + ']',
                        reasonValidators)
            @endforeach
        });
    </script>

@endsection

@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('asset.disposition.index') }}"
                                class="text-decoration-none text-dark">Asset Disposition</a></li>
                        <li class="breadcrumb-item" aria-current="page">Edit Asset Disposition Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Edit Asset Disposition Request</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <div class="card-header fw-bold">Asset Disposition</div>
            <form action="{{ route('asset.disposition.update', $dispositionRequest->id) }}" id="assetDispositionEditForm"
                method="post" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationOffice" class="form-label required-label">Office
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php
                                $selected = old('office_id') ?? $dispositionRequest?->office_id;
                            @endphp
                            <select name="office_id" class="select2 form-control" data-width="100%">
                                <option value="">Select an Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}" {{ $office->id == $selected ? 'selected' : '' }}>
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

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationActivity" class="form-label">Disposition Type</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php
                                $selected = old('disposition_type_id') ?? $dispositionRequest?->disposition_type_id;
                            @endphp
                            <select class="form-control select2" data-width="100%" name="disposition_type_id">
                                <option value="">Select Disposition Type</option>
                                @foreach ($dispositionTypes as $dispositionType)
                                    <option value="{!! $dispositionType->id !!}"
                                        {{ $dispositionType->id == $selected ? 'selected' : '' }}>
                                        {{ $dispositionType->getDispositionType() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('disposition_type_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="disposition_type_id">
                                        {!! $errors->first('disposition_type_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationDispositionDate" class="form-label required-label">Disposition Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('disposition_date')) is-invalid @endif"
                                readonly name="disposition_date"
                                value="{{ old('disposition_date') ?? $dispositionRequest->disposition_date?->format('Y-m-d') }}" />
                            @if ($errors->has('disposition_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="disposition_date">{!! $errors->first('disposition_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationApprover" class="form-label required-label">Send
                                    to </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php
                                $selected = old('approver_id') ?? $dispositionRequest?->approver_id;
                            @endphp
                            <select name="approver_id"
                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select an Approver</option>
                                @foreach ($approvers as $approver)
                                    <option value="{{ $approver->id }}"
                                        {{ $approver->id == $selected ? 'selected' : '' }}>
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

                    <div class="justify-content-end d-flex gap-2 m-2" id="submitRequest">
                        <button type="submit" value="save" class="btn btn-primary btn-sm" name="btn" data-submit="save">Update</button>
                    </div>
                    <div class="row mb-2">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="" style="width: 30%">Asset</th>
                                        <th class="">Reason</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dispositionRequest->disposeAssets as $index => $asset)
                                        <tr data-row-index="{{ $index }}">
                                            <td>
                                                @php
                                                    $selected = old('asset_dispose[asset][' . $index . ']') ?? $asset->asset_id;
                                                @endphp
                                                <div class="row">
                                                    <div class="col">
                                                        <select class="form-control select2" data-width="100%"
                                                            name="asset_dispose[asset][{{ $index }}]">
                                                            <option value="">Select Asset</option>
                                                            @foreach ($assets as $inventoryAsset)
                                                                <option value="{!! $inventoryAsset->id !!}"
                                                                    {{ $inventoryAsset->id == $selected ? 'selected' : '' }}>
                                                                    {{ $inventoryAsset->getAssetNumber() }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col"><textarea name="asset_dispose[reason][{{ $index }}]" class="form-control" placeholder="">{!! $asset->disposition_reason !!} </textarea></div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($loop->first)
                                                    <button type="button" class="btn btn-primary btn-block"
                                                        id="addButton" rel="tooltip" title="Add Row">
                                                        +
                                                    </button>
                                                &nbsp;
                                                    <button type="button" class="btn btn-danger btn-block"
                                                        id="clearButton" rel="tooltip" title="Clear Row">
                                                        x
                                                    </button>
                                                @else
                                                    <button class="btn btn-danger btn-block removeButton" type="button" rel="tooltip" title="Delete Row">
                                                        -
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <div class="col">
                                                        <select class="form-control select2 asset-select" data-width="100%"
                                                        name="asset_dispose[asset][0]">
                                                        <option value="">Select Asset</option>
                                                        @foreach ($assets as $asset)
                                                            <option value="{!! $asset->id !!}"
                                                                {{ $asset->id == $selected ? 'selected' : '' }}>
                                                                {{ $asset->getAssetNumber() }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('asset_dispose[asset][0]'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="asset_dispose[asset][0]">
                                                                {!! $errors->first('asset_dispose[asset][0]') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col">
                                                        <textarea name="asset_dispose[reason][0]" class="form-control" placeholder="">@if (old('asset_dispose[reason][0]')){{ old('asset_dispose[reason][0]') }}@endif</textarea>
                                                        @if ($errors->has('asset_dispose[reason][0]'))
                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                <div data-field="asset_dispose[reason][0]">
                                                                    {!! $errors->first('asset_dispose[reason][0]') !!}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <button type="button" class="btn btn-primary btn-block" id="addButton" rel="tooltip" title="Add Row">
                                                    +
                                                </button>
                                                &nbsp;
                                                <button type="button" class="btn btn-danger btn-block" id="clearButton" rel="tooltip" title="Clear Row">
                                                    x
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse

                                    <!-- Template -->
                                    <tr id="template" style="display: none">
                                        <td>
                                            <div class="row">
                                                <div class="col">
                                                    <select class="form-control select2 asset-select" data-width="100%"
                                                        data-name="asset_dispose.asset">
                                                        <option value="">Select Asset</option>
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
                                                <div class="col"><textarea data-name="asset_dispose.reason" class="form-control" placeholder=""></textarea></div>
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

                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" value="submit" class="btn btn-success btn-sm"
                            name="btn" data-submit="submit">Submit</button>
                        <a href="{!! route('asset.disposition.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
            </form>



    </section>

@stop
