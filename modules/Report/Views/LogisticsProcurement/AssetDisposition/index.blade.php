@extends('layouts.container')

@section('title', 'Report : Asset Disposition')

@section('page_js')
    <script type="text/javascript">
     $(document).ready(function() {
            $('#navbarVerticalMenu').find('#asset-dispose-report-menu').addClass('active');

            $('#assetBookTable').DataTable({
                scrollX: true,});

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autohide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function(e) {
                $(this).datepicker('hide');
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autohide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function(e) {
                $(this).datepicker('hide');
            });

            $('#btn_reset').on('click', function(e) {
                $("input[type=text]").removeAttr('value');
                $("select option").removeAttr('selected');
                $('#item_id').val('').trigger("change");
                $('#disposition_type').val('').trigger("change");
                $('#requester').val('').trigger("change");
                $('#item_category').val('').trigger("change");
                $('#office_id').val('').trigger("change");
            });

            const form = document.getElementById('assetBookSearchForm');
            const fv = FormValidation.formValidation(form, {
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat'
                    }),
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'start_date',
                            message: 'From date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'To date must be a valid date and later than from date.',
                        },
                    }),
                }
            });
     });

    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <form action="{{ route('report.asset.disposition.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="{{$item_id}}">
                        <input type="hidden" name="item_category" value="{{$item_category}}">
                        <input type="hidden" name="requester" value="{{$requester}}">
                        <input type="hidden" name="start_date" value="{{$start_date}}">
                        <input type="hidden" name="end_date" value="{{$end_date}}">
                        <button class="btn btn-sm btn-primary" id="btn_export" type="submit">Export</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{route('report.asset.disposition.index')}}" method="POST" id="assetBookSearchForm">
                    @csrf
                    <div class="row mb-4">

                        <div class="col mb-2">
                            <label class="form-label" for="item_id">Item</label>
                            <select class="form-control select2" name="item_id" id="item_id">
                                <option value="">Select Item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{$item_id == $item->id ? 'selected' : ''}}>{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col mb-2">
                            <label class="form-label" for="item_category">Item Category</label>
                            <select class="form-control select2" name="item_category" id="item_category">
                                <option value="">Select Category...</option>
                                @foreach ($inventoryCategories as $inventoryCategory)
                                    <option value="{{ $inventoryCategory->id }}" {{$item_category == $inventoryCategory->id ? 'selected' : ''}}>{{ $inventoryCategory->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col mb-2">
                            <label class="form-label" for="requester">Disposition Type</label>
                            <select class="form-control select2" name="disposition_type" id="disposition_type">
                                <option value="">Select Disposition Type</option>
                                @foreach ($dispositionTypes as $type)
                                        <option value="{{ $type->id}}" {{$disposition_type == $type->id ? 'selected' : ''}}>{{ $type->getDispositionType() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col mb-2">
                            <label class="form-label" for="requester">Office</label>
                            <select class="form-control select2" name="office_id" id="office_id">
                                <option value="">Select an Office</option>
                                @foreach ($offices as $office)
                                        <option value="{{ $office->id}}" {{$office_id == $office->id ? 'selected' : ''}}>{{ $office->office_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col mb-2">
                            <label class="form-label" for="requester">Requester (Employee)</label>
                            <select class="form-control select2" name="requester" id="requester">
                                <option value="">Select Requester</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}" {{$requester == $employee->user->id ? 'selected' : ''}}>{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 mb-2">
                            <label class="form-label" for="start_date">Disposed From Date</label>
                            <input class="form-control" type="text" name="start_date" id="start_date" value="{{$start_date}}">
                        </div>

                        <div class="col-lg-2 mb-2">
                            <label class="form-label" for="end_date">Disposed To Date</label>
                            <input class="form-control" type="text" name="end_date" id="end_date" value="{{$end_date}}">
                        </div>

                        <div style="display: flex; flex-direction: row; justify-content: flex-end;">
                            <button type="submit" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive table-bordered" id="assetBookTable">
                        <thead>
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Items</th>
                                <th>Coding (Item Code)</th>
                                <th>Item Category</th>
                                <th>Asset Code</th>
                                <th>Old Asset Code</th>
                                <th>Disposition Type</th>
                                <th>Disposed Date</th>
                                <th>Disposed By</th>
                                <th>Office</th>
                                <th>Description</th>
                                <th>Serial Number</th>
                                <th>Price (with VAT)</th>
                                <th>Purchasing Date</th>
                                <th>Execution</th>
                                <th>Voucher Number</th>
                                <th>Account Code</th>
                                <th>Activity Code</th>
                                <th>Donor Code</th>
                                <th>Staff Name</th>
                                <th>Designation</th>
                                <th>Office Code</th>
                                <th>Issued On</th>
                                <th>Condition</th>
                                <th>Room Number</th>
                                <th>Vendors</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $key=>$asset)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $asset->getItemName() }}</td>
                                    <td>{{ $asset->inventoryItem->getCategoryName() }}</td>
                                    <td>{{ $asset->getItemCode() }}</td>
                                    <td>{{ $asset->getAssetNumber() }}</td>
                                    <td>{{ $asset->old_asset_code }}</td>
                                    <td>{{ $asset->getDispositionType() }}</td>
                                    <td>{{ $asset->getDispositionDate() }}</td>
                                    <td>{{ $asset->getDisposedBy() }}</td>
                                    <td>{{ $asset->getDispositionOffice() }}</td>
                                    <td>{{ $asset->getSpecification() }}</td>
                                    <td>{{ $asset->getSerialNumber() }}</td>
                                    <td>{{ $asset->inventoryItem->getTotalAmount() / $asset->inventoryItem->quantity }}</td>
                                    <td>{{ $asset->getPurchaseDate() }}</td>
                                    <td>{{ $asset->inventoryItem->getExecutionType() }}</td>
                                    <td>{{ $asset->inventoryItem->getVoucherNumber() }}</td>
                                    <td>{{ $asset->inventoryItem->accountCode->getAccountCode() }}</td>
                                    <td>{{ $asset->inventoryItem->activityCode->getActivityCode() }}</td>
                                    <td>{{ $asset->inventoryItem->donorCode->getDonorCode() }}</td>
                                    <td>{{ $asset->getAssignedUserName() }}</td>
                                    <td>{{ $asset->getAssignedUserDesignation() }}</td>
                                    <td>{{ $asset->getAssignedUserOfficeCode() }}</td>
                                    <td>{{ $asset->getIssuedDate() }}</td>
                                    <td>{{ $asset->getAssetCondition() }}</td>
                                    <td></td>
                                    <td>{{ $asset->inventoryItem->getSupplierName() }}</td>
                                    <td>{{ $asset->remarks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
