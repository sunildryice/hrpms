@extends('layouts.container')

@section('title', 'Create Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-index').addClass('active');

            $('#employee_id').val('').trigger("change");
            $('#employee_id').attr('disabled', true);
            $('#employee_all').attr('checked', true);

            $('#employee_all').on('change', function() {
                if ($(this).is(':checked')) {
                    // $('#employee_id option:first').prop('selected', true).trigger("change");
                    $('#employee_id').val('').trigger("change");
                    $('#employee_id').attr('disabled', true);
                } else {
                    $('#employee_id').attr('disabled', false);
                }
            });

            var oldEmpId = "{{old('employee_id')}}";
            if (oldEmpId) {
                $('#employee_all').attr('checked', false);
                $('#employee_id').attr('disabled', false);
                $('#employee_id').val(oldEmpId).trigger("change");
            }

            const form = document.getElementById('performanceReviewAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    review_from: {
                        validators: {
                            notEmpty: {
                                message: 'The review from date is required.'
                            }
                        }
                    },
                    review_to: {
                        validators: {
                            notEmpty: {
                                message: 'The review to date is required.'
                            }
                        }
                    },
                    deadline_date: {
                        validators: {
                            notEmpty: {
                                message: 'The deadline date is required.'
                            }
                        }
                    },
                    review_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Review type is required.'
                            }
                        }
                    },
                    fiscal_year_id: {
                        validators: {
                            notEmpty: {
                                message: 'Fiscal year is required.'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat'
                    }),
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'review_from',
                            message: 'From date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'review_to',
                            message: 'To date must be a valid date and later than from date.',
                        },
                    }),
                }
            });

            $('[name="review_from"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                // startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('review_from');
                fv.revalidateField('review_to');
            });

            $('[name="review_to"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                // startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('review_from');
                fv.revalidateField('review_to');
            });

            $('[name="deadline_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                // startDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('deadline_date');
            });


        });

    </script>
@endsection

@section('page-content')

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('performance.index') }}"
                                    class="text-decoration-none text-dark">Performance Review</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('performance.store')}}" method="POST" id="performanceReviewAddForm">
                @csrf
                <div class="card">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">
                            New performance review
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="employee">Employee</label>
                            </div>
                            <div class="col-lg-3">
                                <select class="select2" name="employee_id" id="employee_id">
                                    <option value="" selected disabled>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{$employee->id}}" {{$employee->id  == old('employee_id') ? 'selected' : ''}}>{{$employee->getFullName()}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('employee_id'))
                                    <span class="text-danger">{{$errors->first('employee_id')}}</span>
                                @endif
                            </div>
                            <div class="col" style="display: flex; align-items: center">
                                <span>| &emsp;</span>
                                <input type="checkbox" name="employee_all" id="employee_all">
                                <label for="employee_all">&emsp;All</label>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="review_type_id" class="required-label">Review Type</label>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control" name="review_type_id" id="review_type_id">
                                    <option value="" selected disabled>Select review type</option>
                                    @foreach ($reviewTypes as $reviewType)
                                        <option value="{{$reviewType->id}}" {{$reviewType->id  == old('review_type_id') ? 'selected' : ''}}>{{$reviewType->title}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('review_type_id'))
                                    {{-- <span class="text-danger">{{$errors->first('review_type_id')}}</span> --}}
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="review_type_id">
                                            {!! $errors->first('review_type_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="fiscal_year_id" class="required-label">Fiscal Year</label>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control" name="fiscal_year_id" id="fiscal_year_id">
                                    <option value="" selected>Select fiscal year</option>
                                    @foreach ($fiscalYears as $fiscalYear)
                                        <option value="{{$fiscalYear->id}}" {{ old('fiscal_year_id') ? ($fiscalYear->id  == old('fiscal_year_id') ? 'selected' : '') : ($currentFiscalYearId == $fiscalYear->id ? 'selected' : '')}}>
                                            {{$fiscalYear->title}}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('fiscal_year_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="fiscal_year_id">
                                            {!! $errors->first('fiscal_year_id') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="review_from" class="required-label">Review From</label>
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control @if($errors->has('review_from')) is-invalid @endif"
                                name="review_from" id="review_from" value="{{old('review_from')}}" autocomplete="off">
                                @if ($errors->has('review_from'))
                                    {{-- <span class="text-danger">{{$errors->first('review_from')}}</span> --}}
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="review_from">
                                            {!! $errors->first('review_from') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="review_to" class="required-label">Review To</label>
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control @if($errors->has('review_to')) is-invalid @endif"
                                name="review_to" id="review_to" value="{{old('review_to')}}" autocomplete="off">
                                @if ($errors->has('review_to'))
                                    {{-- <span class="text-danger">{{$errors->first('review_to')}}</span> --}}
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="review_to">
                                            {!! $errors->first('review_to') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="deadline_date" class="required-label">Deadline</label>
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control @if($errors->has('deadline_date')) is-invalid @endif"
                                name="deadline_date" id="deadline_date" value="{{old('deadline_date')}}" autocomplete="off">
                                @if ($errors->has('deadline_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="deadline_date">
                                            {!! $errors->first('deadline_date') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary">Create</button>
                        <a href="{{route('performance.index')}}" role="button" class="btn btn-sm btn-danger">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

@stop
