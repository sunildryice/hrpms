@extends('layouts.container')

@section('title', 'Training Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#training-requests-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('trainingReportAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    textarea: {
                        // The questions are inputs with class .question
                        selector: '.question',
                        // The field is placed inside .col-lg-12 div
                        row: '.col-lg-12',
                        validators: {
                            notEmpty: {
                                message: 'Answers required.'
                            },
                        }
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
                                    <a href="{{ route('training.requests.index') }}" class="text-decoration-none">Training Request</a>
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
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Training Request Information
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center add-info justify-content-end">
                                    @if ($authUser->can('update', $trainingRequest))
                                        <button data-toggle="modal" class="btn btn-primary btn-sm open-modal-form"
                                            href="{!! route('training.requests.edit', $trainingRequest->id) !!}">
                                            <i class="bi-pencil-square"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="p-1">
                                    <ul class="list-unstyled list-py-2 text-dark mb-0">
                                        <li class="pb-2"><span
                                                class="card-subtitle text-uppercase text-primary">About</span></li>
                                        @if($trainingRequest->getTrainingRequestNumber())
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-wrench-adjustable dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->getTrainingRequestNumber() }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Training Request Number"></a>
                                        </li>
                                        @endif
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-book-half dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->title }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Course Name"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-calendar3-range dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->getDuration() }} <span
                                                        class="badge bg-primary">{{ $trainingRequest->getTotalDays() }}
                                                        Days</span> </div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Training Period"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-clock dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->own_time }} Hrs</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Own Time"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-clock-fill dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->work_time }} Hrs</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Work Time"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-file-diff dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->duration }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Course Duration"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-currency-dollar dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->course_fee }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Course Fee"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-activity dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->getActivityCode() }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Activity Code"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-123 dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->getAccountCode() }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Account Code"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-chat-dots dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->description }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Description"></a>
                                        </li>
                                        <li class="position-relative">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="icon-section"> <i
                                                        class="bi-person-badge dropdown-item-icon"></i></div>
                                                <div class="d-content-section"> {{ $trainingRequest->getCreatedBy() }}</div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Requester"></a>
                                        </li>
                                        @if(file_exists('storage/'.$trainingRequest->attachment) && $trainingRequest->attachment != '')
                                            <li class="position-relative">
                                                <div class="d-flex gap-2 align-items-center">
                                                    <div class="icon-section"> <i
                                                            class="bi-eye"></i></div>
                                                    <div class="d-content-section">
                                                        <a href="{!! asset('storage/'.$trainingRequest->attachment) !!}" target="_blank"
                                                            title="View Attachment">Attachment
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            {{-- <div class="card-header fw-bold">
                                Training Details
                            </div>
                            <div class="card-body">
                                @if($trainingRequestQuestions->count()>0)
                                    @foreach($trainingRequestQuestions as $trainingRequestQuestion)
                                        @if($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea')
                                            <div class="row mb-2">
                                                <div class="col-lg-12">
                                                    <label for="qone" class="form-label required-label">{{$trainingRequestQuestion->trainingQuestion->question}}
                                                    </label>
                                                    <textarea name="textarea[{{$trainingRequestQuestion->trainingQuestion['id']}}]" id="qone" cols="30" rows="5" class="form-control question
                                                        @if($errors->has('textarea[{{$trainingRequestQuestion->trainingQuestion["id"]}}]')) is-invalid @endif">{{$trainingRequestQuestion->answer}}</textarea>
                                                    @if($errors->has('textarea[{{$trainingRequestQuestion->trainingQuestion["id"]}}]'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="textarea[{{$trainingRequestQuestion->trainingQuestion['id']}}]">
                                                                {!! $errors->first('textarea[{{$trainingRequestQuestion->trainingQuestion["id"]}}]') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div> --}}
                            <form action="{!! route('training.report.store',$trainingRequest->id) !!}" method="post"
                                enctype="multipart/form-data" id="trainingReportAddForm" autocomplete="off">
                                <div class="card-header fw-bold">
                                    Training Report
                                </div>
                                <div class="card-body">
                                    @foreach($trainingQuestions as $trainingQuestion)
                                        @if($trainingQuestion->answer_type == 'textarea')
                                            <div class="row mb-2">
                                                <div class="col-lg-12">
                                                    <label for="qone" class="form-label required-label">{{$trainingQuestion->question}}
                                                    </label>
                                                    <textarea name="textarea[{{$trainingQuestion['id']}}]" id="qone" cols="30" rows="5" class="form-control question
                                                        @if($errors->has('textarea[{{$trainingQuestion["id"]}}]')) is-invalid @endif"></textarea>
                                                    @if($errors->has('textarea[{{$trainingQuestion["id"]}}]'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="textarea[{{$trainingQuestion['id']}}]">
                                                                {!! $errors->first('textarea[{{$trainingQuestion["id"]}}]') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="row mb-2">
                                        <div class="col-lg-12">
                                            <label for="" class="form-label required-label">{{__('label.send-to')}}</label>
                                            <div class="mt-2">
                                                <select name="reviewer_id" class="select2 form-control @if($errors->has('reviewer_id')) is-invalid @endif" data-width="100%">
                                                    <option value="">Select a Reviewer</option>
                                                    @foreach($reviewers as $reviewer)
                                                        <option value="{{ $reviewer->id }}">
                                                            {{ $reviewer->getFullName() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('reviewer_id'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="reviewer_id">
                                                            {!! $errors->first('reviewer_id') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                    </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" name="btn" value="save">Save</button>
                                    <button type="submit" class="btn btn-success" name="btn" value="submit">Submit</button>
                                    <a href="{{ route('reponses.training.request.index') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
