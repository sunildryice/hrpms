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
                            <th>Week</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Employee</th>
                            <th>{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weeks as $week)
                            @php
                                $rowClass = '';
                                if ($week['is_current']) {
                                    $rowClass = 'current-week';
                                } elseif ($week['is_past']) {
                                    $rowClass = 'past-week';
                                } else {
                                    $rowClass = 'future-week';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $week['label'] }}</td>
                                <td>{{ $week['start_date'] }}</td>
                                <td>{{ $week['end_date'] }}</td>
                                <td>{{ auth()->user()->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('work-plan.details', ['start_of_week' => $week['start_date_raw'], 'end_of_week' => $week['end_date_raw']]) }}"
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
