@extends('layouts.container')

@section('title', 'Probation Review')
@section('page_css')
    <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css">
@endsection
@section('page_js')
    <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function () {
            $('.step-item').click(function () {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });

        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#probation-review-request-menu').addClass('active');
        });

        $("input[type=checkbox]").attr('disabled', true);
        $("input[type=radio]").attr('disabled', true);

        $('.filter-items').on('click', function () {
            var chk_box = $(this).find('.f-check-input');

            var chk_status = !(chk_box.is(':checked'));
            var chkdata = $(this).data("id");
            chk_box.attr('checked', chk_status);
            $(this).find('i').toggleClass('bi-check-square-fill').toggleClass('bi-square');
            $(this).toggleClass('active');
        });

    </script>
@endsection

@section('page-content')

<div class="page-header pb-3 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <div class="brd-crms flex-grow-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                            class="text-decoration-none text-dark">Home</a></li>
                    {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                    {{-- <li class="breadcrumb-item" aria-current="page"><a href="#" class="text-decoration-none">HR</a>
                    </li> --}}
                    <li class="breadcrumb-item" aria-current="page">Probation Review</li>
                </ol>
            </nav>
            <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Probation Review</h4>
        </div>
    </div>
</div>
<section class="registration">
    <div class="card">
        <div class="card-header fw-bold">Employee Details</div>
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
                                <input type="radio"
                                    name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class=""
                                    value='improved_required' @if($probationaryReviewIndicator->improved_required == 1)
                                    checked @endif>&nbsp;Improvement Required
                            </div>
                            <div class="col-lg-3">
                                <input type="radio"
                                    name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class=""
                                    value="satisfactory" @if($probationaryReviewIndicator->satisfactory == 1) checked
                                    @endif>&nbsp;Satisfactory
                            </div>
                            <div class="col-lg-3">
                                <input type="radio"
                                    name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class=""
                                    value="good" @if($probationaryReviewIndicator->good == 1) checked @endif>&nbsp;Good
                            </div>
                            <div class="col-lg-3">
                                <input type="radio"
                                    name="indicator[{{$probationaryReviewIndicator->probationary_indicator_id}}]" class=""
                                    value="excellent" @if($probationaryReviewIndicator->excellent == 1) checked
                                    @endif>&nbsp;Excellent
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach

            <div class="row mb-3">
                <label for="">{{ __('label.performance-improvements') }}</label>
                <div class="mt-2">
                    <textarea rows="5" class="form-control" name=""
                        readonly>@if($probationaryReview->performance_improvements){{ $probationaryReview->performance_improvements}}@endif</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <label for="">{{ __('label.concern-address-summary') }}</label>
                <div class="mt-2">
                    <textarea rows="5" class="form-control" name=""
                        readonly>@if($probationaryReview->concern_address_summary){{ $probationaryReview->concern_address_summary}}@endif</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <label for="">{{ __('label.employee-performance-progress') }}</label>
                <div class="mt-2">
                    <textarea rows="5" class="form-control" name=""
                        readonly>@if($probationaryReview->employee_performance_progress){{ $probationaryReview->employee_performance_progress}}@endif</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-9">
                    <div class=" form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name=""
                            @if($probationaryReview->objectives_met == 1) checked @endif>
                        <label class="form-check-label" for="q3">{{ __('label.objectives-met') }}</label>
                    </div>
                </div>
            </div>
            @php
                $objectives_met_display = " ";
                if ($probationaryReview->objectives_met == 1) {
                    $objectives_met_display = "boolean_display";
                }
            @endphp
            <div class="objectives_met {{$objectives_met_display}}">
                <div class="row">
                    <div class="mb-3 col-6">
                        <label for="" class="">{{ __('label.review-remarks') }} </label>
                        <div class="mt-2">
                            <div class="mt-2">
                                <textarea rows="2" class="form-control" name=""
                                    readonly>@if($probationaryReview->objectives_met == 0){{ $probationaryReview->objectives_review_remarks}}@endif</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="" class="required-label">{{ __('label.review-date') }} </label>
                        <div class="mt-2">
                            <input type="text" class="form-control" name="" readonly
                                value="@if($probationaryReview->objectives_met == 0){{$probationaryReview->objectives_review_date}}@endif">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-9">
                    <div class=" form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name=""
                            @if($probationaryReview->development_addressed == 1) checked @endif>
                        <label class="form-check-label" for="q3">{{__('label.development-addressed')}}</label>
                    </div>
                </div>
            </div>
            @php
                $development_addressed_display = " ";
                if ($probationaryReview->development_addressed == 1) {
                    $development_addressed_display = "boolean_display";
                }
            @endphp
            <div class="development_addressed {{$development_addressed_display}}">
                <div class="row">
                    <div class="mb-3 col-6">
                        <label for="" class="">{{ __('label.review-remarks') }} </label>
                        <div class="mt-2">
                            <div class="mt-2">
                                <textarea rows="2" class="form-control" name=""
                                    readonly>@if($probationaryReview->development_addressed == 0){{$probationaryReview->development_review_remarks}}@endif</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="" class="required-label">{{ __('label.review-date') }} </label>
                        <div class="mt-2">
                            <input type="text" class="form-control" name="" readonly
                                value="@if($probationaryReview->development_addressed == 0){{$probationaryReview->development_review_date}}@endif">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="" class="required-label">{{__('label.supervisor-recommendation')}}</label>
                <div class="mt-2">
                    <textarea rows="5" class="form-control" name=""
                        readonly>@if($probationaryReview->supervisor_recommendation){{$probationaryReview->supervisor_recommendation}}@endif</textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="" class="required-label">{{__('label.director-recommendation')}}</label>
                <div class="mt-2">
                    <textarea rows="5" class="form-control" name=""
                        readonly>@if($probationaryReview->director_recommendation){{$probationaryReview->director_recommendation}}@endif</textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-9">
                    <div class=" form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name=""
                            @if($probationaryReview->appointment_confirmed == 1) checked @endif>
                        <label class="form-check-label" for="q3">{{__('label.appointment-confirmed')}}</label>
                    </div>
                </div>
            </div>
            @php
                $appointment_confirmed_display = " ";
                if ($probationaryReview->appointment_confirmed == 1) {
                    $appointment_confirmed_display = "boolean_display";
                }
            @endphp
            <div class="appointment_confirmed {{$appointment_confirmed_display}}">
                <div class="row mb-3">
                    <label for="">{{__('label.reason-to-address-difficulty')}}</label>
                    <div class="mt-2">
                        <textarea rows="5" class="form-control" name=""
                            readonly>@if($probationaryReview->appointment_confirmed == 0){{$probationaryReview->reason_to_address_difficulty}}@endif</textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-9">
                        <div class=" form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name=""
                                @if($probationaryReview->probation_extended == 1) checked @endif>
                            <label class="form-check-label" for="q3">{{__('label.probation-extended')}}</label>
                        </div>
                    </div>
                </div>
                @php
                    $probation_extended_display = " ";
                    if ($probationaryReview->probation_extended == 0) {
                        $probation_extended_display = "boolean_display";
                    }
                @endphp
                <div class="probation_extended {{$probation_extended_display}}">
                    <div class="row mb-3">
                        <label for="">{{__('label.reason-and-improvement-to-extend')}}</label>
                        <div class="mt-2">
                            <textarea rows="5" class="form-control" name=""
                                readonly>@if($probationaryReview->probation_extended == 1){{ $probationaryReview->reason_and_improvement_to_extend}}@endif</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="" class="required-label">{{__('label.extension-length')}}</label>
                            <div class="mt-2">
                                <input type="number" class="form-control" name="" readonly placeholder="Enter in months"
                                    min="1" max="3"
                                    value="@if($probationaryReview->probation_extended == 1){{ $probationaryReview->extension_length}}@endif">
                            </div>
                        </div>

                        <div class="mb-3 col-6">
                            <label for="" class="required-label">{{__('label.next-probation-complete-date')}}</label>
                            <div class="mt-2">
                                <input type="text" class="form-control" name="" readonly
                                    value="@if($probationaryReview->probation_extended == 1){{date('Y-m-d', strtotime($probationaryReview->next_probation_complete_date))}}@endif">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="">{{__('label.employee-comments')}}</label>
                <div class="mt-2">
                    <textarea rows="5" class="form-control" name=""
                        readonly>{{$probationaryReview->employee_remarks}}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Probation Process</div>
        <div class="card-body">
            <div class="c-b">
                @foreach ($probationaryReview->logs as $log)
                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                        <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                            <i class="bi-person-circle fs-4"></i>
                        </div>
                        <div class="w-100">
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                <div
                                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                    <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
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
        </div>
    </div>
</section>
@stop