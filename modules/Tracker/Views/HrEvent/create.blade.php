@extends('layouts.container')

@section('title', 'Create HR Event')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#hr-event-index').addClass('active');

            function toggleSections() {
                var eventType = $('#event_type').val();
                if (eventType === 'Recruitment') {
                    $('#recruitmentSection').show();
                    $('#orientationSection').hide();
                } else if (eventType === 'Orientation') {
                    $('#recruitmentSection').hide();
                    $('#orientationSection').show();
                } else {
                    $('#recruitmentSection').hide();
                    $('#orientationSection').hide();
                }
            }

            toggleSections();
            $('#event_type').on('change', toggleSections);

            const form = document.getElementById('hrEventCreateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    event_date: {
                        validators: {
                            notEmpty: {
                                message: 'The event date is required.'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Date must be a valid date.'
                            }
                        }
                    },
                    event_type: {
                        validators: {
                            notEmpty: {
                                message: 'The event type is required.'
                            }
                        }
                    },
                    vacancy_for_positions: {
                        validators: {
                            notEmpty: {
                                message: 'The vacancy for positions is required.'
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

            $('[name="event_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function (e) {
                fv.revalidateField('event_date');
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
                                <a href="{{route('hr-event.index')}}" class="text-decoration-none">HR Events</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('hr-event.store')}}" method="POST" id="hrEventCreateForm">
                @csrf
                <div class="card">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">
                            New HR Event
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="event_date">Event Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="event_date" id="event_date" value="{{old('event_date')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="event_type">Event Type <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="event_type" id="event_type">
                                    <option value="">Select Event Type</option>
                                    <option value="Recruitment" {{old('event_type') == 'Recruitment' ? 'selected' : ''}}>Recruitment</option>
                                    <option value="Orientation" {{old('event_type') == 'Orientation' ? 'selected' : ''}}>Orientation</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="vacancy_for_positions">Vacancy For Positions <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="vacancy_for_positions" id="vacancy_for_positions" value="{{old('vacancy_for_positions')}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="associated_project">Associated Project</label>
                                <input class="form-control" type="text" name="associated_project" id="associated_project" value="{{old('associated_project')}}">
                            </div>
                        </div>

                        <div id="recruitmentSection" style="display:none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Recruitment Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_applicants">Total Applicants</label>
                                    <input class="form-control" type="number" name="total_applicants" id="total_applicants" value="{{old('total_applicants', 0)}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="male_shortlisted">Male Shortlisted</label>
                                    <input class="form-control" type="number" name="male_shortlisted" id="male_shortlisted" value="{{old('male_shortlisted', 0)}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="female_shortlisted">Female Shortlisted</label>
                                    <input class="form-control" type="number" name="female_shortlisted" id="female_shortlisted" value="{{old('female_shortlisted', 0)}}" min="0">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="male_recruited">Male Recruited</label>
                                    <input class="form-control" type="number" name="male_recruited" id="male_recruited" value="{{old('male_recruited', 0)}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="female_recruited">Female Recruited</label>
                                    <input class="form-control" type="number" name="female_recruited" id="female_recruited" value="{{old('female_recruited', 0)}}" min="0">
                                </div>
                            </div>
                        </div>

                        <div id="orientationSection" style="display:none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Orientation Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="orientation_title">Orientation Title</label>
                                    <input class="form-control" type="text" name="orientation_title" id="orientation_title" value="{{old('orientation_title')}}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="male_participants">Male Participants</label>
                                    <input class="form-control" type="number" name="male_participants" id="male_participants" value="{{old('male_participants', 0)}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="female_participants">Female Participants</label>
                                    <input class="form-control" type="number" name="female_participants" id="female_participants" value="{{old('female_participants', 0)}}" min="0">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary" id="btnSubmit">Create</button>
                        <a href="{{route('hr-event.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
