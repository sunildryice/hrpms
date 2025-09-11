@extends('layouts.container')

@section('title', 'Edit Payroll Sheet')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#payroll-batches-menu').addClass('active');

            $(document).on('click', '.open-detail-modal-form', function (e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                    const form = document.getElementById('benefitForm');
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            payment_item_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Payment item is required',
                                    },
                                },
                            },
                            amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'Amount is required',
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
                    }).on('core.form.valid', function (event) {
                        $url = fv.form.action;
                        $form = fv.form;
                        data = $($form).serialize();
                        var successCallback = function (response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            console.log(response);
                            oTable.ajax.reload();
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
                                    <a href="{{ route('dashboard.index') }}"
                                       class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{!! route('payroll.batches.index') !!}"
                                       class="text-decoration-none">{{ __('label.payroll-batches') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{!! route('payroll.batches.sheets.index', $payrollBatch->id) !!}"
                                       class="text-decoration-none">{!! __('label.payroll-sheets') . ' || ' . $payrollBatch->getFiscalYear() . '/' . $payrollBatch->getMonth() !!}</a>
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
                                Employee Details
                            </div>
                            <div class="card-body">
                                <div class="p-1">
                                    <ul class="list-unstyled list-py-2 text-dark mb-0">
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-person-bounding-box dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">{{ $payrollSheet->employee->getFullName() }}</div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip"
                                                  title="Full Name"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-calendar-date dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section"> {!! $payrollSheet->employee->getEmployeeCode() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Request Date"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-envelope dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section"> {!! $payrollSheet->employee->official_email_address !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Email Address"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-phone dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">{!! $payrollSheet->employee->mobile_number !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="District Name"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-circle dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">Married
                                                    : {!! $payrollSheet->getMarriedStatus() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Married"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-circle dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">Disabled
                                                    : {!! $payrollSheet->getDisabledStatus() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Disabled"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-circle dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">Remote Category
                                                    : {!! $payrollSheet->remote_category !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Married"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-circle dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">Gender
                                                    : {!! $payrollSheet->employee->getGender() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Married"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-calendar-date dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section"> {!! $payrollSheet->payrollBatch->getPostedDate() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Pay Date"></span>
                                        </li>

                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-calendar-date dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section"> {!! $payrollSheet->payrollBatch->getFiscalYear() . $payrollSheet->payrollBatch->getMonth() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="For Period"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-calendar-date dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section"> {!! $payrollSheet->payrollBatch->getPostedDate() !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="Pay Date"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-sort-numeric-up dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">SSF
                                                    No. {!!  $payrollSheet->employee->finance->ssf_number !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="SSF Number"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-sort-numeric-up dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">PAN
                                                    No. {!!  $payrollSheet->employee->pan_number !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="SSF Number"></span>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"><i
                                                        class="bi-sort-numeric-up dropdown-item-icon"></i></div>
                                                <div
                                                    class="d-content-section">CIT
                                                    No. {!!  $payrollSheet->employee->finance->cit_number !!} </div>
                                            </div>
                                            <span class="stretched-link" rel="tooltip" title="SSF Number"></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Payroll Sheet Details
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="p2">
                                            <div class="d-flex align-items-center add-info justify-content-end">
                                                <button data-toggle="modal"
                                                        class="btn btn-primary btn-sm open-detail-modal-form"
                                                        href="{!! route('payroll.batches.sheets.details.create', [$payrollBatch->id, $payrollSheet->id]) !!}">
                                                    <i class="bi-plus"></i> Add New Benefit/Deduction Item
                                                </button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table" id="payrollSheetDetailTable">
                                                <thead class="thead-light">
                                                <tr>
                                                <tr class="s-totle">
                                                    <td>Earnings/Deduction</td>
                                                    <td></td>
                                                    <td>Amount</td>
                                                    <th scope="col">{{ __('label.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($benefitDetails as $detail)
                                                    <tr>
                                                        <td>{!! $detail->paymentItem->getPaymentItem() !!}</td>
                                                        <td></td>
                                                        <td>{!! $detail->getAmount() !!}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                                <tr class="s-totle">
                                                    <td></td>
                                                    <td> Gross Pay</td>
                                                    <td>{{ $payrollSheet->getGrossAmount() }}</td>
                                                    <td></td>
                                                </tr>
                                                @foreach ($deductionDetails as $detail)
                                                    <tr>
                                                        <td>{{ $detail->paymentItem->getPaymentItem() }}</td>
                                                        <td></td>
                                                        <td>{{ $detail->getAmount() }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="2">TDS</td>
                                                    <td>{{ $payrollSheet->getTaxAmount() }}</td>
                                                    <td></td>
                                                </tr>
                                                <tr class="s-totle">
                                                    <td colspan="2">Deduction Total</td>
                                                    <td>
                                                        @php $totalDeductionAmount = $deductionDetails->sum('amount') + $payrollSheet->tax_amount ?: $payrollSheet->tds_amount  @endphp
                                                        {{ number_format($totalDeductionAmount, 2) }}
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr class="s-totle"
                                                    style="font-weight: bold;background: #eaeaea;color: #515151;">
                                                    <td colspan="2">Net Pay</td>
                                                    <td>{{ $payrollSheet->getNetAmount() }}</td>
                                                    <td></td>
                                                </tr>
                                                </tbody>
                                            </table>
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
