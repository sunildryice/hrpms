@extends('layouts.container')

@section('title', 'Approved Lieu Leave Request Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-lieu-leave-requests-index').addClass('active');
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
                            <a href="{{ route('approved.lieu.leave.requests.index') }}"
                                class="text-decoration-none text-dark">
                                Lieu Leave Requests
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
                @include('LieuLeave::partials.details')
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header fw-bold">Process Logs</div>
                    <div class="card-body">
                        @php
                            $logs = $lieuLeaveRequest->logs;
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
                                                </div>

                                                <small title="{{ $log->created_at }}">
                                                    {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}
                                                </small>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-justify comment-text mb-0 mt-1">
                                                    {{ $log->log_remarks ?? '' }}
                                                </p>
                                                <div class="badge {{ $log->getStatusClass() ?? 'bg-secondary' }}">
                                                    {{ $log->getStatus() }}
                                                </div>
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
