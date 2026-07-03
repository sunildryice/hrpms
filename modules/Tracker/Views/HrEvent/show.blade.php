@extends('layouts.container')

@section('title', 'View HR Event')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#hr-event-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')
<div class="m-content">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('hr-event.index'), 'title' => 'HR Events'],
        ]" />

        <section>
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-light">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6 class="mb-0">HR Event Details</h6>
                        @can('manage-hr-event')
                        <a href="{{ route('hr-event.edit', $hrEvent->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Event Type</label>
                            <p class="mb-0">{{$hrEvent->event_type}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">{{ $hrEvent->event_type === 'Recruitment' ? 'Vacancy Announcement Date' : 'Event Date' }}</label>
                            <p class="mb-0">{{$hrEvent->getEventDate()}}</p>
                        </div>
                    </div>

                    @if($hrEvent->event_type == 'Recruitment')
                    <hr>
                    <h6 class="fw-bold mb-3">Recruitment Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Vacancy For Positions</label>
                            <p class="mb-0">{{$hrEvent->vacancy_for_positions}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Project</label>
                            <p class="mb-0">{{$hrEvent->getProjectTitle()}}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Applicants</label>
                            <p class="mb-0">{{$hrEvent->total_applicants}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Male Shortlisted</label>
                            <p class="mb-0">{{$hrEvent->male_shortlisted}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Female Shortlisted</label>
                            <p class="mb-0">{{$hrEvent->female_shortlisted}}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Recruited</label>
                            <p class="mb-0">{{$hrEvent->getTotalRecruited()}}</p>
                        </div>
                    </div>

                    @if($hrEvent->recruitments->isNotEmpty())
                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width:40px">SN</th>
                                    <th>Member Name</th>
                                    <th style="width:140px">Gender</th>
                                    <th style="width:160px">Onboard Date</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hrEvent->recruitments as $index => $r)
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td>{{ $r->member_name }}</td>
                                    <td>{{ $r->gender }}</td>
                                    <td>{{ $r->onboard_date?->toFormattedDateString() }}</td>
                                    <td>{{ $r->position ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @endif

                    @if($hrEvent->event_type == 'Orientation')
                    <hr>
                    <h6 class="fw-bold mb-3">Orientation Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Orientation Title</label>
                            <p class="mb-0">{{$hrEvent->orientation_title ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Male Participants</label>
                            <p class="mb-0">{{$hrEvent->male_participants}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Female Participants</label>
                            <p class="mb-0">{{$hrEvent->female_participants}}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Participants</label>
                            <p class="mb-0">{{$hrEvent->getTotalParticipants()}}</p>
                        </div>
                    </div>
                    @endif

                    @if($hrEvent->remarks)
                    <hr>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <label class="text-muted fw-bold small">Remarks</label>
                            <p class="mb-0">{{$hrEvent->remarks}}</p>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            <a href="{{URL::previous()}}" type="button" class="btn btn-sm btn-secondary mt-3">Back</a>
        </section>
    </div>
</div>

@stop
