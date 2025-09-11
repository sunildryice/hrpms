@extends('layouts.container')

@section('title', 'Add New Purchase Request Package')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {

            $('#navbarVerticalMenu').find('#packages-menu').addClass('active');
            const form = document.getElementById('packageAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    package_name: {
                        validators: {
                            notEmpty: {
                                message: 'The package name is required',
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
                            <a href="{{ route('master.packages.index') }}" class="text-decoration-none text-dark">Purchase
                                Requests Packages</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <form action="{{ route('master.packages.store') }}" id="packageAddForm" method="post"
                        enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="card-body">


                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationPackageName" class="form-label required-label">Package
                                            Name:</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text"
                                        class="form-control @if ($errors->has('package_name')) is-invalid @endif"
                                        name="package_name" value="{{ old('package_name') }}">
                                    @if ($errors->has('package_name'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="package_name">{!! $errors->first('package_name') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationDescription" class="form-label">Package Description</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control @if ($errors->has('package_description')) is-invalid @endif"
                                        name="package_description">{{ old('package_description') }}</textarea>
                                    @if ($errors->has('package_description'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="package_description">{!! $errors->first('package_description') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Next
                            </button>
                            <a href="{!! route('master.packages.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

@stop
