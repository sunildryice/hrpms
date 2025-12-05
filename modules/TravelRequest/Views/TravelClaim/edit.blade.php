@extends('layouts.container')

@section('title', 'Travel Claim')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#travel-claims-menu').addClass('active');

            const claimForm = document.getElementById('travelClaimEditForm');
            const fv = FormValidation.formValidation(claimForm, {
                fields: {
                    advance_amount: {
                        validators: {
                            numeric: {
                                message: 'The advance amount should be number.',
                            },
                            between: {
                                inclusive: true,
                                min: 0,
                                max: 99999999,
                                message: 'The value must be between 0 to 99999999',
                            },
                        },
                    },
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'The reviewer is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is requried',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(claimForm).on('change', '[name="reviewer_id"]', function(e) {
                fv.revalidateField('reviewer_id');
            }).on('change', '[name="advance_amount"]', function(e) {
                advanceAmount = parseFloat($(this).closest('form').find('[name="advance_amount"]').val());
                totalAmount = parseFloat($(this).closest('form').find('#total_amount').text());
                $(this).closest('form').find('[name="refundable_amount"]').val(totalAmount - advanceAmount);
            });
        });

        var expenseTable = $('#expenseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.expenses.index', $travelClaim->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                // {
                //     data: 'donor',
                //     name: 'donor'
                // },
                {
                    data: 'expense_date',
                    name: 'expense_date'
                },
                {
                    data: 'expense_description',
                    name: 'expense_description'
                },
                {
                    data: 'expense_amount',
                    name: 'expense_amount'
                },
                // {
                //     data: 'charging_office',
                //     name: 'charging_office'
                // },
                {
                    data: 'invoice_bill_number',
                    name: 'invoice_bill_number'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#expenseTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $('#travelClaimEditForm').find('#total_expense_amount').text(response.travelClaim
                    .total_expense_amount);
                $('#travelClaimEditForm').find('#total_itinerary_amount').text(response.travelClaim
                    .total_itinerary_amount);
                $('#travelClaimEditForm').find('#total_amount').text(response.travelClaim.total_amount);
                $('#travelClaimEditForm').find('[name="refundable_amount"]').val(response.travelClaim
                    .refundable_amount);
                expenseTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-expense-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const expenseForm = document.getElementById('travelExpenseForm');
                $(expenseForm).find(".select2").each(function() {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });

                const fv = FormValidation.formValidation(expenseForm, {
                    fields: {
                        activity_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Activity is required',
                                },
                            },
                        },
                        expense_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The expense date is required',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        description: {
                            validators: {
                                notEmpty: {
                                    message: 'Description is required',
                                },
                            },
                        },
                        invoice_bill_number: {
                            validators: {
                                notEmpty: {
                                    message: 'Invoice / Bill number is required',
                                },
                            },
                        },
                        expense_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'The expense amount is required',
                                },
                                numeric: {
                                    message: 'The expense amount should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 1,
                                    max: 99999999,
                                    message: 'The value must be between 1 to 99999999',
                                },
                            },
                        },
                        // office_id: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Charging office is required',
                        //         },
                        //     },
                        // },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
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
                    var formData = new FormData();
                    $('#travelExpenseForm input, #travelExpenseForm select, #travelExpenseForm textarea')
                        .each(
                            function(index) {
                                var input = $(this);
                                formData.append(input.attr('name'), input.val());
                            });
                    var attachmentFiles = expenseForm.querySelector('[name="attachment"]').files;
                    if (attachmentFiles.length > 0) {
                        formData.append('attachment', attachmentFiles[0]);
                    }

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#travelClaimEditForm').find('#total_expense_amount').text(response
                            .travelClaim
                            .total_expense_amount);
                        $('#travelClaimEditForm').find('#total_itinerary_amount').text(response
                            .travelClaim
                            .total_itinerary_amount);
                        $('#travelClaimEditForm').find('#total_amount').text(response
                            .travelClaim
                            .total_amount);
                        $('#travelClaimEditForm').find('[name="refundable_amount"]').val(
                            response
                            .travelClaim.refundable_amount);
                        expenseTable.ajax.reload();
                    }
                    ajaxSubmitFormData($url, 'POST', formData, successCallback);
                });

                $(expenseForm).find('[name="expense_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: '{!! $travelClaim->travelRequest->departure_date->format('Y-m-d') !!}',
                    endDate: '{!! $travelClaim->travelRequest->return_date->format('Y-m-d') !!}',
                    zIndex: 2048,
                }).on('change', function(e) {
                    fv.revalidateField('expense_date');
                });

                $(expenseForm).on('change', '[name="activity_code_id"]', function(e) {
                    fv.revalidateField('activity_code_id');
                });
            });
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.claims.itineraries.index', $travelClaim->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'departure_date',
                    name: 'departure_date'
                },
                {
                    data: 'departure_place',
                    name: 'departure_place'
                },
                {
                    data: 'arrival_date',
                    name: 'arrival_date'
                },
                {
                    data: 'arrival_place',
                    name: 'arrival_place'
                },
                {
                    data: 'overnights',
                    name: 'overnights'
                },
                {
                    data: 'dsa_unit_price',
                    name: 'dsa_unit_price'
                },
                {
                    data: 'percentage_charged',
                    name: 'percentage_charged'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                // {
                //     data: 'charging_office',
                //     name: 'charging_office'
                // },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('click', '.open-itinerary-modal-form', function(e) {
            e.preventDefault();
            $('#claimItineraryModal').find('.modal-content').html('');
            $('#claimItineraryModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const claimItineraryForm = document.getElementById('claimItineraryForm');
                $(claimItineraryForm).find(".select2").each(function() {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });
                const fv = FormValidation.formValidation(claimItineraryForm, {
                    fields: {
                        percentage_charged: {
                            validators: {
                                notEmpty: {
                                    message: 'Percentage charged is required',
                                },
                                numeric: {
                                    message: 'The percentage should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 100,
                                    message: 'The value must be between 0 to 100',
                                },
                            },
                        },
                        charging_office: {
                            validators: {
                                notEmpty: {
                                    message: 'Charging office is required',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
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
                    var formData = new FormData();
                    $('#claimItineraryForm input, #claimItineraryForm select, #claimItineraryForm textarea')
                        .each(function(index) {
                            var input = $(this);
                            formData.append(input.attr('name'), input.val());
                        });
                    var attachmentFiles = claimItineraryForm.querySelector('[name="attachment"]')
                        .files;
                    if (attachmentFiles.length > 0) {
                        formData.append('attachment', attachmentFiles[0]);
                    }

                    var successCallback = function(response) {
                        $('#claimItineraryModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#travelClaimEditForm').find('#total_expense_amount').text(response
                            .travelClaim.total_expense_amount);
                        $('#travelClaimEditForm').find('#total_itinerary_amount').text(response
                            .travelClaim.total_itinerary_amount);
                        $('#travelClaimEditForm').find('#total_amount').text(response
                            .travelClaim.total_amount);
                        $('#travelClaimEditForm').find('[name="refundable_amount"]').val(
                            response.travelClaim.refundable_amount);
                        itineraryTable.ajax.reload();
                    }
                    ajaxSubmitFormData($url, 'POST', formData, successCallback);
                });
                $(claimItineraryForm).on('change', '[name="percentage_charged"]', function(e) {
                    calculationTotalDsaAmount(this);
                });

                function calculationTotalDsaAmount($element) {
                    dsaUnitPrice = parseFloat($($element).closest('form').find('[name="dsa_unit_price"]')
                        .val());
                    overnights = parseFloat($($element).closest('form').find('[name="overnights"]').val());
                    percentageCharged = parseFloat($($element).closest('form').find(
                        '[name="percentage_charged"]').val());
                    dsaTotalAmount = dsaUnitPrice * overnights;
                    totalAmount = dsaTotalAmount ? parseFloat(dsaTotalAmount * percentageCharged / 100) : 0;
                    $($element).closest('form').find('[name="total_amount"]').val(totalAmount);
                }
            });
        });
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
                            <a href="{{ route('travel.claims.index') }}" class="text-decoration-none text-dark">Travel
                                Claims
                            </a>
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
                        Travel Request Details
                    </div>
                    @include('TravelRequest::Partials.detail')
                </div>
                @if ($travelClaim->returnLog()->exists())
                    <div class="card">
                        <div class="card-header fw-bold text-danger">
                            Return Remarks
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 list-unstyled list-py-2 text-dark">

                                <li class="position-relative">
                                    <div class="gap-2 d-flex align-items-start">
                                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> {{ $travelClaim->returnLog->log_remarks }}</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-9">
                <form action="{{ route('travel.claims.update', $travelClaim->id) }}" id="travelClaimEditForm" method="post"
                    enctype="multipart/form-data" autocomplete="off">

                    <div class="card">
                        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                            <span> Travel Expenses</span>
                            @if ($authUser->can('update', $travelClaim))
                                <button data-toggle="modal"
                                    class="m-2 btn btn-primary btn-sm text-capitalize open-expense-modal-form"
                                    href="{!! route('travel.claims.expenses.create', $travelClaim->id) !!}"><i class="bi-plus"></i> Add New
                                    Expense
                                </button>
                            @endif
                        </div>
                        <div class="container-fluid-s">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="expenseTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th scope="col">{{ __('label.activity') }}</th>
                                                    {{-- <th scope="col">{{ __('label.donor') }}</th> --}}
                                                    <th scope="col">{{ __('label.date') }}</th>
                                                    <th scope="col">{{ __('label.description') }}</th>
                                                    <th scope="col">{{ __('label.amount') }}</th>
                                                    <th scope="col">{{ __('label.invoice-bill-number') }}</th>
                                                    {{-- <th scope="col">Charging Office</th> --}}
                                                    <th scope="col">{{ __('label.attachment') }}</th>
                                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3">{{ __('label.sub-total') }}</td>
                                                    <td colspan="4" id="total_expense_amount">
                                                        {{ $travelClaim->total_expense_amount }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card">
                        <div class="card-header fw-bold">
                            Travel Itineraries
                        </div>
                        <div class="container-fluid-s">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="itineraryTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th scope="col">{{ __('label.from-date') }}</th>
                                                    <th scope="col">{{ __('label.from') }}</th>
                                                    <th scope="col">{{ __('label.to-date') }}</th>
                                                    <th scope="col">{{ __('label.to') }}</th>
                                                    <th scope="col">{{ __('label.overnights') }}</th>
                                                    <th scope="col">{{ __('label.dsa-rate') }}</th>
                                                    <th scope="col">{{ __('label.percentage') }}</th>
                                                    <th scope="col">{{ __('label.total-dsa') }}</th>
                                                    {{-- <th scope="col">Charging Office</th> --}}
                                                    <th scope="col">{{ __('label.remarks') }}</th>
                                                    <th scope="col">{{ __('label.attachment') }}</th>
                                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7">{{ __('label.sub-total') }}</td>
                                                    <td id="total_itinerary_amount">
                                                        {{ $travelClaim->total_itinerary_amount }}</td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7">{{ __('label.grand-total') }}</td>
                                                    <td id="total_amount">
                                                        {{ $travelClaim->total_amount }}
                                                    </td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7">{{ __('label.advance-amount') }}
                                                    </td>
                                                    <td colspan="2">
                                                        <input type="number" class="form-control" name="advance_amount"
                                                            value="{{ $travelClaim->advance_amount }}" />
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7">
                                                        {{ __('label.refundable-reimbursable-amount') }}
                                                    </td>
                                                    <td colspan="2">
                                                        <input readonly class="form-control" name="refundable_amount"
                                                            value="{{ $travelClaim->refundable_amount }}" />
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-bold">Process</div>
                        <div class="card-body">
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="Fdname" class="form-label required-label">
                                            Approver
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="form-check form-switch">
                                        @php $selectedReviewerId = old('reviewer_id') ?: $travelClaim->reviewer_id; @endphp
                                        <select name="approver_id"
                                            class="select2 form-control
                                        @if ($errors->has('reviewer_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select an Approver</option>
                                            @foreach ($supervisors as $approver)
                                                <option value="{{ $approver->id }}" @selected($approver->id == (old('approver_id') ?: $travelClaim->approver_id))>
                                                    {{ $approver->full_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('reviewer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reviewer_id">
                                                    {!! $errors->first('reviewer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="Fdname" class="form-label required-label">
                                            Send To
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="form-check form-switch">
                                        @php $selectedReviewerId = old('reviewer_id') ?: $travelClaim->reviewer_id; @endphp
                                        <select name="reviewer_id"
                                            class="select2 form-control
                                        @if ($errors->has('reviewer_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a Verifier</option>
                                            @foreach ($reviewers as $reviewer)
                                                <option value="{{ $reviewer->id }}"
                                                    {{ $reviewer->id == $selectedReviewerId ? 'selected' : '' }}>
                                                    {{ $reviewer->full_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('reviewer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reviewer_id">
                                                    {!! $errors->first('reviewer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="flexSwitchCheckChecked" name="agree"
                                            @if ($travelClaim->agree_at) checked @endif>
                                        <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="Fdname" class="m-0">
                                            I certify that the following information is correct and per the approved Travel
                                            authorization. I authorize HERDi to treat this as the final claim and I will
                                            repay any travel allowances to which I am not entitled. If office provides
                                            breakfast, lunch, dinner or accommodation, this must be deducted from claim,
                                            i.e. % change should be 100%-deducted %
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gap-2 justify-content-end d-flex">
                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                        </button>
                        <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                            Submit
                        </button>
                        <a href="{!! route('travel.claims.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                    {!! method_field('PUT') !!}
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </section>

    <div class="modal fade" id="claimItineraryModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="claimItineraryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
@stop
