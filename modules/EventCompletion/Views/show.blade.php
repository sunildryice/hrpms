@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Event Completion Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#event-menu').addClass('active');

            var oTable = $('#participantsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('event.completion.participants.index', $eventCompletion->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'designation',
                        name: 'designation'
                    },
                    {
                        data: 'contact',
                        name: 'contact'
                    },
                ]
            });

            $('#background-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-background').toggle();
                $('#full-background').toggle();
            });

            $('#objectives-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-objectives').toggle();
                $('#full-objectives').toggle();
            });

            $('#process-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-process').toggle();
                $('#full-process').toggle();
            });

            $('#closing-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-closing').toggle();
                $('#full-closing').toggle();
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
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('event.completion.index') }}" class="text-decoration-none text-dark">Event
                                Completions</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Event Completion Details
                    </div>
                    @include('EventCompletion::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Event Participants
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="participantsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.name') }}</th>
                                        <th scope="col">{{ __('label.office') }}</th>
                                        <th scope="col">{{ __('label.designation') }}</th>
                                        <th scope="col">{{ __('label.contact') }}</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                @include('Attachment::list', [
                    'modelType' => 'Modules\EventCompletion\Models\EventCompletion',
                    'modelId' => $eventCompletion->id,
                ])

                <div class="card">
                    <div class="card-header fw-bold">Event Completion Process</div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($eventCompletion->logs as $log)
                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                        <i class="bi-person-circle fs-4"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                            </div>
                                            <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <p class="text-justify comment-text mb-0 mt-1">
                                            {{ $log->log_remarks }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
