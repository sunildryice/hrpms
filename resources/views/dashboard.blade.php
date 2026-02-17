@extends('layouts.container')

@section('title', 'Dashboard')

@section('page_css')
    <style>
        :root {
            --c-sick-leave: #dc3545;
            --c-annual-leave: #dc3545;
            --c-lieu-leave: #dc3545;
            --c-work-from-home: #0dcaf0;
            --c-travel: #6610f2;
            --c-work: #1f9005;
            --bg-weekend: #e9ecef;
            --c-leave: #dc3545;
            --bg-holiday: #e9ecef;
            --bg-holiday-bar: #fd7e14;
        }

        .bg-travel {
            background-color: var(--c-travel);
        }

        .bg-work-from-home {
            background-color: var(--c-work-from-home);
        }
    </style>
@endsection


@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#dashboard').addClass('active');

            // Check In Today with Confirmation
            $(document).on('click', '.checkin-today-btn', function() {
                let date = $(this).data('date');
                let btn = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    // icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#01aef0',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Check In',
                    cancelButtonText: 'Cancel',
                    // reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="bi bi-hourglass-split"></i> Checking in...');

                        $.ajax({
                            url: "{{ route('attendance.checkin.today') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                date: date
                            },
                            success: function(response) {
                                toastr.success('Checked in at ' + response.time);
                                $('#today-attendance-action').html(`
                        <button class="btn btn-warning btn-sm checkout-today-btn" data-date="${date}">
                            <i class="bi bi-box-arrow-in-left"></i> Check Out
                        </button>
                    `);
                                oTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(
                                    '<i class="bi bi-box-arrow-in-right"></i> Check In Now'
                                );
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to check in');
                            }
                        });
                    }
                });
            });

            // Check Out Today with Confirmation
            $(document).on('click', '.checkout-today-btn', function() {
                let date = $(this).data('date');
                let btn = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    // icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#01aef0',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Check Out',
                    cancelButtonText: 'Cancel',
                    reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="bi bi-hourglass-split"></i> Checking out...');

                        $.ajax({
                            url: "{{ route('attendance.checkout.today') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                date: date
                            },
                            success: function(response) {
                                toastr.success('Checked out at ' + response.time +
                                    ' on ' + response.worked_hours + ' hours worked'
                                );
                                $('#today-attendance-action').html(`
                                    <button class="btn btn-success btn-sm" disabled>
                                        <i class="bi bi-fingerprint"></i> Completed Today
                                    </button>
                                `);
                                oTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(
                                    '<i class="bi bi-box-arrow-in-left"></i> Check Out'
                                );
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to check out');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection

@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">Dashboard</h4>
            </div>
            <div class="add-info justify-content-end d-flex align-items-center gap-1">
                <div class="py-3 rounded text-end" id="today-attendance-action">
                    @php
                        $today = now()->format('Y-m-d');
                        $employeeId = auth()->user()->employee->id;

                        $attendanceMaster = \Modules\EmployeeAttendance\Models\Attendance::where(
                            'employee_id',
                            $employeeId,
                        )
                            ->where('year', now()->year)
                            ->where('month', now()->month)
                            ->first();

                        $hasCheckIn = false;
                        $hasCheckOut = false;

                        if ($attendanceMaster) {
                            $todayDetail = $attendanceMaster
                                ->attendanceDetails()
                                ->where('attendance_date', $today)
                                ->first();

                            $hasCheckIn = $todayDetail && $todayDetail->checkin;
                            $hasCheckOut = $todayDetail && $todayDetail->checkout;
                        }
                    @endphp

                    @if ($hasCheckOut)
                        <button class="btn btn-success btn-sm" disabled>
                            <i class="bi bi-fingerprint"></i> Completed Today
                        </button>
                    @elseif ($hasCheckIn)
                        <button class="btn btn-warning btn-sm checkout-today-btn" data-date="{{ $today }}">
                            <i class="bi bi-box-arrow-in-left"></i> Check Out
                        </button>
                    @else
                        <button class="btn btn-primary btn-sm checkin-today-btn" data-date="{{ $today }}">
                            <i class="bi bi-box-arrow-in-right"></i> Check In
                        </button>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <div class="welcome-page">
        <div class="row">

            {{-- @if ($pendingPerformanceReviews->isNotEmpty())
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold">Pending Performance Review</div>
                        <div class="p-3 div-content-area">

                            @foreach ($pendingPerformanceReviews as $performanceReview)
                                <div class="gap-2 mb-4 d-flex align-items-start">
                                    <div class="">
                                        <span
                                            class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-graph-up-arrow"></i>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div class="request-title d-flex justify-content-between">
                                            <div class="fw-bold">{{$performanceReview->getReviewType()}}</div>
                                            <small class="text-danger fw-semi-bold">
                                                Deadline Date</small>
                                        </div>
                                        <div class="text-muted d-flex flex-column flex-lg-row justify-content-between">
                                            <small>{{$performanceReview->getReviewFromDate()}} - {{$performanceReview->getReviewToDate()}}</small>
                                            <small> {{$performanceReview->getDeadlineDate()}}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endif --}}


            {{-- @if ($eventCompletionReports->isNotEmpty())
                @if ($authUser->can('approve-travel-request') || $authUser->can('approve-recommended-travel-request'))
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold"> Event Completion Report </div>
                        <div class="py-2 div-content-area">
                            @foreach ($eventCompletionReports as $eventCompletion)
                                <div class="gap-2 mb-3 d-flex align-items-center">
                                    <div class="">
                                        <span
                                            class="text-white rounded opacity-75 bg-primary avatar d-flex align-items-center justify-content-center">
                                            <i class="bi-envelope-check"></i>
                                        </span>
                                    </div>
                                    <div class="">
                                        <div class="request-title">
                                            <a href="{{ route('approve.travel.requests.create', $eventCompletion->id) }}"
                                                style="text-decoration: none; color:unset">
                                                <strong>{{ '(' . $eventCompletion->getRequesterName() . ')' }}</strong>
                                            </a>
                                        </div>
                                        <div class="text-muted">
                                            <small> {{ $eventCompletion->created_at->diffForHumans() }} </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif --}}


            @if ($announcements->isNotEmpty())
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold"> Announcement</div>
                        <div class="p-3 div-content-area">
                            @foreach ($announcements as $announcement)
                                <div class="mb-3 row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-megaphone"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="request-title text-capitalize fw-bold">
                                            <a href="{{ route('announcement.show', $announcement->id) }}"
                                                style="text-decoration: none; color: unset">
                                                {{ $announcement->getTitle() }}
                                            </a>
                                        </div>
                                        <div class="text-muted">
                                            <small>Published on: {{ $announcement->getPublishedDate() }}</small>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="rounded d-flex align-items-center justify-content-center">
                                            @if (isset($announcement->attachment))
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ asset('storage/' . $announcement->attachment) }}"
                                                    target="_blank" rel="tooltip" title="View attachment">
                                                    <i class="bi bi-download fs-7"></i>
                                                </a>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($approvedLeaves->isNotEmpty() || $upcomingLeaves->isNotEmpty())
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold"> On Leave / Upcoming Leave</div>
                        <div class="p-3 div-content-area">
                            @foreach ($approvedLeaves as $leave)
                                <div class="gap-2 mb-4 d-flex align-items-start">
                                    <div class="">
                                        <span
                                            class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div class="request-title d-flex justify-content-between">
                                            <div class="fw-bold">{{ $leave->getRequesterName() }}</div>
                                            <small class="text-danger fw-semi-bold">
                                                {{ $leave->leaveType->title }}</small>
                                        </div>
                                        <div class="text-muted d-flex flex-column flex-lg-row justify-content-between">
                                            <small>Leave for {{ $leave->getLeaveDuration() }}
                                                {{ $leave->leaveType->getLeaveBasis() }}.</small>
                                            <small> {{ $leave->getStartDate() }} - {{ $leave->getEndDate() }}</small>
                                        </div>
                                        @if ($time = $leave->getFirstLeaveTime())
                                            <small>Time: {{ $time }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($upcomingLeaves as $leave)
                                <div class="gap-2 mb-4 d-flex align-items-start">
                                    <div class="">
                                        <span
                                            class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div class="request-title d-flex justify-content-between">
                                            <div class="fw-bold">{{ $leave->getRequesterName() }}</div>
                                            @if ($leave->getTable() == 'lieu_leave_requests')
                                                <small class="text-danger fw-semi-bold">
                                                    Lieu Leave</small>
                                            @else
                                                <small class="text-danger fw-semi-bold">
                                                    {{ $leave->leaveType->title }}</small>
                                            @endif
                                        </div>
                                        @if ($leave->getTable() == 'leave_requests')
                                            <div class="text-muted d-flex flex-column flex-lg-row justify-content-between">
                                                <small>Leave for {{ $leave->getLeaveDuration() }}
                                                    {{ $leave->leaveType->getLeaveBasis() }}.</small>
                                                <small> {{ $leave->getStartDate() }} - {{ $leave->getEndDate() }}</small>
                                            </div>
                                            @if ($time = $leave->getFirstLeaveTime())
                                                <small>Time: {{ $time }}</small>
                                            @endif
                                        @else
                                            <div class="text-muted d-flex flex-column flex-lg-row justify-content-between">
                                                <small>Leave for {{ $leave->getLeaveDuration() }} Day</small>
                                                <small> {{ $leave->getStartDate() }} - {{ $leave->getEndDate() }}</small>
                                            </div>
                                            <small>Time: Full Day</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endif

            @if ($approvedTravels->isNotEmpty() || $upcomingTravels->isNotEmpty())
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold"> On Travel / Upcoming Travel</div>
                        <div class="p-3 div-content-area">
                            @foreach ($approvedTravels as $approvedTravel)
                                @if ($approvedTravel->isConsultantTravel())
                                    @continue
                                @endif

                                <div class="mb-3 row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="text-white rounded bg-travel avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-check"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="request-title">
                                            <strong>{{ $approvedTravel->getTravellerName() }}</strong> is on travel for
                                            {{ $approvedTravel->getTotalDays() }} days.
                                        </div>
                                        <div class="text-muted">
                                            <small> {{ $approvedTravel->getDepartureDate() }} -
                                                {{ $approvedTravel->getReturnDate() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($upcomingTravels as $travel)
                                @if ($travel->isConsultantTravel())
                                    @continue
                                @endif
                                <div class="mb-3 row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="text-white rounded bg-travel avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-check"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="request-title">
                                            <strong>{{ $travel->getTravellerName() }}</strong> will be on travel
                                            for
                                            {{ $travel->getTotalDays() }} days.
                                        </div>
                                        <div class="text-muted">
                                            <small> {{ $travel->getDepartureDate() }} -
                                                {{ $travel->getReturnDate() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($approvedWorkFromHomes->isNotEmpty() || $upcomingWorkFromHomes->isNotEmpty())
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold"> On / Upcoming Work From Home</div>
                        <div class="p-3 div-content-area">
                            @foreach ($approvedWorkFromHomes as $workFromHome)
                                <div class="gap-2 mb-4 d-flex align-items-start">
                                    <div class="">
                                        <span
                                            class="text-white rounded bg-work-from-home avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-workspace"></i>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div class="request-title d-flex justify-content-between">
                                            <div class="fw-bold">{{ $workFromHome->getRequesterName() }}</div>
                                        </div>
                                        <div class="text-muted d-flex flex-column flex-lg-row justify-content-between">
                                            <small>WFH for {{ $workFromHome->getWorkFromHomeDuration() }}
                                                Day{{ $workFromHome->getWorkFromHomeDuration() > 1 ? 's' : '' }}</small>
                                            <small> {{ $workFromHome->getStartDate() }} -
                                                {{ $workFromHome->getEndDate() }}</small>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                            @foreach ($upcomingWorkFromHomes as $upcomingWorkFromHome)
                                <div class="gap-2 mb-4 d-flex align-items-start">
                                    <div class="">
                                        <span
                                            class="text-white rounded bg-work-from-home avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div class="request-title d-flex justify-content-between">
                                            <div class="fw-bold">{{ $upcomingWorkFromHome->getRequesterName() }}</div>
                                        </div>
                                        <div class="text-muted d-flex flex-column flex-lg-row justify-content-between">
                                            <small> {{ $upcomingWorkFromHome->getStartDate() }} -
                                                {{ $upcomingWorkFromHome->getEndDate() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endif

        </div>

        <x-calender />

        <div class="row">
            @if ($currentWeekWorkPlans->isNotEmpty())
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold">
                            Work Plan
                            <small class="float-end text-muted fw-normal">
                                {{ $currentWeekStart->format('M d') }} – {{ $currentWeekEnd->format('M d, Y') }}
                            </small>
                        </div>
                        <div class="p-3 div-content-area">
                            @foreach ($currentWeekWorkPlans as $detail)
                                <div class="gap-3 mb-4 d-flex align-items-start">
                                    <div>
                                        <span
                                            class="text-white rounded bg-primary avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-journal-check"></i>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div class="request-title d-flex justify-content-between align-items-start">
                                            <div class="fw-bold">
                                                {{ $detail->activity?->title ?? '—' }}
                                            </div>
                                            <div>
                                                <span
                                                    class="badge rounded-pill 
                                            {{ match ($detail->status ?? 'not_started') {
                                                'completed' => 'bg-success',
                                                'under_progress' => 'bg-warning text-dark',
                                                'not_started' => 'bg-secondary',
                                                'no_required' => 'bg-danger',
                                                default => 'bg-info',
                                            } }}">
                                                    {{ ucfirst(str_replace('_', ' ', $detail->status ?? 'not started')) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-muted">
                                            <div class="mb-1">
                                                {{ $detail->project?->title ?? 'No Project' }}
                                                @if ($detail->project?->short_name)
                                                    <small class="text-muted">({{ $detail->project->short_name }})</small>
                                                @endif
                                            </div>

                                            {{-- @if ($detail->plan_tasks)
                                                <div class="small">
                                                    <strong>Planned:</strong>
                                                    {{ Str::limit($detail->plan_tasks, 90) }}
                                                </div>
                                            @endif
    
                                            @if ($detail->members->isNotEmpty())
                                                <div class="mt-1 small">
                                                    <strong>Members:</strong>
                                                    {{ $detail->members->pluck('full_name')->implode(', ') }}
                                                </div>
                                            @endif
    
                                            @if (in_array($detail->status, ['completed', 'no_required']) && $detail->reason)
                                                <div class="mt-1 small text-muted fst-italic">
                                                    <strong>Reason:</strong> {{ $detail->reason }}
                                                </div>
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($canSeeTeamEvents)
                @if ($upcomingBirthdays->isNotEmpty())
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold">Upcoming Birthday</div>
                            <div class="p-3 div-content-area">
                                @foreach ($upcomingBirthdays as $emp)
                                    <div class="gap-3 mb-4 d-flex align-items-start">
                                        <div>
                                            <span
                                                class="text-white rounded bg-warning avatar d-flex align-items-center justify-content-center">
                                                <i class="bi bi-gift-fill"></i>
                                            </span>
                                        </div>
                                        <div class="w-100">
                                            <div class="request-title d-flex justify-content-between">
                                                <div class="fw-bold">{{ $emp->getFullName() }}</div>
                                                <small class="text-dark fw-semibold">{{ $emp->label }}</small>
                                            </div>
                                            <div class="text-muted">
                                                <div class="mb-1">
                                                    {{ $emp->upcoming_date->format('M d, Y') }}
                                                </div>
                                                {{-- @if ($emp->designation)
                                                    <small>{{ $emp->designation->title }}</small>
                                                @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if ($upcomingAnniversaries->isNotEmpty())
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold">Work Anniversary</div>
                            <div class="p-3 div-content-area">
                                @foreach ($upcomingAnniversaries as $emp)
                                    <div class="gap-3 mb-4 d-flex align-items-start">
                                        <div>
                                            <span
                                                class="text-white rounded bg-success avatar d-flex align-items-center justify-content-center">
                                                <i class="bi bi-briefcase-fill"></i>
                                            </span>
                                        </div>
                                        <div class="w-100">
                                            <div class="request-title d-flex justify-content-between">
                                                <div class="fw-bold">{{ $emp->getFullName() }}</div>
                                                <small class="text-dark fw-semibold">{{ $emp->label }}</small>
                                            </div>
                                            <div class="text-muted">
                                                <div class="mb-1">
                                                    {{ $emp->upcoming_date->format('M d, Y') }}
                                                </div>
                                                {{-- @if ($emp->designation)
                                                    <small>{{ $emp->designation->title }}</small>
                                                @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
@stop

@push('scripts')
    {{-- <canvas id="myChart"></canvas> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ]
                }],
                options: {

                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: 'rgb(255, 99, 132)'
                            }
                        }
                    }
                }
            },

        });

        myChart.canvas.style.height = '150px';
        myChart.canvas.style.width = '100%';
    </script>
@endpush
