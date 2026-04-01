@extends('layouts.container')

@section('title', '360 Feedback - ' . $performanceReview->getReviewType())

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#performance-external-review-index').addClass('active');

            // GROUP H - External Reviewer Comments
            $('#groupHForm').on('submit', function(e) {
                e.preventDefault();

                let result = $('#result').val().trim();
                let comments = $('#comments').val().trim();

                if (!result || !comments) {
                    toastr.error('Please provide both Result and Comments before saving.',
                        'Validation Error');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        performance_review_id: "{{ $performanceReview->id }}",
                        result: result,
                        comments: comments
                    },
                    success: function(response) {
                        if (response.type === 'success') {
                            toastr.success('Result and Comments saved successfully!',
                                'Success');
                        } else {
                            toastr.error(response.message ||
                                'Failed to save result and comments.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        toastr.error('Something went wrong while saving result and comments.');
                    }
                });
            });

        });
    </script>
@endsection

@section('page-content')

    <style>
        td,
        th {
            border: 1px solid grey;
            padding: 8px;
            text-align: left;
        }
    </style>

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('performance.external-review.index') }}"
                                class="text-decoration-none text-dark">360 Feedback</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section>
        <!-- A. Employee and Line Manager Details -->
        @include('PerformanceReview::Partials.employeeDetails')

        <!-- B, C, D, E, F G Forms Deatils-->
        @include('PerformanceReview::Partials.showFormDetails')
    </section>

    <!-- Process Logs -->
    <section>
        <div class="card">
            <div class="card-header fw-bold">Performance Review Process</div>
            <div class="card-body">
                @foreach ($performanceReview->logs as $log)
                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom">
                        <div class="rounded-circle mr-3 user-icon">
                            <i class="bi-person-circle fs-5"></i>
                        </div>
                        <div class="w-100">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $log->createdBy->getFullName() }}</strong>
                                    <span class="badge bg-primary ms-2">
                                        {!! $log->createdBy->employee?->latestTenure?->getDesignationName() !!}
                                    </span>
                                </div>
                                <small>{{ $log->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <p class="mb-0 mt-1">{{ $log->log_remarks }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
