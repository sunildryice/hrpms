@extends('layouts.container')

@section('title', 'Dashboard')

@section('page_js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#dashboard').addClass('active');
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
        </div>

    </div>
    <div class="welcome-page">
        <div class="row">

            @if ($pendingStaffClearances->isNotEmpty())
                @if (
                    $authUser->can('finance-staff-clearance') ||
                        $authUser->can('hr-staff-clearance') ||
                        $authUser->can('logistic-staff-clearance'))
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold"> Pending Staff Clearance </div>
                            <div class="p-3 div-content-area">
                                @foreach ($pendingStaffClearances as $clearance)
                                    <div class="mb-3 row align-items-center">
                                        <div class="col-auto">
                                            <span
                                                class="text-white rounded opacity-75 bg-primary avatar d-flex align-items-center justify-content-center">
                                                <i class="bi-clipboard-check"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="request-title">
                                                <a href="{{ route('staff.clearance.edit', $clearance->id) }}"
                                                    style="text-decoration: none; color:unset">
                                                    <strong>{{ $clearance->employee->getFullName() }}</strong>
                                                </a>
                                            </div>
                                            <div class="text-muted">
                                                <small>{{ $clearance->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
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

            @if ($travelRequests->isNotEmpty())
                @if ($authUser->can('approve-travel-request') || $authUser->can('approve-recommended-travel-request'))
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold"> Travel Request </div>
                            <div class="py-2 div-content-area">
                                @foreach ($travelRequests as $travelRequest)
                                    <div class="gap-2 mb-3 d-flex align-items-center">
                                        <div class="">
                                            <span
                                                class="text-white rounded opacity-75 bg-primary avatar d-flex align-items-center justify-content-center">
                                                <i class="bi-envelope-check"></i>
                                            </span>
                                        </div>
                                        <div class="">
                                            <div class="request-title">
                                                <a href="{{ route('approve.travel.requests.create', $travelRequest->id) }}"
                                                    style="text-decoration: none; color:unset">
                                                    <strong>{{ $travelRequest->getTravelRequestNumber() . ' (' . $travelRequest->getTravellerName() . ')' }}</strong>
                                                </a>
                                            </div>
                                            <div class="text-muted">
                                                <small> {{ $travelRequest->created_at->diffForHumans() }} </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

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

            @if ($localTravelRequests->isNotEmpty())
                @if ($authUser->can('approve-local-travel'))
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold"> Local Travel Request
                            </div>
                            <div class="py-2 div-content-area">
                                @foreach ($localTravelRequests as $localTravelRequest)
                                    <div class="mb-3 row align-items-center">
                                        <div class="col-auto">
                                            <span
                                                class="text-white rounded opacity-75 bg-primary avatar d-flex align-items-center justify-content-center">
                                                <i class="bi-envelope-check"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="request-title">
                                                <a href="{{ route('approve.local.travel.reimbursements.create', $localTravelRequest->id) }}"
                                                    style="text-decoration: none; color:unset">
                                                    <strong>{{ $localTravelRequest->getLocalTravelNumber() . ' (' . $localTravelRequest->getRequesterName() . ')' }}</strong>
                                                </a>
                                            </div>
                                            <div class="text-muted">
                                                <small> {{ $localTravelRequest->created_at->diffForHumans() }} </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if ($vehicleRequests->isNotEmpty())
                @if ($authUser->can('approve-hire-vehicle-request') || $authUser->can('assign-office-vehicle'))
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold"> Vehicle Request </div>
                            <div class="p-3 div-content-area">
                                @foreach ($vehicleRequests as $vehicleRequest)
                                    <div class="gap-2 mb-4 d-flex align-items-start">
                                        <div>
                                            <span
                                                class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                                <i class="bi bi-truck"></i>
                                            </span>
                                        </div>
                                        <div class="w-100">
                                            <div class="request-title">
                                                <a href="{{ route('approve.vehicle.requests.create', $vehicleRequest->id) }}"
                                                    style="text-decoration: none; color:unset">
                                                    <strong>{{ $vehicleRequest->getVehicleRequestNumber() . ' (' . $vehicleRequest->getRequesterName() . ')' }}</strong>
                                                </a>
                                            </div>
                                            <div class="text-muted">
                                                <small>{{ $vehicleRequest->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if ($leaveRequests->isNotEmpty())
                @if ($authUser->can('approve-leave-request'))
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold"> Leave Request </div>
                            <div class="p-3 div-content-area">
                                @foreach ($leaveRequests as $leaveRequest)
                                    <div class="mb-3 row align-items-center">
                                        <div class="col-auto">
                                            <span
                                                class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                                <i class="bi bi-person-check"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="request-title">
                                                @if ($leaveRequest->status_id == config('constant.SUBMITTED_STATUS'))
                                                    <a href="{{ route('review.leave.requests.create', $leaveRequest->id) }}"
                                                        style="text-decoration: none; color:unset">
                                                        <strong>{{ $leaveRequest->getLeaveNumber() . ' (' . $leaveRequest->getRequesterName() . ')' }}</strong>
                                                    </a>
                                                @else
                                                    <a href="{{ route('approve.leave.requests.create', $leaveRequest->id) }}"
                                                        style="text-decoration: none; color:unset">
                                                        <strong>{{ $leaveRequest->getLeaveNumber() . ' (' . $leaveRequest->getRequesterName() . ')' }}</strong>
                                                    </a>
                                                @endif
                                            </div>
                                            @if ($time = $leaveRequest->getFirstLeaveTime())
                                                Time: <span>{{ $time }}</span>
                                            @endif
                                            <div class="text-muted">
                                                <small>{{ $leaveRequest->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            @endif



            @if ($purchaseOrders->isNotEmpty())
                @if ($authUser->can('approve-purchase-order') || $authUser->can('review-purchase-order'))
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold"> Purchase Order </div>
                            <div class="p-3 div-content-area">
                                @foreach ($purchaseOrders as $purchaseOrder)
                                    <div class="mb-3 row align-items-center">
                                        <div class="col-auto">
                                            <span
                                                class="text-white rounded opacity-75 bg-danger avatar d-flex align-items-center justify-content-center">
                                                <i class="bi bi-truck"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="request-title">
                                                @if ($purchaseOrder->status_id == config('constant.SUBMITTED_STATUS'))
                                                    <a href="{{ route('review.purchase.orders.create', $purchaseOrder->id) }}"
                                                        style="text-decoration: none; color:unset">
                                                        <strong>{{ $purchaseOrder->getPurchaseOrderNumber() . ' (' . $purchaseOrder->getCreatedBy() . ')' }}</strong>
                                                    </a>
                                                @else
                                                    <a href="{{ route('approve.purchase.orders.create', $purchaseOrder->id) }}"
                                                        style="text-decoration: none; color:unset">
                                                        <strong>{{ $purchaseOrder->getPurchaseOrderNumber() . ' (' . $purchaseOrder->getCreatedBy() . ')' }}</strong>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="text-muted">
                                                <small>{{ $purchaseOrder->order_date->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if ($purchaseRequests->isNotEmpty())
                @if (
                    $authUser->can('approve-purchase-request') ||
                        $authUser->can('finance-review-purchase-request') ||
                        $authUser->can('approve-recommended-purchase-request'))
                    <div class="mb-3 col-lg-4">
                        <div class="mb-2 border-0 shadow-sm card">
                            <div class="card-header fw-bold"> Purchase Request </div>
                            <div class="p-3 div-content-area">
                                @foreach ($purchaseRequests as $purchaseRequest)
                                    <div class="gap-2 mb-4 d-flex align-items-start">
                                        <div>
                                            <span
                                                class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                                <i class="bi bi-truck"></i>
                                            </span>
                                        </div>
                                        <div class="w-100">
                                            <div class="request-title">
                                                @if ($purchaseRequest->status_id == config('constant.SUBMITTED_STATUS'))
                                                    <a href="{{ route('review.purchase.requests.create', $purchaseRequest->id) }}"
                                                        style="text-decoration: none; color:unset">
                                                        <strong>{{ $purchaseRequest->getPurchaseRequestNumber() . ' (' . $purchaseRequest->getRequesterName() . ')' }}</strong>
                                                    </a>
                                                @else
                                                    <a href="{{ route('approve.purchase.requests.create', $purchaseRequest->id) }}"
                                                        style="text-decoration: none; color:unset">
                                                        <strong>{{ $purchaseRequest->getPurchaseRequestNumber() . ' (' . $purchaseRequest->getRequesterName() . ')' }}</strong>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="text-muted">
                                                <small>{{ $purchaseRequest->request_date->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

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
                                            class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
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
                                            class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
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

            @if ($hallBookings->isNotEmpty())
                <div class="mb-3 col-lg-4">
                    <div class="mb-2 border-0 shadow-sm card">
                        <div class="card-header fw-bold"> Hall Booking</div>
                        <div class="p-3 div-content-area">
                            @foreach ($hallBookings as $hallBooking)
                                <div class="mb-3 row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="text-white rounded bg-danger avatar d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-check"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="request-title">
                                            <strong>{{ $hallBooking->getMeetingHall() }}</strong>
                                        </div>
                                        <div class="text-muted">
                                            <small>{{ $hallBooking->meeting_date->toFormattedDateString() }}</small>
                                            <small>({{ $hallBooking->getStartTime() . ' - ' . $hallBooking->getEndTime() }})</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
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
