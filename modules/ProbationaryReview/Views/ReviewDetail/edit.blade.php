@extends('layouts.container')

@section('title', 'Probation Review')
@section('page_css')
    {{-- <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css"> --}}
@section('page_js')
    {{-- <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script>
        $(document).ready(function() {
            $('.step-item').click(function() {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#probation-review-details-menu').addClass('active');

        });

        $('.filter-items').on('click', function() {
            // //
            var chk_box = $(this).find('.f-check-input');

            var chk_status = !(chk_box.is(':checked'));
            var chkdata = $(this).data("id");
            chk_box.attr('checked', chk_status);
            $(this).find('i').toggleClass('bi-check-square-fill').toggleClass('bi-square');
            $(this).toggleClass('active');
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('probationReviewAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    indicator: {
                        // The questions are inputs with class .question
                        selector: '.indicator',
                        // The field is placed inside .col-lg-12 div
                        row: '.indicator-input',
                        validators: {
                            notEmpty: {
                                message: 'Answers required.'
                            },
                        }
                    },
                    objectives_review_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Objectives for the period is required.',
                            },
                            callback: {
                                message: 'Objectives for the period is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="objectives_met"]').is(':checked')) {
                                        validator.updateStatus('objectives_review_remarks', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    objectives_review_date: {
                        validators: {
                            notEmpty: {
                                message: 'Objectives review date is required.',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date.',
                            },
                            callback: {
                                message: 'Objectives review date is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="objectives_met"]').is(':checked')) {
                                        validator.updateStatus('objectives_review_date', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    development_review_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Development/training needed field is required.',
                            },
                            callback: {
                                message: 'Development/training needed field is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="development_addressed"]').is(':checked')) {
                                        validator.updateStatus('development_review_remarks', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    development_review_date: {
                        validators: {
                            notEmpty: {
                                message: 'Development/training needed review date is required.',
                            },
                        date: {
                            format: 'YYYY-MM-DD',
                            message: 'The value is not a valid date.',
                        },
                        callback: {
                                message: 'Development/training needed review date is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="development_addressed"]').is(':checked')) {
                                        validator.updateStatus('development_review_remarks', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        },
                    },
                    supervisor_recommendation: {
                        validators: {
                            notEmpty: {
                                message: 'Supervisor recommendation is required.',
                            },
                        },
                    },
                    // director_recommendation: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Executive Director recommendation is required.',
                    //         },
                    //     },
                    // },
                    reason_to_address_difficulty: {
                        validators: {
                            notEmpty: {
                                message: 'Reason to address difficulty is required.',
                            },
                            callback: {
                                message: 'Reason to address difficulty is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="appointment_confirmed"]').is(':checked')) {
                                        validator.updateStatus('reason_to_address_difficulty', validator.STATUS_VALID);
                                        return true;
                                    } else {
                                        return true;
                                    }
                                }
                            },
                        },
                    },
                    reason_and_improvement_to_extend: {
                        validators: {
                            notEmpty: {
                                message: 'Reason and improvement to extend is required.',
                            },
                            callback: {
                                message: 'Reason and improvement to extend is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="probation_extended"]').is(':checked')) {
                                        return true;
                                    } else {
                                        validator.updateStatus('reason_and_improvement_to_extend', validator.STATUS_VALID);
                                        return true;
                                    }
                                }
                            },
                        },
                    },
                    next_probation_complete_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Next Probation Complete date is required.',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'The Next Probation Complete date is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="probation_extended"]').is(':checked')) {
                                        return true;
                                    } else {
                                        validator.updateStatus('next_probation_complete_date', validator.STATUS_VALID);
                                        return true;
                                    }
                                }
                            },
                        },
                    },
                    extension_length: {
                        validators: {
                            notEmpty: {
                                message: 'The Length of the extension is required.',
                            },
                            numeric: {
                                message: 'The Length of the extension should be number',
                            },
                            between: {
                                inclusive: true,
                                max:3,
                                min:1,
                                message: 'The Length of the extension should be maximum 3 months.',
                            },
                            callback: {
                                message: 'The Length of the extension is required.',
                                callback: function (value, validator, $field) {
                                    if ($('[name="probation_extended"]').is(':checked')) {
                                        return true;
                                    } else {
                                        validator.updateStatus('extension_length', validator.STATUS_VALID);
                                        return true;
                                    }
                                }
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

            $(form.querySelector('[name="next_probation_complete_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('next_probation_complete_date');
            });

            $(form.querySelector('[name="development_review_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('development_review_date');
            });

            $(form.querySelector('[name="objectives_review_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('objectives_review_date');
            });

            $(form.querySelector('[name="development_addressed"]')).on('change', function (e) {
                fv.revalidateField('development_review_remarks');
                fv.revalidateField('development_review_date');
                $element = $(this);
                var checked = $(this).is(':checked');
                if (checked) {
                    $('.development_addressed').hide();
                    $(form.querySelector('[name="development_review_remarks"]')).val('');
                    $(form.querySelector('[name="development_review_date"]')).val('');
                } else {
                    fv.revalidateField('development_review_remarks');
                    fv.revalidateField('development_review_date');
                    $('.development_addressed').show();
                }
            });

            $(form.querySelector('[name="objectives_met"]')).on('change', function (e) {
                fv.revalidateField('objectives_review_remarks');
                fv.revalidateField('objectives_review_date');
                $element = $(this);
                var checked = $(this).is(':checked');
                if (checked) {
                    $('.objectives_met').hide();
                    $(form.querySelector('[name="objectives_review_remarks"]')).val('');
                    $(form.querySelector('[name="objectives_review_date"]')).val('');
                } else {
                    fv.revalidateField('objectives_review_remarks');
                    fv.revalidateField('objectives_review_date');
                    $('.objectives_met').show();
                }
            });

            $(form.querySelector('[name="appointment_confirmed"]')).on('change', function (e) {
                fv.revalidateField('reason_to_address_difficulty');
                fv.revalidateField('reason_and_improvement_to_extend');
                fv.revalidateField('extension_length');
                fv.revalidateField('next_probation_complete_date');
                $element = $(this);
                var checked = $(this).is(':checked');
                if (checked) {
                    $('.appointment_confirmed').hide();
                    $(form.querySelector('[name="reason_to_address_difficulty"]')).val('');
                    $(form.querySelector('[name="reason_and_improvement_to_extend"]')).val('');
                    $(form.querySelector('[name="probation_extended"]')).prop('checked', false);
                    $(form.querySelector('[name="extension_length"]')).val('');
                    $(form.querySelector('[name="next_probation_complete_date"]')).val('');
                } else {
                    fv.revalidateField('reason_to_address_difficulty');
                    fv.revalidateField('reason_and_improvement_to_extend');
                    fv.revalidateField('extension_length');
                    fv.revalidateField('next_probation_complete_date');
                    $('.appointment_confirmed').show();
                }
            });

            $(form.querySelector('[name="probation_extended"]')).on('change', function (e) {
                fv.revalidateField('reason_and_improvement_to_extend');
                fv.revalidateField('extension_length');
                fv.revalidateField('next_probation_complete_date');
                $element = $(this);
                var checked = $(this).is(':checked');
                if (checked) {
                    fv.revalidateField('reason_and_improvement_to_extend');
                    fv.revalidateField('extension_length');
                    fv.revalidateField('next_probation_complete_date');
                    $('.probation_extended').show();
                } else {
                    $('.probation_extended').hide();
                    $(form.querySelector('[name="reason_and_improvement_to_extend"]')).val('');
                    $(form.querySelector('[name="extension_length"]')).val('');
                    $(form.querySelector('[name="next_probation_complete_date"]')).val('');
                }
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
                                <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a></li>
                                {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                                <li class="breadcrumb-item" aria-current="page"><a href="#" class="text-decoration-none">HR</a></li>
                                <li class="breadcrumb-item" aria-current="page">Probation Review</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Probation Review</h4>
                    </div>
                </div>
            </div>
            <section class="registration">
                <div class="card">
                    <div class="card-header fw-bold">
                        <h4 class="s-title fw-bold fs-6 text-custom border-bottom p-1 mb-2">
                            Employee Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label> Employee Name: {{$probationaryReview->getEmployeeName()}}</label>
                        </div>
                        <div class="row mb-3">
                            <label> Designation Name: {{$probationaryReview->employee->getDesignationName()}}</label>
                        </div>
                        <div class="row mb-3">
                            <label> Department Name: {{$probationaryReview->employee->getDepartmentName()}}</label>
                        </div>
                        <div class="row mb-3">
                            <label> Joining Date: {{$probationaryReview->employee->getFirstJoinedDate()}}</label>
                        </div>

                    </div>
                </div>
                <div class="card">
                    <form action="{!! route('probation.review.detail.requests.update', $probationaryReview->id) !!}" method="post"
                        enctype="multipart/form-data" id="probationReviewAddForm" autocomplete="off">
                        <div class="card-body">
                            <h4 class="s-title fw-bold fs-6 text-custom border-bottom p-1 mb-2">
                                Indicators
                            </h4>
                            @foreach($probationaryReviewIndicators as $probationaryReviewIndicator)
                                <div class="row mb-3">
                                    <label for="" class="">{{ $loop->iteration }}. {{$probationaryReviewIndicator->getIndicator() }}</label>
                                    <div class="mt-2">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <input type="radio" name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class="" value='improved_required'
                                                    @if($probationaryReviewIndicator->improved_required == 1) checked @endif>&nbsp;Improvement Required
                                            </div>
                                            <div class="col-lg-3">
                                                <input type="radio" name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class="" value="satisfactory"
                                                    @if($probationaryReviewIndicator->satisfactory == 1) checked @endif>&nbsp;Satisfactory
                                            </div>
                                            <div class="col-lg-3">
                                                <input type="radio" name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class="" value="good"
                                                    @if($probationaryReviewIndicator->good == 1) checked @endif>&nbsp;Good
                                            </div>
                                            <div class="col-lg-3">
                                                <input type="radio" name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class="" value="excellent"
                                                    @if($probationaryReviewIndicator->excellent == 1) checked @endif>&nbsp;Excellent
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="row mb-3">
                                <label for="">{{ __('label.performance-improvements') }}</label>
                                <div class="mt-2">
                                    <textarea rows="5" class="form-control @if($errors->has('performance_improvements')) is-invalid @endif" name="performance_improvements"
                                    >@if($probationaryReview->performance_improvements){{ $probationaryReview->performance_improvements}}@endif</textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="">{{ __('label.concern-address-summary') }}</label>
                                <div class="mt-2">
                                    <textarea rows="5" class="form-control @if($errors->has('concern_address_summary')) is-invalid @endif" name="concern_address_summary"
                                    >@if($probationaryReview->concern_address_summary){{ $probationaryReview->concern_address_summary}}@endif</textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="">{{ __('label.employee-performance-progress') }}</label>
                                <div class="mt-2">
                                    <textarea rows="5" class="form-control @if($errors->has('employee_performance_progress')) is-invalid @endif" name="employee_performance_progress"
                                    >@if($probationaryReview->employee_performance_progress){{ $probationaryReview->employee_performance_progress}}@endif</textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-9">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="q3"
                                        name="objectives_met" @if($probationaryReview->objectives_met == 1) checked @endif>
                                        <label class="form-check-label" for="q3">{{ __('label.objectives-met') }}</label>
                                    </div>
                                </div>
                            </div>
                            @php
                                $objectives_met_display = " ";
                                if($probationaryReview->objectives_met == 1){
                                    $objectives_met_display = "boolean_display";
                                }
                            @endphp
                            <div class="objectives_met {{$objectives_met_display}}">
                                <div class="row">
                                    <div class="mb-3 col-6">
                                        <label for="" class="">{{ __('label.review-remarks') }} </label>
                                        <div class="mt-2">
                                            <div class="mt-2">
                                                <textarea rows="2" class="form-control @if($errors->has('objectives_review_remarks')) is-invalid @endif"
                                                    name="objectives_review_remarks"
                                                    >@if($probationaryReview->objectives_met == 0){{ $probationaryReview->objectives_review_remarks}}@endif</textarea>
                                            </div>
                                        </div>
                                        @if ($errors->has('objectives_review_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="objectives_review_remarks">
                                                    {!! $errors->first('objectives_review_remarks') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3 col-6">
                                        <label for="" class="required-label">{{ __('label.review-date') }} </label>
                                        <div class="mt-2">
                                            <input type="text" class="form-control @if($errors->has('objectives_review_date')) is-invalid @endif"
                                                name="objectives_review_date"
                                                value="@if($probationaryReview->objectives_met == 0){{$probationaryReview->objectives_review_date}}@endif"
                                                readonly>
                                        </div>
                                        @if ($errors->has('objectives_review_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="objectives_review_date">
                                                    {!! $errors->first('objectives_review_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-9">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="q3"
                                        name="development_addressed" @if($probationaryReview->development_addressed == 1) checked @endif>
                                        <label class="form-check-label" for="q3">{{__('label.development-addressed')}}</label>
                                    </div>
                                </div>
                            </div>
                            @php
                                $development_addressed_display = " ";
                                if($probationaryReview->development_addressed == 1){
                                    $development_addressed_display = "boolean_display";
                                }
                            @endphp
                            <div class="development_addressed {{$development_addressed_display}}">
                                <div class="row">
                                    <div class="mb-3 col-6">
                                        <label for="" class="">{{ __('label.review-remarks') }} </label>
                                        <div class="mt-2">
                                            <div class="mt-2">
                                                <textarea rows="2" class="form-control @if($errors->has('development_review_remarks')) is-invalid @endif"
                                                    name="development_review_remarks"
                                                    >@if($probationaryReview->development_addressed == 0){{$probationaryReview->development_review_remarks}}@endif</textarea>
                                            </div>
                                        </div>
                                        @if ($errors->has('development_review_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="development_review_remarks">
                                                    {!! $errors->first('development_review_remarks') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3 col-6">
                                        <label for="" class="required-label">{{ __('label.review-date') }} </label>
                                        <div class="mt-2">
                                            <input type="text" class="form-control @if($errors->has('development_review_date')) is-invalid @endif"
                                                name="development_review_date"
                                                value="@if($probationaryReview->development_addressed == 0){{$probationaryReview->development_review_date}}@endif"
                                                readonly>
                                        </div>
                                        @if ($errors->has('development_review_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="development_review_date">
                                                    {!! $errors->first('development_review_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="" class="required-label">{{__('label.supervisor-recommendation')}}</label>
                                <div class="mt-2">
                                    <textarea rows="5" class="form-control  @if($errors->has('supervisor_recommendation')) is-invalid @endif"
                                        name="supervisor_recommendation"
                                        >@if($probationaryReview->supervisor_recommendation){{$probationaryReview->supervisor_recommendation}}@endif</textarea>
                                </div>
                                @if ($errors->has('supervisor_recommendation'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="supervisor_recommendation">
                                            {!! $errors->first('supervisor_recommendation') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- <div class="row mb-3">
                                <label for="" class="required-label">{{__('label.director-recommendation')}}</label>
                                <div class="mt-2">
                                    <textarea rows="5" class="form-control  @if($errors->has('director_recommendation')) is-invalid @endif"
                                        name="director_recommendation"
                                        >@if($probationaryReview->director_recommendation){{$probationaryReview->director_recommendation}}@endif</textarea>
                                </div>
                                @if ($errors->has('director_recommendation'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="director_recommendation">
                                            {!! $errors->first('director_recommendation') !!}
                                        </div>
                                    </div>
                                @endif
                            </div> --}}

                            <div class="row mb-3">
                                <div class="col-lg-9">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="q3"
                                        name="appointment_confirmed"
                                        @if($probationaryReview->appointment_confirmed == 1) checked @endif>
                                        <label class="form-check-label" for="q3">{{__('label.appointment-confirmed')}}</label>
                                    </div>
                                </div>
                            </div>
                            @php
                                $appointment_confirmed_display = " ";
                                if($probationaryReview->appointment_confirmed == 1){
                                    $appointment_confirmed_display = "boolean_display";
                                }
                            @endphp
                            <div class="appointment_confirmed {{$appointment_confirmed_display}}">
                                <div class="row mb-3">
                                    <label for="">{{__('label.reason-to-address-difficulty')}}</label>
                                    <div class="mt-2">
                                        <textarea rows="5" class="form-control @if($errors->has('reason_to_address_difficulty')) is-invalid @endif"
                                            name="reason_to_address_difficulty"
                                            >@if($probationaryReview->appointment_confirmed == 0){{$probationaryReview->reason_to_address_difficulty}}@endif</textarea>
                                    </div>
                                    @if ($errors->has('reason_to_address_difficulty'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="reason_to_address_difficulty">
                                                {!! $errors->first('reason_to_address_difficulty') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-9">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="q3" name="probation_extended"
                                            @if($probationaryReview->probation_extended == 1) checked @endif>
                                            <label class="form-check-label" for="q3">{{__('label.probation-extended')}}</label>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $probation_extended_display = " ";
                                    if($probationaryReview->probation_extended == 0){
                                        $probation_extended_display = "boolean_display";
                                    }
                                @endphp
                                <div class="probation_extended {{$probation_extended_display}}">
                                    <div class="row mb-3">
                                        <label for="">{{__('label.reason-and-improvement-to-extend')}}</label>
                                        <div class="mt-2">
                                            <textarea rows="5" class="form-control @if($errors->has('reason_and_improvement_to_extend')) is-invalid @endif" name="reason_and_improvement_to_extend"
                                                >@if($probationaryReview->probation_extended == 1){{ $probationaryReview->reason_and_improvement_to_extend}}@endif</textarea>
                                        </div>
                                        @if ($errors->has('reason_and_improvement_to_extend'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reason_and_improvement_to_extend">
                                                    {!! $errors->first('reason_and_improvement_to_extend') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-6">
                                            <label for="" class="required-label">{{__('label.extension-length')}}</label>
                                            <div class="mt-2">
                                                <input type="number" class="form-control @if($errors->has('extension_length')) is-invalid @endif"
                                                name="extension_length" placeholder="Enter in months" min="1" max="3"
                                                value="@if($probationaryReview->probation_extended == 1){{ $probationaryReview->extension_length}}@endif">
                                            </div>
                                            @if ($errors->has('extension_length'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="extension_length">
                                                        {!! $errors->first('extension_length') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3 col-6">
                                            <label for="" class="required-label">{{__('label.next-probation-complete-date')}}</label>
                                            <div class="mt-2">
                                                <input type="text" class="form-control @if($errors->has('next_probation_complete_date')) is-invalid @endif"
                                                name="next_probation_complete_date"
                                                value="@if($probationaryReview->probation_extended == 1){{date('Y-m-d', strtotime($probationaryReview->next_probation_complete_date))}}@endif"
                                                readonly>
                                            </div>
                                            @if ($errors->has('next_probation_complete_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="next_probation_complete_date">
                                                        {!! $errors->first('next_probation_complete_date') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! csrf_field() !!}
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm next">Save</button>
                                <button type="submit" name="btn" value="submit" class="btn btn-primary btn-sm">Submit</button>
                                <button type="reset" class="btn btn-danger btn-sm">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
@stop
