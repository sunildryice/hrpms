@extends('layouts.container')

@section('title', 'Add Event Completion')

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#event-completion-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            // $("#substitutes").select2({
            //     width: '100%',
            //     dropdownAutoWidth: true
            // });
            const form = document.getElementById('eventCompletionAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'The District is required',
                            },
                        },
                    },
                    activity_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The activity code is required',
                            },
                        },
                    },
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Program date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Program date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'The End date must be greater than Start date',
                                callback: function(value, validator, field) {
                                    const startDate = new Date(form.querySelector('[name="start_date"]').value);
                                    return new Date(value.value) >= startDate;
                                },
                            }
                        },
                    },
                    // program_time: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'The Program time is required',
                    //         },
                    //         time: {
                    //             format: 'HH:mm:ss',
                    //             message: 'The value is not a valid time',
                    //         },
                    //     }
                    // },
                    venue: {
                        validators: {
                            notEmpty: {
                                message: 'The venue is required',
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

            $(form.querySelector('[name="start_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                if(form.querySelector('[name="end_date"]').value){
                    fv.revalidateField('end_date');
                }
            });

            $(form.querySelector('[name="end_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('end_date');
            });


            $(form).on('change', '[name="district_id"]', function(e) {
                fv.revalidateField('district_id');
            });
            $(form).on('change', '[name="activity_code_id"]', function(e) {
                fv.revalidateField('activity_code_id');
            });
        });
    </script>

@endsection

@section('page-content')



    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('event.completion.index') }}"
                                class="text-decoration-none text-dark">Event Completion</a></li>
                        <li class="breadcrumb-item" aria-current="page">Add New Event Completion</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Add New Event Completion</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <div class="card-header fw-bold">Event Completion</div>
            <form action="{{ route('event.completion.store') }}" id="eventCompletionAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">District
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="district_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ $district->id == old('district_id') ? 'selected' : '' }}>
                                        {{ $district->district_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('district_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="district_id">
                                        {!! $errors->first('district_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationActivity" class="form-label">Activity</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control select2" data-width="100%" name="activity_code_id">
                                <option value="">Select Activity</option>
                                @foreach ($activityCodes as $activityCode)
                                    <option value="{!! $activityCode->id !!}"
                                        {{ $activityCode->id == old('activity_code_id') ? 'selected' : '' }}>
                                        {{ $activityCode->getActivityCodeWithDescription() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('activity_code_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="activity_code_id">
                                        {!! $errors->first('activity_code_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationVenue" class="form-label required-label">Venue:</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control @if ($errors->has('venue')) is-invalid @endif"
                                name="venue" value="{{ old('venue') }}" placeholder="Venue">
                            @if ($errors->has('venue'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="venue">{!! $errors->first('venue') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Program Start Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('start_date')) is-invalid @endif"
                                readonly name="start_date" value="{{ old('start_date') }}" />
                            @if ($errors->has('start_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="start_date">{!! $errors->first('start_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Program End Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('end_date')) is-invalid @endif"
                                readonly name="end_date" value="{{ old('end_date') }}" />
                            @if ($errors->has('end_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="end_date">{!! $errors->first('end_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationTime" class="form-label required-label">Time
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="time"
                                class="form-control
                                        @if ($errors->has('program_time')) is-invalid @endif"
                                name="program_time" value="{{ old('program_time') }}" />
                            @if ($errors->has('program_time'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="program_time">{!! $errors->first('program_time') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> --}}
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationBackground" class="form-label">Background</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('background')) is-invalid @endif" name="background">
@if (old('background'))
{{ old('background') }}
@endif
</textarea>
                            @if ($errors->has('background'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="background">{!! $errors->first('background') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationObjective" class="form-label">Objectives </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('objectives')) is-invalid @endif" name="objectives">
@if (old('objectives'))
{{ old('objectives') }}
@endif
</textarea>
                            @if ($errors->has('objectives'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="objectives">{!! $errors->first('objectives') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProcess" class="form-label">Process</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('process')) is-invalid @endif" name="process">
@if (old('process'))
{{ old('process') }}
@endif
</textarea>
                            @if ($errors->has('process'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="process">{!! $errors->first('process') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationClosing" class="form-label">Closing </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('closing')) is-invalid @endif" name="closing">
@if (old('closing'))
{{ old('closing') }}
@endif
</textarea>
                            @if ($errors->has('closing'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="closing">{!! $errors->first('closing') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {!! csrf_field() !!}
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Next</button>
                        <a href="{!! route('event.completion.create') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
            </form>
        </div>
    </section>

@stop
