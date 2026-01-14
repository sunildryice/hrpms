@extends('layouts.container')

@section('title', 'Edit Exit Interview')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#update-employees-exit-menu').addClass('active');
            let exitQuestions = "{{ $exitQuestions->pluck('id') }}";
            let exitRatings = "{{ $exitRatings->pluck('id') }}";
            let exitFeedbacks = "{{ $exitFeedbacks->pluck('id') }}";
            exitQuestions = JSON.parse(exitQuestions);
            exitRatings = JSON.parse(exitRatings);
            exitFeedbacks = JSON.parse(exitFeedbacks);

            const answerValidator = {
                validators: {
                    notEmpty: {
                        message: 'Answer is required',
                    },
                }
            };

            const optionValidator = {
                validators: {
                    notEmpty: {
                        message: 'Chose an option',
                    },
                }
            }

            const feedbackValidator = {
                validators: {
                    notEmpty: {
                        message: 'Feedback is required',
                    },
                }
            }

            const ratingValidator = {
                validators: {
                    notEmpty: {
                        message: 'Rating is required',
                    },
                }
            }

            const form = document.getElementById('exitInterviewForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    'textarea[]': answerValidator,
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required',
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
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'departure_date',
                            message: 'Departure date must be a valid date and earlier than return date.',
                        },
                        endDate: {
                            field: 'return_date',
                            message: 'Return date must be a valid date and later than departure date.',
                        },
                    }),
                },
            });

            exitQuestions.forEach((question) => {
                fv.addField('checkbox[' + question + ']',
                    optionValidator)
                    .addField('boolean[' + question + ']', optionValidator)
            })
            exitRatings.forEach((question) => {
                fv.addField('ratingAnswers[' + question + ']',
                    ratingValidator)
            })
            exitFeedbacks.forEach((question) => {
                fv.addField('feedbackAnswers[' + question + ']',
                    optionValidator)
                    .addField('boolean[' + question + ']', optionValidator)
            })
        });
    </script>
@endsection

