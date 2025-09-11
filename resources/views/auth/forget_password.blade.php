@extends('auth.loginmaster')

@section('title', 'Forget password')

@section('page_js')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('forgetPasswordForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    email_address: {
                        validators: {
                            notEmpty: {
                                message: 'The email address is required.',
                            },
                            emailAddress: {
                                message: 'Please enter valid email address.',
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
        });
    </script>

@endsection
@section('page-content')
    <form
        class="form-signin rounded shadow bg-white flex-grow-1 gap-3 d-flex flex-column align-items-center justify-content-center"
        id="forgetPasswordForm" autocomplete="off" method="post"
        action="{{ route('forget.password.store') }}">

        <div class="w-100 d-flex flex-column align-items-center justify-content-center pt-3 pb-1 ">
            <img src="{{ asset('img/logonp.png') }}" alt="">

        </div>
        <h2 class="form-signin-heading owh-text-light fs-5 text-uppercase fw-bold mt-1 mb-2">@yield('title')</h2>
        @if (session('warning_message'))
            <span class="badge bg-danger justify-content-start p-2 w-75 text-capitalize"><i
                    class="bi-exclamation-octagon-fill"></i> {!! session('warning_message') !!}</span>
        @endif
        <div class="login-wrap p-4 w-100 pt-1">
            <div class="form-group mb-3">
                <input type="email" class="form-control  @if ($errors->has('email_address')) is-invalid @endif" name="email_address" placeholder="Email"/>
                @if ($errors->has('email_address'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div
                            data-field="email_address">{!! $errors->first('email_address') !!}</div>
                    </div>
                @endif
            </div>
            <button class="btn btn-block btn-primary" type="submit">Reset Password</button>
            {!! csrf_field() !!}

            <div class="mb-3 mt-3">
                <div class="row">
                    <div class="col-lg-12">
                        Already have login and password? <a href="{!! route('signin') !!}"
                                                            class="text-decoration-none fw-bold">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
