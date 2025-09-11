@extends('layouts.container')

@section('title', 'Report : Stock Book')

@section('page_js')
    <script type="text/javascript">
     $(document).ready(function() {
            $('#navbarVerticalMenu').find('#stock-book-menu').addClass('active');

            $('#stockBookTable').DataTable({
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
                    <form action="{{ route('report.stock.book.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="{{$item_id}}">
                        <input type="hidden" name="item_category" value="{{$item_category}}">
                        <input type="hidden" name="issued_to" value="{{$issued_to}}">
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
                <form action="{{route('report.stock.book.index')}}" method="POST" id="stockBookSearchForm">
                    @csrf
                    <div class="row mb-4">

                        {{-- <div class="col-md-2">
                            <label class="form-label" for="item_id">Year</label>
                            <select class="form-control select2" name="fiscal_year" id="fiscal_year">
                                <option value="">Select year...</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year->id }}" {{$fiscal_year == $year->id ? 'selected' : ''}}>{{ $year->getFiscalYear() }}</option>
                                @endforeach
                            </select>
                        </div> --}}

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

                        <div class="col mb-2">
                            <label class="form-label" for="issued_to">Issued To (Employee)</label>
                            <select class="form-control select2" name="issued_to" id="issued_to">
                                <option value="">Select Issued To...</option>
                                @foreach ($employees as $employee)
                                    @if ($employee->user)
                                        <option value="{{ $employee->user->id }}" {{$issued_to == $employee->user->id ? 'selected' : ''}}>{{ $employee->getFullName() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-md-2">
                            <label class="form-label" for="location">Location</label>
                            <input class="form-control" type="text" name="location" id="location" value="{{$location}}">
                        </div> --}}

                        <div class="col mb-2">
                            <label class="form-label" for="start_date">Issued From Date</label>
                            <input class="form-control" type="text" name="start_date" id="start_date" value="{{$start_date}}">
                        </div>

                        <div class="col mb-2">
                            <label class="form-label" for="end_date">Issued To Date</label>
                            <input class="form-control" type="text" name="end_date" id="end_date" value="{{$end_date}}">
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
                            <button type="submit" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive table-bordered" id="stockBookTable">
                        <thead>
                            <tr>
                                <th colspan="15" style="text-align: center;">IN</th>
                                <th colspan="18" style="text-align: center;">OUT</th>
                                <th rowspan="2">Balance Quantity</th>
                                <th rowspan="2">Balance Amount</th>
                            </tr>
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>Items</th>
                                <th>Description</th>
                                <th>Inventory Type</th>
                                <th>Used For</th>
                                <th>Item Category</th>
                                <th>Unit</th>
                                <th>Purchased Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>VAT Amount</th>
                                <th>Total Amount with VAT</th>
                                <th>GRN No.</th>
                                <th>Purchased Date</th>
                                <th>Goods Source/Vendor</th>


                                <th>{{__('label.sn')}}</th>
                                <th>Items</th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Used Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>VAT</th>
                                <th>Total Amount</th>
                                <th>Issued To</th>
                                <th>Location</th>
                                <th>Project</th>
                                <th>Account Code</th>
                                <th>Activity Code</th>
                                <th>Donor Code</th>
                                <th>GRN No.</th>
                                <th>Stock Requisition No.</th>
                                <th>Issued Date</th>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $key=>$inventoryItem)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{$inventoryItem->item->title}}</td>
                                    <td>{{$inventoryItem->specification}}</td>
                                    <td>{{$inventoryItem->item->category->inventoryType->title}}</td>
                                    <td>{{$inventoryItem->getDistributionType()}}</td>
                                    <td>{{$inventoryItem->item->category->title}}</td>
                                    <td>{{$inventoryItem->unit->title}}</td>
                                    <td>{{$inventoryItem->quantity}}</td>
                                    <td>{{$inventoryItem->unit_price}}</td>
                                    <td>{{$inventoryItem->total_price}}</td>
                                    <td>{{$inventoryItem->vat_amount}}</td>
                                    <td>{{$inventoryItem->total_price + $inventoryItem->vat_amount}}</td>
                                    <td>{{$inventoryItem->grn->getGrnNumber()}}</td>
                                    <td>{{$inventoryItem->grn->getReceivedDate()}}</td>
                                    <td>{{$inventoryItem->grn->getSupplierName()}}</td>

                                    @php
                                        $items = $inventoryItem->goodRequestAndDistributionItems();
                                    @endphp

                                    <td>
                                        @if ($items->isNotEmpty())
                                            @foreach ($items as $key=>$item)
                                                <div>{{++$key}}</div>
                                            @endforeach
                                        @else
                                            <div></div>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('item')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('description')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('unit')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('used_quantity')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('rate')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('amount')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('vat')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('total_amount')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('issued_to')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('location')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('project')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('account_code')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('activity_code')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('donor_code')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('grn_number')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('stock_requisition_number')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($items as $item)
                                            <div>{{$item->get('issued_date')}}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div>{{$inventoryItem->quantity - $inventoryItem->assigned_quantity}}</div>
                                    </td>
                                    <td>
                                        <div>{{$inventoryItem->unit_price * ($inventoryItem->quantity - $inventoryItem->assigned_quantity)}}</div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
