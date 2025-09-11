@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Review Recommended Payment Sheet')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#review-recommended-payment-sheets-menu').addClass('active');

            var oTable = $('#paymentSheetDetailTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('payment.sheets.details.index', $paymentSheet->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'bill_number',
                        name: 'bill_number',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'activity',
                        name: 'activity',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'account',
                        name: 'account',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'donor',
                        name: 'donor',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'charged_office',
                        name: 'charged_office',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'vat_amount',
                        name: 'vat_amount'
                    },
                    {
                        data: 'tds_amount',
                        name: 'tds_amount'
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount'
                    },
                ],
                drawCallback: function() {
                    let data = this.api().ajax.json();
                    let table = this[0];
                    let footer = table.getElementsByTagName('tfoot')[0];
                    if (!footer) {
                        footer = document.createElement("tfoot");
                        table.appendChild(footer);
                    }

                    let sum_total_amount = new Intl.NumberFormat('en-IN', {style: 'currency', currency: 'NPR', currencyDisplay: 'narrowSymbol'}).format(data.sum_total_amount);
                    let sum_vat_amount = new Intl.NumberFormat('en-IN', {style: 'currency', currency: 'NPR', currencyDisplay: 'narrowSymbol'}).format(data.sum_vat_amount);
                    let sum_tds_amount = new Intl.NumberFormat('en-IN', {style: 'currency', currency: 'NPR', currencyDisplay: 'narrowSymbol'}).format(data.sum_tds_amount);
                    let sum_net_amount = new Intl.NumberFormat('en-IN', {style: 'currency', currency: 'NPR', currencyDisplay: 'narrowSymbol'}).format(data.sum_net_amount);

                    footer.innerHTML = '';
                    footer.innerHTML = `<tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>${data.sum_total_amount}</td>
                                            <td>${data.sum_vat_amount}</td>
                                            <td>${data.sum_tds_amount}</td>
                                            <td>${data.sum_net_amount}</td>
                                        </tr>`;

                    footer.innerHTML += `<td colspan="8"></td>
                                        <td>Deduction Amount</td>
                                        <td>{{ $paymentSheet->deduction_amount }}</td>`;

                    footer.innerHTML += `<td colspan="8"></td>
                                        <td>Paid Amount</td>
                                        <td>{{ $paymentSheet->paid_amount }}</td>`;

                    if ("{{$paymentSheet->deduction_remarks}}") {
                        footer.innerHTML += `<td colspan="6"></td>
                                            <td>Deduction Remarks :</td>
                                            <td colspan="3">{{ $paymentSheet->deduction_remarks }}</td>`;
                    }
                },
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('paymentSheetReviewForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Remarks is required',
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

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
            }).on('change', '[name="log_remarks"]', function(e) {
                fv.revalidateField('log_remarks');
            });
        });

        // Start - Attachment Scripts Section
        var attachmentTable = $('#attachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('payment.sheets.attachment.index', $paymentSheet->id) }}",
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
        // End - Attachment Scripts Section
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
                                    <a href="{{ route('review.recommended.payment.sheets.index') }}"
                                        class="text-decoration-none">Payment Sheets</a>
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
                                Payment Sheet Details
                            </div>
                            @include('PaymentSheet::Partials.detail')
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Payment Sheet Details
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="paymentSheetDetailTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col">{{ __('label.bill-no') }}</th>
                                                        <th scope="col">{{ __('label.activity') }}</th>
                                                        <th scope="col">{{ __('label.account') }}</th>
                                                        <th scope="col">{{ __('label.donor') }}</th>
                                                        <th scope="col">{{ __('label.charged-office') }}</th>
                                                        <th scope="col">{{ __('label.description') }}</th>
                                                        <th scope="col">{{ __('label.bill-amount') }}</th>
                                                        <th scope="col">{{ __('label.vat-amount') }}</th>
                                                        <th scope="col">{{ __('label.tds-amount') }}</th>
                                                        <th scope="col">{{ __('label.total-amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6" class="text-end">Total</td>
                                                        <td>{{$paymentSheet->total_amount}}</td>
                                                        <td>{{$paymentSheet->vat_amount}}</td>
                                                        <td>{{$paymentSheet->tds_amount}}</td>
                                                        <td>{{$paymentSheet->net_amount}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="9" class="text-end">Deduction</td>
                                                        <td>{{ $paymentSheet->deduction_amount }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="9" class="text-end">Grand Total</th>
                                                        <th>{{ $paymentSheet->net_amount - $paymentSheet->deduction_amount }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
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
                        </div>

                        <div class="card">
                            <div class="card-header fw-bold">
                                Payment Sheet Process
                            </div>
                            <form action="{{ route('review.recommended.payment.sheets.store', $paymentSheet->id) }}"
                                id="paymentSheetReviewForm" method="post" enctype="multipart/form-data"
                                autocomplete="off">
                                <div class="card-body">
                                        <div class="c-b">
                                            @foreach ($paymentSheet->logs as $log)
                                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                    <div width="40" height="40"
                                                        class="rounded-circle mr-3 user-icon">
                                                        <i class="bi-person"></i>
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
                                                        <label for="status_id"
                                                            class="form-label required-label">Status</label>
                                                    </div>
                                                </div>

                                                <div class="col-lg-9">
                                                    <select name="status_id" class="select2 form-control" data-width="100%">
                                                        <option value="">Select a Status</option>
                                                        <option value="{{ config('constant.RETURNED_STATUS') }}">Return to Requester</option>
                                                        <option value="{{ config('constant.RECOMMENDED2_STATUS') }}">Verify</option>
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
                                                        <label for="log_remarks"
                                                            class="form-label required-label">Remarks</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                        name="log_remarks">{{ old('log_remarks') }}</textarea>
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
                                    <a href="{!! route('review.recommended.payment.sheets.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@stop
