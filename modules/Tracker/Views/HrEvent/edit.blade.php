@extends('layouts.container')

@section('title', 'Edit HR Event')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#hr-event-index').addClass('active');

            function updateEventDateLabel(eventType) {
                var label = eventType === 'Recruitment' ? 'Vacancy Announcement Date' : 'Event Date';
                $('#eventDateLabel').html(label + ' <span class="text-danger">*</span>');
            }

            function toggleSections() {
                var eventType = $('#event_type').val();
                updateEventDateLabel(eventType);
                if (eventType === 'Recruitment') {
                    $('#recruitmentSection').slideDown(200);
                    $('#orientationSection').slideUp(200);
                    $('#recruitment_remarks').prop('disabled', false);
                    $('#orientation_remarks').prop('disabled', true);
                    try { fv.enableValidator('vacancy_for_positions'); } catch(e) {}
                    try { fv.disableValidator('orientation_title'); } catch(e) {}
                } else if (eventType === 'Orientation') {
                    $('#recruitmentSection').slideUp(200);
                    $('#orientationSection').slideDown(200);
                    $('#recruitment_remarks').prop('disabled', true);
                    $('#orientation_remarks').prop('disabled', false);
                    try { fv.disableValidator('vacancy_for_positions'); } catch(e) {}
                    try { fv.enableValidator('orientation_title'); } catch(e) {}
                } else {
                    $('#recruitmentSection').slideUp(200);
                    $('#orientationSection').slideUp(200);
                    $('#recruitment_remarks').prop('disabled', true);
                    $('#orientation_remarks').prop('disabled', true);
                    try { fv.disableValidator('vacancy_for_positions'); } catch(e) {}
                    try { fv.disableValidator('orientation_title'); } catch(e) {}
                }
            }

            function generateRecruitmentRows(count, existingData) {
                var container = $('#recruitmentTable tbody');
                container.empty();

                if (count <= 0) return;

                for (var i = 0; i < count; i++) {
                    var data = existingData && existingData[i] ? existingData[i] : {};
                    var row = '<tr>';
                    row += '<td class="text-center align-middle fw-bold">' + (i + 1) + '</td>';
                    row += '<td>';
                    row += '<input class="form-control form-control-sm recruitment-name" type="text" name="recruitments[' + i + '][member_name]" value="' + (data.member_name || '') + '" placeholder="Enter name">';
                    row += '</td>';
                    row += '<td>';
                    row += '<select class="form-select form-select-sm recruitment-gender" name="recruitments[' + i + '][gender]">';
                    row += '<option value="">Select</option>';
                    row += '<option value="Male"' + (data.gender === 'Male' ? ' selected' : '') + '>Male</option>';
                    row += '<option value="Female"' + (data.gender === 'Female' ? ' selected' : '') + '>Female</option>';
                    row += '<option value="Other"' + (data.gender === 'Other' ? ' selected' : '') + '>Other</option>';
                    row += '</select>';
                    row += '</td>';
                    row += '<td>';
                    var onboardDate = data.onboard_date_formatted ? data.onboard_date_formatted : '';
                    row += '<input class="form-control form-control-sm onboard-datepicker" type="text" name="recruitments[' + i + '][onboard_date]" value="' + onboardDate + '" placeholder="YYYY-MM-DD" onfocus="this.blur()">';
                    row += '</td>';
                    row += '<td>';
                    row += '<input class="form-control form-control-sm" type="text" name="recruitments[' + i + '][position]" value="' + (data.position || '') + '" placeholder="Enter position">';
                    row += '</td>';
                    row += '</tr>';
                    container.append(row);
                }

                $('.onboard-datepicker').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                });
            }

            function validateRecruitments() {
                var count = parseInt($('#total_recruited').val()) || 0;
                var valid = true;
                $('#recruitmentTable tbody tr').each(function() {
                    var name = $(this).find('.recruitment-name');
                    var gender = $(this).find('.recruitment-gender');
                    var nameValid = name.val().trim() !== '';
                    var genderValid = gender.val() !== '';
                    name.toggleClass('is-invalid', !nameValid);
                    gender.toggleClass('is-invalid', !genderValid);
                    if (!nameValid || !genderValid) valid = false;
                });
                return valid;
            }

            const form = document.getElementById('hrEventUpdateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    event_date: {
                        validators: {
                            notEmpty: {
                                message: 'The date is required.'
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
                    orientation_title: {
                        validators: {
                            notEmpty: {
                                message: 'The orientation title is required.'
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat'
                    }),
                }
            });

            $('#btnUpdate').on('click', function(e) {
                e.preventDefault();
                var recruitmentsValid = validateRecruitments();
                fv.validate().then(function(status) {
                    if (status !== 'Invalid' && recruitmentsValid) {
                        form.submit();
                    }
                });
            });

            $('[name="event_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('#total_recruited').on('input change', function() {
                var count = parseInt($(this).val()) || 0;
                $('#recruitmentTableWrapper').toggle(count > 0);
                generateRecruitmentRows(count);
            });

            var existingRecruitments = @json($hrEvent->recruitments->toArray());
            var initialCount = parseInt('{{ $hrEvent->total_recruited }}') || 0;
            if (existingRecruitments.length > 0) {
                $('#recruitmentTableWrapper').show();
                generateRecruitmentRows(existingRecruitments.length, existingRecruitments);
            } else if (initialCount > 0) {
                $('#recruitmentTableWrapper').show();
                generateRecruitmentRows(initialCount);
            }

            toggleSections();
            $('#event_type').on('change', toggleSections);

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
            <form action="{{route('hr-event.update', $hrEvent->id)}}" method="POST" id="hrEventUpdateForm">
                @csrf
                @method('put')
                <div class="card shadow-sm">
                    <div class="card-header fw-bold bg-light">
                        <h6 class="card-title mb-0">
                            Edit HR Event
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="event_type">Event Type <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="event_type" id="event_type">
                                    <option value="">Select Event Type</option>
                                    <option value="Recruitment" {{$hrEvent->event_type == 'Recruitment' ? 'selected' : ''}}>Recruitment</option>
                                    <option value="Orientation" {{$hrEvent->event_type == 'Orientation' ? 'selected' : ''}}>Orientation</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" id="eventDateLabel" for="event_date">Event Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="event_date" id="event_date" value="{{$hrEvent->event_date?->format('Y-m-d')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <div id="recruitmentSection" style="display:none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Recruitment Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="vacancy_for_positions">Vacancy For Positions <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="vacancy_for_positions" id="vacancy_for_positions" value="{{$hrEvent->vacancy_for_positions}}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="project_id">Project</label>
                                    <select class="form-select select2" name="project_id" id="project_id">
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{$project->id}}" {{$hrEvent->project_id == $project->id ? 'selected' : ''}}>{{$project->short_name ?? $project->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_applicants">Total Applicants</label>
                                    <input class="form-control" type="number" name="total_applicants" id="total_applicants" value="{{$hrEvent->total_applicants}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="male_shortlisted">Male Shortlisted</label>
                                    <input class="form-control" type="number" name="male_shortlisted" id="male_shortlisted" value="{{$hrEvent->male_shortlisted}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="female_shortlisted">Female Shortlisted</label>
                                    <input class="form-control" type="number" name="female_shortlisted" id="female_shortlisted" value="{{$hrEvent->female_shortlisted}}" min="0">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_recruited">Total Recruited</label>
                                    <input class="form-control" type="number" name="total_recruited" id="total_recruited" value="{{$hrEvent->total_recruited}}" min="0">
                                </div>
                            </div>

                            <div id="recruitmentTableWrapper" style="display:none;">
                            <div class="table-responsive mt-3">
                                <table class="table table-sm table-bordered mb-0" id="recruitmentTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:40px">SN</th>
                                            <th>Member Name <span class="text-danger">*</span></th>
                                            <th style="width:140px">Gender <span class="text-danger">*</span></th>
                                            <th style="width:160px">Onboard Date</th>
                                            <th>Position</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            </div>

                            <hr>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <label class="form-label" for="recruitment_remarks">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="recruitment_remarks" rows="2" maxlength="500" disabled>{{$hrEvent->remarks}}</textarea>
                                </div>
                            </div>
                        </div>

                        <div id="orientationSection" style="display:none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Orientation Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label required-label" for="orientation_title">Orientation Title</label>
                                    <input class="form-control" type="text" name="orientation_title" id="orientation_title" value="{{$hrEvent->orientation_title}}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="male_participants">Male Participants</label>
                                    <input class="form-control" type="number" name="male_participants" id="male_participants" value="{{$hrEvent->male_participants}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="female_participants">Female Participants</label>
                                    <input class="form-control" type="number" name="female_participants" id="female_participants" value="{{$hrEvent->female_participants}}" min="0">
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <label class="form-label" for="orientation_remarks">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="orientation_remarks" rows="2" maxlength="500" disabled>{{$hrEvent->remarks}}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-sm btn-primary" id="btnUpdate">Update</button>
                        <a href="{{route('hr-event.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
