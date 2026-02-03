@extends('layouts.container')

@section('title', 'Off Day Work Request Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#rejected-off-day-work-approve').addClass('active');
        });
    </script>
@endsection

@section('page-content')
    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('rejected.off.day.work.index') }}" class="text-decoration-none text-dark">
                                Rejected Off Day Work Requests
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    @yield('title')
                </h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-4">
                @include('OffDayWork::partials.details')
            </div>
            <div class="col-lg-8">

                {{-- DELIVERABLES --}}
                <div class="card mb-4">
                    <div class="card-header fw-bold text-primary text-uppercase">
                        Deliverables
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Project</th>
                                        <th style="width: 25%;">Activity</th>
                                        <th>Task</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deliverables as $task)
                                        <tr>
                                            <td title="Project">{{ $task['project_name'] }}</td>
                                            <td title="Activity">{{ $task['activity_name'] }}</td>
                                            <td title="Task">{{ $task['task'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header fw-bold">Process Logs</div>
                    <div class="card-body">
                        @php
                            $logs = $offDayWork->logs;
                        @endphp
                        @if (count($logs))
                            <div class="c-b">
                                @foreach ($logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom">
                                        <div class="rounded-circle user-icon"
                                            style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi-person-circle fs-5"></i>
                                        </div>
                                        <div class="w-100">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                <div
                                                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2">
                                                    <label class="form-label mb-0">
                                                        {{ $log->createdBy->full_name ?? 'User' }}
                                                    </label>
                                                    <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                </div>

                                                <small title="{{ $log->created_at }}">
                                                    {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}
                                                </small>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-justify comment-text mb-0 mt-1">
                                                    {{ $log->log_remarks ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <em>No process logs available.</em>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
@stop
