@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Employee Requisition Detail')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employee-requests-menu').addClass('active');
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
@section('page-content')


            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('employee.requests.index') }}" class="text-decoration-none text-dark">Employee
                                        Requisition</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>
            @php

            @endphp
            <section class="registration">
                <div class="card">
                    <div class="card-header fw-bold">
                        Employee Request Details
                    </div>
                    @include('EmployeeRequest::Partials.detail')
                </div>
                @if($employeeRequest->logs->count()>0)
                    <div class="card">
                        <div class="card-header fw-bold">
                            Employee Request Process
                        </div>
                        <div class="card-body">
                            <div class="c-b">
                                    @foreach ($employeeRequest->logs as $log)
                                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                            <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                <i class="bi-person-circle fs-5"></i>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                    <div class="d-flex flex-column flex-md-row align-items-center gap-md-2">
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
                @endif
            </section>
@stop
