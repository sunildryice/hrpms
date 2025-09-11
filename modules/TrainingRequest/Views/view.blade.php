@extends('layouts.container')

@section('title', 'Training Request View')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#training-requests-menu').addClass('active');
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
                            <a href="{{ route('training.requests.index') }}" class="text-decoration-none text-dark">Training
                                Request</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Training Request View</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Training Request View</h4>
            </div>
        </div>
    </div>
    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Training Request Information
                    </div>
                    @include('TrainingRequest::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Training Details
                    </div>
                    <div class="card-body">
                        @if ($trainingRequestQuestions->count() > 0)
                            @foreach ($trainingRequestQuestions as $trainingRequestQuestion)
                                @if (
                                    $trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' &&
                                        $trainingRequestQuestion->trainingQuestion->type == '1')
                                    <div class="mb-3 border-bottom pb-2 mb-2">
                                        <label
                                            for="">{{ $trainingRequestQuestion->trainingQuestion->question }}</label>
                                        <p><i
                                                class="me-2"><strong>Answer:</strong></i>{{ $trainingRequestQuestion->answer }}
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                @if ($hrResponseCount > 0)
                    <div class="card">
                        <div class="card-header fw-bold">
                            HR Response
                        </div>
                        <div class="card-body">
                            @if ($trainingRequestQuestions->count() > 0)
                                @foreach ($trainingRequestQuestions as $trainingRequestQuestion)
                                    @if (
                                        $trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' &&
                                            $trainingRequestQuestion->trainingQuestion->type == '3')
                                        <div class="mb-3 border-bottom pb-2 mb-2">
                                            <label
                                                for="">{{ $trainingRequestQuestion->trainingQuestion->question }}</label>
                                            <p><i
                                                    class="me-2"><strong>Answer:</strong></i>{{ $trainingRequestQuestion->answer }}
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header fw-bold">
                        Training Request Remarks
                    </div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($trainingRequest->logs as $log)
                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                        <i class="bi-person-circle fs-5"></i>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
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
            </div>
        </div>
    </section>
@stop
