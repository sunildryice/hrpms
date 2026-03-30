@extends('layouts.container')

@section('title', 'View Annual Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('performance.employee.index') }}"
                                class="text-decoration-none text-dark">Performance Review</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
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

    @if ($performanceReview->status_id == config('constant.RETURNED_STATUS'))
        <section>
            <div class="col-lg-6">
                <div class="p-3 mb-2 border row">
                    <div class="fw-bold text-decoration-underline">Remarks:</div>
                    <div class="mt-2">{{ $performanceReview->getLatestRemark() }}</div>
                </div>
            </div>
        </section>
    @endif

@endsection
