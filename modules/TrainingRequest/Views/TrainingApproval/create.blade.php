@extends('layouts.container')

@section('title', 'Training Request Approve')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approve-training-requests-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('trainingApproveAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    approved_amount: {
                        validators: {
                            greaterThan: {
                                min: 0,
                                message: 'Approved amount should not be less than 0.',
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
            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                if (this.value == 6) {
                    $(form).find('#amountBlock').show();
                } else {
                    $(form).find('#amountBlock').hide();
                }
            }).on('change', '[name="recommended_to"]', function(e) {
                fv.revalidateField('recommended_to');
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
                                    <a href="{{ route('approve.training.requests.index') }}" class="text-decoration-none text-dark">Training Request Approve</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">Approve Training Request</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Approve Training Request</h4>
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
                                @if($trainingRequestQuestions->count()>0)
                                    @foreach($trainingRequestQuestions as $trainingRequestQuestion)
                                        @if($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' && $trainingRequestQuestion->trainingQuestion->type == '1')
                                            <div class="mb-3 border-bottom pb-2 mb-2">
                                                <label>{{$trainingRequestQuestion->trainingQuestion->question}}</label>
                                                <p><i class="me-2"><strong>Answer:</strong></i>{{$trainingRequestQuestion->answer}}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header fw-bold">
                                HR Response
                            </div>
                            <div class="card-body">
                                @if($trainingRequestQuestions->count()>0)
                                    @foreach($trainingRequestQuestions as $trainingRequestQuestion)
                                        @if($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' && $trainingRequestQuestion->trainingQuestion->type == '3')
                                            <div class="mb-3 border-bottom pb-2 mb-2">
                                                <label class="form-label">{{$trainingRequestQuestion->trainingQuestion->question}}</label>
                                                <p><i class="me-2"><strong>Answer:</strong></i>{{$trainingRequestQuestion->answer}}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <form action="{!! route('approve.training.request.store',$trainingRequest->id) !!}" method="post"
                            enctype="multipart/form-data" id="trainingApproveAddForm" autocomplete="off">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-6">
                                            <label class="form-label required-label">{{__('label.status')}}</label>
                                            <div class="mt-2">
                                                <select name="status_id" class="select2 @if($errors->has('status_id')) is-invalid @endif" data-width="100%">
                                                    <option value="">Select a Status</option>
                                                    <option value="6" @if(old('status_id') == '6') selected @endif>Approve</option>
                                                    <option value="2" @if(old('status_id') == '2') selected @endif>Return to Requester</option>
                                                    <option value="8" @if(old('status_id') == '8') selected @endif>Reject</option>
                                                </select>
                                                @if($errors->has('status_id'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="status_id">
                                                            {!! $errors->first('status_id') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-6" id="amountBlock" style="display: none;">
                                            <label class="form-label required-label">{{__('label.approved-amount')}}</label>
                                            <div class="mt-2">
                                                <input type="number" name="approved_amount" class="form-control @if($errors->has('approved_amount')) is-invalid @endif" data-width="100%" min="0">
                                                @if($errors->has('approved_amount'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div data-field="approved_amount">
                                                            {!! $errors->first('approved_amount') !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" class="btn btn-success" name="btn" value="submit">Submit</button>
                                    <a href="{{ route('training.requests.recommend.index') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
@stop
