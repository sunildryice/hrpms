@extends('layouts.container')

@section('title', __('label.payroll-batches'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#review-payroll-batches-menu').addClass('active');

            var oTable = $('#batchDetailTable').DataTable({
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('payroll.batches.sheets.index', $payrollBatch->id) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'gross_amount', name: 'gross_amount'},
                    {data: 'total_deduction_amount', name: 'total_deduction_amount'},
                    {data: 'tax_amount', name: 'tax_amount'},
                    {data: 'net_amount', name: 'net_amount'},
                    {data: 'updated_at', name: 'updated_at', searchable: false, orderable: false},
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('payrollBatchReviewForm');
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
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function (e) {
                fv.revalidateField('status_id');
            });
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
                                   class="text-decoration-none text-dark">{{ __('label.home') }}</a></li>
                            <li class="breadcrumb-item">
                                <a href="#" class="text-decoration-none">{{ __('label.payroll') }}</a></li>
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
            <div class="card">
                <div class="card-header fw-bold">
                    For Period : {!! $payrollBatch->getFiscalYear() .'/'. $payrollBatch->getMonth() !!} <br />
                    Verifier : {!! $payrollBatch->getReviewerName() !!} <br />
                    Approver : {!! $payrollBatch->getApproverName() !!} <br />
                    Description : {!! $payrollBatch->description !!}
                </div>
                <div class="card-body">
                    <table class="table" id="batchDetailTable">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">{{ __('label.sn') }}</th>
                            <th scope="col">{{ __('label.employee') }}</th>
                            <th scope="col">{{ __('label.gross-amount') }}</th>
                            <th scope="col">{{ __('label.deduction-amount') }}</th>
                            <th scope="col">{{ __('label.tax-amount') }}</th>
                            <th scope="col">{{ __('label.net-amount') }}</th>
                            <th scope="col">{{ __('label.updated-on') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header fw-bold">
                    Process
                </div>
                <form action="{{ route('payroll.batches.review.store', $payrollBatch->id) }}"
                      id="payrollBatchReviewForm" method="post"
                      enctype="multipart/form-data" autocomplete="off">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5">
                                @foreach($payrollBatch->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40"
                                             class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person"></i>
                                        </div>
                                      <div class="w-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                    <span class="me-2">{{ $log->getCreatedBy() }}</span>
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
                            <div class="col-lg-7">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Status </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" class="select2 form-control"
                                                data-width="100%">
                                            <option value="">Select Status</option>
                                            <option value="{{ config('constant.RETURNED_STATUS') }}">Return to Requester</option>
                                            <option value="{{ config('constant.VERIFIED_STATUS') }}">Verify</option>
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
                    </div>
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                            Submit
                        </button>
                        <a href="{!! route('payroll.batches.review.index') !!}"
                           class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop
