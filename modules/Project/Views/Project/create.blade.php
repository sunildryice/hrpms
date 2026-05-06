@extends('layouts.container')

@section('title', 'Create Project')

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

            // $('.select2').on('change', function() {
            //     const fieldName = $(this).attr('name');
            //     if (fieldName) {
            //         fv.revalidateField(fieldName);
            //     }
            // });

            // $('.select2[multiple]').on('select2:select select2:unselect', function() {
            //     fv.revalidateField($(this).attr('name'));
            // });

            $('[data-toggle="datepicker"]').on('change', function() {
                fv.revalidateField($(this).attr('name'));
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
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Create Project
                </h4>
            </div>
        </div>
    </div>
    <section class="registration">
        <div class="row">
            <div class="c-tabs-content active">
                <form action="{{ route('project.store') }}" id="ProjectAddForm" method="post" enctype="multipart/form-data"
                    autocomplete="off">
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
                                    value="{!! old('title') !!}" autofocus />
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
                                    name="short_name" value="{!! old('short_name') !!}" />
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
                                <textarea class="form-control @if ($errors->has('description')) is-invalid @endif" name="description" rows="4">{!! old('description') !!}</textarea>
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
                                    name="start_date" value="{!! old('start_date') !!}" placeholder="yyyy-mm-dd"
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
                                    name="completion_date" value="{!! old('completion_date') !!}" placeholder="yyyy-mm-dd"
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
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('team_lead_id') == $user->id)>
                                            {{ $user->full_name }}</option>
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
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('focal_person_id') == $user->id)>
                                            {{ $user->full_name }}</option>
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
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->full_name }}</option>
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
                                        <option value="{{ $stage->id }}">
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

                        {{-- Primary Funder --}}
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Primary Funder</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control @error('primary_funder') is-invalid @enderror"
                                    name="primary_funder" value="{{ old('primary_funder') }}" />
                                @error('primary_funder')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="primary_funder">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Contracting Agency --}}
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Contracting Agency</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text"
                                    class="form-control @error('contracting_agency') is-invalid @enderror"
                                    name="contracting_agency" value="{{ old('contracting_agency') }}" />
                                @error('contracting_agency')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="contracting_agency">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Working Area (District) — multi-select --}}
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Working Area (District)</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="district_ids[]"
                                    class="select2 form-control @error('district_ids') is-invalid @enderror" multiple
                                    data-placeholder="Select Districts" style="width: 100%">
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}"
                                            @if (in_array($district->id, old('district_ids', []))) selected @endif>
                                            {{ $district->district_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district_ids')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="district_ids">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Country — multi-select --}}
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Country</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="country_ids[]"
                                    class="select2 form-control @error('country_ids') is-invalid @enderror" multiple
                                    data-placeholder="Select Countries" style="width: 100%">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            @if (in_array($country->id, old('country_ids', []))) selected @endif>
                                            {{ $country->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_ids')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="country_ids">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Budget (US$) --}}
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Budget (US$)</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('budget_usd') is-invalid @enderror" name="budget_usd"
                                    value="{{ old('budget_usd') }}" placeholder="0.00" />
                                @error('budget_usd')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="budget_usd">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Project Theme — multi-select --}}
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Project Theme</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="project_theme_ids[]"
                                    class="select2 form-control @error('project_theme_ids') is-invalid @enderror" multiple
                                    data-placeholder="Select Project Themes" data-width="100%" style="width:100%">
                                    @foreach ($projectThemes as $theme)
                                        <option value="{{ $theme->id }}"
                                            @if (in_array($theme->id, (array) old('project_theme_ids', []))) selected @endif>
                                            {{ $theme->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_theme_ids')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="project_theme_ids">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Approaches — multi-select --}}
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Approaches</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="approach_ids[]"
                                    class="select2 form-control @error('approach_ids') is-invalid @enderror" multiple
                                    data-placeholder="Select Approaches" style="width:100%">
                                    @foreach ($approaches as $approach)
                                        <option value="{{ $approach->id }}"
                                            @if (in_array($approach->id, old('approach_ids', []))) selected @endif>
                                            {{ $approach->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('approach_ids')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="approach_ids">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Sector — multi-select --}}
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="form-label">Sector</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="sector_ids[]"
                                    class="select2 form-control @error('sector_ids') is-invalid @enderror" multiple
                                    data-placeholder="Select Sectors" data-width="100%" style="width:100%">
                                    <option value="">Select Sector</option>
                                    @foreach ($sectors as $sector)
                                        <option value="{{ $sector->id }}"
                                            @if (in_array($sector->id, (array) old('sector_ids', []))) selected @endif>
                                            {{ $sector->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sector_ids')
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="sector_ids">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {!! csrf_field() !!}
                    </div>
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        {{-- <button class="btn btn-success btn-sm">Update</button> --}}
                        <a href="{!! route('project.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
