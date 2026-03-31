<div class="card-header fw-bold">Add New Insurance Record</div>
<form class="needs-validation" action="{{ route('employees.insurance.store', $employee->id) }}" method="post"
      id="insuranceAddForm" enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Insurer</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if($errors->has('insurer')) is-invalid @endif" name="insurer" placeholder="Insurer name"
                       value="{{ old('insurer') }}" />
                @if($errors->has('insurer'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="insurer">{!! $errors->first('insurer') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Premium Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control @if($errors->has('amount')) is-invalid @endif" name="amount" placeholder="Premium Amount"
                       value="{{ old('amount') }}">
                @if($errors->has('amount'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="amount">{!! $errors->first('amount') !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Fdname" class="form-label required-label">Paid Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input name="paid_date" class="form-control" placeholder="Paid Date" value="{!! old('paid_date') !!}" >
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationcitizenship" class="form-label">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control @if($errors->has('attachment')) is-invalid @endif" name="attachment"/>
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                @if($errors->has('attachment'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                    </div>
                @endif
            </div>
        </div>

    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
@push('scripts')
    <script type="text/javascript">
        var end_date = "{!! date('Y-m-d') !!}";
        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('insuranceAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    insurer: {
                        validators: {
                            notEmpty: {
                                message: 'Insurer name is required',
                            },
                        },
                    },
                    amount: {
                        validators: {
                            notEmpty: {
                                message: 'Premium amount is required',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 1',
                                min: 1,
                            },
                        },
                    },
                    // payroll_fiscal_year_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'FY is required',
                    //         },
                    //     },
                    // },
                    paid_date: {
                        validators: {
                            notEmpty: {
                                message: 'Training from date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: 2097152, // 2048 * 1024
                                message: 'The selected file is not valid image or pdf or must not be greater than 2 MB.',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    excluded: new FormValidation.plugins.Excluded(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),

                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $('[name="paid_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '2022-04-02',
                endDate: end_date,
            }).on('change', function (e) {
                fv.revalidateField('paid_date');
            });
        });
    </script>
@endpush
