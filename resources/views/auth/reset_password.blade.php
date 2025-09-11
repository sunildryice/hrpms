@extends('auth.loginmaster')

@section('title', 'Reset password')

@section('page_js')
    <script>
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('resetPasswordForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    new_password: {
                        validators: {
                            notEmpty: {
                                message: 'The password is required.',
                            },
                        },
                    },
                    confirm_password: {
                        validators: {
                            identical: {
                                compare: function() {
                                    return form.querySelector('[name="new_password"]').value;
                                },
                                message: 'The password and its confirm are not the same',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            // Revalidate the confirmation password when changing the password
            form.querySelector('[name="new_password"]').addEventListener('input', function() {
                fv.revalidateField('confirm_password');
            });
        });
    </script>

    <script>
        var state = false;

        function toggle() {
            if (state) {
                document.getElementById("password").setAttribute("type", "password");
                document.getElementById("eye").style.color = '#7a797e';
                state = false;
            } else {
                document.getElementById("password").setAttribute("type", "text");
                document.getElementById("eye").style.color = '#5887ef';
                state = true;
            }
        }
    </script>

@endsection
@section('page-content')
    <form
        class="form-signin rounded shadow bg-white flex-grow-1 gap-3 d-flex flex-column align-items-center justify-content-center"
        id="resetPasswordForm" autocomplete="off" method="post"
        action="{{ route('reset.password.store', $user->reset_token) }}">

        <div class="w-100 d-flex flex-column align-items-center justify-content-center pt-3 pb-1 ">
            <img src="{{ asset('img/logonp.png') }}" alt="">

        </div>
        <h2 class="form-signin-heading owh-text-light fs-5 text-uppercase fw-bold mt-1 mb-2">@yield('title')</h2>

        @if ($errors->has('new_password'))
            <span class="badge bg-danger justify-content-start p-2 w-75 text-capitalize"><i
                    class="bi-exclamation-octagon-fill"></i> {!! $errors->first('new_password') !!}</span>
        @endif
        @if ($errors->has('confirm_password'))
            <span class="badge bg-danger justify-content-start p-2 w-75 text-capitalize"><i
                    class="bi-exclamation-octagon-fill"></i> {!! $errors->first('confirm_password') !!}</span>
        @endif

        <div class="login-wrap p-4 w-100 pt-1">
            <div class="form-group mb-3 position-relative">
                {{-- <label class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-5"> --}}
                <input type="password" class="form-control" name="new_password" placeholder="Password" />
                <span class="t-eye">
                    <i class="bi-eye" id="eye" onclick="toggle()">
                    </i>
                </span>
                {{-- </div> --}}
            </div>

            <div class="form-group mb-3">
                {{-- <label class="col-sm-3 col-form-label">Retype password</label>
                <div class="col-sm-5"> --}}
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" />

                {{-- </div> --}}
            </div>
            <button class="btn btn-block btn-primary" type="submit">Reset Password</button>

            {!! csrf_field() !!}
        </div>
    </form>
@endsection
