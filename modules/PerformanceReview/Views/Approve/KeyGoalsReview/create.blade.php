@extends('layouts.container')

@section('title', 'Approve Performance Review Key Goals')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-recommend-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')

<style>
    td, th {
        border: 1px solid grey;
        padding: 8px;
        text-align: left;
    }
</style>



        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('performance.approve.index') }}"
                                    class="text-decoration-none text-dark">Performance Review</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>

            <div id="employeeAndSupervisorDetails" class="mb-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">A.</span>
                            <span>
                                Employee and Supervisor Details
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Employee Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getEmployeeName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Employee Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getEmployeeTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Supervisor Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getSupervisorName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Supervisor Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getSupervisorTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Technical Supervisor's Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getTechnicalSupervisorName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Technical Supervisor's Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getTechnicalSupervisorTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Date of Joining</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->employee->getFirstJoinedDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">In Current Position Since</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getJoinedDate()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Review period from:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getReviewFromDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Review period to:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getReviewToDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-3">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Deadline:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getDeadlineDate()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="keygoals" class="mb-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">B.</span>
                            <span>
                                Key Goals
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        <ol>
                            @foreach ($currentKeyGoals as $goal)
                                <li>{{$goal->title}}</li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>

            <div id="professionalDevelopmentPlan" class="mb-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">C.</span>
                            <span>
                                Professional Development Plan
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <span>{{$performanceReview->getAnswer($professionalDevelopmentPlanQuestion->id)}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    Performance Review Process
                </div>
                <form action="{{route('performance.approve.store')}}"
                      id="performanceReviewProcessForm" method="post"
                      enctype="multipart/form-data" autocomplete="off">
                      <input type="hidden" name="performance_review_id" value="{{$performanceReview->id}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                @foreach($performanceReview->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40"
                                            class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person"></i>
                                        </div>
                                      <div class="w-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                    <span class="me-2">{{ $log->createdBy->getFullName() }}</span>
                                                    <span class="badge bg-primary c-badge">
                                                        {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                    </span>
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
                            <div class="col-lg-6">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="status_id" class="form-label required-label">Status </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select Status</option>
                                            <option value="{{ config('constant.RETURNED_STATUS') }}">Return to Employee</option>
                                            <option value="{{ config('constant.APPROVED_STATUS') }}">Approve</option>
                                        </select>
                                        @if ($errors->has('status_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="status_id">
                                                    {!! $errors->first('status_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="log_remarks" class="form-label required-label">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea type="text"
                                                  class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                  name="log_remarks">{{ old('log_remarks') }}</textarea>
                                        @if ($errors->has('log_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div
                                                    data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                            Submit
                        </button>
                        <a href="{!! route('performance.recommend.index') !!}"
                           class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </section>

@stop














