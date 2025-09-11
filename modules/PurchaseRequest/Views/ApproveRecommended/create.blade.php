@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Approve Recommended Purchase Request')

@section('page_css')
    <style>
        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background: white;
            z-index: 99;

        }

        .table-container {
            /* height: calc(100vh - 215px); */
            overflow: auto;
        }

        .wrap-col {
            min-width: 300px;
            max-width: 350px;
            white-space: pre-line;
            left: 0px;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approve-recommended-purchase-requests-menu').addClass('active');

            var oTable = $('#purchaseRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('purchase.requests.items.index', $purchaseRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item',
                        name: 'item',
                        className: 'sticky-col wrap-col'
                    },
                    {
                        data: 'specification',
                        name: 'specification',
                        className: 'wrap-col',
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'donor',
                        name: 'donor'
                    },
                ],
                initComplete: () => {
                    const table = $('#purchaseRequestItemTable');
                    const tableContainer = $('.table-container');
                    const tableHeight = table[0].clientHeight;
                    if (tableHeight > 682) {
                        tableContainer.css('height', 'calc(100vh - 215px)');
                    }
                },

            });
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            //
            const form = document.getElementById('purchaseRequestApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required.',
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
                        excluded: function (field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]').value);
                            return (field === 'recommended_to' && statusId !== 4) || (field === 'status_id' && statusId === 4);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function (e) {
                fv.revalidateField('status_id');
                if (this.value == 4) {
                    $(form).find('#recommendBlock1').show();
                    $(form).find('#recommendBlock2').show();
                } else {
                    $(form).find('#recommendBlock1').hide();
                    $(form).find('#recommendBlock2').hide();
                }
            }).on('change', '[name="approver_id"]', function (e) {
                fv.revalidateField('approver_id');
            });
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
                                    <a href="{{ route('approve.recommended.purchase.requests.index') }}"
                                       class="text-decoration-none text-dark">Purchase
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
                                Purchase Request Details
                            </div>
                            @include("PurchaseRequest::Partials.detail")
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Purchase Request Items
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-container">
                                    <table class="table" id="purchaseRequestItemTable">
                                        <thead class="thead-light sticky-top">
                                        <tr>
                                            <th scope="col" class="sticky-col wrap-col">{{ __('label.item') }}</th>
                                            <th scope="col">{{ __('label.specification') }}</th>
                                            <th scope="col">{{ __('label.unit') }}</th>
                                            <th scope="col">{{ __('label.quantity') }}</th>
                                            <th scope="col">{{ __('label.estimated-rate') }}</th>
                                            <th scope="col">{{ __('label.estimated-amount') }}</th>
                                            <th scope="col">{{ __('label.office') }}</th>
                                            <th scope="col">{{ __('label.activity') }}</th>
                                            <th scope="col">{{ __('label.account') }}</th>
                                            <th scope="col">{{ __('label.donor') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tr>
                                            <td colspan="5">Total Tentative Amount</td>
                                            <td>{{ $purchaseRequest->total_amount }}</td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header fw-bold">
                                Purchase Request Budget
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">Office</th>
                                                <th scope="col">Activity</th>
                                                <th scope="col">Balance Budget</th>
                                                <th scope="col">Commitment Amount</th>
                                                <th scope="col">Estimated Balance Budget</th>
                                                <th scope="col">Budgeted</th>
                                                <th scope="col">Justification (if not budgeted)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchaseRequest->purchaseRequestBudgets as $prBudget)
                                                <tr>
                                                    <td>{{ $prBudget->getOffice() }}</td>
                                                    <td>{{ $prBudget->activityCode?->getActivityCode() }}</td>
                                                    <td>{{ $prBudget->balance_budget }}</td>
                                                    <td>{{ $prBudget->commitment_amount }}</td>
                                                    <td>{{ $prBudget->estimated_balance_budget }}</td>
                                                    <td>{{ (bool)$prBudget->budgeted ? 'Yes' : 'No' }}</td>
                                                    <td class="wrap-col">{{ $prBudget->description }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @include('Attachment::list', ['modelType' => 'Modules\PurchaseRequest\Models\PurchaseRequest', 'modelId' => $purchaseRequest->id])

                        <div class="card">
                            <div class="card-header fw-bold">
                                Purchase Process
                            </div>
                            <form action="{{ route('approve.recommended.purchase.requests.store', $purchaseRequest->id) }}"
                                  id="purchaseRequestApproveForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                        <div class="c-b">
                                            @foreach($purchaseRequest->logs as $log)
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
                                                        <label for="validationleavetype" class="form-label required-label">Status </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select name="status_id" class="select2 form-control"
                                                            data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="{{config('constant.RETURNED_STATUS')}}">Return to Requester</option>
                                                        <option value="{{config('constant.REJECTED_STATUS')}}">Reject</option>
                                                        <option value="{{config('constant.APPROVED_STATUS')}}">Approve</option>
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

                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="validationRemarks" class="form-label required-label">Remarks </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea type="text"
                                                              class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                              name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                    @if ($errors->has('log_remarks'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div
                                                                data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
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
                                    <a href="{!! route('approve.purchase.requests.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
@stop
