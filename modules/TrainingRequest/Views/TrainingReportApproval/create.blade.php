@extends('layouts.container')

@section('title', 'Approve Training Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approve-training-report-menu').addClass('active');
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
                                    <a href="{{ route('approve.training.reports.index') }}" class="text-decoration-none">Training Reports</a>
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
                                            <a href="#" class="stretched-link" rel="tooltip" title="Training Report Number"></a>
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
                            <div class="card-header fw-bold">
                                Training Details
                            </div>
                            <div class="card-body">
                                @if($trainingRequestQuestions->count()>0)
                                    @foreach($trainingRequestQuestions as $trainingRequestQuestion)
                                        @if($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' && $trainingRequestQuestion->trainingQuestion->type == '1')
                                            <div class="mb-3 border-bottom pb-2 mb-2">
                                                <label for="">{{$trainingRequestQuestion->trainingQuestion->question}}</label>
                                                <p><i class="me-2"><strong>Answer:</strong></i>{{$trainingRequestQuestion->answer}} </p>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        {{-- <div class="card mb-3">
                            <div class="card-header fw-bold">
                                HR Response
                            </div>
                            <div class="card-body">
                                @if($trainingRequestQuestions->count()>0)
                                    @foreach($trainingRequestQuestions as $trainingRequestQuestion)
                                        @if($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' && $trainingRequestQuestion->trainingQuestion->type == '3')
                                            <div class="mb-3 border-bottom pb-2 mb-2">
                                                <label for="">{{$trainingRequestQuestion->trainingQuestion->question}}</label>
                                                <p><i class="me-2"><strong>Answer:</strong></i>{{$trainingRequestQuestion->answer}}g </p>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div> --}}
                        <div class="card">
                            <div class="card-header fw-bold">
                                Training Report
                            </div>
                            <div class="card-body">
                                @if($trainingReportQuestions->count()>0)
                                    @foreach($trainingReportQuestions as $trainingReportQuestion)
                                        @if($trainingReportQuestion->trainingQuestion->answer_type == 'textarea' && $trainingReportQuestion->trainingQuestion->type == '6')
                                            <div class="mb-3 border-bottom pb-2 mb-2">
                                                <label for="">{{$trainingReportQuestion->trainingQuestion->question}}</label>
                                                <p><i class="me-2"><strong>Answer:</strong></i>{{$trainingReportQuestion->answer}} </p>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <form action="{!! route('approve.training.reports.store',$trainingReport->id) !!}" method="post"
                            enctype="multipart/form-data" id="trainingApproveAddForm" autocomplete="off">
                            <div class="card">
                                <div class="card-body">
                                    @if($trainingReport->status_id == 3)
                                        <div class="row mb-2">
                                            <div class="col-lg-12">
                                                <label for="" class="form-label required-label">{{__('label.send-to')}}</label>
                                                <div class="mt-2">
                                                    <select name="approver_id" class="select2 form-control @if($errors->has('approver_id')) is-invalid @endif" data-width="100%">
                                                        <option value="">Select a Approver</option>
                                                        @foreach($approvers as $approver)
                                                            <option value="{{ $approver->id }}">
                                                                {{ $approver->getFullName() }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if($errors->has('approver_id'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="approver_id">
                                                                {!! $errors->first('approver_id') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row mb-2">
                                        <div class="col-lg-12">
                                            <label for="" class="form-label required-label">{{__('label.status')}}</label>
                                            <div class="mt-2">
                                                <select name="status_id" class="select2 @if($errors->has('status_id')) is-invalid @endif" data-width="100%">
                                                    <option value="">Select a Status</option>
                                                    @if($trainingReport->status_id == 3)
                                                        <option value="4" @if(old('status_id') == '4') selected @endif>Recommend</option>
                                                    @endif
                                                    @if($trainingReport->status_id == 4)
                                                        <option value="6" @if(old('status_id') == '6') selected @endif>Approve</option>
                                                        <option value="2" @if(old('status_id') == '2') selected @endif>Return to Requester</option>
                                                        <option value="8" @if(old('status_id') == '8') selected @endif>Reject</option>
                                                    @endif

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
                                    </div>
                                    {!! csrf_field() !!}
                                    </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" class="btn btn-success" name="btn" value="submit">Submit</button>
                                    <a href="{{ route('approve.training.reports.index') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
