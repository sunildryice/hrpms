@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Paid Settlement Advance Request Detail')

@section('page_js')
    <script type="text/javascript">
        function collapse(cell) {
            var row = cell.parentElement;
            var target_row = row.parentElement.children[row.rowIndex + 1];
            if (target_row.style.display == 'table-row') {
                cell.innerHTML = '+';
                target_row.style.display = 'none';
            } else {
                cell.innerHTML = '-';
                target_row.style.display = 'table-row';
            }
        }

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#paid-advance-settlement-menu').addClass('active');

            var oTable = $('#advanceSettlementRequestTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('advance.settlement.activities.index', $advanceSettlementRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                    data: 'description',
                    name: 'description'
                }, ]
            });
        });

        // Start - Attachment Scripts Section
        var attachmentTable = $('#attachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('advance.settlement.attachment.index', $advanceSettlementRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'link',
                    name: 'link',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        // Start - Attachment Scripts Section

        var summaryTable = $('#settlementExpenseSummary').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('advance.settlement.expense.summary', $advanceSettlementRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                // {
                //     data: 'expenseCategory',
                //     name: 'expenseCategory'
                // },
                {
                    data: 'expenseType',
                    name: 'expenseType'
                },
                {
                    data: 'gross_amount',
                    name: 'gross_amount'
                },
                {
                    data: 'tax_amount',
                    name: 'tax_amount'
                },
                {
                    data: 'net_amount',
                    name: 'net_amount'
                },
            ]
        });
    </script>
@endsection
@section('page-content')


    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('paid.advance.settlement.index') }}"
                                class="text-decoration-none text-dark">Paid Advance Settlement
                                Requests</a>
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
                    <div class="card-header fw-bold">
                        Approved Settlement Advance Request Details
                    </div>
                    @include('AdvanceRequest::Partials.settlement')
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Settlement Details
                    </div>
                    @include('AdvanceRequest::Partials.settlementDetails')
                </div>
                @if (isset($advanceSettlementRequest->paid_at))
                    <div class="card">
                        <div class="card-header fw-bold">
                            Payment Details
                        </div>
                        @include('AdvanceRequest::Partials.paymentDetails')
                    </div>
                @endif

            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Advance Activity Details
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-wrap" id="advanceSettlementRequestTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold"> Attachments</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="attachmentTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col" style="width: 150px">Attachment</th>
                                        <th scope="col" style="width: 150px">Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold">
                        Expense Detail
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="expenseTable">
                                <tr>
                                    <th></th>
                                    <th scope="col">Narration</th>
                                    <th scope="col">District</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Activity Code</th>
                                    <th scope="col">Account Code</th>
                                    <th scope="col">Donor Code</th>
                                    <th scope="col">Advance Amount Taken</th>
                                    <th scope="col">Gross Amount</th>
                                    <th scope="col">Less Tax</th>
                                    <th scope="col">Net Amount Paid</th>
                                </tr>
                                @foreach ($advanceSettlementRequest->settlementExpenses as $expense)
                                    <tr id="expenseRow-{{ $expense->id }}">
                                        <td id="collapseButton" onclick="collapse(this)">+</td>
                                        <td class="narration">{{ $expense->narration }}</td>
                                        <td class="district">{{ $expense->getDistrictName() }}</td>
                                        <td class="location">{{ $expense->location }}</td>
                                        <td>{{ $expense->activityCode->getActivityCode() }}</td>
                                        <td>{{ $expense->accountCode->getAccountCode() }}</td>
                                        <td>{{ $expense->getDonorCode() }}</td>
                                        <td>{{ $expense->advanceRequestDetail->amount }}</td>
                                        <td class="gross_amount">{{ $expense->gross_amount }}</td>
                                        <td class="tax_amount">{{ $expense->tax_amount }}</td>
                                        <td class="net_amount">{{ $expense->net_amount }}</td>
                                    </tr>
                                    <tr id="hidden">
                                        <td></td>
                                        <td colspan="10">
                                            <table class="table table-bordered" id="expenseTable-{{ $expense->id }}">
                                                <tr>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Invoice/Bill No</th>
                                                    <th scope="col">Description</th>
                                                    <th scope="col">Gross Amount</th>
                                                    <th scope="col">Tax Amount (Less)</th>
                                                    <th scope="col">Net Amount Paid</th>
                                                    <th scope="col">Expense Type</th>
                                                    <th scope="col">Attachment</th>
                                                </tr>
                                                @foreach ($expense->details as $detail)
                                                    <tr id="detailRow-{{ $detail->id }}">
                                                        <td class="expense_date">{{ $detail->getExpenseDate() }}</td>
                                                        <td class="bill_number">{{ $detail->bill_number }}</td>
                                                        <td class="description">{{ $detail->description }}</td>
                                                        <td class="gross_amount">{{ $detail->gross_amount }}</td>
                                                        <td class="tax_amount">{{ $detail->tax_amount }}</td>
                                                        <td class="net_amount">{{ $detail->net_amount }}</td>
                                                        <td class="expense_type">{{ $detail->getExpenseType() }}</td>
                                                        <td class="attachment">
                                                            @if (file_exists('storage/' . $detail->attachment) && $detail->attachment != '')
                                                                <div class="media">
                                                                    <a href="{!! asset('storage/' . $detail->attachment) !!}" target="_blank"
                                                                        class="fs-5" title="View Attachment"><i
                                                                            class="bi bi-file-earmark-medical"></i></a>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold">
                        Expense Summary
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="settlementExpenseSummary">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Description</th>
                                        <th scope="col">Gross Amount</th>
                                        <th scope="col">Less Tax</th>
                                        <th scope="col">Net Amount Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold">
                        Advance Settlement Process
                    </div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($advanceSettlementRequest->logs as $log)
                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                        <i class="bi-person-circle fs-5"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                            </div>
                                            <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <p class="text-justify comment-text mb-0 mt-1">
                                            {{ $log->log_remarks }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@stop
