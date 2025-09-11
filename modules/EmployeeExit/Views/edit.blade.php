@extends('layouts.container')

@section('title', 'Edit Employee Exit')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#update-employees-exit-menu').addClass('active');
            $('.datepicker').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                zIndex: 2048,
            });

            const form = document.getElementById('exitHandOverNoteEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    last_duty_date: {
                        validators: {
                            notEmpty: {
                                message: 'Last Date of Duty is required',
                            },
                        },
                    },
                    resignation_date: {
                        validators: {
                            notEmpty: {
                                message: 'Resignation Date is required',
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
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
                             <li class="breadcrumb-item"><a href="{{ route('employee.exits.index') }}" class="text-decoration-none">Employee Exit</a></li>
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
                        <form action="{{ route('employee.exits.update', $exitHandOverNote->id) }}"
                              id="exitHandOverNoteEditForm" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Employee</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input class="form-control" data-width="100%" readonly value="{{ $exitHandOverNote->getEmployeeName() }}" />
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="form-label required-label">Last Duty Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control datepicker" name="last_duty_date"
                                               value="{{ old('last_duty_date') ?: $exitHandOverNote->last_duty_date->format('Y-m-d') }}" placeholder="Last Date of Duty" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="form-label required-label">Resignation Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control datepicker" name="resignation_date"
                                               value="{{ old('resignation_date') ?: $exitHandOverNote->resignation_date->format('Y-m-d') }}" placeholder="Resignation Date" readonly>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Is Insurance ?</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                                                   name="is_insurance" @if($exitHandOverNote->is_insurance == 1) checked @endif>
                                            <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                                        </div>
                                    </div>
                                </div>

                                {!! csrf_field() !!}
                                {!! method_field('PUT') !!}
                            </div>

                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="save"
                                        class="btn btn-primary btn-sm">Update
                                </button>
{{--                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">--}}
{{--                                    Submit--}}
{{--                                </button>--}}
                                <a href="{!! route('employee.exits.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
