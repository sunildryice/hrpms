@extends('layouts.container')

@section('title', __('label.payroll-sheets'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#payroll-batches-menu').addClass('active');

            var oTable = $('#batchDetailTable').DataTable({
                scrollX: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('payroll.batches.sheets.index', $payrollBatch->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'gross_amount',
                        name: 'gross_amount'
                    },
                    {
                        data: 'total_deduction_amount',
                        name: 'total_deduction_amount'
                    },
                    {
                        data: 'tax_amount',
                        name: 'tax_amount'
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'sticky-col'
                    },
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('payrollBatchSubmitForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Reviewer is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required',
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

            $(form).on('change', '[name="reviewer_id"]', function(e) {
                fv.revalidateField('reviewer_id');
            }).on('change', '[name="approver_id"]', function(e) {
                fv.revalidateField('approver_id');
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
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{!! route('payroll.batches.index') !!}" class="text-decoration-none">{!! __('label.payroll-batches') !!}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                @yield('title')
                                || {!! $payrollBatch->getFiscalYear() . '/' . $payrollBatch->getMonth() !!}
                            </li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                        @yield('title')
                    </h4>
                </div>
                <div class="add-info justify-content-end">

                </div>
            </div>
        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
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
                                    <th scope="col">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (auth()->user()->can('submit', $payrollBatch))
                    <div class="card-body">
                        <form id="payrollBatchSubmitForm" method="post" enctype="multipart/form-data"
                            action="{{ route('payroll.batches.update', $payrollBatch->id) }}">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">{{ __('label.send-to') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select class="form-control select2 @if ($errors->has('reviewer_id')) is-invalid @endif"
                                        name="reviewer_id">
                                        <option value="">Select Reviewer</option>
                                        @foreach ($reviewers as $reviewer)
                                            <option value="{!! $reviewer->id !!}">{!! $reviewer->getFullName() !!}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('reviewer_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="reviewer_id">{!! $errors->first('reviewer_id') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">{{ __('label.approver') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select class="form-control select2 @if ($errors->has('approver_id')) is-invalid @endif"
                                        name="approver_id">
                                        <option value="">Select Approver</option>
                                        @foreach ($approvers as $approver)
                                            <option value="{!! $approver->id !!}">{!! $approver->getFullName() !!}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('approver_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="approver_id">{!! $errors->first('approver_id') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            </div>
                            {!! csrf_field() !!}
                        </form>
                    </div>
                @endif

            </div>

        </div>
    </div>

@stop
