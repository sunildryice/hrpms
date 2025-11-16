@php $authUser = auth()->user(); @endphp
<aside class="bg-white navbar-vertical-fixed border-end hidden-print">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <a href="{{ route('dashboard.index') }}"
               class="p-0 branding-section d-flex align-items-center justify-content-center bg-light">
                <img src="{{ asset('img/logo.svg') }}" class="l-logo" style="width: 120px;" alt="Logo">
                <img src="{{ asset('img/logo.svg') }}" class="s-logo d-none" alt="Logo">
            </a>
            <div class="navbar-vertical-content">

                <div id="navbarVerticalMenu" class="nav nav-vertical card-navbar-nav nav-tabs flex-column">
                    <span class="dropdown-header fw-bold">Human Resources</span>

                    @if ($authUser->can('manage-employee'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('employees.index') }}" role="button" id="employees-menu"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Employees">
                                <i class="bi bi-people nav-icon"></i>
                                <span class="nav-link-title">Employees</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('consultant.index') }}" role="button"
                               id="consultant-menu" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Consultants">
                                <i class="bi bi-person-lines-fill nav-icon"></i>
                                <span class="nav-link-title">Consultants</span>
                            </a>
                        </div>
                    @endif

                    @if (
                        $authUser->can('leave-request') ||
                            $authUser->can('review-leave-request') ||
                            $authUser->can('approve-leave-request') ||
                            $authUser->can('view-approved-leave-request'))

                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarleaveName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarleaveName" aria-expanded="false"
                               aria-controls="navbarleaveName" title="Leave Requests">
                                <i class="bi-door-open nav-icon"></i>
                                <span class="nav-link-title">Leave</span>
                            </a>

                            <div id="navbarleaveName" class="nav-collapse collapse" data-bs-parent="#navbarleave"
                                 hs-parent-area="#navbarleave" style="">
                                @if ($authUser->can('leave-request'))
                                    <a class="nav-link" id="leave-requests-menu"
                                       href="{{ route('leave.requests.index') }}">Leave Requests</a>
                                @endif
                                @if ($authUser->can('review-leave-request'))
                                    <a class="nav-link hs-rqst" id="review-leave-requests-menu"
                                       href="{{ route('review.leave.requests.index') }}">Review Leave
                                        Requests({!! $reviewLeaveCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-leave-request'))
                                    <a class="nav-link hs-rqst" id="approve-leave-requests-menu"
                                       href="{{ route('approve.leave.requests.index') }}">Approve Leave
                                        Requests({!! $approveLeaveCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-leave-request'))
                                    <a class="nav-link" id="approved-leave-requests-menu"
                                       href="{{ route('approved.leave.requests.index') }}">Approved Leave Requests</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('leave-encash') ||
                            $authUser->can('approve-leave-encash') ||
                            $authUser->can('review-leave-encash') ||
                            $authUser->can('view-approved-leave-encash') ||
                            $authUser->can('pay-leave-encash'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarEncashName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarEncashName" aria-expanded="false"
                               aria-controls="navbarEncashName" title="Leave Encashment">
                                <i class="bi bi-wallet nav-icon"></i>
                                <span class="nav-link-title">Leave Encashment</span>
                            </a>

                            <div id="navbarEncashName" class="nav-collapse collapse" data-bs-parent="#navbarleave"
                                 hs-parent-area="#navbarleave" style="">
                                @if ($authUser->can('leave-encash'))
                                    <a class="nav-link" id="leave-encash-menu"
                                       href="{{ route('leave.encash.index') }}">Leave
                                        Encash Requests</a>
                                @endif
                                @if ($authUser->can('review-leave-encash'))
                                    <a class="nav-link hs-rqst" id="review-leave-encash-menu"
                                       href="{{ route('review.leave.encash.index') }}">Review Leave Encash
                                        Requests({!! $reviewLeaveEncashCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-leave-encash'))
                                    <a class="nav-link hs-rqst" id="approve-leave-encash-menu"
                                       href="{{ route('approve.leave.encash.index') }}">Approve Leave Encash
                                        Requests({!! $approveLeaveEncashCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-leave-encash'))
                                    <a class="nav-link" id="approved-leave-encash-menu"
                                       href="{{ route('approved.leave.encash.index') }}">Approved Leave Encash
                                        Requests</a>
                                @endif
                                @if ($authUser->can('pay-leave-encash'))
                                    <a class="nav-link" id="paid-leave-encash-menu"
                                       href="{{ route('paid.leave.encash.index') }}">Paid Leave Encash Requests
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('employee-attendance') ||
                            $authUser->can('review-employee-attendance') ||
                            $authUser->can('approve-employee-attendance'))

                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarEmployeeAttendance" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarEmployeeAttendance"
                               aria-expanded="false" aria-controls="navbarEmployeeAttendance"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Attendance">
                                <i class="bi bi-fingerprint nav-icon"></i>
                                <span class="nav-link-title">Attendance</span> </a>

                            <div id="navbarEmployeeAttendance" class="collapse">

                                @if ($authUser->can('employee-attendance'))
                                    <a class="nav-link" id="attendance-index"
                                       href="{{ route('attendance.index') }}">Employees Attendance</a>
                                @endif

                                @if ($authUser->can('review-employee-attendance'))
                                    <a class="nav-link" id="attendance-review-index"
                                       href="{{ route('attendance.review.index') }}">Review Attendance
                                        ({{ $verifyAttendanceCount }})</a>
                                @endif

                                @if ($authUser->can('approve-employee-attendance'))
                                    <a class="nav-link" id="attendance-approve-index"
                                       href="{{ route('attendance.approve.index') }}">Approve Attendance
                                        ({{ $approveAttendanceCount }})</a>
                                @endif
                                @if ($authUser->can('view-approved-attendance'))
                                    <a class="nav-link" id="approved-attendance-menu"
                                       href="{{ route('attendance.approved.index') }}">Approved Attendance</a>
                                @endif
                                @if ($authUser->can('employee-attendance'))
                                    <a class="nav-link" id="pending-attendance-menu"
                                       href="{{ route('attendance.pending.index') }}">Pending Attendance</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($authUser->can('local-travel') ||
                        $authUser->can('approve-local-travel') ||
                        $authUser->can('pay-local-travel') ||

                        $authUser->can('travel-request') ||
                        $authUser->can('approve-travel-form') ||
                        $authUser->can('view-approved-travel-request') ||
                        $authUser->can('finance-review-travel-claim') ||
                        $authUser->can('approve-travel-claim') ||
                        $authUser->can('pay-travel-claim') ||

                        $authUser->can('travel-authorization') ||
                        $authUser->can('approve-travel-authorization') ||
                        $authUser->can('view-approved-travel-authorization') ||


                        $authUser->can('vehicle-request') ||
                        $authUser->can('approve-hire-vehicle-request') ||
                        $authUser->can('assign-office-vehicle') ||
                        $authUser->can('manage-hire-vehicle-procurement') ||

                        $authUser->can('manage-announcement')
                        )
                        <span class="dropdown-header fw-bold">Admin</span>
                    @endif

                    @if ($authUser->can('local-travel') || $authUser->can('approve-local-travel') || $authUser->can('pay-local-travel'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarLocalTravelMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarLocalTravelMenuName"
                               aria-expanded="false" aria-controls="navbarLocalTravelMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Local Travel">
                                <i class="bi bi-truck nav-icon"></i>
                                <span class="nav-link-title"> Local Travel</span>
                            </a>
                            <div id="navbarLocalTravelMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarTravelMenu" hs-parent-area="#navbarTravelMenu" style="">
                                @if ($authUser->can('local-travel'))
                                    <a class="nav-link" href="{{ route('local.travel.reimbursements.index') }}"
                                       id="local-travel-reimbursements-menu">
                                        Local Travel Reimbursements</a>
                                @endif
                                @if ($authUser->can('approve-local-travel'))
                                    <a class="nav-link"
                                       href="{{ route('approve.local.travel.reimbursements.index') }}"
                                       id="approve-local-travel-reimbursements-menu">Approve
                                        Local Travel ({{ $approveLocalTravelCount }})</a>
                                @endif
                                @if ($authUser->can('view-approved-local-travel'))
                                    <a class="nav-link"
                                       href="{{ route('approved.local.travel.reimbursements.index') }}"
                                       id="approved-local-travel-reimbursements-menu">Approved Local Travel</a>
                                @endif
                                @if ($authUser->can('pay-local-travel'))
                                    <a class="nav-link" href="{{ route('paid.local.travel.reimbursements.index') }}"
                                       id="paid-local-travel-reimbursements-menu">Paid Local Travel</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('travel-request') ||
                            $authUser->can('approve-travel-form') ||
                            $authUser->can('view-approved-travel-request') ||
                            $authUser->can('finance-review-travel-claim') ||
                            $authUser->can('approve-travel-claim') ||
                            $authUser->can('pay-travel-claim'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarTravelMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarTravelMenuName"
                               aria-expanded="false" aria-controls="navbarTravelMenuName" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Travel">
                                <i class="bi bi-stoplights nav-icon"></i>
                                <span class="nav-link-title">Travel</span>
                            </a>
                            <div id="navbarTravelMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarTravelMenu" hs-parent-area="#navbarTravelMenu" style="">
                                @if ($authUser->can('travel-request'))
                                    <a class="nav-link" href="{{ route('travel.requests.index') }}"
                                       id="travel-request-menu">Travel Requests</a>
                                    <a class="nav-link" href="{{ route('travel.reports.index') }}"
                                       id="travel-report-menu">Travel Reports</a>
                                    <a class="nav-link" href="{{ route('travel.claims.index') }}"
                                       id="travel-claims-menu">Travel Claim</a>
                                @endif
                                @if ($authUser->can('approve-travel-form'))
                                    <a class="nav-link" href="{{ route('approve.travel.requests.index') }}"
                                       id="approve-travel-request-menu">Approve
                                        Travel Requests ({{ $approveTravelCount }})</a>
                                    <a class="nav-link" href="{{ route('approve.travel.requests.cancel.index') }}"
                                       id="approve-travel-request-cancel-menu">Approve
                                        TR Cancellation ({{ $approveTravelCancelCount }})</a>
                                    <a class="nav-link" href="{{ route('approve.travel.reports.index') }}"
                                       id="approve-travel-report-menu">Approve
                                        Travel Reports ({{ $approveTravelReportCount }})</a>
                                @endif
                                @if ($authUser->can('travel-request-advance'))
                                    <a class="nav-link" href="{{ route('approve.travel.requests.advance.index') }}"
                                       id="approve-travel-advance-menu">Approve Travel Advance </a>
                                @endif
                                @if ($authUser->can('finance-review-travel-claim'))
                                    <a class="nav-link" href="{{ route('review.travel.claims.index') }}"
                                       id="review-travel-claims-menu">Review
                                        Travel Claims ({{ $reviewTravelClaimCount }})</a>
                                @endif
                                @if ($authUser->can('approve-travel-claim'))
                                    <a class="nav-link" href="{{ route('approve.travel.claims.index') }}"
                                       id="approve-travel-claims-menu">Approve
                                        Travel Claims ({{ $approveTravelClaimCount }})</a>
                                @endif
                                @if ($authUser->can('view-approved-travel-request'))
                                    <a class="nav-link" href="{{ route('approved.travel.requests.index') }}"
                                       id="approved-travel-request-menu">Approved
                                        Travel Requests </a>
                                    <a class="nav-link" href="{{ route('approved.travel.reports.index') }}"
                                       id="approved-travel-report-menu">Approved
                                        Travel Reports</a>
                                @endif
                                @if ($authUser->can('view-approved-travel-claim'))
                                    <a class="nav-link" href="{{ route('approved.travel.claims.index') }}"
                                       id="approved-travel-claims-menu">Approved Travel Claims</a>
                                @endif
                                @if ($authUser->can('pay-travel-claim'))
                                    <a class="nav-link" href="{{ route('paid.travel.claims.index') }}"
                                       id="paid-travel-claims-menu">Paid Travel Claims</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('vehicle-request') ||
                            $authUser->can('approve-hire-vehicle-request') ||
                            $authUser->can('assign-office-vehicle') ||
                            $authUser->can('manage-hire-vehicle-procurement'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarVehicleMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarVehicleMenuName"
                               aria-expanded="false" aria-controls="navbarVehicleMenuName" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Vehicle">
                                <i class="bi bi-truck-flatbed nav-icon"></i>
                                <span class="nav-link-title">Vehicle</span>
                            </a>
                            <div id="navbarVehicleMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarVehicleMenu" hs-parent-area="#navbarVehicleMenu"
                                 style="">
                                @if ($authUser->can('vehicle-request'))
                                    <a class="nav-link" href="{{ route('vehicle.requests.index') }}"
                                       id="vehicle-requests-menu">Vehicle Requests</a>
                                @endif
                                @if ($authUser->can('approve-hire-vehicle-request'))
                                    <a class="nav-link" href="{{ route('approve.vehicle.requests.index') }}"
                                       id="approve-vehicle-requests-menu">Approve
                                        Vehicle Requests ({{ $approveVehicleRequestCount }})</a>
                                @endif
                                @if ($authUser->can('assign-office-vehicle'))
                                    <a class="nav-link" href="{{ route('assign.vehicle.requests.index') }}"
                                       id="assign-vehicle-requests-menu">Assign
                                        Vehicle Requests ({{ $assignVehicleRequestCount }})</a>
                                @endif
                                @if ($authUser->can('view-approved-vehicle-request'))
                                    <a class="nav-link" href="{{ route('approved.vehicle.requests.index') }}"
                                       id="approved-vehicle-requests-menu">Approved Vehicle Requests</a>
                                @endif
                                @if ($authUser->can('view-approved-vehicle-request') || $authUser->can('manage-hire-vehicle-procurement'))
                                    <a class="nav-link" href="{{ route('closed.vehicle.requests.index') }}"
                                       id="closed-vehicle-requests-menu">Closed Vehicle Requests</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($authUser->can('manage-announcement'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('announcement.index') }}" role="button"
                               id="announcement-index" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Announcement">
                                <i class="bi bi-megaphone nav-icon"></i>
                                <span class="nav-link-title">Announcement</span>
                            </a>
                        </div>
                    @endif

                    @if (
                        $authUser->can('manage-inventory') || $authUser->can('manage-asset') ||

                        $authUser->can('asset-disposition') ||
                        $authUser->can('approve-asset-disposition') ||
                        $authUser->can('view-approved-asset-disposition') ||

                        $authUser->can('maintenance-request') ||
                        $authUser->can('review-maintenance-request') ||
                        $authUser->can('approve-maintenance-request') ||
                        $authUser->can('maintenance-request-action') ||

                        $authUser->can('good-request') ||
                        $authUser->can('review-good-request') ||
                        $authUser->can('approve-good-request') ||
                        $authUser->can('direct-dispatch-good-request') ||
                        $authUser->can('approve-direct-dispatch-good-request')
                        )
                        <span class="dropdown-header fw-bold">Logistics/Procurement</span>
                    @endif

                    @if ($authUser->can('manage-inventory') || $authUser->can('manage-asset'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarInventoriesMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarInventoriesMenuName"
                               aria-expanded="false" aria-controls="#navbarInventoriesMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Inventories">
                                <i class="bi bi-boxes me-1 nav-icon"></i>
                                <span class="nav-link-title">Inventories</span>
                            </a>
                            <div id="navbarInventoriesMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarInventoriesMenu" hs-parent-area="#navbarInventoriesMenu">
                                <a href="{{ route('inventories.index') }}" class="nav-link"
                                   id="inventories-menu">Inventories</a>
                                <a href="{{ route('inventories.office.use.index') }}" class="nav-link"
                                   id="inventories-consumable-menu">Office Use Items</a>
                                <a href="{{ route('inventories.distribution.index') }}" class="nav-link"
                                   id="inventories-distribution-menu">Distribution Items</a>
                                <a href="{{ route('assets.index') }}" class="nav-link" id="assets-menu">Assets</a>
                                <a href="{{ route('assets.store.index') }}" class="nav-link"
                                   id="assets-store-menu">Assets on Store</a>
                                <a href="{{ route('assets.assigned.index') }}" class="nav-link"
                                   id="assets-assigned-menu">Assets Assigned</a>
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('asset-disposition') ||
                            $authUser->can('approve-asset-disposition') ||
                            $authUser->can('view-approved-asset-disposition'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarAssetDisposeMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarAssetDisposeMenuName"
                               aria-expanded="false" aria-controls="navbarAssetDisposeMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Asset Disposition">
                                <i class="bi bi-trash nav-icon"></i>
                                <span class="nav-link-title">Asset Disposition</span>
                            </a>
                            <div id="navbarAssetDisposeMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarAssetDisposeMenu" hs-parent-area="#navbarAssetDisposeMenu"
                                 style="">
                                @if ($authUser->can('asset-disposition'))
                                    <a class="nav-link" id="asset-disposition-menu"
                                       href="{{ route('asset.disposition.index') }}">Asset Disposition </a>
                                @endif

                                @if ($authUser->can('approve-asset-disposition'))
                                    <a class="nav-link" id="approve-asset-disposition-menu"
                                       href="{{ route('approve.asset.disposition.index') }}">Approve Asset
                                        Disposition
                                        ({{ $approveDispositionRequestCount }})
                                    </a>
                                @endif

                                @if ($authUser->can('view-approved-asset-disposition'))
                                    <a class="nav-link" id="approved-asset-disposition-menu"
                                       href="{{ route('approved.asset.disposition.index') }}">Approved Asset
                                        Disposition
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('maintenance-request') ||
                            $authUser->can('review-maintenance-request') ||
                            $authUser->can('approve-maintenance-request') ||
                            $authUser->can('maintenance-request-action'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarMaintenanceMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarMaintenanceMenuName"
                               aria-expanded="false" aria-controls="#navbarMaintenanceMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Maintenance">
                                <i class="bi-wrench-adjustable me-1 nav-icon"></i>
                                <span class="nav-link-title">Maintenance</span>
                            </a>

                            <div id="navbarMaintenanceMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarMaintenanceMenu" hs-parent-area="#navbarMaintenanceMenu"
                                 style="">
                                @if ($authUser->can('maintenance-request'))
                                    <a class="nav-link" href="{{ route('maintenance.requests.index') }}"
                                       id="maintenance-requests-menu">Maintenance Request</a>
                                @endif
                                @if ($authUser->can('review-maintenance-request'))
                                    <a class="nav-link" href="{{ route('review.maintenance.requests.index') }}"
                                       id="review-maintenance-requests-menu">Review Maintenance Request
                                        ({{ $verifyMaintenanceCount }})</a>
                                @endif
                                @if ($authUser->can('approve-maintenance-request'))
                                    <a class="nav-link" href="{{ route('approve.maintenance.requests.index') }}"
                                       id="approve-maintenance-requests-menu">Approve Maintenance Request
                                        ({{ $approveMaintenanceCount }})</a>
                                @endif
                                @if ($authUser->can('approve-maintenance-request') || $authUser->can('view-approved-maintenance-request'))
                                    <a class="nav-link" href="{{ route('approved.maintenance.requests.index') }}"
                                       id="approved-maintenance-requests-menu">Approved Maintenance Request</a>
                                @endif
                            </div>
                        </div>
                    @endif


                    @if (
                        $authUser->can('good-request') ||
                            $authUser->can('review-good-request') ||
                            $authUser->can('approve-good-request') ||
                            $authUser->can('direct-dispatch-good-request') ||
                            $authUser->can('approve-direct-dispatch-good-request'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarGoodRequestMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarGoodRequestMenuName"
                               aria-expanded="false" aria-controls="#navbarGoodRequestMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Good Request">
                                <i class="bi bi-arrow-repeat me-1 nav-icon"></i>
                                <span class="nav-link-title">Good Request</span>
                            </a>
                            <div id="navbarGoodRequestMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarGoodRequestMenu" hs-parent-area="#navbarGoodRequestMenu"
                                 style="">
                                @if ($authUser->can('good-request'))
                                    <a class="nav-link" href="{{ route('good.requests.index') }}"
                                       id="good-requests-menu">Good Request</a>
                                    {{-- <a class="nav-link" href="{{ route('assets.index') }}"
                                       id="assets-menu">Assets</a> --}}
                                @endif
                                @if ($authUser->can('direct-dispatch-good-request'))
                                    <a href="{{ route('good.requests.direct.dispatch.index') }}" class="nav-link"
                                       id="direct-dispatch-menu">Direct Dispatch Requests</a>
                                @endif
                                @if ($authUser->can('approve-direct-dispatch-good-request'))
                                    <a href="{{ route('good.requests.direct.dispatch.approve.index') }}"
                                       class="nav-link" id="approve-direct-dispatch-menu">Approve Direct Dispatch</a>
                                @endif
                                {{-- @if ($authUser->can('approve-direct-dispatch-good-request')) --}}
                                <a href="{{ route('approve.good.requests.direct.assign.index') }}" class="nav-link"
                                   id="approve-direct-assign-menu">Approve Direct Assign</a>
                                {{-- @endif --}}
                                @if ($authUser->can('review-good-request'))
                                    <a class="nav-link" href="{{ route('review.good.requests.index') }}"
                                       id="review-good-requests-menu">Review Good Request
                                        ({{ $reviewGoodRequestCount }}
                                        )</a>
                                @endif
                                @if ($authUser->can('approve-good-request'))
                                    <a class="nav-link" href="{{ route('approve.good.requests.index') }}"
                                       id="approve-good-requests-menu">Approve Good Request
                                        ({{ $approveGoodRequestCount }})</a>
                                @endif
                                @if ($authUser->can('assign-good-request'))
                                    <a class="nav-link" href="{{ route('assign.good.requests.index') }}"
                                       id="assign-good-requests-menu">Assign Good Request
                                        ({{ $assignGoodRequestCount }})</a>
                                @endif
                                @if ($authUser->can('approve-asset-handover'))
                                    <a class="nav-link" href="{{ route('approve.asset.handovers.index') }}"
                                       id="approve-asset-handover-menu">Approve Asset Handover ({{ $approveAssetHandoverCount }})</a>
                                @endif
                                @if ($authUser->can('view-approved-good-request'))
                                    <a class="nav-link" href="{{ route('approved.good.requests.index') }}"
                                       id="approved-good-requests-menu">Approved Good Request</a>
                                @endif
                                <a class="nav-link" href="{{ route('receive.good.requests.direct.assign.index') }}"
                                   id="asset-receive-menu">Receive Item/Asset ({{ $receiveGoodRequestCount }})</a>
                            </div>
                        </div>
                    @endif


                    @if ($authUser->can('manage-privilege'))
                        <span class="dropdown-header fw-bold">Setup</span>
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarPrivilege" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPrivilege" aria-expanded="false"
                               aria-controls="navbarPrivilege" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Privilege">
                                <i class="bi bi-star-half nav-icon"></i>
                                <span class="nav-link-title">Privilege</span></a>

                            <div id="navbarPrivilege" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPrivilegeMenuName" hs-parent-area="#navbarPrivilegeMenuName"
                                 style="">
                                @if ($authUser->can('manage-role'))
                                    <a class="nav-link" id="roles-menu"
                                       href="{{ route('privilege.roles.index') }}">Roles</a>
                                @endif
                                @if ($authUser->isDeveloper())
                                    <a class="nav-link" id="permissions-menu"
                                       href="{{ route('privilege.permissions.index') }}">Permissions</a>
                                    <a class="nav-link" id="users-menu"
                                       href="{{ route('privilege.users.index') }}">Users</a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($authUser->can('manage-master'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarMasterMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarMasterMenuName"
                               aria-expanded="false" aria-controls="navbarMasterMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Master">
                                <i class="bi bi-bezier nav-icon"></i>
                                <span class="nav-link-title">Master</span>
                            </a>
                            <div id="navbarMasterMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarMasterMenu" hs-parent-area="#navbarMasterMenu">
                                @if ($authUser->can('manage-activity-area'))
                                    <a class="nav-link" href="{{ route('master.activity.areas.index') }}"
                                       id="activity-areas-menu">{{ __('label.activity-areas') }}</a>
                                @endif
                                @if ($authUser->can('manage-activity-code'))
                                    <a class="nav-link" href="{{ route('master.activity.codes.index') }}"
                                       id="activity-codes-menu">{{ __('label.activity-codes') }}</a>
                                @endif
                                @if ($authUser->can('manage-account-code'))
                                    <a class="nav-link" href="{{ route('master.account.codes.index') }}"
                                       id="account-codes-menu">{{ __('label.account-codes') }}</a>
                                @endif
                                @if ($authUser->can('manage-bill-category'))
                                    <a class="nav-link" href="{{ route('master.bill.categories.index') }}"
                                       id="bill-categories-menu">{{ __('label.bill-categories') }}</a>
                                @endif

                                @if ($authUser->can('manage-department'))
                                    <a class="nav-link" href="{{ route('master.departments.index') }}"
                                       id="departments-menu">Departments</a>
                                @endif
                                @if ($authUser->can('manage-designation'))
                                    <a class="nav-link" href="{{ route('master.designations.index') }}"
                                       id="designations-menu">Designations</a>
                                @endif
                                @if ($authUser->can('manage-district'))
                                    <a class="nav-link" href="{{ route('master.districts.index') }}"
                                       id="districts-menu">Districts </a>
                                @endif
                                @if ($authUser->can('manage-donor-code'))
                                    <a class="nav-link" href="{{ route('master.donor.codes.index') }}"
                                       id="donor-codes-menu">{{ __('label.donor-codes') }}</a>
                                @endif
                                @if ($authUser->can('manage-dsa-category'))
                                    <a class="nav-link" href="{{ route('master.dsa.categories.index') }}"
                                       id="dsa-categories-menu">{{ __('label.dsa-categories') }}</a>
                                @endif

                                @if ($authUser->can('manage-expense-category'))
                                    <a class="nav-link" href="{{ route('master.expense.categories.index') }}"
                                       id="expense-categories-menu">{{ __('label.expense-categories') }}</a>
                                @endif
                                @if ($authUser->can('manage-expense-type'))
                                    <a class="nav-link" href="{{ route('master.expense.types.index') }}"
                                       id="expense-types-menu">{{ __('label.expense-types') }}</a>
                                @endif
                                @if ($authUser->can('manage-exit-question'))
                                    <a class="nav-link" href="{{ route('master.exit.questions.index') }}"
                                       id="exit-questions-menu">{{ __('label.exit-questions') }}</a>
                                    <a class="nav-link" href="{{ route('master.exit.feedbacks.index') }}"
                                       id="exit-feedbacks-menu">{{ __('label.exit-feedbacks') }}</a>
                                    <a class="nav-link" href="{{ route('master.exit.ratings.index') }}"
                                       id="exit-ratings-menu">{{ __('label.exit-ratings') }}</a>
                                @endif
                                @if ($authUser->can('manage-execution-type'))
                                    <a class="nav-link" href="{{ route('master.execution.types.index') }}"
                                       id="execution-menu">{{ __('label.execution-type') }}</a>
                                @endif

                                @if ($authUser->can('manage-family-relation'))
                                    <a class="nav-link" href="{{ route('master.family.relations.index') }}"
                                       id="districts-menu">Family Relation </a>
                                @endif

                                @if ($authUser->can('manage-health-facility'))
                                    <a class="nav-link" href="{{ route('master.health.facilities.index') }}"
                                       id="health-facilities-menu">{{ __('label.health-facility') }}</a>
                                @endif

                                @if ($authUser->can('manage-item'))
                                    <a class="nav-link" href="{{ route('master.items.index') }}"
                                       id="items-menu">{{ __('label.items') }}</a>
                                @endif
                                @if ($authUser->can('manage-inventory-category'))
                                    <a class="nav-link" href="{{ route('master.inventory.categories.index') }}"
                                       id="inventory-categories-menu">{{ __('label.inventory-categories') }}</a>
                                @endif

                                @if ($authUser->can('manage-leave-type'))
                                    <a class="nav-link" href="{{ route('master.leave.types.index') }}"
                                       id="leave-types-menu">Leave Types </a>
                                @endif

                                @if ($authUser->can('manage-meeting-hall'))
                                    <a class="nav-link" href="{{ route('master.meeting.hall.index') }}"
                                       id="meeting-hall-menu">{{ __('label.meeting-hall') }}</a>
                                @endif

                                @if ($authUser->can('manage-office'))
                                    <a class="nav-link" href="{{ route('master.offices.index') }}"
                                       id="offices-menu">Offices</a>
                                @endif
                                @if ($authUser->can('manage-office-type'))
                                    <a class="nav-link" href="{{ route('master.office.types.index') }}"
                                       id="office-types-menu">Office Types</a>
                                @endif
                                @if ($authUser->can('manage-holiday'))
                                    <a class="nav-link" href="{{ route('master.holidays.index') }}"
                                       id="holidays-menu">Office Holidays </a>
                                @endif

                                @if ($authUser->can('manage-project-code'))
                                    <a class="nav-link" href="{{ route('master.project.codes.index') }}"
                                       id="project-codes-menu">{{ __('label.project-codes') }}</a>
                                @endif
                                {{-- @if ($authUser->can('manage-partner-organization')) --}}
                                <a class="nav-link" href="{{ route('master.partner.org.index') }}"
                                   id="partner-org-menu">{{ __('label.partner-org') }}</a>
                                {{-- @endif --}}
                                @if ($authUser->can('manage-probationary-indicator'))
                                    <a class="nav-link" href="{{ route('master.probationary.indicators.index') }}"
                                       id="probationary-indicators-menu">{{ __('label.probationary-indicators') }}</a>
                                @endif

                                @if ($authUser->can('manage-training-question'))
                                    <a class="nav-link" href="{{ route('master.training.questions.index') }}"
                                       id="training-questions-menu">{{ __('label.training-questions') }}</a>
                                @endif

                                @if ($authUser->can('manage-unit'))
                                    <a class="nav-link" href="{{ route('master.units.index') }}"
                                       id="units-menu">{{ __('label.units') }}</a>
                                @endif

                                @if ($authUser->can('manage-vehicle'))
                                    <a class="nav-link" href="{{ route('master.vehicles.index') }}"
                                       id="vehicles-menu">{{ __('label.vehicles') }}</a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($authUser->can('view-report'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarReportMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarReportMenuName"
                               aria-expanded="false" aria-controls="navbarReportMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Report">
                                <i class="bi bi-menu-button-wide-fill nav-icon"></i>
                                <span class="nav-link-title">Report</span>
                            </a>
                            <div id="navbarReportMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarReportMenu" hs-parent-area="#navbarReportMenu"
                                 style="">
                                @if ($authUser->can('stock-book-report'))
                                    <a class="nav-link" href="{{ route('report.asset.book.index') }}"
                                       id="asset-book-menu">Asset Book</a>
                                @endif
                                @if ($authUser->can('stock-book-report'))
                                    <a class="nav-link" href="{{ route('report.asset.disposition.index') }}"
                                       id="asset-dispose-report-menu">Asset Disposition</a>
                                @endif

                                @if ($authUser->can('employee-profile-report'))
                                    <a class="nav-link" href="{{ route('report.employee.profile.index') }}"
                                       id="employee-profile-report-menu">Employee Profile</a>
                                @endif

                                @if ($authUser->can('leave-request-report'))
                                    <a class="nav-link" href="{{ route('report.leave.summary.index') }}"
                                       id="leave-summary-report-menu">Leave Record Summary</a>
                                @endif
                                @if ($authUser->can('leave-request-report'))
                                    <a class="nav-link" href="{{ route('report.leave.requests.index') }}"
                                       id="leave-request-report-menu">Leave Requests</a>
                                @endif
                                @if ($authUser->can('maintenance-request-report'))
                                    <a class="nav-link" href="{{ route('report.maintenance.request.index') }}"
                                       id="maintenance-request-report-menu">Maintenance Request</a>
                                @endif

                                @if ($authUser->can('stock-book-report'))
                                    <a class="nav-link" href="{{ route('report.stock.book.office.use.index') }}"
                                       id="stock-book-office-use-menu">Stock Book (Office Use)</a>
                                @endif
                                @if ($authUser->can('stock-book-report'))
                                    <a class="nav-link" href="{{ route('report.stock.book.distribution.index') }}"
                                       id="stock-book-distribution-menu">Stock Book (Distribution)</a>
                                @endif
                                @if ($authUser->can('travel-request-report'))
                                    <a class="nav-link" href="{{ route('report.travel.request.index') }}"
                                       id="travel-request-report-menu">Travel Request</a>
                                @endif

                                @if ($authUser->can('vehicle-movement-report'))
                                    <a class="nav-link" href="{{ route('report.vehicle.movement.index') }}"
                                       id="vehicle-movement-report-menu">Vehicle Movement</a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="position-absolute t-ggle text-dark"><i class="bi-arrow-left"></i></a>
</aside>
