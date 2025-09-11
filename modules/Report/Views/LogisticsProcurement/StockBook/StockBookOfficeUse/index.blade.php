@extends('layouts.container')

@section('title', 'Report : Stock Book Office Use')

@section('page_css')
    <style>
        .wrap-col {
            min-width: 300px;
            max-width: 350px;
            white-space: pre-line;
            left: 0px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#stock-book-office-use-menu').addClass('active');

            $('#stockBookTable').DataTable({
                scrollX: true,
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autohide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function (e) {
                $(this).datepicker('hide');
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autohide: true,
                format: 'yyyy-mm-dd'
            }).on('change', function (e) {
                $(this).datepicker('hide');
            });

            $('#btn_reset').on('click', function (e) {
                $("input[type=text]").removeAttr('value');
                $("select option").removeAttr('selected');
                $('#item_id').val('').trigger("change");
                $('#items_in').val('').trigger("change");
                $('#items_out').val('').trigger("change");
                $('#issued_to').val('').trigger("change");
                $('#item_category').val('').trigger("change");
                $('#staff_name').val('').trigger("change");
            });

            const form = document.getElementById('stockBookSearchForm');
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
            <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                                           class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <form action="{{ route('report.stock.book.office.use.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item_id }}">
                        <input type="hidden" name="item_category" value="{{ $item_category }}">
                        <input type="hidden" name="issued_to" value="{{ $issued_to }}">
                        <input type="hidden" name="start_date" value="{{ $start_date }}">
                        <input type="hidden" name="end_date" value="{{ $end_date }}">
                        <button class="btn btn-sm btn-primary" id="btn_export" type="submit">Export</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="rounded border shadow-sm card c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{ route('report.stock.book.office.use.index') }}" method="POST" id="stockBookSearchForm">
                    @csrf
                    <div class="mb-4 row">

                        {{-- <div class="col-md-2">
                            <label class="form-label" for="item_id">Year</label>
                            <select class="form-control select2" name="fiscal_year" id="fiscal_year">
                                <option value="">Select year...</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year->id }}" {{$fiscal_year == $year->id ? 'selected' : ''}}>{{ $year->getFiscalYear() }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="mb-2 col">
                            <label class="form-label" for="item_id">Item</label>
                            <select class="form-control select2" name="item_id" id="item_id">
                                <option value="">Select Item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{ $item_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2 col">
                            <label class="form-label" for="item_category">Item Category</label>
                            <select class="form-control select2" name="item_category" id="item_category">
                                <option value="">Select Category...</option>
                                @foreach ($inventoryCategories as $inventoryCategory)
                                    <option value="{{ $inventoryCategory->id }}"
                                        {{ $item_category == $inventoryCategory->id ? 'selected' : '' }}>
                                        {{ $inventoryCategory->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-md-2">
                            <label class="form-label" for="items_in">Items In</label>
                            <select class="form-control select2" name="items_in" id="items_in">
                                <option value="">Select Item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{$items_in == $item->id ? 'selected' : ''}}>{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="items_out">Items Out</label>
                            <select class="form-control select2" name="items_out" id="items_out">
                                <option value="">Select Item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{$items_out == $item->id ? 'selected' : ''}}>{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="mb-2 col">
                            <label class="form-label" for="issued_to">Issued To (Office)</label>
                            <select class="form-control select2" name="issued_to" id="issued_to">
                                <option value="">Select Issued To...</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}" {{ $issued_to == $office->id ? 'selected' : '' }}>
                                        {{ $office->getOfficeName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-md-2">
                            <label class="form-label" for="location">Location</label>
                            <input class="form-control" type="text" name="location" id="location" value="{{$location}}">
                        </div> --}}

                        <div class="mb-2 col">
                            <label class="form-label" for="start_date">Issued From Date</label>
                            <input class="form-control" type="text" name="start_date" id="start_date"
                                   value="{{ $start_date }}">
                        </div>

                        <div class="mb-2 col">
                            <label class="form-label" for="end_date">Issued To Date</label>
                            <input class="form-control" type="text" name="end_date" id="end_date"
                                   value="{{ $end_date }}">
                        </div>

                        {{-- <div class="col-md-2">
                            <label class="form-label" for="staff_name">Staff Name</label>
                            <select class="form-control select2" name="staff_name" id="staff_name">
                                <option value="">Select Staff...</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->id }}" {{$staff_name == $employee->id ? 'selected' : ''}}>{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div> --}}

                        <div style="display: flex; flex-direction: row; justify-content: flex-end;">
                            <button type="submit" id="btn_search" class="m-1 btn btn-primary btn-sm">Search</button>
                            <button type="reset" id="btn_reset" class="m-1 btn btn-secondary btn-sm">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>

                <div class="table-responsive">

                    <table class="table table-responsive table-bordered" id="stockBookTable">
                        <thead>
                        <tr>
                            <th colspan="19" style="text-align: center;">IN</th>
                            <th rowspan="2">Balance Quantity</th>
                            <th rowspan="2">Balance Amount</th>
                            <th colspan="12" style="text-align: center;">OUT</th>
                        </tr>
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>Items</th>
                            <th class="wrap-col">Description</th>
                            <th>Inventory Type</th>
                            <th>Item Category</th>
                            <th>Batch No.</th>
                            <th>Unit</th>
                            <th>Purchased Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>VAT Amount</th>
                            <th>Total Amount with VAT</th>
                            <th>GRN No.</th>
                            <th>Purchased Date</th>
                            <th>Execution Type</th>
                            <th>Account Code</th>
                            <th>Activity Code</th>
                            <th>Donor Code</th>
                            <th>Goods Source/Vendor</th>
                            <th>{{ __('label.sn') }}</th>
                            <th>Used Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>VAT</th>
                            <th>Total Amount</th>
                            <th>Received By</th>
                            <th>Location</th>
                            <th>GRN No.</th>
                            <th>Stock Requisition No.</th>
                            <th>Handover Date</th>
                            <th>Issued Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $inventoryItem)
                            @php
                                $items = $inventoryItem->goodRequestAndDistributionItems(true);
                //if ($key == 1) {
                //    dd($inventoryItem->goodRequestAndDistributionItems(true));
                //}
                                $rowCount = count($items) == 0 ? 1 : count($items);
                                $usedQuantity = $items->sum('used_quantity');
                            @endphp
                            <tr>
                                <td rowspan="{{ $rowCount }}">{{ ++$key }} | {{ $inventoryItem->id }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->item->title }}</td>
                                <td rowspan="{{ $rowCount }}" class="wrap-col">
                                    {{ $inventoryItem->specification }}</td>
                                <td rowspan="{{ $rowCount }}">
                                    {{ $inventoryItem->item->category->inventoryType->title }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->item->category->title }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->batch_number }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->unit->title }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->quantity }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->getUnitPrice() }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->getTotalPrice() }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->getVatAmount() }}</td>
                                <td rowspan="{{ $rowCount }}">
                                    {{ $inventoryItem->total_price + $inventoryItem->vat_amount }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->grn->getGrnNumber() }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->getPurchaseDate() }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->getExecutionType() }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->accountCode->getAccountCode() }}
                                </td>
                                <td rowspan="{{ $rowCount }}">
                                    {{ $inventoryItem->activityCode->getActivityCode() }}</td>
                                <td rowspan="{{ $rowCount }}">
                                    {{ $inventoryItem->donorCode->getDonorCodeWithDescription() }}</td>
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->getSupplierName() }}</td>

                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->quantity - $usedQuantity }}</td> <!-- balance quenaity -->
                                <td rowspan="{{ $rowCount }}">{{ $inventoryItem->unit_price * ($inventoryItem->quantity - $usedQuantity) }}</td>
                            @if($items && $items->isNotEmpty())
                                @foreach ($items as $key => $item)
                                    @if(!$loop->first)
                                        <tr>
                                            @endif
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $item->get('used_quantity') }}</td>
                                            <td>{{ $item->get('rate') }}</td>
                                            <td>{{ $item->get('amount') }}</td>
                                            <td>{{ $item->get('vat') }}</td>
                                            <td>{{ $item->get('total_amount') }}</td>
                                            {{-- <td>{{ $item->get('office_code') }}</td> --}}
                                            <td>{{ $inventoryItem->grn?->createdBy?->getFullName() }}</td>
                                            <td>{{ $item->get('location') }}</td>
                                            <td>{{ $item->get('grn_number') }}</td>
                                            <td>{{ $item->get('stock_requisition_number') }}</td>
                                            <td>{{ $item->get('handover_date') }}</td>
                                            <td>{{ $item->get('issued_date') }}</td>
                                            @if(!$loop->first)
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <td colspan="12"></td>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
