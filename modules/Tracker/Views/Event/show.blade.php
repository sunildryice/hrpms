@extends('layouts.container')

@section('title', 'View Event')

@section('page_js')
    <script type="text/javascript">
        $(function () {
            $('#navbarVerticalMenu').find('#event-index').addClass('active');

        });
    </script>
@endsection

@section('page-content')
    <div class="m-content">
        <div class="container-fluid">

            <x-breadcrumb :items="[
            ['route' => route('event.index'), 'title' => 'Events'],
        ]"/>

            <section>
                <div class="card">
                    <div class="card-header fw-bold">
                        <div style="display: flex; flex-direction: row; justify-content: space-between;">
                            <h6>Event Details</h6>
                            @if($event->created_by == auth()->id())
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
                            @if($event->event_organized_by == 'external')
                                <div class="col-md-4">
                                    <label class="text-muted fw-bold small">Organized By</label>
                                    <p>{{$event->organized_by ?: 'N/A'}}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted fw-bold small">Role</label>
                                    <p>{{$event->role ?: 'N/A'}}</p>
                                </div>
                            @endif
                        </div>

                        @if($event->accompanyingMembers->isNotEmpty())
                            <hr>
                            <h6 class="fw-bold mb-2">Accompanying Members</h6>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p>{{ $event->accompanyingMembers->map(fn($e) => $e->getFullName())->implode(', ') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($event->event_organized_by == 'internal')
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
                        @endif

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

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="text-muted fw-bold small">Attachment</label>
                                <p>
                                    @if ($event->attachment)
                                        <a href="{{asset('storage/'.$event->attachment)}}" target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark-text"></i> View Attachment
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                @if($event->roaster_details && $event->roasters->isNotEmpty())
                    <div class="card mt-3">
                        <div class="card-header fw-bold">
                            <h6>Event Roaster Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                    <tr>
                                        <th>Organisation</th>
                                        <th>Organisation Name</th>
                                        <th>Position</th>
                                        <th>Ethnicity</th>
                                        <th>Gender</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($event->roasters as $roaster)
                                        <tr>
                                            <td>{{$roaster->organisation}}</td>
                                            <td>{{$roaster->organisation_name}}</td>
                                            <td>{{$roaster->position}}</td>
                                            <td>{{$roaster->ethnicity}}</td>
                                            <td>{{$roaster->gender}}</td>
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
