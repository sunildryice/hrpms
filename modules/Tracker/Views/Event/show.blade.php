@extends('layouts.container')

@section('title', 'View Event')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#event-index').addClass('active');

            @if($event->roaster_details)
            // Add Roaster
            $('#addRoasterForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Success', { timeout: 5000 });
                        var roaster = response.roaster;
                        var row = '<tr id="roaster-row-' + roaster.id + '">';
                        row += '<td>' + (roaster.organisation || '') + '</td>';
                        row += '<td>' + (roaster.organisation_name || '') + '</td>';
                        row += '<td>' + (roaster.position || '') + '</td>';
                        row += '<td>' + (roaster.ethnicity || '') + '</td>';
                        row += '<td>' + (roaster.gender || '') + '</td>';
                        row += '<td><a href="javascript:;" class="btn btn-danger btn-sm delete-roaster" data-id="' + roaster.id + '"><i class="bi-trash"></i></a></td>';
                        row += '</tr>';
                        $('#roasterTableBody').append(row);
                        form[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0], 'Validation Error');
                            });
                        } else {
                            toastr.error('Something went wrong.', 'Error');
                        }
                    }
                });
            });

            // Delete Roaster
            $(document).on('click', '.delete-roaster', function(e) {
                e.preventDefault();
                var roasterId = $(this).data('id');
                var $row = $('#roaster-row-' + roasterId);
                var url = "{{ route('event.roaster.destroy', [$event->id, ':roasterId']) }}".replace(':roasterId', roasterId);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This roaster entry will be deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: url,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message, 'Success', { timeout: 5000 });
                                $row.remove();
                            },
                            error: function() {
                                toastr.error('Could not delete roaster.', 'Error');
                            }
                        });
                    }
                });
            });
            @endif
        });
    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('event.index'), 'title' => 'Events'],
        ]" />

        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>Event Details</h6>
                        @can('manage-event')
                        <a href="{{ route('event.edit', $event->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Project</label>
                            <p>{{$event->getProjectTitle()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Event Organized By</label>
                            <p>{{ucfirst($event->event_organized_by)}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Event Type</label>
                            <p>{{ucfirst($event->event_type) ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Event Name</label>
                            <p>{{$event->event_name}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">From Date</label>
                            <p>{{$event->getFromDate()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">To Date</label>
                            <p>{{$event->getToDate()}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Country</label>
                            <p>{{$event->country ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Province</label>
                            <p>{{$event->province ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">District</label>
                            <p>{{$event->district ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">City / Local Level</label>
                            <p>{{$event->city_local_level ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Organized By</label>
                            <p>{{$event->organized_by ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Role</label>
                            <p>{{$event->role ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold mb-2">Participant Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Participants (Government)</label>
                            <p>{{$event->total_participants_government ?? 0}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total HERDi Participants</label>
                            <p>{{$event->total_herdi_participants ?? 0}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Other Participants</label>
                            <p>{{$event->total_other_participants ?? 0}}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Participants</label>
                            <p>{{$event->getTotalParticipants()}}</p>
                        </div>
                    </div>

                    @if($event->action_points || $event->remarks)
                    <hr>
                    <div class="row mb-3">
                        @if($event->action_points)
                        <div class="col-md-6">
                            <label class="text-muted fw-bold small">Action Points</label>
                            <p>{{$event->action_points}}</p>
                        </div>
                        @endif
                        @if($event->remarks)
                        <div class="col-md-6">
                            <label class="text-muted fw-bold small">Remarks</label>
                            <p>{{$event->remarks}}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>

            @if($event->roaster_details)
            <div class="card mt-3">
                <div class="card-header fw-bold">
                    <h6>Event Roaster Details</h6>
                </div>
                <div class="card-body">
                    @can('manage-event')
                    <form id="addRoasterForm" action="{{ route('event.roaster.store', $event->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="row mb-2 align-items-end">
                            <div class="col-lg-2">
                                <label class="form-label" for="organisation">Organisation <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="organisation" id="organisation" required>
                                    <option value="">-- Select --</option>
                                    <option value="HERDi">HERDi</option>
                                    <option value="Government">Government</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="organisation_name">Organisation Name</label>
                                <input class="form-control form-control-sm" type="text" name="organisation_name" id="organisation_name">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="position">Position</label>
                                <input class="form-control form-control-sm" type="text" name="position" id="position">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="ethnicity">Ethnicity</label>
                                <input class="form-control form-control-sm" type="text" name="ethnicity" id="ethnicity">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="gender">Gender</label>
                                <select class="form-select form-select-sm" name="gender" id="gender">
                                    <option value="">-- Select --</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="bi-plus"></i> Add</button>
                            </div>
                        </div>
                    </form>
                    @endcan

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Organisation</th>
                                    <th>Organisation Name</th>
                                    <th>Position</th>
                                    <th>Ethnicity</th>
                                    <th>Gender</th>
                                    @can('manage-event')
                                    <th>Action</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody id="roasterTableBody">
                                @foreach($event->roasters as $roaster)
                                <tr id="roaster-row-{{$roaster->id}}">
                                    <td>{{$roaster->organisation}}</td>
                                    <td>{{$roaster->organisation_name}}</td>
                                    <td>{{$roaster->position}}</td>
                                    <td>{{$roaster->ethnicity}}</td>
                                    <td>{{$roaster->gender}}</td>
                                    @can('manage-event')
                                    <td>
                                        <a href="javascript:;" class="btn btn-danger btn-sm delete-roaster" data-id="{{$roaster->id}}">
                                            <i class="bi-trash"></i>
                                        </a>
                                    </td>
                                    @endcan
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <a href="{{URL::previous()}}" type="button" class="btn btn-sm btn-secondary mt-3">Back</a>
        </section>
    </div>
</div>

@stop
