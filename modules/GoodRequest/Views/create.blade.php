@extends('layouts.container')

@section('title', 'Add New Good Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#good-requests-menu').addClass('active');
            const form = document.getElementById('goodRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    purpose: {
                        validators: {
                            notEmpty: {
                                message: 'The purpose of good request is required',
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
    <div class="m-content p-3">
        <div class="container-fluid">

            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('good.requests.index') }}" class="text-decoration-none">Good
                                        Requests</a>
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
                            <form action="{{ route('good.requests.store') }}" id="goodRequestAddForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">


                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label required-label">Purpose</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text" rows="10"
                                                      class="form-control @if($errors->has('purpose')) is-invalid @endif"
                                                      name="purpose">{{ old('purpose') }}</textarea>
                                            @if($errors->has('purpose'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="purpose">{!! $errors->first('purpose') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Next
                                    </button>
                                    <a href="{!! route('good.requests.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
