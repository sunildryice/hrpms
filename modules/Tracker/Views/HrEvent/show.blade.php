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
<div class="m-content p-3">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('hr-event.index'), 'title' => 'HR Events'],
        ]" />

        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>HR Event Details</h6>
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
                            <label class="text-muted fw-bold small">Event Date</label>
                            <p>{{$hrEvent->getEventDate()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Event Type</label>
                            <p>{{$hrEvent->event_type}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Vacancy For Positions</label>
                            <p>{{$hrEvent->vacancy_for_positions}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Project</label>
                            <p>{{$hrEvent->getProjectTitle()}}</p>
                        </div>
                    </div>

                    @if($hrEvent->event_type == 'Recruitment')
                    <hr>
                    <h6 class="fw-bold mb-2">Recruitment Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Applicants</label>
                            <p>{{$hrEvent->total_applicants}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Male Shortlisted</label>
                            <p>{{$hrEvent->male_shortlisted}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Female Shortlisted</label>
                            <p>{{$hrEvent->female_shortlisted}}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Male Recruited</label>
                            <p>{{$hrEvent->male_recruited}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Female Recruited</label>
                            <p>{{$hrEvent->female_recruited}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Recruited</label>
                            <p>{{$hrEvent->getTotalRecruited()}}</p>
                        </div>
                    </div>
                    @endif

                    @if($hrEvent->event_type == 'Orientation')
                    <hr>
                    <h6 class="fw-bold mb-2">Orientation Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Orientation Title</label>
                            <p>{{$hrEvent->orientation_title ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Male Participants</label>
                            <p>{{$hrEvent->male_participants}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Female Participants</label>
                            <p>{{$hrEvent->female_participants}}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Participants</label>
                            <p>{{$hrEvent->getTotalParticipants()}}</p>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            <a href="{{URL::previous()}}" type="button" class="btn btn-sm btn-secondary">Back</a>
        </section>
    </div>
</div>

@stop
