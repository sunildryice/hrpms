@extends('layouts.container')

@section('title', 'Work Plan')

@section('page_css')
    <style>
        .current-week {
            background-color: rgb(209, 236, 241) !important;
        }

        .past-week {
            background-color: rgba(226, 227, 229, 0.4) !important;
            color: #6c757d;
        }

        .future-week {
            background-color: #ffffff !important;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#work-plan-index').addClass('active');
        });
    </script>
@endsection
@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded c-tabs-content active" id="weekly-plan-table">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="WeeklyPlanTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workPlans as $workPlan)
                            <tr class="{{ $workPlan->row_class }}">
                                <td>{{ $workPlan->from_date->format('M j, Y') }}</td>
                                <td>{{ $workPlan->to_date->format('M j, Y') }}</td>
                                <td>
                                    <a href="{{ route('work-plan.details', ['workPlan' => $workPlan->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    @stop