@section('page-content')
<div class="container-fluid">

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">@yield('title')</a>
                        </li> --}}
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <section class="registration">

        <div class="row">
            <div class="col-lg-3">
                <div class="pt-3 pb-3 bg-white rounded border shadow-sm vertical-navigation sm-menu-vr">
                    <ul class="m-0 list-unstyled">
                        <li class="nav-item"><a href="@if ($authUser->can('update', $exitHandOverNote)) {{ route('exit.employee.handover.note.edit') }}
                          @else
                                        {{ route('exit.employee.handover.note.show') }} @endif"
                                class="nav-link text-decoration-none"><i class="nav-icon bi-info-circle"></i> Handover
                                Note</a></li>
                        <li class="nav-item"><a
                                href="@if ($authUser->can('update', $exitAssetHandover)) {{ route('exit.employee.handover.asset.edit') }} @else {{ route('exit.employee.handover.asset.show') }} @endif"
                                class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i>
                                Asset Handover</a></li>
                        <li class="nav-item"><a href="#" class="nav-link text-decoration-none active"><i
                                    class="nav-icon bi-people"></i> Exit interview</a></li>

                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <form action="{{ route('exit.employee.interview.update', $exitInterview->employee_id) }}"
                        id="exitInterviewForm" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">


                            @if (count($exitInterviewQuestionAnswers))
                                @foreach ($exitInterviewQuestionAnswers as $answer)
                                    <div class="mb-3 row">
                                        @if ($answer->exitQuestionsAnswer->answer_type == 'textarea')
                                            <div class="col-lg-3">
                                                <label for="">{{ $answer->exitQuestionsAnswer->question }}</label>
                                            </div>
                                            <div class="col-lg-9">
                                                <textarea rows="3"
                                                    class="form-control @if ($errors->has('textarea.{{ $answer->question_id }}')) is-invalid @endif"
                                                    name="textarea[{{ $answer->question_id }}]">{{ old('textarea.' . $answer->question_id) ?? $answer->answer }}</textarea>
                                                @if ($errors->has('textarea.{{ $answer->question_id }}'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="textarea.{{ $answer->question_id }}">
                                                            {!! $errors->first('textarea.{{ $answer->question_id }}') !!}


                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($answer->exitQuestionsAnswer->answer_type == 'boolean')
                                            <div class="col-lg-3">
                                                <label for="">{{ $answer->exitQuestionsAnswer->question }}</label>
                                            </div>
                                            <div class="col-lg-9">
                                                <div class=" form-switch">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="boolean[{{ $answer['question_id'] }}]" id="inlineRadio1"
                                                            value="on" @if (old('boolean.' . $answer->question_id) == 'on') checked
                                                            @endif>
                                                        <label class="form-check-label" for="inlineRadio1">
                                                            @php
                                                                $firstOption = json_decode(
                                                                    $answer->exitQuestionsAnswer->options,
                                                                );
                                                            @endphp
                                                            {{ $firstOption[0] }}</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="boolean[{{ $answer['question_id'] }}]" id="inlineRadio2"
                                                            value="off" @if ((old('boolean.' . $answer->question_id) ?? $answer['answer']) == 'off') checked @endif>
                                                        <label class="form-check-label" for="inlineRadio2">
                                                            @php
                                                                $secondOption = json_decode(
                                                                    $answer->exitQuestionsAnswer->options,
                                                                );
                                                            @endphp
                                                            {{ $secondOption[1] }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-lg-3">
                                                <label for="">{{ $answer->exitQuestionsAnswer->question }}</label>
                                            </div>
                                            <div class="col-lg-9">
                                                @php
                                                    $checkboxOption = json_decode(
                                                        $answer->exitQuestionsAnswer->options,
                                                    );
                                                @endphp

                                                @foreach ($checkboxOption as $val)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="checkbox[{{ $answer['question_id'] }}]" id="inlineRadio1"
                                                            value="{{ $val }}" @if ((old('checkbox' . $answer->question_id) ?? $answer->answer) == $val) checked @endif>
                                                        <label class="form-check-label" for="inlineRadio1">{{ $val }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                @foreach ($exitQuestions as $question)
                                    <div class="mb-3 row">

                                        @if ($question->answer_type == 'textarea')
                                            <div class="col-lg-3">
                                                <label for="">{{ $question->question }}</label>
                                            </div>
                                            <div class="col-lg-9">
                                                <textarea rows="3"
                                                    class="form-control @if ($errors->has('textarea.{{ $question->id }}')) is-invalid @endif"
                                                    name="textarea[{{ $question->id }}]">{{ old('textarea.' . $question->id) }}</textarea>
                                                @if ($errors->has('textarea.{{ $question->id }}'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="textarea.{{ $question->id }}">
                                                            {!! $errors->first('textarea.{{ $question->id }}') !!}


                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($question->answer_type == 'boolean')
                                            <div class="col-lg-3">
                                                <label for="">{{ $question->question }}</label>
                                            </div>
                                            <div class="col-lg-9">
                                                <!-- //checkbox -->
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="boolean[{{ $question['id'] }}]" id="inlineRadio1" value="on" @if (old('boolean.' . $question->id) == 'on') checked @endif>
                                                    <label class="form-check-label" for="inlineRadio1">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="boolean[{{ $question['id'] }}]" id="inlineRadio2" value="off" @if (old('boolean.' . $question->id) == 'off') checked @endif>
                                                    <label class="form-check-label" for="inlineRadio2">No</label>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-lg-3">
                                                <label for="">{{ $question->question }}</label>
                                            </div>
                                            <div class="col-lg-9">
                                                @php
                                                    $checkboxOption = json_decode($question->options);
                                                @endphp

                                                @foreach ($checkboxOption as $val)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="checkbox[{{ $question['id'] }}]" id="inlineRadio-{{ $val }}"
                                                            value="{{ $val }}" @if (old('checkbox.' . $question->id) == $val) checked
                                                            @endif>
                                                        <label class="form-check-label" for="inlineRadio-{{ $val }}">{{ $val }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif

                            <div class="mb-3 row">
                                <div class="col-lg-9">
                                    <label for="" class="fw-bold">
                                        What did you think of your immediate head / supervisor on the following
                                        points?
                                    </label>
                                </div>
                            </div>
                            @if (count($exitInterviewFeedbackAnswers))
                                @foreach ($exitInterviewFeedbackAnswers as $exitInterviewFeedbackAnswer)
                                    <div class="mb-3 row">
                                        <div class="col-lg-6">
                                            <label for="">{{ $exitInterviewFeedbackAnswer->exitFeedback['title'] }}</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <select class="select2 form-control"
                                                name="feedbackAnswers[{{ $exitInterviewFeedbackAnswer->exit_feedback_id }}]">
                                                <option>Select Feedback</option>
                                                <option value="always" {{ $exitInterviewFeedbackAnswer->always == 1 ? 'selected' : '' }}>
                                                    Always
                                                </option>
                                                <option value="almost" {{ $exitInterviewFeedbackAnswer->almost == 1 ? 'selected' : '' }}>
                                                    Almost
                                                </option>
                                                <option value="usually" {{ $exitInterviewFeedbackAnswer->usually == 1 ? 'selected' : '' }}>
                                                    Usually
                                                </option>
                                                <option value="sometimes" {{ $exitInterviewFeedbackAnswer->sometimes == 1 ? 'selected' : '' }}>
                                                    Sometimes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @foreach ($exitFeedbacks as $exitfeedback)
                                    <div class="mb-3 row">
                                        <div class="col-lg-6">
                                            <label for="">{{ $exitfeedback->title }}</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <select class="select2 form-control"
                                                name="feedbackAnswers[{{ $exitfeedback->id }}]">
                                                <option value="">Select Feedback</option>
                                                <option value="always">Always</option>
                                                <option value="almost">Almost</option>
                                                <option value="usually">Usually</option>
                                                <option value="sometimes">Sometimes</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="mb-3 row">
                                <div class="col-lg-6">
                                    <label for="" class="fw-bold">
                                        How would you rate the following ?
                                    </label>
                                </div>
                            </div>

                            @if (count($exitInterviewFeedbackAnswers))
                                @foreach ($exitInterviewRatingAnswers as $exitInterviewRatingAnswer)
                                    <div class="mb-3 row">
                                        <div class="col-lg-6">
                                            <label for="">{{ $exitInterviewRatingAnswer->exitRating['title'] }}</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <select class="select2 form-control"
                                                name="ratingAnswers[{{ $exitInterviewRatingAnswer->exit_rating_id }}]">
                                                <option value="">Select Rating</option>

                                                <option value="excellent" {{ $exitInterviewRatingAnswer->excellent == 1 ? 'selected' : '' }}>
                                                    Excellent
                                                </option>
                                                <option value="good" {{ $exitInterviewRatingAnswer->good == 1 ? 'selected' : '' }}>
                                                    Good
                                                </option>
                                                <option value="fair" {{ $exitInterviewRatingAnswer->fair == 1 ? 'selected' : '' }}>
                                                    Fair
                                                </option>
                                                <option value="poor" {{ $exitInterviewRatingAnswer->poor == 1 ? 'selected' : '' }}>
                                                    Poor
                                                </option>
                                            </select>

                                        </div>

                                    </div>
                                @endforeach
                            @else
                                @foreach ($exitRatings as $exitRate)
                                    <div class="mb-3 row">
                                        <div class="col-lg-6">
                                            <label for="">{{ $exitRate->title }}</label>
                                        </div>

                                        <div class="col-lg-6">
                                            <select class="select2 form-control" name="ratingAnswers[{{ $exitRate->id }}]">
                                                <option value="">Select Rating</option>
                                                <option value="excellent">Excellent</option>
                                                <option value="good">Good</option>
                                                <option value="fair">Fair</option>
                                                <option value="poor">Poor</option>
                                            </select>

                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body">
                                        @if ($exitInterview->status_id == config('constant.RETURNED_STATUS'))
                                            <div class="mb-3 row">
                                                <div class="col-lg-3">
                                                    <label>Return Remarks</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <span>{{ $exitInterview->returnedLog->log_remarks }}</span>
                                                </div>
                                            </div>
                                        @endif


                                        <div class="mb-3 row">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="approver_id" class="form-label required-label">Send
                                                        To</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                @php $selectedApproverId = old('approver_id') ?: $exitInterview->approver_id; @endphp
                                                <select name="approver_id" class="select2 form-control
                                                        @if ($errors->has('approver_id')) is-invalid @endif"
                                                    data-width="100%">
                                                    <option value="">Select an approver</option>
                                                    @foreach ($approvers as $approver)
                                                        <option value="{{ $approver->id }}" {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                            {{ $approver->full_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('approver_id'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="approver_id">
                                                            {!! $errors->first('approver_id') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            {!! csrf_field() !!}
                            {!! method_field('PUT') !!}
                        </div>
                        <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                            </button>
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{!! route('advance.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
    @stop