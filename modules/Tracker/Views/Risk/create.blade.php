@extends('layouts.container')

@section('title', 'Create Risk')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#risk-index').addClass('active');

            const form = document.getElementById('riskCreateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    project_id: {
                        validators: {
                            notEmpty: {
                                message: 'The project is required.'
                            }
                        }
                    },
                    date_added: {
                        validators: {
                            notEmpty: {
                                message: 'The date added is required.'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Date must be a valid date.'
                            }
                        }
                    },
                    risk_name: {
                        validators: {
                            notEmpty: {
                                message: 'The risk name is required.'
                            }
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
                        validating: 'bi bi-arrow-repeat'
                    }),
                }
            });

            $('[name="date_added"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function (e) {
                fv.revalidateField('date_added');
            });

        });
    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a href="{{route('risk.index')}}" class="text-decoration-none">Risk Tracker</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('risk.store')}}" method="POST" id="riskCreateForm">
                @csrf
                <div class="card">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">
                            New Risk
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="project_id">Project</label>
                                <select class="form-select select2" name="project_id" id="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{old('project_id') == $project->id ? 'selected' : ''}}>{{$project->short_name ?? $project->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="date_added">Date Added <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date_added" id="date_added" value="{{old('date_added')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_name">Risk Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="risk_name" id="risk_name" value="{{old('risk_name')}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_status_id">Risk Status</label>
                                <select class="form-select select2" name="risk_status_id" id="risk_status_id">
                                    <option value="">Select Risk Status</option>
                                    @foreach($riskStatuses as $status)
                                        <option value="{{$status->id}}" {{old('risk_status_id') == $status->id ? 'selected' : ''}}>{{$status->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_type_id">Risk Type</label>
                                <select class="form-select select2" name="risk_type_id" id="risk_type_id">
                                    <option value="">Select Risk Type</option>
                                    @foreach($riskTypes as $type)
                                        <option value="{{$type->id}}" {{old('risk_type_id') == $type->id ? 'selected' : ''}}>{{$type->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_probability_id">Risk Probability</label>
                                <select class="form-select select2" name="risk_probability_id" id="risk_probability_id">
                                    <option value="">Select Risk Probability</option>
                                    @foreach($riskProbabilities as $probability)
                                        <option value="{{$probability->id}}" {{old('risk_probability_id') == $probability->id ? 'selected' : ''}}>{{$probability->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_impact_id">Residual Impact</label>
                                <select class="form-select select2" name="risk_impact_id" id="risk_impact_id">
                                    <option value="">Select Residual Impact</option>
                                    @foreach($riskImpacts as $impact)
                                        <option value="{{$impact->id}}" {{old('risk_impact_id') == $impact->id ? 'selected' : ''}}>{{$impact->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_rating_id">Risk Rating</label>
                                <select class="form-select select2" name="risk_rating_id" id="risk_rating_id">
                                    <option value="">Select Risk Rating</option>
                                    @foreach($riskRatings as $rating)
                                        <option value="{{$rating->id}}" {{old('risk_rating_id') == $rating->id ? 'selected' : ''}}>{{$rating->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_response_type_id">Risk Response Type</label>
                                <select class="form-select select2" name="risk_response_type_id" id="risk_response_type_id">
                                    <option value="">Select Risk Response Type</option>
                                    @foreach($riskResponseTypes as $responseType)
                                        <option value="{{$responseType->id}}" {{old('risk_response_type_id') == $responseType->id ? 'selected' : ''}}>{{$responseType->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="risk_owner">Risk Owner</label>
                                <input class="form-control" type="text" name="risk_owner" id="risk_owner" value="{{old('risk_owner')}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label class="form-label" for="description_of_risk">Description of Risk</label>
                                <textarea class="form-control" name="description_of_risk" id="description_of_risk" rows="3">{{old('description_of_risk')}}</textarea>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="mitigating_action">Mitigating Action</label>
                                <textarea class="form-control" name="mitigating_action" id="mitigating_action" rows="3">{{old('mitigating_action')}}</textarea>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label class="form-label" for="whats_changed_this_quarter">What's Changed This Quarter</label>
                                <textarea class="form-control" name="whats_changed_this_quarter" id="whats_changed_this_quarter" rows="3">{{old('whats_changed_this_quarter')}}</textarea>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3">{{old('remarks')}}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary" id="btnSubmit">Create</button>
                        <a href="{{route('risk.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
