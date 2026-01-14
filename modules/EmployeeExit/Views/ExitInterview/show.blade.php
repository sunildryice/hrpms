@extends('layouts.container')

@section('title', 'Show Exit Interview')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#update-employees-exit-menu').addClass('active');
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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">@yield('title')</a>
                        </li>--}}
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            {{-- <div class="ad-info justify-content-end">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                        class="bi-wrench-adjustable me-1"></i> New Employee Exit</button>
            </div> --}}
        </div>
    </div>
    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                    <ul class="m-0 list-unstyled">
                        <li class="nav-item"><a
                                href="@if($authUser->can('update', $exitHandOverNote)){{route('exit.employee.handover.note.edit')}}  @else {{route('exit.employee.handover.note.show')}} @endif"
                                class="nav-link  text-decoration-none"><i class="nav-icon bi-info-circle"></i> Handover
                                Note</a></li>


                        <!--  <li class="nav-item"><a href="#" class="nav-link text-decoration-none"><i
                                        class="nav-icon bi-pin-map"></i> Asset Handover</a></li> -->
                        <li class="nav-item"><a
                                href="@if($authUser->can('update', $exitAssetHandover)) {{route('exit.employee.handover.asset.edit')}} @else {{route('exit.employee.handover.asset.show')}} @endif"
                                class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i>
                                Asset Handover</a></li>
                        <li class="nav-item"><a
                                href="@if($authUser->can('update', $exitInterview)) {{route('exit.employee.interview.edit')}} @else {{route('exit.employee.interview.show')}} @endif"
                                class="nav-link active text-decoration-none"><i class="nav-icon bi-people"></i> Exit
                                interview</a></li>
                        <li class="nav-item"><a
                                href="{{route('exit.payable.show', $exitHandOverNote->employeeExitPayable->id)}}"
                                class="nav-link  text-decoration-none"><i
                                    class="nav-icon bi bi-currency-exchange"></i>Payable</a></li>

                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            Approver : {!! $exitInterview->getApproverName() !!}
                        </div>
                        @if(count($exitInterviewQuestionAnswers))
                            @foreach($exitInterviewQuestionAnswers as $exitInterviewQuestionAnswer)
                                <div class="row mb-3">
                                    @if($exitInterviewQuestionAnswer->exitQuestionsAnswer->answer_type == 'textarea')
                                        <div class="col-lg-6">
                                            <label for="">{{$exitInterviewQuestionAnswer->exitQuestionsAnswer->question}}</label>
                                        </div>
                                        <div class="col-lg-12">
                                            <textarea rows="3" class="form-control"
                                                name="textarea[{{$exitInterviewQuestionAnswer->question_id}}]"
                                                readonly>{{$exitInterviewQuestionAnswer->answer}}</textarea>
                                        </div>
                                    @elseif($exitInterviewQuestionAnswer->exitQuestionsAnswer->answer_type == 'boolean')
                                        <div class="col-lg-6">
                                            <label for="">{{$exitInterviewQuestionAnswer->exitQuestionsAnswer->question}}</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class=" form-switch">
                                                @if($exitInterviewQuestionAnswer['answer'] == 'on')
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="boolean[{{$exitInterviewQuestionAnswer['question_id']}}]"
                                                            id="inlineRadio1" value="on"
                                                            @if($exitInterviewQuestionAnswer['answer'] == 'on') checked @endif>
                                                        <label class="form-check-label" for="inlineRadio1">
                                                            @php $firstOption = json_decode($exitInterviewQuestionAnswer->exitQuestionsAnswer->options); @endphp
                                                            {{$firstOption[0]}}
                                                        </label>
                                                    </div>
                                                @else
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="boolean[{{$exitInterviewQuestionAnswer['question_id']}}]"
                                                            id="inlineRadio2" value="off"
                                                            @if($exitInterviewQuestionAnswer['answer'] == 'off') checked @endif>
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
                                            <div class=" form-switch">
                                                @php
                                                    $checkboxOption = json_decode($exitInterviewQuestionAnswer->exitQuestionsAnswer->options);
                                                @endphp
                                                @foreach($checkboxOption as $val)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" disabled type="radio"
                                                            name="boolean[{{$exitInterviewQuestionAnswer['question_id']}}]"
                                                            id="inlineRadio1" value="on"
                                                            @if($exitInterviewQuestionAnswer['answer'] == $val) checked @endif>
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

                        @if(count($exitInterviewFeedbackAnswers) > 0)
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
                                        <select class="form-control"
                                            name="feedbackAnswers[{{$exitInterviewFeedbackAnswer->question_id}}]"
                                            disabled="true">
                                            <option>Select Feedback</option>
                                            <option value="always" {{ $exitInterviewFeedbackAnswer->always == 1 ? "selected" : "" }}>Always</option>
                                            <option value="almost" {{ $exitInterviewFeedbackAnswer->almost == 1 ? "selected" : "" }}>Almost</option>
                                            <option value="usually" {{ $exitInterviewFeedbackAnswer->usually == 1 ? "selected" : "" }}>Usually</option>
                                            <option value="sometimes" {{ $exitInterviewFeedbackAnswer->sometimes == 1 ? "selected" : "" }}>Sometimes</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        @if($exitInterviewRatingAnswers->count() > 0)
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
                                        <select class="form-control"
                                            name="ratingAnswers[{{$exitInterviewRatingAnswer->question_id}}]" disabled="true">
                                            <option>Select Rating</option>
                                            <option value="excellent" {{ $exitInterviewRatingAnswer->excellent == 1 ? "selected" : "" }}>Excellent</option>
                                            <option value="good" {{ $exitInterviewRatingAnswer->good == 1 ? "selected" : "" }}>Good
                                            </option>
                                            <option value="fair" {{ $exitInterviewRatingAnswer->fair == 1 ? "selected" : "" }}>Fair
                                            </option>
                                            <option value="poor" {{ $exitInterviewRatingAnswer->fair == 1 ? "selected" : "" }}>Poor
                                            </option>
                                        </select>

                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Exit interview Process
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                @foreach($exitInterview->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person"></i>
                                        </div>
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex flex-row align-items-center">
                                                    <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                    <span
                                                        class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                </div>
                                                <small
                                                    title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <p class="text-justify comment-text mb-0 mt-1">
                                                {{ $log->log_remarks }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@stop