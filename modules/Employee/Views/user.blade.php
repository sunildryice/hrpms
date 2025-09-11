<div class="card-header fw-bold">Assign User</div>
@php
$action = $employee->user ? route('employees.user.update', $employee->id) : route('employees.user.store', $employee->id);
$selectedRoleIds = $employee->user ? $employee->user->roles->pluck('id')->toArray() : [3];
@endphp
<form class="needs-validation" action="{{ $action }}" method="post" id="employeeRoleForm"
    enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="username" class="form-label">User Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control @if ($errors->has('email_address')) is-invalid @endif"
                    name="email_address" value="{{ $employee->official_email_address }}" readonly />
                @if ($errors->has('email_address'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="email_address">{!! $errors->first('email_address') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="roles" class="form-label required-label">Roles</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="hidden" name="role_ids"
                    value="{{ $selectedRoleIds ? implode(',', $selectedRoleIds) : $roles->last()->id }}" readonly>
                <select name="roles" class="form-control" style="width: 100%" multiple>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @if (in_array($role->id, $selectedRoleIds)) selected @endif>
                            {{ $role->role }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        @if ($employee->user)
            {!! method_field('PUT') !!}
            <button type="submit" class="btn btn-primary btn-sm">Update Roles</button>
        @else
            <button type="submit" class="btn btn-primary btn-sm">Send Link</button>
        @endif
    </div>
    {!! csrf_field() !!}
</form>

@push('scripts')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('employeeRoleForm');
            const roleField = $(form.querySelector('[name="roles"]'));
            const fv = FormValidation.formValidation(form, {
                fields: {
                    username: {
                        validators: {
                            notEmpty: {
                                message: 'Username is required',
                            },
                        },
                    },
                    roles: {
                        validators: {
                            callback: {
                                message: 'Please choose roles.',
                                callback: function(input) {
                                    // Get the selected options
                                    const options = roleField.select2('data');
                                    return options != null && options.length >= 1;
                                },
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

            roleField.select2().on('change.select2', function() {
                fv.revalidateField('roles');
                $('#employeeRoleForm').find('[name="role_ids"]').val(roleField.val());
            });
        });
    </script>
@endpush
