@extends('layouts.container')

@section('title', 'Assigned Asset Detail')
@php
    $authUser = auth()->user();
@endphp
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assets-assigned-menu').addClass('active');


            var assetConditionLogTable = $('#assetConditionLogTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('asset.condition.logs.index', $asset->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [
                    { data: 'asset_condition', name: 'asset_condition' },
                    { data: 'description', name: 'description' },
                    { data: 'created_by', name: 'created_by' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className:'sticky-col'
                    },
                ]
            });

            $('#assetConditionLogTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    assetConditionLogTable.ajax.reload();
                }

                ajaxDeleteSweetAlert($url, successCallback);
            });


            var assetAssignmentLogTable = $('#assetAssignmentLogTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('asset.assignment.logs.index', $asset->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [
                    { data: 'assigned_user', name: 'assigned_user' },
                    { data: 'assigned_office', name: 'assigned_office' },
                    { data: 'assigned_department', name: 'assigned_department' },
                    { data: 'assigned_district', name: 'assigned_district' },
                    { data: 'assigned_date', name: 'assigned_date' },
                    { data: 'condition', name: 'condition' },
                    { data: 'remarks', name: 'remarks' },
                ]
            });


            $(document).on('click', '.open-asset-condition-log-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                    const assetConditionLogForm = document.getElementById('assetConditionLogForm');
                    const fv = FormValidation.formValidation(assetConditionLogForm, {
                        fields: {
                            asset_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'The asset is required.',
                                    },
                                },
                            },
                            condition_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'The asset condition is required.',
                                    },
                                },
                            },
                            // description: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'The asset description is required.',
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
                        let data = new FormData(assetConditionLogForm);
                        let url  = assetConditionLogForm.getAttribute('action');
                        var successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            assetConditionLogTable.ajax.reload();
                        }
                        ajaxSubmitFormData(url, 'POST', data, successCallback);
                    });
                });
            });

            $(document).on('click', '.open-asset-assign-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                    const form = document.getElementById('assetassignform');
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
                            $('#asset-assign').hide();
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
                                    <a href="{!! route('assets.assigned.index') !!}" class="text-decoration-none">Assigned Assets</a>
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
                            <div class="card-header d-flex justify-content-between">
                                <span class="fw-bold">
                                Inventory Details
                                </span>
                                <span>
                                    <a href="{{route('inventories.show', $inventory->id)}}" class="fs-7" title="View Inventory"><i class="bi bi-box-arrow-up-right
"></i></a>
                                </span>
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
                                                <div class="icon-section"><i
                                                        class="bi-currency-dollar dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getUnitPrice() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Unit Price"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-currency-dollar dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getTotalPrice() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Total Price"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-currency-dollar dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {!! $inventory->getVatAmount() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="VAT Amount"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-currency-dollar dropdown-item-icon"></i></div>
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
                                        @can('directAssign', $asset)
                                            <div id="asset-assign">
                                                <li class="pt-4 pb-2 d-flex flex-row justify-content-start">
                                                    <button data-toggle="modal" class="btn btn-sm btn-outline-primary open-asset-assign-modal-form"
                                                    href="{{route('good.requests.direct.assign.create', $asset->id)}}">
                                                        Asset Assign <i class="bi bi-arrow-up-right-circle"></i>
                                                    </button>
                                                </li>
                                            </div>
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @if($asset->isDisposed())
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Disposition Details
                                </div>
                                <div class="card-body">
                                    <div class="p-1">
                                        <ul class="list-unstyled list-py-2 text-dark mb-0">
                                            <li class="pb-2"><span
                                                    class="card-subtitle text-uppercase text-primary">About</span></li>
                                                    <li class="position-relative">
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                                                            <div class="d-content-section">{{ $disposition->getRequesterName() }}</div>
                                                        </div>
                                                        <span class="stretched-link" rel="tooltip" title="Requester"></span>
                                                    </li>
                                                    @if ($disposition->approver->id)
                                                        <li class="position-relative">
                                                            <div class="d-flex gap-2 align-items-center">
                                                                <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                                                                <div class="d-content-section"> {!! $disposition->getApproverName() !!} </div>
                                                            </div>
                                                            <span class="stretched-link" rel="tooltip" title="Approver"></span>
                                                        </li>
                                                    @endif
                                                    <li class="position-relative">
                                                        <div class="d-flex gap-2 align-items-start">
                                                            <div class="icon-section"><i class="bi-geo-fill dropdown-item-icon"></i></div>
                                                            <div class="d-content-section"> {!! $disposition->getDispositionType() !!}</div>
                                                        </div>
                                                        <span class="stretched-link" rel="tooltip" title="Disposition Type"></span>
                                                    </li>
                                                    <li class="position-relative">
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <div class="icon-section"><i class="bi-calendar-date dropdown-item-icon"></i></div>
                                                            <div class="d-content-section"> {!! $disposition->getDispositionDate() !!} </div>
                                                        </div>
                                                        <span class="stretched-link" rel="tooltip" title="Disposition Date"></span>
                                                    </li>
                                                    <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Reason</span>
                                                    </li>
                                                    <li class="position-relative">
                                                        <div class="d-flex gap-2 align-items-start">
                                                            <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                                                            <div class="d-content-section">
                                                                <div id="initial-reason">
                                                                    {!!  $asset->disposition->disposition_reason !!}
                                                                </div>

                                                            </div>
                                                        </div>
                                        </ul>       </li>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>


                    <div class="col-lg-9">
                        <div class="row">
                            <div>
                                <div class="card">
                                    <div class="card-header fw-bold">
                                        Asset Detail
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table" id="assetTable">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">{{ __('label.asset-number') }}</th>
                                                                <th scope="col">{{ __('label.purchase-date') }}</th>
                                                                <th scope="col">{{ __('label.serial-number') }}</th>
                                                                <th scope="col">{{ __('label.item') }}</th>
                                                                <th scope="col">{{ __('label.remarks') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>{{$asset->getAssetNumber()}}</td>
                                                                <td>{{$asset->getPurchaseDate()}}</td>
                                                                <td>{{$asset->getSerialNumber()}}</td>
                                                                <td>{{$asset->getItemName()}}</td>
                                                                <td>{{$asset->remarks}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        Asset Condition Log
                                        <a data-toggle="modal" class="btn btn-primary btn-sm open-asset-condition-log-modal-form"
                                        href="{{route('asset.condition.logs.create', $asset->id) }}" rel="tooltip" title="Add Asset Condition">Add Condition</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table" id="assetConditionLogTable">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">Asset Condition</th>
                                                                <th scope="col">Description</th>
                                                                <th scope="col">Audited By</th>
                                                                <th scope="col">Action</th>
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


                            <div>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        Asset Assigment Log
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table" id="assetAssignmentLogTable">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">Employee Name</th>
                                                                <th scope="col">Office</th>
                                                                <th scope="col">Department</th>
                                                                <th scope="col">District</th>
                                                                <th scope="col">Assigned On</th>
                                                                <th scope="col">Asset Condition</th>
                                                                <th scope="col">Remarks</th>
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
                    </div>



                </div>
            </section>
        </div>
    </div>
@stop
