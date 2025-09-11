@extends('layouts.container')

@section('title', 'Change password')
@push('scripts')
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
@endpush
@section('page_js')
    <script>
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('changePasswordForm');
            //
            const fv = FormValidation.formValidation(form, {
                fields: {
                    current_password: {
                        validators: {
                            notEmpty: {
                                message: 'The current password is required.',
                            },
                        },
                    },
                    new_password: {
                        validators: {
                            notEmpty: {
                                message: 'The new password is required.',
                            },
                            different: {
                                compare: function() {
                                    return form.querySelector('[name="current_password"]').value;
                                },
                                message: 'The current and new password cannot be the same.',
                            },
                        },
                    },
                    confirm_password: {
                        validators: {
                            identical: {
                                compare: function() {
                                    return form.querySelector('[name="new_password"]').value;
                                },
                                message: 'The confirm password did not match with new password.',
                            },
                        }
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

            form.querySelector('[name="current_password"]').addEventListener('input', function() {
                fv.revalidateField('new_password');
            });
            form.querySelector('[name="new_password"]').addEventListener('input', function() {
                fv.revalidateField('confirm_password');
            });
        });
    </script>

@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="brd-crms flex-grow-1">
                    <h4 class="m-0 lh1 fs-6 text-uppercase fw-bold text-primary mb-2">@yield('title')</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>

                </div>
                <div class="ad-info justify-content-end">
                    {{-- <button class="btn btn-primary btn-sm"><i class="bi-person-plus"></i> Add info</button> --}}
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">

                <div class="card-body">
                    <form class="fv-plugins-bootstrap5 fv-plugins-framework" id="changePasswordForm" autocomplete="off"
                        method="post" action="{{ route('change.password.store') }}">

                        @if (session('warning_message'))
                            <span class="badge bg-danger justify-content-start p-2 w-75 text-capitalize"><i
                                    class="bi-exclamation-octagon-fill"></i> {!! session('warning_message') !!}</span>
                        @endif
                        @if (session('success_message'))
                            <span class="badge bg-success justify-content-start p-2 w-75 text-capitalize"><i
                                    class="bi-exclamation-octagon-fill"></i> {!! session('success_message') !!}</span>
                        @endif
                        <div class="login-wrap p-4 w-100 pt-1">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">Current Password</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 ">
                                            <div class="mb-3 position-relative">
                                                <input type="password"
                                                    class="form-control  @if ($errors->has('current_password')) is-invalid @endif"
                                                    name="current_password" placeholder="Current Password" autofocus />
                                                <span class="t-eye">
                                                    <i class="bi-eye" id="eye" onclick="toggle()">
                                                    </i>
                                                </span>
                                                @if ($errors->has('current_password'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="current_password">{!! $errors->first('current_password') !!}</div>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">New Password</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 position-relative">
                                            <input type="password"
                                                class="form-control  @if ($errors->has('new_password')) is-invalid @endif"
                                                name="new_password" placeholder="New Password" id="new_password" />
                                            @if ($errors->has('new_password'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="new_password">{!! $errors->first('new_password') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">Confirm Password</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 position-relative">
                                            <input type="password"
                                                class="form-control  @if ($errors->has('confirm_password')) is-invalid @endif"
                                                name="confirm_password" placeholder="Confirm Password" />
                                            @if ($errors->has('confirm_password'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="confirm_password">{!! $errors->first('confirm_password') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-primary" type="submit">Change Password</button>
                            {!! csrf_field() !!}

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
