@extends('layouts.container')

@section('title', 'Edit Project')

@section('page_css')
    <style>
        #deliverables-table th,
        #deliverables-table td {
            border-color: #dee2e6;
        }

        .deliverable-row .btn {
            padding-inline: .35rem;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            // Highlight Project nav; rely on global datepicker init
            $('#navbarVerticalMenu').find('#project-index').addClass('active');

            const form = document.getElementById('ProjectAddForm');

            const fv = FormValidation.formValidation(form, {
                fields: {
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'Project title is required'
                            }
                        }
                    },
                    short_name: {
                        validators: {
                            notEmpty: {
                                message: 'Short name is required'
                            }
                        }
                    },
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'Start date is required'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Please enter a valid date (yyyy-mm-dd)'
                            }
                        }
                    },
                    completion_date: {
                        validators: {
                            notEmpty: {
                                message: 'Completion date is required'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Please enter a valid date (yyyy-mm-dd)'
                            },
                            callback: {
                                message: 'Completion date must be after or equal to start date',
                                callback: function(input) {
                                    if (!input.value || !fv.getFieldValue('start_date')) {
                                        return true;
                                    }
                                    return moment(input.value, 'YYYY-MM-DD').isSameOrAfter(
                                        moment(fv.getFieldValue('start_date'), 'YYYY-MM-DD')
                                    );
                                }
                            }
                        }
                    },
                    team_lead_id: {
                        validators: {
                            notEmpty: {
                                message: 'Team lead is required'
                            }
                        }
                    },
                    focal_person_id: {
                        validators: {
                            notEmpty: {
                                message: 'Focal person is required'
                            }
                        }
                    },
                    'members[]': {
                        validators: {
                            notEmpty: {
                                message: 'Please select at least one member'
                            }
                        }
                    },
                    'stages[]': {
                        validators: {
                            notEmpty: {
                                message: 'Please select at least one stage'
                            }
                        }
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.row.mb-2, .row.mb-3',
                        eleInvalidClass: 'is-invalid',
                        eleValidClass: '',
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                }
            });

            $('select[name="members[]"]').select2({
                placeholder: "Select Members",
                width: '100%',
                closeOnSelect: false,
            });

            // Persist side-tab selection like Employee edit
            var queryTab = @json(request()->query('tab'));
            var selectedTab = queryTab ?? localStorage.getItem('project-edit-tab') ?? 'projectInformation';
            if ($("[data-tag='" + selectedTab + "']").length == 0 || $('#' + selectedTab).length == 0) {
                selectedTab = 'projectInformation';
            }

            // Tab click handler
            $('.step-item').on('click', function(e) {
                e.preventDefault();
                selectedTab = $(this).data('tag');
                localStorage.setItem('project-edit-tab', selectedTab);
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + selectedTab).addClass('active').removeClass('hide');
            });

            // Initialize current tab
            $(function() {
                $("[data-tag='" + selectedTab + "']").addClass('active');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + selectedTab).addClass('active').removeClass('hide');
            });
        });
    </script>
@endsection

@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('project.index') }}" class="text-decoration-none text-dark">
                                Project
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Edit Project
                </h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="c-tabs-content active" id="projectInformation">
                {{-- <div class="card-header fw-bold">Project Information</div> --}}
                <form action="{{ route('project.update', $project->id) }}" id="ProjectAddForm" method="post"
                    enctype="multipart/form-data" autocomplete="off">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.title') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text"
                                    class="form-control @if ($errors->has('title')) is-invalid @endif" name="title"
                                    value="{!! old('title', $project->title) !!}" autofocus />
                                @if ($errors->has('title'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="title">{!! $errors->first('title') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.short-name') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text"
                                    class="form-control @if ($errors->has('short_name')) is-invalid @endif"
                                    name="short_name" value="{!! old('short_name', $project->short_name) !!}" />
                                @if ($errors->has('short_name'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="short_name">{!! $errors->first('short_name') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.description') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea class="form-control @if ($errors->has('description')) is-invalid @endif" name="description" rows="4">{!! old('description', $project->description) !!}</textarea>
                                @if ($errors->has('description'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="description">{!! $errors->first('description') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div> --}}
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.start-date') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" data-toggle="datepicker"
                                    class="form-control @if ($errors->has('start_date')) is-invalid @endif"
                                    name="start_date" value="{!! old('start_date', $project->start_date->format('Y-m-d')) !!}" placeholder="yyyy-mm-dd"
                                    onfocus="this.blur()" />
                                @if ($errors->has('start_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="start_date">{!! $errors->first('start_date') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.completion-date') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" data-toggle="datepicker"
                                    class="form-control @if ($errors->has('completion_date')) is-invalid @endif"
                                    name="completion_date" value="{!! old('completion_date', $project->completion_date->format('Y-m-d')) !!}" placeholder="yyyy-mm-dd"
                                    onfocus="this.blur()" />
                                @if ($errors->has('completion_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="completion_date">{!! $errors->first('completion_date') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.team-lead') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="team_lead_id"
                                    class="select2 form-control @if ($errors->has('team_lead_id')) is-invalid @endif"
                                    data-width="100%">
                                    <option value="">Select Team Lead</option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}"
                                            @if (old('team_lead_id', $project->team_lead_id) == $id) selected @endif>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('team_lead_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="team_lead_id">{!! $errors->first('team_lead_id') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.focal-person') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="focal_person_id"
                                    class="select2 form-control @if ($errors->has('focal_person_id')) is-invalid @endif"
                                    data-width="100%">
                                    <option value="">Select Focal Person</option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}"
                                            @if (old('focal_person_id', $project->focal_person_id) == $id) selected @endif>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('focal_person_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="focal_person_id">{!! $errors->first('focal_person_id') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.members') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="members[]"
                                    class="select2 form-control @if ($errors->has('members')) is-invalid @endif"
                                    multiple data-placeholder="Select Members" style="width: 100%">
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}"
                                            @if (in_array($id, old('members', $project->members->pluck('id')->toArray()))) selected @endif>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('members'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="members">{!! $errors->first('members') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label required-label">{{ __('label.stages') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="stages[]"
                                    class="select2 form-control @if ($errors->has('stages')) is-invalid @endif"
                                    multiple data-placeholder="Select Stages" style="width: 100%">
                                    @foreach ($stages as $stage)
                                        <option value="{{ $stage->id }}"
                                            @if (in_array($stage->id, old('stages', $project->stages->pluck('id')->toArray()))) selected @endif>
                                            {{ $stage->title }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('stages'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="stages">{!! $errors->first('stages') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {!! csrf_field() !!}

                    </div>
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        {{-- <button class="btn btn-success btn-sm">Update</button> --}}
                        <a href="{!! route('project.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
