@extends('layouts.container')

@section('title', 'Create Lieu Leave Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#lieu-leave-requests-index').addClass('active');

            $('#project_id, #send_to').addClass('select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            const form = document.getElementById('lieuLeaveRequestAddForm');

            $('[name="leave_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function() {
                if (window.fv) {
                    // fv.revalidateField('leave_date');
                    let leaveDate = $(this).val();
                    checkAvailableStatus(leaveDate);
                }
            });

            function checkAvailableStatus(month) {
                const url = "{{ route('api.lieu.leave.check.status', ':month') }}".replace(':month', month);

                const successCallback = function(response) {
                    if (response.status === 'success') {
                        $('#balance').val(response.data.available_balance_status);
                    }
                };

                const errorCallback = function(error) {
                    console.error(error);
                };

                ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
            }


            if (form) {
                window.fv = FormValidation.formValidation(form, {
                    fields: {
                        leave_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The Leave date is required'
                                }
                            }
                        },
                        reason: {
                            validators: {
                                notEmpty: {
                                    message: 'Reason is required'
                                }
                            }
                        },
                        send_to: {
                            validators: {
                                notEmpty: {
                                    message: 'The approver is required'
                                }
                            }
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.mb-3',
                            eleInvalidClass: 'is-invalid',
                            eleValidClass: 'is-valid',
                        }),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                });
            }

            // Revalidate selects on change
            $(form).on('change', '#project_id', function() {
                fv.revalidateField('project_id');
            });
            $(form).on('change', '#send_to', function() {
                fv.revalidateField('send_to');
            });

            // Initialize remove buttons state on load
            updateRemoveButtons();
        });
    </script>
@endsection

@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('lieu.leave.requests.index') }}" class="text-decoration-none text-dark">
                                Lieu Leave Requests
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Create Lieu Leave Request
                </h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded">
        <div class="card-body">
            <form action="{{ route('lieu.leave.requests.store') }}" id="lieuLeaveRequestAddForm" method="POST"
                autocomplete="off">
                @csrf
                <div class="row">
                    <div class="mb-3 col-2">
                        <label for="leave_date" class="form-label required-label">Leave Date</label>
                    </div>
                    <div class="mb-3 col-8">
                        <input type="date" class="form-control" id="leave_date" name="leave_date"
                            value="{{ old('leave_date') }}" required>
                    </div>
                    <div class="mb-3 col-2">
                        <input type="text" class="form-control" id="balance" name="balance"
                            value="{{ old('balance', '') }}" required readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-2">
                        <label for="reason" class="form-label required-label">Reason</label>
                    </div>
                    <div class="col-lg-10">
                        <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-lg-2">
                        <div class="d-flex align-items-start h-100">
                            <label for="validationSubstitutes" class="form-label">Substitutes</label>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        <select name="substitutes[]" class="select2 @if ($errors->has('substitutes')) is-invalid @endif"
                            data-width="100%" multiple>
                            <option value="">Select substitutes</option>
                            @foreach ($substitutes as $staff)
                                <option value="{{ $staff->id }}"
                                    {{ in_array($staff->id, old('substitutes', [])) ? 'selected' : '' }}>
                                    {{ $staff->getFullNameWithCode() }}
                                </option>
                            @endforeach
                        </select>

                        @if ($errors->has('substitutes'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div data-field="substitutes">
                                    {!! $errors->first('substitutes') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-lg-2">
                        <label for="send_to" class="form-label required-label">Send To</label>
                    </div>
                    <div class="col-lg-10">
                        <select class="form-control" id="send_to" name="send_to" required>
                            <option value="">Select Approver</option>
                            @foreach ($supervisors as $id => $fullName)
                                <option value="{{ $id }}" {{ old('send_to') == $id ? 'selected' : '' }}>
                                    {{ $fullName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="gap-2 border-0 card-footer justify-content-end d-flex lieu-leave-form-actions">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save</button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">Submit</button>
                    <a href="{{ route('lieu.leave.requests.index') }}" class="btn btn-danger btn-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
