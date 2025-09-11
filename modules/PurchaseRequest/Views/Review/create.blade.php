@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Review Purchase Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#review-purchase-requests-menu').addClass('active');

        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('purchaseRequestReviewForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    // status_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Status is required',
                    //         },
                    //     },
                    // },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
                            },
                        },
                    },
                    // budget_description: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'The budget description is required',
                    //         },
                    //     },
                    // },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const status = form.querySelector('[name="status_id"]').value;
                            statusId = status ? parseInt(status) : 0;
                            return (field === 'log_remarks' && statusId === 0) || (field ===
                                'status_id' && statusId !== 0);
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
                fv.revalidateField('log_remarks');
            }).on('change', '[id="balance_budget"]', function(e) {
                calculateBalanceBudget(this);
            }).on('change', '[id="commitment_amount"]', function(e) {
                calculateBalanceBudget(this);
            });

            function calculateBalanceBudget($element) {
                console.log($element);
                balanceBudget = parseFloat($($element).closest('tr').find('[id="balance_budget"]').val());
                commitmentAmount = parseFloat($($element).closest('tr').find('[id="commitment_amount"]').val());
                balanceBudget = isNaN(balanceBudget) ? 0 : balanceBudget;
                commitmentAmount = isNaN(commitmentAmount) ? 0 : commitmentAmount;
                estimatedBalanceAmount = balanceBudget - commitmentAmount;
                $($element).closest('tr').find('[id="estimated_balance_budget"]').val(estimatedBalanceAmount
                    .toFixed(2));
            }
        });

        function toggleDescription(event) {
            let description = event.target.closest('tr').querySelector('#description');
            description.classList.toggle('d-none');
            if (description.classList.contains('d-none')) {
                description.value = '';
            }
        }
    </script>
@endsection
@section('page-content')


    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('approve.purchase.requests.index') }}"
                                class="text-decoration-none text-dark">Purchase
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
                        Purchase Request Details
                    </div>
                    @include('PurchaseRequest::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Purchase Request Items
                    </div>
                    <div class="card-body">
                        @include('PurchaseRequest::Partials.pr-items')
                    </div>
                </div>
                <form action="{{ route('review.purchase.requests.store', $purchaseRequest->id) }}"
                    id="purchaseRequestReviewForm" method="post" enctype="multipart/form-data" autocomplete="off">

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
                                            <th scope="col">Total Est. Amount</th>
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
                                                <td>{{ $purchaseRequest->purchaseRequestItems()->select('total_price')->where('activity_code_id', $prBudget->activity_code_id)->where('office_id', $prBudget->office_id)->sum('total_price') }}
                                                </td>
                                                <td>
                                                    <input class="form-control" type="number" min="0"
                                                        name="balance_budget[{{ $prBudget->id }}]" id="balance_budget"
                                                        value="{{ $prBudget->balance_budget }}">
                                                </td>
                                                <td>
                                                    <input class="form-control" type="number" min="0"
                                                        name="commitment_amount[{{ $prBudget->id }}]" id="commitment_amount"
                                                        value="{{ $prBudget->commitment_amount }}">
                                                </td>
                                                <td>
                                                    <input class="form-control" type="number" min="0"
                                                        name="estimated_balance_budget[{{ $prBudget->id }}]"
                                                        id="estimated_balance_budget"
                                                        value="{{ $prBudget->estimated_balance_budget }}" disabled>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="budgeted[{{ $prBudget->id }}]"
                                                        id="budgeted" onchange="toggleDescription(event)">
                                                        <option value="1"
                                                            {{ (bool) $prBudget->budgeted ? 'selected' : '' }}>Yes</option>
                                                        <option value="0"
                                                            {{ !(bool) $prBudget->budgeted ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="description[{{ $prBudget->id }}]" id="description" rows="2"
                                                        class="form-control {{ (bool) $prBudget->budgeted ? 'd-none' : '' }}">{{ $prBudget->description }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" name="btn" value="update"
                                class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    @include('Attachment::list', [
                        'modelType' => 'Modules\PurchaseRequest\Models\PurchaseRequest',
                        'modelId' => $purchaseRequest->id,
                    ])


                    <div class="card">
                        <div class="card-header fw-bold">
                            Purchase Process
                        </div>
                        <div class="card-body">
                            <div class="c-b">
                                @foreach ($purchaseRequest->logs as $log)
                                    <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                        <div width="40" height="40" class="mr-3 rounded-circle user-icon">
                                            <i class="bi-person-circle fs-5"></i>
                                        </div>
                                        <div class="w-100">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                <div
                                                    class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                    <label class="mb-0 form-label">{{ $log->getCreatedBy() }}</label>
                                                    <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
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
                            <div class="pt-4 border-top">
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Status
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id"
                                            class="select2 form-control  @if ($errors->has('status_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a Status</option>
                                            <option value="{!! config('constant.RETURNED_STATUS') !!}">Return to Requester</option>
                                            <option value="{!! config('constant.REJECTED_STATUS') !!}">Reject</option>
                                            <option value="{!! config('constant.VERIFIED_STATUS') !!}">Verify</option>
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
                                            <label for="validationRemarks" class="form-label required-label">Remarks
                                            </label>
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
                        <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{!! route('review.purchase.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
