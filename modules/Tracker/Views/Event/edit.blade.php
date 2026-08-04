@extends('layouts.container')

@section('title', 'Edit Event')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#event-index').addClass('active');

            const form = document.getElementById('eventUpdateForm');
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
                let $accompanying = $('#accompanyingMembersField');
                if (val === 'external') {
                    $('#organizedByField, #roleField').show();
                    $('#internalFields').hide();
                    $accompanying.detach().insertAfter('#roleField');
                } else if (val === 'internal') {
                    $('#organizedByField, #roleField').hide();
                    $('#internalFields').show();
                    $accompanying.detach().insertAfter('#cityLocalLevelField');
                } else {
                    $('#organizedByField, #roleField').hide();
                    $('#internalFields').hide();
                    $accompanying.detach().insertAfter('#cityLocalLevelField');
                }
            }

            $('#event_organized_by').on('change', toggleEventOrganizedBy);
            toggleEventOrganizedBy();

            // Roaster section toggle
            let roasterIndex = {{ $event->roasters->count() }};

            $('#roaster_details').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#roasterSection').slideDown();
                } else {
                    $('#roasterSection').slideUp();
                }
            });

            @if($event->roaster_details)
            $('#roasterSection').show();
            @endif

            $('#addRoasterBtn').on('click', function() {
                let name = $('#roaster_name').val();
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
                row += '<td>' + $('<span>').text(name || '').html() + '<input type="hidden" name="roasters[' + roasterIndex + '][name]" value="' + $('<span>').text(name || '').html() + '"></td>';
                row += '<td>' + $('<span>').text(org).html() + '<input type="hidden" name="roasters[' + roasterIndex + '][organisation]" value="' + $('<span>').text(org).html() + '"></td>';
                row += '<td>' + $('<span>').text(orgName || '').html() + '<input type="hidden" name="roasters[' + roasterIndex + '][organisation_name]" value="' + $('<span>').text(orgName || '').html() + '"></td>';
                row += '<td>' + $('<span>').text(pos || '').html() + '<input type="hidden" name="roasters[' + roasterIndex + '][position]" value="' + $('<span>').text(pos || '').html() + '"></td>';
                row += '<td>' + $('<span>').text(eth || '').html() + '<input type="hidden" name="roasters[' + roasterIndex + '][ethnicity]" value="' + $('<span>').text(eth || '').html() + '"></td>';
                row += '<td>' + $('<span>').text(gen || '').html() + '<input type="hidden" name="roasters[' + roasterIndex + '][gender]" value="' + $('<span>').text(gen || '').html() + '"></td>';
                row += '<td><button type="button" class="btn btn-outline-primary btn-sm edit-roaster me-1" title="Edit"><i class="bi-pencil-square"></i></button><button type="button" class="btn btn-danger btn-sm remove-roaster"><i class="bi-trash"></i></button></td>';
                row += '</tr>';

                $('#roasterTableBody').append(row);
                roasterIndex++;

                $('#roaster_name').val('');
                $('#roaster_organisation').val('').trigger('change');
                $('#roaster_organisation_name').val('');
                $('#roaster_position').val('');
                $('#roaster_ethnicity').val('').trigger('change');
                $('#roaster_gender').val('').trigger('change');
            });

            // Edit roaster modal
            let editingRow = null;

            $('#editRoasterModal').on('shown.bs.modal', function() {
                $(this).find('.select2').each(function() {
                    let $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    $el.select2({dropdownParent: $('#editRoasterModal')});
                });
            }).on('hidden.bs.modal', function() {
                $(this).find('.select2').each(function() {
                    let $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                });
            });

            $(document).on('click', '.edit-roaster', function() {
                editingRow = $(this).closest('tr');
                let name = editingRow.find('input[name$="[name]"]').val();
                let org = editingRow.find('input[name$="[organisation]"]').val();
                let orgName = editingRow.find('input[name$="[organisation_name]"]').val();
                let pos = editingRow.find('input[name$="[position]"]').val();
                let eth = editingRow.find('input[name$="[ethnicity]"]').val();
                let gen = editingRow.find('input[name$="[gender]"]').val();

                $('#edit_name').val(name);
                $('#edit_organisation').val(org).trigger('change');
                $('#edit_organisation_name').val(orgName);
                $('#edit_position').val(pos);
                $('#edit_ethnicity').val(eth).trigger('change');
                $('#edit_gender').val(gen).trigger('change');
                $('#editRoasterModal').modal('show');
            });

            $('#saveEditRoaster').on('click', function() {
                if (!editingRow) return;

                let name = $('#edit_name').val();
                let org = $('#edit_organisation').val();
                let orgName = $('#edit_organisation_name').val();
                let pos = $('#edit_position').val();
                let eth = $('#edit_ethnicity').val();
                let gen = $('#edit_gender').val();

                if (!org) {
                    toastr.error('Organisation is required.', 'Error');
                    return;
                }

                let cells = editingRow.find('td');
                let esc = function(v) { return $('<span>').text(v || '').html(); };

                cells.eq(0).contents().first().replaceWith(esc(name));
                cells.eq(0).find('input[name$="[name]"]').val(name);

                cells.eq(1).contents().first().replaceWith(esc(org));
                cells.eq(1).find('input[name$="[organisation]"]').val(org);

                cells.eq(2).contents().first().replaceWith(esc(orgName));
                cells.eq(2).find('input[name$="[organisation_name]"]').val(orgName);

                cells.eq(3).contents().first().replaceWith(esc(pos));
                cells.eq(3).find('input[name$="[position]"]').val(pos);

                cells.eq(4).contents().first().replaceWith(esc(eth));
                cells.eq(4).find('input[name$="[ethnicity]"]').val(eth);

                cells.eq(5).contents().first().replaceWith(esc(gen));
                cells.eq(5).find('input[name$="[gender]"]').val(gen);

                $('#editRoasterModal').modal('hide');
                editingRow = null;
            });

            $(document).on('click', '.remove-roaster', function() {
                let roasterId = $(this).data('roaster-id');
                if (roasterId) {
                    $('#deletedRoasters').append('<input type="hidden" name="deleted_roasters[]" value="' + roasterId + '">');
                }
                $(this).closest('tr').remove();
            });

            $('#addActionRemarkBtn').on('click', function() {
                $('#actionRemarkTableBody').append('<tr>' +
                    '<td><input type="text" class="form-control form-control-sm" name="action_points[]" placeholder="Enter action point"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="remarks[]" placeholder="Enter remark"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-action-remark" title="Remove"><i class="bi-trash"></i></button></td>' +
                '</tr>');
            });

            $(document).on('click', '.remove-action-remark', function() {
                $(this).closest('tr').remove();
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
            <form action="{{route('event.update', $event->id)}}" method="POST" id="eventUpdateForm" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="card">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">
                            Edit Event
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="project_id">Project <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="project_id" id="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{$event->project_id == $project->id ? 'selected' : ''}}>{{$project->short_name ?? $project->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="event_organized_by">Event Organized By <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="event_organized_by" id="event_organized_by">
                                    <option value="">Select Event Organized By</option>
                                    <option value="internal" {{$event->event_organized_by == 'internal' ? 'selected' : ''}}>Internal</option>
                                    <option value="external" {{$event->event_organized_by == 'external' ? 'selected' : ''}}>External</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="event_type">Event Type</label>
                                <select class="form-select select2" name="event_type" id="event_type">
                                    <option value="">Select Event Type</option>
                                    <option value="orientation" {{$event->event_type == 'orientation' ? 'selected' : ''}}>Orientation</option>
                                    <option value="meeting" {{$event->event_type == 'meeting' ? 'selected' : ''}}>Meeting</option>
                                    <option value="training" {{$event->event_type == 'training' ? 'selected' : ''}}>Training</option>
                                    <option value="workshop" {{$event->event_type == 'workshop' ? 'selected' : ''}}>Workshop</option>
                                    <option value="conference" {{$event->event_type == 'conference' ? 'selected' : ''}}>Conference</option>
                                    <option value="other" {{$event->event_type == 'other' ? 'selected' : ''}}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="event_name">Event Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="event_name" id="event_name" value="{{$event->event_name}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="from_date">From Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="from_date" id="from_date" value="{{$event->from_date?->format('Y-m-d')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="to_date">To Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="to_date" id="to_date" value="{{$event->to_date?->format('Y-m-d')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="country">Country</label>
                                <input class="form-control" type="text" name="country" id="country" value="{{$event->country}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="province">Province</label>
                                <input class="form-control" type="text" name="province" id="province" value="{{$event->province}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="district">District</label>
                                <input class="form-control" type="text" name="district" id="district" value="{{$event->district}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4" id="cityLocalLevelField">
                                <label class="form-label" for="city_local_level">Local Level</label>
                                <input class="form-control" type="text" name="city_local_level" id="city_local_level" value="{{$event->city_local_level}}">
                            </div>
                            <div class="col-lg-4" id="accompanyingMembersField">
                                <label class="form-label" for="accompanying_members">Accompanying Members</label>
                                <select class="form-select select2" name="accompanying_members[]" id="accompanying_members" multiple>
                                    @foreach($employees as $employee)
                                        <option value="{{$employee->id}}" {{ $event->accompanyingMembers->contains($employee->id) ? 'selected' : '' }}>{{$employee->getFullName()}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4" id="organizedByField" style="display: none;">
                                <label class="form-label" for="organized_by">Organized By</label>
                                <input class="form-control" type="text" name="organized_by" id="organized_by" value="{{$event->organized_by}}">
                            </div>
                            <div class="col-lg-4" id="roleField" style="display: none;">
                                <label class="form-label" for="role">Role</label>
                                <select class="form-select select2" name="role" id="role">
                                    <option value="">Select Role</option>
                                    @foreach($eventRoles as $eventRole)
                                        <option value="{{$eventRole->value}}" {{$event->role == $eventRole->value ? 'selected' : ''}}>{{$eventRole->label()}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="internalFields" style="display: none;">
                            <hr>
                            <h6 class="fw-bold mb-2">Participant Details</h6>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_participants_government">Total Participants (Government)</label>
                                    <input class="form-control" type="number" name="total_participants_government" id="total_participants_government" value="{{$event->total_participants_government}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_herdi_participants">Total HERDi Participants</label>
                                    <input class="form-control" type="number" name="total_herdi_participants" id="total_herdi_participants" value="{{$event->total_herdi_participants}}" min="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="total_other_participants">Total Other Participants</label>
                                    <input class="form-control" type="number" name="total_other_participants" id="total_other_participants" value="{{$event->total_other_participants}}" min="0">
                                </div>
                            </div>

                            <hr>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" name="roaster_details" id="roaster_details" value="1" {{$event->roaster_details ? 'checked' : ''}}>
                                        <label class="form-check-label fw-bold" for="roaster_details">Add Roster Details</label>
                                    </div>
                                </div>
                            </div>

                            <div id="roasterSection" style="display: none;">
                                <hr>
                                <h6 class="fw-bold mb-2">Event Roster Details</h6>
                                <div class="row mb-2 align-items-end">
                                    <div class="col-lg-2">
                                        <label class="form-label" for="roaster_name">Name</label>
                                        <input type="text" class="form-control form-control-sm" id="roaster_name">
                                    </div>
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
                                        <select class="form-select form-select-sm select2" id="roaster_ethnicity">
                                            <option value="">Select Ethnicity</option>
                                            @foreach($ethnicities as $ethnicity)
                                                <option value="{{$ethnicity->value}}">{{$ethnicity->label()}}</option>
                                            @endforeach
                                        </select>
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
                                                <th>Name</th>
                                                <th>Organisation</th>
                                                <th>Organisation Name</th>
                                                <th>Position</th>
                                                <th>Ethnicity</th>
                                                <th>Gender</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="roasterTableBody">
                                            @foreach($event->roasters as $i => $roaster)
                                            <tr>
                                                <td>
                                                    {{$roaster->name}}
                                                    <input type="hidden" name="roasters[{{$i}}][name]" value="{{$roaster->name}}">
                                                </td>
                                                <td>
                                                    {{$roaster->organisation}}
                                                    <input type="hidden" name="roasters[{{$i}}][id]" value="{{$roaster->id}}">
                                                    <input type="hidden" name="roasters[{{$i}}][organisation]" value="{{$roaster->organisation}}">
                                                </td>
                                                <td>
                                                    {{$roaster->organisation_name}}
                                                    <input type="hidden" name="roasters[{{$i}}][organisation_name]" value="{{$roaster->organisation_name}}">
                                                </td>
                                                <td>
                                                    {{$roaster->position}}
                                                    <input type="hidden" name="roasters[{{$i}}][position]" value="{{$roaster->position}}">
                                                </td>
                                                <td>
                                                    {{$roaster->ethnicity}}
                                                    <input type="hidden" name="roasters[{{$i}}][ethnicity]" value="{{$roaster->ethnicity}}">
                                                </td>
                                                <td>
                                                    {{$roaster->gender}}
                                                    <input type="hidden" name="roasters[{{$i}}][gender]" value="{{$roaster->gender}}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-primary btn-sm edit-roaster me-1" title="Edit">
                                                        <i class="bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm remove-roaster" data-roaster-id="{{$roaster->id}}">
                                                        <i class="bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="deletedRoasters"></div>
                        </div>

                        <!-- Edit Roaster Modal -->
                        <div class="modal fade" id="editRoasterModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h6 class="modal-title">Edit Roster</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-2">
                                            <div class="col-lg-4">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control form-control-sm" id="edit_name">
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Organisation <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm select2" id="edit_organisation">
                                                    <option value="">Select Organisation</option>
                                                    <option value="HERDi">HERDi</option>
                                                    <option value="Government">Government</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Organisation Name</label>
                                                <input type="text" class="form-control form-control-sm" id="edit_organisation_name">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-lg-4">
                                                <label class="form-label">Position</label>
                                                <input type="text" class="form-control form-control-sm" id="edit_position">
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Ethnicity</label>
                                                <select class="form-select form-select-sm select2" id="edit_ethnicity">
                                                    <option value="">Select Ethnicity</option>
                                                    @foreach($ethnicities as $ethnicity)
                                                        <option value="{{$ethnicity->value}}">{{$ethnicity->label()}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Gender</label>
                                                <select class="form-select form-select-sm select2" id="edit_gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-sm btn-primary" id="saveEditRoaster">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-12">
                                <label class="form-label fw-bold">Action Points &amp; Remarks</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Action Point</th>
                                                <th>Remark</th>
                                                <th class="text-center" style="width: 60px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="actionRemarkTableBody">
                                            @php
                                                $ap = $event->actionPoints->pluck('action_point');
                                                $rm = $event->remarks->pluck('remark');
                                                $count = max($ap->count(), $rm->count(), 1);
                                            @endphp
                                            @for($i = 0; $i < $count; $i++)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="action_points[]" value="{{ $ap[$i] ?? '' }}" placeholder="Enter action point">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="remarks[]" value="{{ $rm[$i] ?? '' }}" placeholder="Enter remark">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-action-remark" title="Remove"><i class="bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" id="addActionRemarkBtn"><i class="bi-plus"></i> Add</button>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label class="form-label" for="attachment">Attachment</label>
                                <input class="form-control" type="file" name="attachment" id="attachment">
                                <small class="text-muted">Supported files: pdf, jpg, jpeg, png, doc, docx, xlsx (Max 2MB)</small>
                                @if ($event->attachment)
                                    <a href="{{asset('storage/'.$event->attachment)}}" target="_blank" class="ms-2 fs-5" title="View Attachment">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @endif
                                @if ($errors->has('attachment'))
                                    <span class="text-danger">{{$errors->first('attachment')}}</span>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        <a href="{{route('event.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
