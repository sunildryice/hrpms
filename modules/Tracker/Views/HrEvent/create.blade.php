@extends('layouts.container')

@section('title', 'Create HR Event')

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

            function addRecruitmentRow(data) {
                data = data || {};
                var container = $('#recruitmentTable tbody');
                var idx = container.find('tr').length;
                var row = '<tr>';
                row += '<td class="text-center align-middle fw-bold sn">' + (idx + 1) + '</td>';
                row += '<td>';
                row += '<input class="form-control form-control-sm recruitment-name" type="text" name="recruitments[' + idx + '][member_name]" value="' + (data.member_name || '') + '" placeholder="Enter name">';
                row += '</td>';
                row += '<td>';
                row += '<select class="form-select form-select-sm recruitment-gender" name="recruitments[' + idx + '][gender]">';
                row += '<option value="">Select</option>';
                row += '<option value="Male"' + (data.gender === 'Male' ? ' selected' : '') + '>Male</option>';
                row += '<option value="Female"' + (data.gender === 'Female' ? ' selected' : '') + '>Female</option>';
                row += '<option value="Other"' + (data.gender === 'Other' ? ' selected' : '') + '>Other</option>';
                row += '</select>';
                row += '</td>';
                row += '<td>';
                row += '<input class="form-control form-control-sm onboard-datepicker" type="text" name="recruitments[' + idx + '][onboard_date]" value="' + (data.onboard_date || '') + '" placeholder="YYYY-MM-DD" onfocus="this.blur()">';
                row += '</td>';
                row += '<td>';
                row += '<input class="form-control form-control-sm" type="text" name="recruitments[' + idx + '][position]" value="' + (data.position || '') + '" placeholder="Enter position">';
                row += '</td>';
                row += '<td class="text-center align-middle" style="width:40px">';
                row += '<button type="button" class="btn btn-sm btn-outline-danger delete-row"><i class="bi bi-trash"></i></button>';
                row += '</td>';
                row += '</tr>';
                container.append(row);

                $('.onboard-datepicker').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                });
            }

            function syncRecruitmentRows(targetCount) {
                var currentCount = $('#recruitmentTable tbody tr').length;
                if (targetCount > currentCount) {
                    for (var i = currentCount; i < targetCount; i++) {
                        addRecruitmentRow();
                    }
                } else if (targetCount < currentCount) {
                    for (var i = currentCount; i > targetCount; i--) {
                        $('#recruitmentTable tbody tr:last').remove();
                    }
                    reindexRows();
                }
            }

            function deleteRecruitmentRow(btn) {
                $(btn).closest('tr').remove();
                reindexRows();
                if ($('#recruitmentTable tbody tr').length === 0) {
                    $('#recruitmentTableWrapper').hide();
                    $('#total_recruited').val(0);
                }
            }

            function reindexRows() {
                $('#recruitmentTable tbody tr').each(function(i) {
                    $(this).find('.sn').text(i + 1);
                    $(this).find('input, select').each(function() {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/recruitments\[\d+\]/, 'recruitments[' + i + ']'));
                        }
                    });
                });
            }

            function validateRecruitments() {
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

            const form = document.getElementById('hrEventCreateForm');
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

            $('#btnSubmit').on('click', function(e) {
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
                if (count > 0) {
                    $('#recruitmentTableWrapper').show();
                    syncRecruitmentRows(count);
                } else {
                    $('#recruitmentTableWrapper').hide();
                    $('#recruitmentTable tbody').empty();
                }
            });

            $('#recruitmentTableWrapper').on('click', '.delete-row', function() {
                deleteRecruitmentRow(this);
                $('#total_recruited').val($('#recruitmentTable tbody tr').length);
            });

            $('#addRecruitmentRow').on('click', function() {
                if (!$('#recruitmentTableWrapper').is(':visible')) {
                    $('#recruitmentTableWrapper').show();
                    $('#total_recruited').val(1);
                } else {
                    $('#total_recruited').val(parseInt($('#total_recruited').val()) + 1);
                }
                syncRecruitmentRows(parseInt($('#total_recruited').val()));
            });

            toggleSections();
            $('#event_type').on('change', toggleSections);

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
                <div class="card shadow-sm">
                    <div class="card-header fw-bold bg-light">
                        <h6 class="card-title mb-0">
                            New HR Event
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="event_type">Event Type <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="event_type" id="event_type">
                                    <option value="">Select Event Type</option>
                                    <option value="Recruitment" {{old('event_type') == 'Recruitment' ? 'selected' : ''}}>Recruitment</option>
                                    <option value="Orientation" {{old('event_type') == 'Orientation' ? 'selected' : ''}}>Orientation</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" id="eventDateLabel" for="event_date">Event Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="event_date" id="event_date" value="{{old('event_date')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <div id="recruitmentSection" style="display:none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Recruitment Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="vacancy_for_positions">Vacancy For Positions <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="vacancy_for_positions" id="vacancy_for_positions" value="{{old('vacancy_for_positions')}}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="project_id">Project</label>
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
                                    <label class="form-label" for="total_recruited">Total Recruited</label>
                                    <input class="form-control" type="number" name="total_recruited" id="total_recruited" value="{{old('total_recruited', 0)}}" min="0">
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
                                            <th class="text-center" style="width:40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addRecruitmentRow"><i class="bi bi-plus"></i> Add Row</button>
                            </div>

                            <hr>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <label class="form-label" for="recruitment_remarks">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="recruitment_remarks" rows="2" maxlength="500" disabled>{{old('remarks')}}</textarea>
                                </div>
                            </div>
                        </div>

                        <div id="orientationSection" style="display:none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Orientation Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label required-label" for="orientation_title">Orientation Title</label>
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
                            <hr>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <label class="form-label" for="orientation_remarks">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="orientation_remarks" rows="2" maxlength="500" disabled>{{old('remarks')}}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-sm btn-primary" id="btnSubmit">Create</button>
                        <a href="{{route('hr-event.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
