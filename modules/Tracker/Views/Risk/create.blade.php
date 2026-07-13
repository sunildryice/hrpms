@extends('layouts.container')

@section('title', 'Create Risk')

@section('page_css')
<style>
    .risk-history-table td, .risk-history-table th {
        padding: 10px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .risk-history-table td:nth-child(2),
    .risk-history-table td:nth-child(4),
    .risk-history-table td:nth-child(5),
    .risk-history-table td:nth-child(6),
    .risk-history-table th:nth-child(2),
    .risk-history-table th:nth-child(4),
    .risk-history-table th:nth-child(5),
    .risk-history-table th:nth-child(6) {
        min-width: 200px;
        white-space: normal;
    }
    .risk-history-table td:first-child { min-width: 140px; }
    .risk-history-table td:nth-child(3) { min-width: 160px; }
    .risk-history-table td:last-child { min-width: 60px; text-align: center; }
    .risk-history-table textarea {
        min-height: 38px;
    }
</style>
@endsection

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

            // Risk History management
            let historyIndex = 0;

            $(document).on('click', '.remove-risk-history', function() {
                $(this).closest('tr').remove();
            });

            $('#addRiskHistoryBtn').on('click', function() {
                let row = '<tr>';
                row += '<td><input class="form-control risk-history-date" type="text" name="risk_histories[' + historyIndex + '][updated_date]" onfocus="this.blur()" placeholder="YYYY-MM-DD" autocomplete="off"></td>';
                row += '<td><textarea class="form-control" name="risk_histories[' + historyIndex + '][description_of_risk]" rows="1"></textarea></td>';
                row += '<td><select class="form-select" name="risk_histories[' + historyIndex + '][risk_status_id]">';
                row += '<option value="">Select Status</option>';
                @foreach($riskStatuses as $status)
                row += '<option value="{{$status->id}}">{{$status->title}}</option>';
                @endforeach
                row += '</select></td>';
                row += '<td><textarea class="form-control" name="risk_histories[' + historyIndex + '][mitigating_action]" rows="1"></textarea></td>';
                row += '<td><textarea class="form-control" name="risk_histories[' + historyIndex + '][whats_changed_this_period]" rows="1"></textarea></td>';
                row += '<td><textarea class="form-control" name="risk_histories[' + historyIndex + '][remarks]" rows="1"></textarea></td>';
                row += '<td><button type="button" class="btn btn-danger btn-sm remove-risk-history"><i class="bi-trash"></i></button></td>';
                row += '</tr>';

                var $row = $(row);
                $('#riskHistoryTableBody').append($row);
                historyIndex++;

                // Initialize datepicker only on new row's date field
                $row.find('.risk-history-date').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                });
            });

            // Auto-resize textareas
            $(document).on('input', '.risk-history-table textarea', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

        });
    </script>
@endsection

@section('page-content')
<div class="m-content">
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
                                <label class="form-label" for="risk_name">Risk Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="risk_name" id="risk_name" value="{{old('risk_name')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="date_added">Date Added <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date_added" id="date_added" value="{{old('date_added')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label required-label" for="project_id">Project</label>
                                <select class="form-select select2" name="project_id" id="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{old('project_id') == $project->id ? 'selected' : ''}}>{{$project->short_name ?? $project->title}}</option>
                                    @endforeach
                                </select>
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
                                <input class="form-control" type="text" name="risk_owner" id="risk_owner" value="{{old('risk_owner')}}" placeholder="Enter risk owner name">
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-primary mt-4" id="addRiskHistoryBtn"><i class="bi-plus"></i> Add</button>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-2">Risk Change History</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered risk-history-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Updated Date</th>
                                        <th>Description of Risk</th>
                                        <th>Risk Status</th>
                                        <th>Mitigating Action</th>
                                        <th>What's Changed This Period</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="riskHistoryTableBody">
                                </tbody>
                            </table>
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
