@extends('layouts.container')

@section('title', 'View Annual Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-index').addClass('active');

            getKeyGoalsEmployee();
            getKeyGoalsSupervisor();
        });

        function getKeyGoalsEmployee() {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.employee.get') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
                },
                success: function(data) {
                    $('#keygoal_employee').html.empty;
                    $('#keygoal_employee').html(data.goal);
                },
                error: function(data) {
                    // toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
                }
            });
        }

        function getKeyGoalsSupervisor() {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.supervisor.get') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
                },
                success: function(data) {
                    $('#keygoal_supervisor').html.empty;
                    $('#keygoal_supervisor').html(data.goal);
                },
                error: function(data) {
                    // toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
                }
            });
        }
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
                            <a href="{{ route('performance.index') }}" class="text-decoration-none text-dark">Performance
                                Review</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <section>

        <!-- A. Employee and Line Manager Details -->
        @include('PerformanceReview::Partials.employeeDetails')

        <!-- B, C, D, E, F G Forms Deatils-->
        @include('PerformanceReview::Partials.showFormDetails')

    </section>


    <section>
        <div class="card">
            <div class="card-header fw-bold">
                Performance Review Process
            </div>
            <div class="card-body">
                <div class="c-b">
                    @foreach ($performanceReview->logs as $log)
                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                            <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                <i class="bi-person-circle fs-5"></i>
                            </div>
                            <div class="w-100">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                    <div
                                        class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                        <label class="form-label mb-0">{{ $log->createdBy->getFullName() }}</label>
                                        <span class="badge bg-primary c-badge">
                                            {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                        </span>
                                    </div>
                                    <small
                                        title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
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
    </section>
@endsection
