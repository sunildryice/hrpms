@extends('layouts.container')

@section('title', __('label.payslip'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#payroll-batches-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')

    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
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
                                <a href="{!! route('payroll.batches.sheets.index', $payrollBatch->id) !!}" class="text-decoration-none">{!! __('label.payroll-sheets') . ' || ' . $payrollBatch->getFiscalYear() . '/' . $payrollBatch->getMonth() !!}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                </div>
            </div>
        </div>
        <div class="container-fluid-s">

            <div class="payslip-main">
                <div class="payslip-header">
                    <div class="p-business-area d-flex justify-content-between">
                        <div class="bs-info mb-3">
                            <img style="max-width: 100px;" src="">
                            <h3 class="bs-title mb-0"> HERD International </h3>
                            <span class="bs-subtext">Bagdol-4, Lalitpur, Nepal</span>
                        </div>
                        <div class="bs-m-info">
                            <h4 class="m-0 lh1 mt-4 fs-4 text-uppercase fw-bold text-primary">Payslip</h4>
                        </div>
                    </div>
                    <div class="p-emp-info ">

                        <div class="row">

                            <div class="col-md-4 col-4">
                                <div class="em-ifno">
                                    <div class="em-title text-uppercase fw-bold mb-2 text-primary">Employee Information
                                    </div>
                                    <h3 class="text-uppercase fs-7 mb-0">Full Name : {{ $payrollSheet->employee->getFullName() }}</h3>
                                    <p class="mb-0">Employee Code : {{ $payrollSheet->employee->getEmployeeCode() }}</p>
                                    <p class="mb-0">Mobile No : {{ $payrollSheet->employee->mobile_number }}</p>
                                    <p class="mb-0">Email Address : {{ $payrollSheet->employee->official_email_address }}</p>
                                    <p class="mb-0">Married : {{ $payrollSheet->getMarriedStatus() }}</p>
                                    <p class="mb-0">Disabled : {{ $payrollSheet->getDisabledStatus() }}</p>
                                    <p class="mb-0">Remote Category : {{ $payrollSheet->remote_category }}</p>
                                    <p class="mb-0">Gender : {{ $payrollSheet->employee->getGender() }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 col-5 offset-3 offset-md-4">
                                <div class="em-details">
                                    <div class="emp-tbl">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <tbody>
                                                    <tr class="tr-header">
                                                        <td colspan="2">Pay Date</td>
                                                        <td>Period</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">{{ $payrollSheet->payrollBatch->getPostedDate() }}</td>
                                                        <td>{!! $payrollSheet->payrollBatch->getFiscalYear() . $payrollSheet->payrollBatch->getMonth() !!}</td>
                                                    </tr>
                                                    <tr class="tr-header">
                                                        <td>SSF No.</td>
                                                        <td>PAN No.</td>
                                                        <td>CIT No.</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $payrollSheet->employee->finance->ssf_number }}</td>
                                                        <td>{{ $payrollSheet->employee->pan_number }}</td>
                                                        <td>{{ $payrollSheet->employee->finance->cit_number }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="pay-meth">
{{--                                        Payment Method : <span></span>--}}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="payslip-body">
                    <div class="table-responsive">
                        <table id="tblPaySlip" class="table table-borderless table-sm">
                            <thead>
                                <tr class="s-totle" style="font-weight: bold;background: #eaeaea;color: #515151;">
                                    <td>Earnings/Deduction</td>
                                    <td></td>
                                    <td align="right">Amount</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($benefitDetails as $detail)
                                    <tr>
                                        <td>{!! $detail->paymentItem->getPaymentItem() !!}</td>
                                        <td></td>
                                        <td align="right">{!! $detail->getAmount() !!}</td>
                                    </tr>
                                @endforeach
                                <tr class="s-totle" style="font-weight: bold;background: #eaeaea;color: #515151;">
                                    <td></td>
                                    <td align="right"> Gross Pay</td>
                                    <td align="right">{{ $payrollSheet->getGrossAmount() }}</td>
                                </tr>
                                @foreach ($deductionDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->paymentItem->getPaymentItem() }}</td>
                                        <td></td>
                                        <td align="right">{{ $detail->getAmount() }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2">TDS</td>
                                    <td align="right">{{ $payrollSheet->getTaxAmount() }}</td>
                                </tr>
                                <tr class="s-totle" style="font-weight: bold;background: #eaeaea;color: #515151;">
                                    <td align="right" width="80%" colspan="2">Deduction Total</td>
                                    <td align="right">
                                        @php $totalDeductionAmount = $deductionDetails->sum('amount') + $payrollSheet->tax_amount ?: $payrollSheet->tds_amount  @endphp
                                        {{ number_format($totalDeductionAmount, 2) }}
                                    </td>
                                </tr>
                                <tr class="s-totle" style="font-weight: bold;background: #eaeaea;color: #515151;">
                                    <td align="right" width="80%" colspan="2">Net Pay</td>
                                    <td align="right">{{ $payrollSheet->getNetAmount() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
