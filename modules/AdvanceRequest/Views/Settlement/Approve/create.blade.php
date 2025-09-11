@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Approve Advance Settlement Request')

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
            $('#navbarVerticalMenu').find('#approve-settlement-advance-requests-menu').addClass('active');

            var oTable = $('#advanceRequestSettlementTable').DataTable({
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

            var oTable = $('#advanceRequestDetailsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('advance.requests.details.index', $advanceRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'donor',
                        name: 'donor'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    }
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('advanceRequestApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    recommended_to: {
                        validators: {
                            notEmpty: {
                                message: 'Recommended to is required.',
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
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]')
                                .value);
                            return (field === 'recommended_to' && statusId !== 5) || (field ===
                                'status_id' && statusId === 4);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                if (this.value == 4) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="recommended_to"]', function(e) {
                fv.revalidateField('recommended_to');
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
            columns: [
                {
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
                                    <a href="{{ route('approve.advance.settlements.index') }}"
                                        class="text-decoration-none text-dark">Advance Request Settlements</a>
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
                                Advance Request Details
                            </div>
                            @include('AdvanceRequest::Partials.settlement')
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                Settlement Details
                            </div>
                            @include('AdvanceRequest::Partials.settlementDetails')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Advance Request Details
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="advanceRequestDetailsTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">Amount</th>
                                                <th scope="col">{{ __('label.activity') }}</th>
                                                <th scope="col">{{ __('label.description') }}</th>
                                                <th scope="col">{{ __('label.account') }}</th>
                                                <th scope="col">{{ __('label.donor') }}</th>
                                                <th scope="col">{{ __('label.attachment') }}</th>
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
                                Advance Settlement Activity
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="advanceRequestSettlementTable">
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
                            <div class="card-header fw-bold" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                <span>
                                    Attachments
                                </span>
                            </div>
                            <div class="container-fluid-s">
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
                        </div>

                        <div class="card">
                            <div class="card-header fw-bold">
                                Expense Detail
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="expenseTable">
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
                                        @foreach($advanceSettlementRequest->settlementExpenses as $expense)
                                            <tr id="expenseRow-{{ $expense->id }}">
                                                <td id="collapseButton" onclick="collapse(this)">+</td>
                                                <td class="narration">{{ $expense->narration }}</td>
                                                <td class="district">{{ $expense->getDistrictName() }}</td>
                                                <td class="location">{{ $expense->location }}</td>
                                                <td>{{ $expense->getActivityCode() }}</td>
                                                <td>{{ $expense->getAccountCode() }}</td>
                                                <td>{{ $expense->getDonorCode() }}</td>
                                                <td>{{ $expense->advanceRequestDetail->amount }}</td>
                                                <td class="gross_amount">{{ $expense->gross_amount }}</td>
                                                <td class="tax_amount">{{ $expense->tax_amount }}</td>
                                                <td class="net_amount">{{ $expense->net_amount }}</td>
                                            </tr>
                                            <tr id="hidden">
                                                <td></td>
                                                <td colspan="10">
                                                    <table class="table table-bordered"
                                                           id="expenseTable-{{ $expense->id }}">
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
                                                        @foreach($expense->details as $detail)
                                                            <tr id="detailRow-{{ $detail->id }}">
                                                                <td class="expense_date">{{ $detail->getExpenseDate() }}</td>
                                                                <td class="bill_number">{{ $detail->bill_number }}</td>
                                                                <td class="description">{{ $detail->description }}</td>
                                                                <td class="gross_amount">{{ $detail->gross_amount }}</td>
                                                                <td class="tax_amount">{{ $detail->tax_amount }}</td>
                                                                <td class="net_amount">{{ $detail->net_amount }}</td>
                                                                <td class="expense_type">{{ $detail->getExpenseType() }}</td>
                                                                <td class="attachment">
                                                                    @if(file_exists('storage/'.$detail->attachment) && $detail->attachment != '')
                                                                        <div class="media">
                                                                            <a href="{!! asset('storage/'.$detail->attachment) !!}" target="_blank" class="fs-5"
                                                                            title="View Attachment"><i class="bi bi-file-earmark-medical"></i></a>
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

                        <!-- //place here -->
                        <div class="card">
                            <div class="card-header fw-bold">
                                Advance Process
                            </div>
                            <form action="{{ route('approve.advance.settlements.store', $advanceSettlementRequest->id) }}"
                                id="advanceRequestApproveForm" method="post" enctype="multipart/form-data"
                                autocomplete="off">
                                <div class="card-body">
                                        <div class="c-b">
                                            @foreach ($advanceSettlementRequest->logs as $log)
                                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                    <div width="40" height="40"
                                                        class="rounded-circle mr-3 user-icon">
                                                        <i class="bi-person-circle fs-5"></i>
                                                    </div>
                                                    <div class="w-100">
                                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                                <span
                                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
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
                                        <div class="border-top pt-4">
                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationleavetype"
                                                            class="form-label required-label">Status</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="status_id" class="select2 form-control" data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="2">Return to Requester</option>
                                                        @if ($advanceSettlementRequest->status_id != 4)
                                                            <option value="4">Recommend</option>
                                                        @endif
                                                        <option value="6">Approve</option>
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

                                            <div class="row mb-2" id="recommendBlock" style="display: none;">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationleavetype"
                                                            class="form-label required-label">Recommended</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="recommended_to" class="select2 form-control"
                                                        data-width="100%">
                                                        <option value="">Select Recommended To</option>
                                                        @foreach ($approvers as $approver)
                                                            <option value="{{ $approver->id }}">
                                                                {{ $approver->getFullName() }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('approver_id'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="approver_id">
                                                                {!! $errors->first('approver_id') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row mb-2">
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
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('approve.advance.settlements.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
@stop
