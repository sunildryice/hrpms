@extends('layouts.container')

@section('title', 'Training Details')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#training-requests-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('trainingDetailAddForm');
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
                    boolean: {
                        // The questions are inputs with class .boolean
                        selector: '.boolean',
                        // The field is placed inside .col-lg-12 div
                        row: '.col-lg-12',
                        validators: {
                            notEmpty: {
                                message: 'Check before submit'
                            },
                            // callback: {
                            //     message: 'Check before submit.',
                            //     callback: function (value, validator, $field) {
                            //         if ($('[name="btn"]').val() = 'submit') {
                            //             validator.updateStatus('boolean', validator.STATUS_VALID);
                            //             return true;
                            //         } else {
                            //             return true;
                            //         }
                            //     }
                            // },
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function (field, ele, eles) {
                            let submitButton = $('[name="btn"]:focus').data('submit');
                            console.log(submitButton)
                            if (submitButton == 'save') {
                                return true;
                            } else {
                                return false;
                            }
                        },
                    }),
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
                    <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                </ol>
            </nav>
            <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
        </div>
        <div class="add-info justify-content-end">
            @if ($authUser->can('update', $trainingRequest))
                <a class="btn btn-primary btn-sm" href="{!! route('training.requests.edit', $trainingRequest->id) !!}">
                    <i class="bi-pencil-square"></i> Edit
                </a>
            @endif
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
                <form action="{!! route('training.requests.details.store', $trainingRequest->id) !!}" method="post"
                    enctype="multipart/form-data" id="trainingDetailAddForm" autocomplete="off">
                    <div class="card-body">
                        @if ($trainingRequestQuestions->count() > 0)
                            @foreach ($trainingRequestQuestions as $trainingRequestQuestion)
                                @if (
                                        $trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' &&
                                        $trainingRequestQuestion->trainingQuestion->type == '1'
                                    )
                                    @php
                                        $question = $trainingRequestQuestion->trainingQuestion['id'];
                                    @endphp
                                    <div class="row mb-2">
                                        <div class="col-lg-12">
                                            <label for="qone"
                                                class="form-label required-label">{{ $trainingRequestQuestion->trainingQuestion->question }}
                                            </label>
                                            <textarea name="textarea[{{ $question }}]" id="qone" cols="30" rows="5"
                                                class="form-control question
                                                                        @if ($errors->has('textarea[{{ $question }}]')) is-invalid @endif">{{ $trainingRequestQuestion->answer }}</textarea>
                                            @if ($errors->has('textarea[{{ $question }}]'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="textarea[{{ $question }}]">
                                                        {!! $errors->first('textarea[{{ $question }}]') !!}


                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            @foreach ($trainingQuestions as $trainingQuestion)
                                @if ($trainingQuestion->answer_type == 'textarea')
                                    @php
                                        $question = $trainingQuestion['id'];
                                    @endphp
                                    <div class="row mb-2">
                                        <div class="col-lg-12">
                                            <label for="qone" class="form-label required-label">{{ $trainingQuestion->question }}
                                            </label>
                                            <textarea name="textarea[{{ $question }}]" id="qone" cols="30" rows="5"
                                                class="form-control question
                                                                        @if ($errors->has('textarea[{{ $question }}]')) is-invalid @endif"></textarea>
                                            @if ($errors->has('textarea[{{ $question }}]'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="textarea[{{ $question }}]">
                                                        {!! $errors->first('textarea[{{ $question }}]') !!}


                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <label for="" class="form-label required-label">{{ __('label.send-to') }}
                                    HR</label>
                                <div class="mt-2">
                                    <select name="reviewer_id"
                                        class="select2 form-control @if ($errors->has('reviewer_id')) is-invalid @endif"
                                        data-width="100%">
                                        <option value="">Select a Reviewer</option>
                                        @foreach ($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}" @if ($reviewer->id == $trainingRequest->reviewer_id) selected @endif>
                                                {{ $reviewer->getFullName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('reviewer_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="reviewer_id">
                                                {!! $errors->first('reviewer_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($trainingRequest->requester->can('self-approve-training-request'))
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label for="" class="form-label required-label">Approver</label>
                                    <div class="mt-2">
                                        <select name="approver_id"
                                            class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a approver</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}" @if ($approver->id == $trainingRequest->approver_id) selected @endif>
                                                    {{ $approver->getFullName() }}
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
                        @else
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label for="" class="form-label required-label">Recommender</label>
                                    <div class="mt-2">
                                        <select name="recommender_id"
                                            class="select2 form-control @if ($errors->has('recommender_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a recommender</option>
                                            @foreach ($recommenders as $recommender)
                                                <option value="{{ $recommender->id }}" @if ($recommender->id == $trainingRequest->recommender_id) selected @endif>
                                                    {{ $recommender->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('recommender_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="recommender_id">
                                                    {!! $errors->first('recommender_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <label for="" class="form-label required-label">Approver</label>
                                    <div class="mt-2">
                                        <select name="approver_id"
                                            class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select an approver</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}" @if ($approver->id == $trainingRequest->approver_id) selected @endif>
                                                    {{ $approver->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('approver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="recommender_id">
                                                    {!! $errors->first('recommender_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @foreach ($trainingQuestions as $trainingQuestion)
                            @if ($trainingQuestion->answer_type == 'boolean')
                                @php
                                    $question = $trainingQuestion['id'];
                                @endphp
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <div class=" form-switch">
                                            <input
                                                class="form-check-input boolean
                                                                    @if ($errors->has('boolean[{{ $question }}]')) is-invalid @endif"
                                                type="checkbox" role="switch" name="boolean[{{ $question }}]">
                                            <label class="form-check-label" for="q3">{{ $trainingQuestion->question }}</label>
                                            @if ($errors->has('boolean[{{ $question }}]'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="boolean[{{ $question }}]">
                                                        {!! $errors->first('boolean[{{ $question }}]') !!}


                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        {!! csrf_field() !!}
                    </div>
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" class="btn btn-primary" name="btn" value="save" id="trainingDetailSave"
                            data-submit="save">Save</button>
                        <button type="submit" class="btn btn-success" id="trainingDetailSubmit" name="btn"
                            value="submit" data-submit="submit">Submit
                        </button>
                        <a href="{{ route('training.requests.index') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@stop