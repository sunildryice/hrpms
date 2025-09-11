@extends('layouts.container')

@section('title', 'Training Request Recommend')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#recommend-training-requests-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('trainingRecommendAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required',
                            },
                        },
                    },
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    approved_amount: {
                        validators: {
                            notEmpty: {
                                message: 'Approved Amount is required',
                            },
                            greaterThan: {
                                message: 'Approved Amount must be greater than 0',
                                min: 0,
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
                        excluded: function(field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]')
                                .value);
                            return field === 'approver_id' && statusId !== 5 ||
                                field === 'approved_amount' && statusId !== 6;
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });


            $(form).on('change', '[name="status_id"]', function() {
                fv.revalidateField('status_id');
                $("html, body").animate({
                    scrollTop: $(
                        'html, body').get(0).scrollHeight
                }, 500);
                if (this.value == 6) {
                    $(form).find('#amountBlock').show();
                    $(form).find('#recommenderBlock').hide();
                } else if(this.value == 5){
                    $(form).find('#amountBlock').hide();
                    $(form).find('#recommenderBlock').show();
                }else{
                    $(form).find('#amountBlock').hide();
                    $(form).find('#recommenderBlock').hide();
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
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('training.requests.recommend.index') }}"
                                class="text-decoration-none text-dark">Training Request Recommend</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Recommend Training Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Recommend Training
                    Request</h4>
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
                <form action="{!! route('training.requests.recommend.store', $trainingRequest->id) !!}" method="post" enctype="multipart/form-data"
                    id="trainingRecommendAddForm" autocomplete="off">
                    <div class="card">
                        <div class="card-body">

                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <label for="" class="form-label required-label">{{ __('label.status') }}</label>
                                    <div class="mt-2">
                                        <select name="status_id"
                                            class="select2 @if ($errors->has('status_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a Status</option>
                                            @if ($authUser->employee->designation_id == 9)
                                                <option value="{{config('constant.APPROVED_STATUS')}}" @if (old('status_id') == config('constant.APPROVED_STATUS')) selected @endif>
                                                    Approve
                                                </option>
                                            @endif
                                            <option value="5" @if (old('status_id') == '5') selected @endif>
                                                Recommend
                                            </option>
                                            <option value="2" @if (old('status_id') == '2') selected @endif>Return
                                                to Requester</option>
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

                            <div class="row mb-2" id="recommenderBlock" style="display: none;">
                                <div class="col-lg-12">
                                    <label for=""
                                        class="form-label required-label">{{ __('label.send-to') }}</label>
                                    <div class="mt-2">
                                        <select name="approver_id"
                                            class="select2 form-control @if ($errors->has('approver_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select a Approver</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}">
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

                            <div class="col-lg-6" id="amountBlock" style="display: none;" >
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

                            {!! csrf_field() !!}
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" class="btn btn-success" name="btn" value="submit">Submit
                            </button>
                            <a href="{{ route('training.requests.recommend.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
