@extends('auth.loginmaster')

@section('title', 'User Login')

@push('scripts')
    <script>
        $("#eye").click(function () {

            $(this).toggleClass('bi-eye-slash');
            $(this).toggleClass('bi-eye');
        });
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
@section('page-content')
    <form
        class="form-signin rounded shadow bg-white flex-grow-1 gap-3 d-flex flex-column align-items-center justify-content-center"
        action="{{ route('auth.login') }}" method="POST" autocomplete="off">
        <div class="w-100 d-flex flex-column align-items-center justify-content-center pt-3 pb-1 ">
{{--            <img src="{{ asset('img/logonp.png') }}" alt="LOGO">--}}
        </div>
        <h2 class="form-signin-heading owh-text-light fs-5 text-uppercase fw-bold mt-1 mb-2">sign in</h2>

        @if (session('warning_message'))
            <span class="badge bg-danger justify-content-start p-2 w-75 text-capitalize"><i
                    class="bi-exclamation-octagon-fill"></i> {!! session('warning_message') !!}</span>
        @endif
        @if (session('success_message'))
            <span class="badge bg-success justify-content-start p-2 w-75 text-capitalize"><i
                    class="bi-exclamation-octagon-fill"></i> {!! session('success_message') !!}</span>
        @endif

        <div class="login-wrap p-4 w-100 pt-1">
            <div class="form-group mb-3">
                <input type="text" class="form-control" placeholder="User ID" autofocus name="username"
                       value="{!! old('username') !!}">
            </div>
            <div class="form-group mb-3 position-relative">
                <input type="password" class="form-control" placeholder="Password" name="password" id="password">
                <span class="t-eye">
                <i class="bi-eye" id="eye" onclick="toggle()">
                </i>
            </span>
            </div>
            <div class="mb-3">
                <div class="row">
                    <div class="col-lg-6">
                        <label for="rememberme" class="fw-bold"><input type="checkbox" name="remember" id="remember">
                            Remember Me</label>
                    </div>
                    <div class="col-lg-6">
                        <a href="{!! route('forget.password.create') !!}"
                           class="float-end text-decoration-none fw-bold">Forgot password</a>
                    </div>
                </div>
            </div>
            @if (request()->has('previous'))
                <input type="hidden" name="previous" value="{{ request()->get('previous') }}">
            @else
                <input type="hidden" name="previous" value="{{ url()->previous() }}">
            @endif

            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
            <button class="btn btn-block btn-primary" type="submit">Sign in</button>
        </div>
    </form>
@endsection
