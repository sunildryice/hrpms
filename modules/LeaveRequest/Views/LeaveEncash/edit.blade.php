@extends('layouts.container')
@section('title', 'Edit Leave Encashment Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#leave-encash-menu').addClass('active');
        });

        var employeeId = '{{ auth()->user()->employee_id }}'
        var leaveTypeId = '{{ $leaveEncash->leave_type_id }}'
        var employeeId = '12'

        function isEmpty(str) {
            return (!str || str.length === 0);
        }

        var checkBalanceOverflow = function($element) {
            var availableBalance = $($element).closest('form').find('[name="available_balance"]').val();
            var encashBalance = $($element).closest('form').find('[name="encash_balance"]').val();
            if (parseInt(encashBalance) > parseInt(availableBalance)) {
                toastr.warning("Encase Balance cannot excees available balance", 'Warning', {
                    timeOut: 9000
                });;
            }
        }

        console.log($('[name="leave_type_id"]')
            .val());

        $(document).ready(function() {
            $(".select2").select2({
                width: '100%',
                dropdownAutoWidth: true
            });



        });
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('leaveEncashAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    leave_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Leave Type is required',
                            },
                        },
                    },
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'A reviewer is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is required',
                            },
                        },
                    },
                    encash_balance: {
                        validators: {
                            notEmpty: {
                                message: 'The encash balance is required',
                            },
                            regexp: {
                                regexp: /^[0-9]+$/,
                                message: 'Encash must be a valid number.',
                            },
                            callback: {
                                message: 'Encash balance cannot exceed available balance',
                                callback: function(value, validator, $field) {
                                    var availableBalance = parseFloat($('[name="available_balance"]')
                                        .val());
                                    var encashBalance = parseFloat(value.value);
                                    return encashBalance <= availableBalance;
                                }
                            }
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

            $('[name="encash_balance"]').prop('disabled', true);

            var leaveTypes;

            $(form).on('change', '[name="employee_id"]', function(e) {
                ele = $(this);
                employeeId = ele.val();
                var field = '<option value="">Select a Leave Type</option>';
                if (employeeId) {
                    var url = baseUrl + '/api/employee/' + employeeId + '/leaves/fetch';
                    var successCallback = function(response) {
                        leaveTypes = response.leaveTypes;
                        $(ele).closest('form').find('[name="balance"]').val(null);
                        $(ele).closest('form').find('[name="available_balance"]').val(null);
                        response.leaveTypes.forEach(leave => {
                            var selected = leave.leave_type.id == leaveTypeId ? 'selected' : '';
                            field += '<option value="' + leave.leave_type.id +
                                '" data-leave="' + leave.id +
                                '" ' + selected + '>' + leave.leave_type.title + '</option>';
                        });
                        $(ele).closest('form').find('[name="leave_type_id"]').html(field).trigger(
                            'change');
                    }
                    var errorCallback = function(error) {
                        console.log(error);

                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                }
            }).on('change', '[name="leave_type_id"]', function(e) {
                $element = $(this);
                var leaveTypeId = $element.val();
                var employeeLeaveId = $($element).find(':selected').attr('data-leave');
                if (leaveTypeId) {
                    var leave = leaveTypes.filter(element => {
                        return element.leave_type_id == leaveTypeId;
                    })[0];
                    var leaveBasis = leave.leave_type.leave_basis == 2 ? 'Hours' : 'Days';
                    var balance = 'Available Balance : ' + leave.balance + ' - ' + leaveBasis;
                    $($element).closest('form').find('[name="balance"]').val(balance);
                    $($element).closest('form').find('[name="available_balance"]').val(leave.balance);

                    if ($('[name="balance"]').val()) {
                        $('[name="encash_balance"]').prop('disabled', false);
                    }
                    fv.revalidateField('encash_balance');

                } else {
                    $($element).closest('form').find('[name="balance"]').val(0);
                    $('[name="encash_balance"]').prop('disabled', true);
                }
                fv.revalidateField('leave_type_id');
                fv.revalidateField('encash_balance');

            }).on('change', '[name="reviewer_id"]', function(e) {
                fv.revalidateField('reviewer_id');
            });

            if ($('[name="employee_id"]').val()) {
                $(form).find('[name="employee_id"]').trigger('change');
            }
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
                            <a href="{{ route('leave.encash.index') }}" class="text-decoration-none text-dark">Leave
                                Encashment Requests</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>


    <section class="registration">
        <div class="card shadow-sm border-0 rounded">
            <form action="{{ route('leave.encash.update', $leaveEncash->id) }}" id="leaveEncashAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="card-header">
                    {{-- {{ date('F', strtotime($leaveTypes[0]->reported_date)) }} --}}
                </div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start  h-100">
                                <label for="validationEmployee" class="form-label required-label">Select Employee</label>
                            </div>
                        </div>

                        <div class="col-lg-7 mb-3 mb-lg-0">
                            <select name="employee_id" class="select2 form-control" data-width="100%">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-employee="{{ $employee->id }}"
                                        {{ $employee->id == $leaveEncash->employee_id ? 'selected' : '' }}>
                                        {{ $employee->getFullName() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('leave_type_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="leave_type_id">
                                        {!! $errors->first('leave_type_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start  h-100">
                                <label for="validationleavetype" class="form-label required-label">Leave
                                    Type</label>
                            </div>
                        </div>

                        <div class="col-lg-7 mb-3 mb-lg-0">
                            <select name="leave_type_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Leave Type</option>

                            </select>
                            @if ($errors->has('leave_type_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="leave_type_id">
                                        {!! $errors->first('leave_type_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" disabled name="balance" value="" />
                            <input type="hidden" class="form-control" readonly name="available_balance" value="" />
                        </div>
                    </div>

                    <div class="row
                                mb-3">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start  h-100">
                                <label for="validationEncashBalance" class="form-label">Encash Balance</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <input type="number" class="form-control @if ($errors->has('remarks')) is-invalid @endif"
                                name="encash_balance" value="{{ $leaveEncash->encash_balance }}" />
                            @if ($errors->has('encash_balance'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start  h-100">
                                <label for="validationRemarks" class="form-label">Remarks</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ $leaveEncash->encash_balance }}</textarea>
                            @if ($errors->has('remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationReviewer" class="form-label required-label">Select reviewer</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <select name="reviewer_id"
                                class="select2 form-control
                                                @if ($errors->has('reviewer_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select a Reviewer</option>
                                @foreach ($reviewers as $reviewer)
                                    <option value="{{ $reviewer->id }}"
                                        {{ $reviewer->id == $leaveEncash->reviewer_id ? 'selected' : '' }}>
                                        {{ $reviewer->getFullName() }}</option>
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

                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationApprover" class="form-label required-label">Select Approvers</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <select name="approver_id"
                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select an Approver</option>
                                @foreach ($approvers as $approver)
                                    <option value="{{ $approver->id }}"
                                        {{ $approver->id == $leaveEncash->approver_id ? 'selected' : '' }}>
                                        {{ $approver->getFullName() }}</option>
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

                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                    </button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                        Submit
                    </button>
                    <a href="{!! route('leave.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>



@stop
