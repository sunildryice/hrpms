@extends('layouts.container')

@section('title', 'Approve Exit Interview')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approve-exit-interview').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('interviewApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    recommended_to: {
                        validators: {
                            notEmpty: {
                                message: 'Recommended to is required.',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function (field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]').value);
                            return (field === 'recommended_to' && statusId !== 4);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });
            $(form).on('change', '[name="status_id"]', function (e) {
                fv.revalidateField('status_id');
                if (this.value == 4) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="recommended_to"]', function (e) {
                fv.revalidateField('recommended_to');
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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
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
                        <div class="card-body">
                            @if(count($exitInterviewQuestionAnswers))
                                @foreach($exitInterviewQuestionAnswers as $exitInterviewQuestionAnswer)
                                    <div class="row mb-3">
                                        @if($exitInterviewQuestionAnswer->exitQuestionsAnswer->answer_type == 'textarea')
                                            <div class="col-lg-6">
                                                <label for="">{{$exitInterviewQuestionAnswer->exitQuestionsAnswer->question}}</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea rows="3" class="form-control" name= "textarea[{{$exitInterviewQuestionAnswer->question_id}}]" readonly >{{$exitInterviewQuestionAnswer->answer}}</textarea>
                                            </div>
                                        @elseif($exitInterviewQuestionAnswer->exitQuestionsAnswer->answer_type == 'boolean')
                                            <div class="col-lg-6">
                                                <label for="">{{$exitInterviewQuestionAnswer->exitQuestionsAnswer->question}}</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch">
                                                    @if($exitInterviewQuestionAnswer['answer'] == 'on')
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="boolean[{{$exitInterviewQuestionAnswer['question_id']}}]" id="inlineRadio1" value="on" @if($exitInterviewQuestionAnswer['answer'] == 'on') checked @endif>
                                                            <label class="form-check-label" for="inlineRadio1">
                                                                @php $firstOption = json_decode($exitInterviewQuestionAnswer->exitQuestionsAnswer->options); @endphp
                                                                {{$firstOption[0]}}
                                                            </label>
                                                        </div>
                                                    @else
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="boolean[{{$exitInterviewQuestionAnswer['question_id']}}]" id="inlineRadio2" value="off" @if($exitInterviewQuestionAnswer['answer'] == 'off') checked @endif>
                                                            <label class="form-check-label" for="inlineRadio2">
                                                                @php $secondOption = json_decode($exitInterviewQuestionAnswer->exitQuestionsAnswer->options); @endphp
                                                                {{$secondOption[1]}}</label>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-lg-6">
                                                <label for="">{{$exitInterviewQuestionAnswer->exitQuestionsAnswer->question}}</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch">
                                                    @php
                                                        $checkboxOption = json_decode($exitInterviewQuestionAnswer->exitQuestionsAnswer->options);
                                                    @endphp
                                                    @foreach($checkboxOption as $val)
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" disabled type="radio" name="boolean[{{$exitInterviewQuestionAnswer['question_id']}}]" id="inlineRadio1" value="on" @if($exitInterviewQuestionAnswer['answer'] == $val) checked @endif>
                                                            <label class="form-check-label" for="inlineRadio1">
                                                                {{$val}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                            @if(count($exitInterviewFeedbackAnswers)>0)
                                <div class="row mb-3">
                                    <div class="col-lg-9">
                                        <label for="" class="fw-bold">
                                            What did you think of your immediate head / supervisor on the following
                                            points?
                                        </label>
                                    </div>
                                </div>
                                @foreach($exitInterviewFeedbackAnswers as $exitInterviewFeedbackAnswer)
                                    <div class="row mb-3">
                                        <div class="col-lg-6">
                                            <label for="">{{$exitInterviewFeedbackAnswer->exitFeedback->title}}</label>
                                        </div>
                                        <div class="col-lg-6">
                                        <select class="form-control" name="feedbackAnswers[{{$exitInterviewFeedbackAnswer->question_id}}]" disabled="true">
                                            <option>Select Feedback</option>
                                            <option value="always"  {{ $exitInterviewFeedbackAnswer->always == 1 ? "selected":"" }}>Always</option>
                                            <option value="almost" {{ $exitInterviewFeedbackAnswer->almost == 1 ? "selected":"" }}>Almost</option>
                                            <option value="usually" {{ $exitInterviewFeedbackAnswer->usually == 1 ? "selected":"" }}>Usually</option>
                                            <option value="sometimes" {{ $exitInterviewFeedbackAnswer->sometimes == 1 ? "selected":"" }}>Sometimes</option>
                                        </select>
                                        </div>
                                        </div>
                                @endforeach
                            @endif
                            @if($exitInterviewRatingAnswers->count()> 0)
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="" class="fw-bold">
                                            How would you rate the following ?
                                        </label>
                                    </div>
                                </div>
                                @foreach($exitInterviewRatingAnswers as $exitInterviewRatingAnswer)
                                    <div class="row mb-3">
                                        <div class="col-lg-6">
                                            <label for="">{{$exitInterviewRatingAnswer->exitRating->title}}</label>
                                        </div>

                                        <div class="col-lg-6">
                                        <select class="form-control" name="ratingAnswers[{{$exitInterviewRatingAnswer->question_id}}]" disabled="true">
                                            <option>Select Rating</option>
                                            <option value="excellent" {{ $exitInterviewRatingAnswer->excellent == 1 ? "selected":"" }}>Excellent</option>
                                            <option value="good" {{ $exitInterviewRatingAnswer->good == 1 ? "selected":"" }}>Good</option>
                                            <option value="fair" {{ $exitInterviewRatingAnswer->fair == 1 ? "selected":"" }}>Fair</option>
                                            <option value="poor" {{ $exitInterviewRatingAnswer->fair == 1 ? "selected":"" }}>Poor</option>
                                        </select>

                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            Exit Interview Process
                        </div>
                        <form action="{{ route('approve.exit.interview.store', $exitInterview->id) }}"
                              method="post" enctype="multipart/form-data" autocomplete="off" id="interviewApproveForm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        @foreach($exitInterview->logs as $log)
                                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                <div width="40" height="40"
                                                    class="rounded-circle mr-3 user-icon">
                                                    <i class="bi-person"></i>
                                                </div>
                                                <div class="w-100">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                            <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                            <span
                                                                class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                        </div>
                                                        <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                    </div>
                                                    <p class="text-justify comment-text mb-0 mt-1">
                                                        {{ $log->log_remarks }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationleavetype" class="form-label required-label">Status</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select name="status_id" class="select2 form-control"
                                                        data-width="100%">
                                                    <option value="">Select a Status</option>
                                                    <option value="2">Return to Requester</option>
                                                @if($exitInterview->status_id == '3')
                                                    <option value="4">Recommend</option>
                                                @endif
                                                    <option value="6">Approve</option>
                                                </select>
                                                @if ($errors->has('status_id'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="status_id">
                                                            {!! $errors->first('status_id') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-2" id="recommendBlock" style="display: none;">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationleavetype"
                                                            class="form-label required-label">Recommended To</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select name="recommended_to" class="select2 form-control"
                                                        data-width="100%">
                                                    <option value="">Select Recommended To</option>
                                                    @foreach ($approvers as $approver)
                                                        <option value="{{ $approver->id }}">
                                                            {{ $approver->getFullName() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('recommended_to'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="recommended_to">
                                                            {!! $errors->first('recommended_to') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationRemarks" class="form-label required-label">Remarks</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <textarea type="text"
                                                          class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                          name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                @if ($errors->has('log_remarks'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div
                                                            data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('approve.exit.interview.index') !!}"
                                   class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
