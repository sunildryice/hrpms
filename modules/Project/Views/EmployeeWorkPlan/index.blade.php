@extends('layouts.container')

@section('title', 'Employee Work Plans')


@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employee-work-plan-index').addClass('active');

            $('#week_selector').change(function() {
                var weekStart = $(this).val();
                if (weekStart) {
                    window.location.href = "{{ route('employee-work-plan.index') }}?week_start=" +
                        weekStart;
                }
            });

            $('#WeeklyPlanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('employee-work-plan.index') }}",
                    data: function(d) {
                        d.week_start = "{{ $currentWeekStart->format('Y-m-d') }}";
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee_full_name',
                        name: 'employee_full_name',
                        orderable: true
                    },
                    {
                        data: 'projects',
                        name: 'projects',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
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
                        <li class="breadcrumb-item" aria-current="page">@yield('title')
                        </li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')
                    </h4>

                    <div style="width: 300px">
                        <select class="form-select select2" name="week_selector" id="week_selector">
                            <option value="">Select Week</option>
                            @foreach ($weeks as $date => $label)
                                <option value="{{ $date }}"
                                    {{ $currentWeekStart->format('Y-m-d') == $date ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded c-tabs-content active" id="weekly-plan-table">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="WeeklyPlanTable">
                    <thead class="bg-light">
                        <tr>
                            <th>SN</th>
                            <th>Employee</th>
                            <th>
                                Projects
                            </th>
                            <th>{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>

    @stop
