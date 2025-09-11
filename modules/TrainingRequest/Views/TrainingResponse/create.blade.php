@extends('layouts.container')

@section('title', 'Training Details')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#response-training-requests-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('trainingDetailAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        selector: '[name="status_id"]',
                        row: '.col-lg-12',
                        validators: {
                            notEmpty: {
                                message: 'Action is required'
                            },
                        }
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
                            },
                        },
                    },
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


                    // boolean: {
                    //     // The questions are inputs with class .boolean
                    //     selector: '.boolean',
                    //     // The field is placed inside .col-lg-12 div
                    //     row: '.form-switch',
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Check before submit'
                    //         },
                    //         // callback: {
                    //         //     message: 'Check before submit.',
                    //         //     callback: function (value, validator, $field) {
                    //         //         if ($('[name="btn"]').val() = 'submit') {
                    //         //             validator.updateStatus('boolean', validator.STATUS_VALID);
                    //         //             return true;
                    //         //         } else {
                    //         //             return true;
                    //         //         }
                    //         //     }
                    //         // },
                    //     }
                    // },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]')
                                .value);
                            if (statusId === 4) {
                                fv.resetField('textarea');
                                fv.resetField('log_remarks');
                                return field === 'log_remarks';
                            }
                            if (statusId === 2) {
                                fv.resetField('log_remarks');
                                fv.resetField('textarea');
                                return field === 'textarea';
                            }
                            return false;
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                $("html, body").animate({
                    scrollTop: $(
                        'html, body').get(0).scrollHeight
                }, 500);
                if (this.value == 4) {
                    $(form).find('#responseBlock').show();
                    $(form).find('#remarksBlock').hide();
                } else if (this.value == 2) {
                    $(form).find('#responseBlock').hide();
                    $(form).find('#remarksBlock').show();
                } else {
                    $(form).find('#responseBlock').hide();
                    $(form).find('#remarksBlock').hide();
                }
            })
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
                            <a href="{{ route('reponses.training.request.index') }}"
                                class="text-decoration-none text-dark">Training Request Response</a>
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
                                        <p>
                                            <i
                                                class="me-2"><strong>Answer:</strong></i>{{ $trainingRequestQuestion->answer }}
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                <form action="{!! route('reponses.training.request.store', $trainingRequest->id) !!}" method="post" enctype="multipart/form-data"
                    id="trainingDetailAddForm" autocomplete="off">

                    <div class="card" style="">
                        <div class="card-header fw-light text-capitalize">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationactiontype" class="form-label required-label">Action</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="status_id" class="select2 form-control" data-width="100%">
                                        <option value="">Select an Action</option>
                                        <option value="4">
                                            Add Response
                                        </option>
                                        <option value="2">Return
                                            to Requester
                                        </option>

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
                        </div>
                        <div class="card-body">

                            <div id="responseBlock" style="display: none;">
                                <div class="fw-bold text-uppercase mb-2">Response to be filled by HR</div>
                                @foreach ($trainingQuestions as $trainingQuestion)
                                    @php
                                        $question = $trainingQuestion['id'];
                                    @endphp
                                    @if ($trainingQuestion->answer_type == 'textarea')
                                        <div class="row mb-2">
                                            <div class="col-lg-12">
                                                <label for="qone"
                                                    class="form-label required-label">{{ $trainingQuestion->question }}
                                                </label>
                                                <textarea name="textarea[{{ $question }}]" id="qone" cols="30" rows="5"
                                                    class="form-control question
                                                            @if ($errors->has('textarea[{{ $question }}]')) is-invalid @endif"></textarea>
                                                @if ($errors->has('textarea[{{ $question }}]'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="textarea[{{ $trainingQuestion['id'] }}]">
                                                            {!! $errors->first('textarea[{{ $question }}]') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="row mb-2" id="remarksBlock" style="display: none;">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationRemarks" class="form-label required-label">Remarks</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                    @if ($errors->has('log_remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- <div class="row mb-2">
                                        <div class="col-lg-12">
                                            <label for="" class="form-label required-label">{{__('label.send-to')}}</label>
                                            <div class="mt-2">
                                                <select name="recommender_id"
                                                        class="select2 form-control @if ($errors->has('recommender_id')) is-invalid @endif"
                                                        data-width="100%">
                                                    <option value="">Select a Recommender</option>
                                                    @foreach ($supervisors as $supervisor)
                                                        <option value="{{ $supervisor->id }}">
                                                            {{ $supervisor->getFullName() }}
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
                                    </div> --}}
                        </div>
                    </div>
                    <div class=" justify-content-end d-flex gap-2">
                        <button type="submit" class="btn btn-success" name="btn" value="submit">Submit
                        </button>
                        <a href="{{ route('reponses.training.request.index') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </section>
@stop
