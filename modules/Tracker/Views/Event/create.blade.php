@extends('layouts.container')

@section('title', 'Create Event')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#event-index').addClass('active');

            const form = document.getElementById('eventCreateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    project_id: {
                        validators: {
                            notEmpty: {
                                message: 'The project is required.'
                            }
                        }
                    },
                    event_name: {
                        validators: {
                            notEmpty: {
                                message: 'The event name is required.'
                            }
                        }
                    },
                    event_organized_by: {
                        validators: {
                            notEmpty: {
                                message: 'The event organized by is required.'
                            }
                        }
                    },
                    from_date: {
                        validators: {
                            notEmpty: {
                                message: 'The from date is required.'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Date must be a valid date.'
                            }
                        }
                    },
                    to_date: {
                        validators: {
                            notEmpty: {
                                message: 'The to date is required.'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Date must be a valid date.'
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
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'from_date',
                            message: 'From date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'to_date',
                            message: 'To date must be a valid date and later than or equal to from date.',
                        },
                    }),
                }
            });

            $('[name="from_date"], [name="to_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function (e) {
                fv.revalidateField($(this).attr('name'));
            });

            // Toggle fields based on Event Organized By
            function toggleEventOrganizedBy() {
                let val = $('#event_organized_by').val();
                if (val === 'external') {
                    $('#organizedByField, #roleField').show();
                    $('#internalFields').hide();
                } else if (val === 'internal') {
                    $('#organizedByField, #roleField').hide();
                    $('#internalFields').show();
                } else {
                    $('#organizedByField, #roleField').hide();
                    $('#internalFields').hide();
                }
            }

            $('#event_organized_by').on('change', toggleEventOrganizedBy);
            toggleEventOrganizedBy();

            // Roaster section toggle
            let roasterIndex = 0;

            $('#roaster_details').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#roasterSection').slideDown();
                } else {
                    $('#roasterSection').slideUp();
                }
            });

            $('#addRoasterBtn').on('click', function() {
                let org = $('#roaster_organisation').val();
                let orgName = $('#roaster_organisation_name').val();
                let pos = $('#roaster_position').val();
                let eth = $('#roaster_ethnicity').val();
                let gen = $('#roaster_gender').val();

                if (!org) {
                    toastr.error('Organisation is required.', 'Error');
                    return;
                }

                let row = '<tr>';
                row += '<td>' + org + '<input type="hidden" name="roasters[' + roasterIndex + '][organisation]" value="' + org + '"></td>';
                row += '<td>' + (orgName || '') + '<input type="hidden" name="roasters[' + roasterIndex + '][organisation_name]" value="' + orgName + '"></td>';
                row += '<td>' + (pos || '') + '<input type="hidden" name="roasters[' + roasterIndex + '][position]" value="' + pos + '"></td>';
                row += '<td>' + (eth || '') + '<input type="hidden" name="roasters[' + roasterIndex + '][ethnicity]" value="' + eth + '"></td>';
                row += '<td>' + (gen || '') + '<input type="hidden" name="roasters[' + roasterIndex + '][gender]" value="' + gen + '"></td>';
                row += '<td><button type="button" class="btn btn-danger btn-sm remove-roaster"><i class="bi-trash"></i></button></td>';
                row += '</tr>';

                $('#roasterTableBody').append(row);
                roasterIndex++;

                $('#roaster_organisation').val('').trigger('change');
                $('#roaster_organisation_name').val('');
                $('#roaster_position').val('');
                $('#roaster_ethnicity').val('');
                $('#roaster_gender').val('').trigger('change');
            });

            $(document).on('click', '.remove-roaster', function() {
                $(this).closest('tr').remove();
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
                                <a href="{{route('event.index')}}" class="text-decoration-none">Events</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('event.store')}}" method="POST" id="eventCreateForm">
                @csrf
                <div class="card">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">
                            New Event
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="project_id">Project <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="project_id" id="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{old('project_id') == $project->id ? 'selected' : ''}}>{{$project->short_name ?? $project->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="event_organized_by">Event Organized By <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="event_organized_by" id="event_organized_by">
                                    <option value="">Select Event Organized By</option>
                                    <option value="internal" {{old('event_organized_by') == 'internal' ? 'selected' : ''}}>Internal</option>
                                    <option value="external" {{old('event_organized_by') == 'external' ? 'selected' : ''}}>External</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="event_type">Event Type</label>
                                <select class="form-select select2" name="event_type" id="event_type">
                                    <option value="">Select Event Type</option>
                                    <option value="orientation" {{old('event_type') == 'orientation' ? 'selected' : ''}}>Orientation</option>
                                    <option value="meeting" {{old('event_type') == 'meeting' ? 'selected' : ''}}>Meeting</option>
                                    <option value="training" {{old('event_type') == 'training' ? 'selected' : ''}}>Training</option>
                                    <option value="workshop" {{old('event_type') == 'workshop' ? 'selected' : ''}}>Workshop</option>
                                    <option value="conference" {{old('event_type') == 'conference' ? 'selected' : ''}}>Conference</option>
                                    <option value="other" {{old('event_type') == 'other' ? 'selected' : ''}}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="event_name">Event Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="event_name" id="event_name" value="{{old('event_name')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="from_date">From Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="from_date" id="from_date" value="{{old('from_date')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="to_date">To Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="to_date" id="to_date" value="{{old('to_date')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="country">Country</label>
                                <input class="form-control" type="text" name="country" id="country" value="{{old('country')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="province">Province</label>
                                <input class="form-control" type="text" name="province" id="province" value="{{old('province')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="district">District</label>
                                <input class="form-control" type="text" name="district" id="district" value="{{old('district')}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="city_local_level">City / Local Level</label>
                                <input class="form-control" type="text" name="city_local_level" id="city_local_level" value="{{old('city_local_level')}}">
                            </div>
                            <div class="col-lg-4" id="organizedByField" style="display: none;">
                                <label class="form-label" for="organized_by">Organized By</label>
                                <input class="form-control" type="text" name="organized_by" id="organized_by" value="{{old('organized_by')}}">
                            </div>
                            <div class="col-lg-4" id="roleField" style="display: none;">
                                <label class="form-label" for="role">Role</label>
                                <input class="form-control" type="text" name="role" id="role" value="{{old('role')}}">
                            </div>
                        </div>

                        <div id="internalFields" style="display: none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Participant Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_participants_government">Total Participants (Government)</label>
                                    <input class="form-control" type="number" name="total_participants_government" id="total_participants_government" value="{{old('total_participants_government', 0)}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_herdi_participants">Total HERDi Participants</label>
                                    <input class="form-control" type="number" name="total_herdi_participants" id="total_herdi_participants" value="{{old('total_herdi_participants', 0)}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_other_participants">Total Other Participants</label>
                                    <input class="form-control" type="number" name="total_other_participants" id="total_other_participants" value="{{old('total_other_participants', 0)}}" min="0">
                                </div>
                            </div>

                            <hr>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" name="roaster_details" id="roaster_details" value="1" {{old('roaster_details') ? 'checked' : ''}}>
                                        <label class="form-check-label fw-bold" for="roaster_details">Add Roaster Details</label>
                                    </div>
                                </div>
                            </div>

                            <div id="roasterSection" style="display: none;">
                                <hr>
                                <h6 class="fw-bold mb-2">Event Roaster Details</h6>
                                <div class="row mb-2 align-items-end">
                                    <div class="col-lg-2">
                                        <label class="form-label" for="roaster_organisation">Organisation <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm select2" id="roaster_organisation">
                                            <option value="">Select Organisation</option>
                                            <option value="HERDi">HERDi</option>
                                            <option value="Government">Government</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label" for="roaster_organisation_name">Organisation Name</label>
                                        <input type="text" class="form-control form-control-sm" id="roaster_organisation_name">
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label" for="roaster_position">Position</label>
                                        <input type="text" class="form-control form-control-sm" id="roaster_position">
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label" for="roaster_ethnicity">Ethnicity</label>
                                        <input type="text" class="form-control form-control-sm" id="roaster_ethnicity">
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label" for="roaster_gender">Gender</label>
                                        <select class="form-select form-select-sm select2" id="roaster_gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-sm btn-primary mt-4" id="addRoasterBtn"><i class="bi-plus"></i> Add</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Organisation</th>
                                                <th>Organisation Name</th>
                                                <th>Position</th>
                                                <th>Ethnicity</th>
                                                <th>Gender</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="roasterTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label class="form-label" for="action_points">Action Points</label>
                                <textarea class="form-control" name="action_points" id="action_points" rows="3">{{old('action_points')}}</textarea>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3">{{old('remarks')}}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary" id="btnSubmit">Create</button>
                        <a href="{{route('event.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
