@php $authUser = auth()->user(); @endphp
<aside class="bg-white navbar-vertical-fixed border-end hidden-print">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <a href="{{ route('dashboard.index') }}"
               class="p-0 branding-section d-flex align-items-center justify-content-center bg-light">
{{--                <img src="{{ asset('img/logonp.png') }}" class="l-logo" alt="">--}}
{{--                <img src="{{ asset('img/slogo.png') }}" class="s-logo d-none" alt="">--}}
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

                    @if ($authUser->can('work-log') || $authUser->can('approve-work-log') || $authUser->can('view-approved-work-log'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarWorkLogMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarWorkLogMenuName" aria-expanded="false"
                               aria-controls="#navbarWorkLogMenuName" data-bs-toggle="tooltip"
                               data-bs-placement="right" data-bs-title="Work Log" title="Work Log">
                                <i class="bi bi-clipboard-minus nav-icon"></i>
                                <span class="nav-link-title">Work Log</span>
                            </a>

                            <div id="navbarWorkLogMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarWorkLogMenu" hs-parent-area="#navbarWorkLogMenu"
                                 style="">
                                @if ($authUser->can('work-log'))
                                    <a class="nav-link" href="{{ route('monthly.work.logs.index') }}"
                                       id="work-logs-menu">Monthly Worklog</a>
                                @endif
                                @if ($authUser->can('view-all-work-log'))
                                    <a class="nav-link" href="{{ route('all.monthly.work.logs.index') }}"
                                       id="all-work-logs-menu">All Monthly Worklog</a>
                                @endif
                                @if ($authUser->can('approve-work-log'))
                                    <a class="nav-link" href="{{ route('approve.work.logs.index') }}"
                                       id="approve-work-logs-menu">Approve Monthly Worklog
                                        ({{ $approveWorkPlanCount }}
                                        )</a>
                                @endif
                                @if ($authUser->can('view-approved-work-log'))
                                    <a class="nav-link" href="{{ route('approved.monthly.work.logs.index') }}"
                                       id="approved-work-logs-menu">Approved Work Logs</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('employee-requisition') ||
                            $authUser->can('review-employee-requisition') ||
                            $authUser->can('approve-employee-requisition') ||
                            $authUser->can('view-approved-employee-requisition'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarEmployeeRequestMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarEmployeeRequestMenuName"
                               aria-expanded="false" aria-controls="#navbarEmployeeRequestMenuName"
                               title="Employee Requisition" data-bs-toggle="tooltip" data-bs-placement="right"
                               data-bs-title="Employee Requisition">
                                <i class="bi bi-list-check nav-icon"></i>
                                <span class="nav-link-title">Employee Requisition</span>
                            </a>
                            <div id="navbarEmployeeRequestMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarEmployeeRequestMenu"
                                 hs-parent-area="#navbarEmployeeRequestMenu" style="">
                                @if ($authUser->can('employee-requisition'))
                                    <a class="nav-link" href="{{ route('employee.requests.index') }}"
                                       id="employee-requests-menu">Requisition</a>
                                @endif
                                @if ($authUser->can('review-employee-requisition'))
                                    <a class="nav-link" href="{{ route('review.employee.requests.index') }}"
                                       id="review-employee-requests-menu">Review Requisition
                                        ({{ $verifyEmployeeRequisitionCount }})</a>
                                @endif
                                @if ($authUser->can('approve-employee-requisition') || $authUser->can('approve-recommended-employee-requisition'))
                                    <a class="nav-link" href="{{ route('approve.employee.requests.index') }}"
                                       id="approve-employee-requests-menu">Approve Requisition
                                        ({{ $approveEmployeeRequisitionCount }})</a>
                                @endif
                                @if ($authUser->can('view-approved-employee-requisition'))
                                    <a class="nav-link" href="{{ route('approved.employee.requests.index') }}"
                                       id="approved-employee-requests-menu">Approved Requisition</a>
                                @endif
                            </div>
                        </div>
                    @endif


                    @if (
                        $authUser->can('training-request') ||
                            $authUser->can('hr-review-training-request') ||
                            $authUser->can('recommend-training-request') ||
                            $authUser->can('approve-training-request') ||
                            $authUser->can('training-report') ||
                            $authUser->can('approved-training-request') ||
                            $authUser->can('approve-training-report'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarTrainingMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarTrainingMenuName"
                               aria-expanded="false" aria-controls="#navbarTrainingMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Training">
                                <i class="bi-file-plus nav-icon"></i>
                                <span class="nav-link-title">Training</span>
                            </a>

                            <div id="navbarTrainingMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarTrainingMenu" hs-parent-area="#navbarTrainingMenu"
                                 style="">
                                @if ($authUser->can('training-request'))
                                    <a class="nav-link" href="{{ route('training.requests.index') }}"
                                       id="training-requests-menu">Training Request</a>
                                @endif
                                @if ($authUser->can('hr-review-training-request'))
                                    <a class="nav-link" href="{{ route('reponses.training.request.index') }}"
                                       id="response-training-requests-menu">Training Request Responses
                                        ({{ $reviewTrainingRequestCount }})</a>
                                @endif
                                @if ($authUser->can('recommend-training-request'))
                                    <a class="nav-link" href="{{ route('training.requests.recommend.index') }}"
                                       id="recommend-training-requests-menu">Recommend Training Request
                                        ({{ $recommendTrainingRequestCount }})</a>
                                @endif
                                @if ($authUser->can('approve-training-request'))
                                    <a class="nav-link" href="{{ route('approve.training.requests.index') }}"
                                       id="approve-training-requests-menu">Approve Training Request
                                        ({{ $approveTrainingRequestCount }})</a>
                                @endif
                                @if ($authUser->can('approved-training-request'))
                                    <a class="nav-link" href="{{ route('approved.training.requests.index') }}"
                                       id="approved-training-requests-menu">Approved Training Request</a>
                                @endif
                                @if ($authUser->can('training-report'))
                                    <a class="nav-link" href="{{ route('training.report.index') }}"
                                       id="training-report-menu">Training Reports</a>
                                @endif
                                @if ($authUser->can('approve-training-report'))
                                    <a class="nav-link" href="{{ route('approve.training.reports.index') }}"
                                       id="approve-training-report-menu">Approve Training Reports</a>
                                @endif
                                @if ($authUser->can('view-approved-training-report'))
                                    <a class="nav-link" href="{{ route('approved.training.reports.index') }}"
                                       id="approved-training-report-menu">Approved Training Reports</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('probation-review-request') ||
                            $authUser->can('probation-review-detail') ||
                            $authUser->isProbationExists() ||
                            $authUser->can('approve-probation-review-request'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarProbationReviewMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarProbationReviewMenuName"
                               aria-expanded="false" aria-controls="#navbarProbationReviewMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Probation Review">
                                <i class="bi bi-file-post-fill nav-icon"></i>
                                <span class="nav-link-title">Probation Review</span>
                            </a>
                            <div id="navbarProbationReviewMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarProbationReviewMenu"
                                 hs-parent-area="#navbarProbationReviewMenu" style="">
                                @if ($authUser->can('probation-review-request'))
                                    <a class="nav-link" href="{{ route('probation.review.requests.index') }}"
                                       id="probation-review-request-menu">Requests</a>
                                @endif
                                @if ($authUser->can('probation-review-detail'))
                                    <a class="nav-link" href="{{ route('probation.review.detail.requests.index') }}"
                                       id="probation-review-details-menu">Review Requests</a>
                                @endif
                                @if ($authUser->isProbationExists())
                                    <a class="nav-link"
                                       href="{{ route('employeeProbation.review.detail.requests.index') }}"
                                       id="employee-probation-review-menu">Employee Review</a>
                                @endif
                                @if ($authUser->can('approve-probation-review-request'))
                                    <a class="nav-link" href="{{ route('approve.probation.review.requests.index') }}"
                                       id="approve-probation-review-request-menu">Approve Requests</a>
                                @endif
                                @if ($authUser->can('view-approved-probation-review-request'))
                                    <a class="nav-link"
                                       href="{{ route('approved.probation.review.requests.index') }}"
                                       id="approved-probation-review-request-menu">Approved Requests</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('exit-staff-clearance') ||
                            $authUser->can('endorse-staff-clearance') ||
                            $authUser->can('approve-staff-clearance') ||
                            $authUser->can('hr-staff-clearance') ||
                            $authUser->can('logistic-staff-clearance') ||
                            $authUser->can('finance-staff-clearance') ||
                            $authUser->can('verify-staff-clearance') ||
                            $authUser->isHandoverNoteExists())
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarClearance" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarClearance"
                               aria-expanded="false" aria-controls="navbarClearance"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Attendance">
                                <i class="bi bi-clipboard-check nav-icon"></i>
                                <span class="nav-link-title">Exit Staff Clearance</span> </a>

                            <div id="navbarClearance" class="collapse">

                                {{-- @if ($authUser->can('exit-staff-clearance')) --}}
                                <a class="nav-link" id="staff-clearance-menu"
                                   href="{{ route('staff.clearance.index') }}">Staff Clearance</a>
                                {{-- @endif --}}
                                @if ($authUser->can('endorse-staff-clearance'))
                                    <a class="nav-link" id="staff-clearance-endorse-menu"
                                       href="{{ route('staff.clearance.endorse.index') }}">Endorse Staff Clearance</a>
                                @endif
                                @if ($authUser->can('approve-staff-clearance'))
                                    <a class="nav-link" id="staff-clearance-approve-menu"
                                       href="{{ route('staff.clearance.approve.index') }}">Approve Staff Clearance</a>
                                @endif
                                @if ($authUser->isHandoverNoteExists())
                                    @if (in_array($authUser->employee->exitHandOverNote->status_id, [1, 2]))
                                        <a class="nav-link" id="update-employees-exit-menu"
                                           href="{{ route('exit.employee.handover.note.edit') }}">Edit Employees
                                            Exit</a>
                                    @else
                                        <a class="nav-link" id="update-employees-exit-menu"
                                           href="{{ route('exit.employee.handover.note.show') }}">Show Employees
                                            Exit</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('manage-employee-exit') ||
                            $authUser->isHandoverNoteExists() ||
                            $authUser->can('approve-exit-handover-note') ||
                            $authUser->can('approve-exit-interview') ||
                            $authUser->can('create-exit-payable') ||
                            $authUser->can('approve-exit-payable') ||
                            $authUser->can('view-approved-exit-handover-note') ||
                            $authUser->can('view-approved-exit-interview') ||
                            $authUser->can('view-approved-exit-payable'))

                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarEmployeeExit" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarEmployeeExit" aria-expanded="false"
                               aria-controls="navbarEmployeeExit" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Employees Exit">
                                <i class="bi bi-person-dash nav-icon"></i>
                                <span class="nav-link-title">Employees Exit</span> </a>

                            <div id="navbarEmployeeExit" class="nav-collapse collapse"
                                 data-bs-parent="#navbarEmployeeExitMenuName"
                                 hs-parent-area="#navbarEmployeeExitMenuName" style="">
                                @if ($authUser->can('manage-employee-exit'))
                                    <a class="nav-link" id="employees-exit-menu"
                                       href="{{ route('employee.exits.index') }}">Employees Exit</a>
                                    <a class="nav-link" id="pending-employees-exit-menu"
                                       href="{{ route('employee.exit.pending.index') }}">Pending Employees Exit</a>
                                @endif
                                @if ($authUser->can('approve-exit-handover-note'))
                                    <a class="nav-link" id="approve-exit-handover-note"
                                       href="{{ route('approve.exit.handover.note.index') }}">Approve Exit Handover
                                        Note ({{$approveExitHandoverNoteCount}})</a>
                                @endif
                                @if ($authUser->can('view-approved-exit-handover-note'))
                                    <a class="nav-link" id="approved-exit-handover-note"
                                       href="{{ route('approved.exit.handover.note.index') }}">Approved Handover
                                        Note</a>
                                @endif
                                @if ($authUser->can('approve-exit-asset-handover'))
                                    <a class="nav-link" id="approve-exit-asset"
                                       href="{{ route('approve.exit.handover.asset.index') }}">Approve Asset
                                        Handover</a>
                                @endif
                                @if ($authUser->can('view-approved-exit-asset-handover'))
                                    <a class="nav-link" id="approved-asset-handover"
                                       href="{{ route('approved.exit.handover.asset.index') }}">Approved
                                        Asset Handover</a>
                                @endif
                                @if ($authUser->can('approve-exit-interview'))
                                    <a class="nav-link" id="approve-exit-interview"
                                       href="{{ route('approve.exit.interview.index') }}">Approve Exit Interview
                                        ({{$approveExitInterviewCount}})</a>
                                @endif
                                @if ($authUser->can('view-approved-exit-interview'))
                                    <a class="nav-link" id="approved-exit-interview"
                                       href="{{ route('approved.exit.interview.index') }}">Approved Exit
                                        Interview</a>
                                @endif
                                @if ($authUser->can('create-exit-payable'))
                                    <a class="nav-link" id="create-employee-exit-payable"
                                       href="{{ route('exit.payable.index') }}">Employee Exit Payable</a>
                                @endif
                                @if ($authUser->can('approve-exit-payable'))
                                    <a class="nav-link" id="update-employees-exit-payable"
                                       href="{{ route('exit.approve.payable.index') }}">Approve Employee Exit Payable
                                        ({{ $reviewEmployeeExitPayableCount + $approveEmployeeExitPayableCount }})</a>
                                @endif

                                @if ($authUser->can('approved-exit-payable'))
                                    <a class="nav-link" id="approved-employees-exit-payable"
                                       href="{{ route('exit.approved.payable.index') }}">Approved Payable</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('payroll') ||
                            $authUser->can('verify-payroll') ||
                            $authUser->can('approve-payroll') ||
                            $authUser->can('view-approved-payroll'))

                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarPayrollBatch" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPayrollBatch" aria-expanded="false"
                               aria-controls="navbarPayrollBatch" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Payroll">
                                <i class="bi bi-currency-dollar nav-icon"></i>
                                <span class="nav-link-title">Payroll</span> </a>

                            <div id="navbarPayrollBatch" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPayrollBatchMenuName"
                                 hs-parent-area="#navbarPayrollBatchMenuName" style="">

                                @if ($authUser->can('payroll'))
                                    <a class="nav-link" id="payroll-batches-menu"
                                       href="{{ route('payroll.batches.index') }}">Payroll Batches</a>
                                @endif

                                @if ($authUser->can('verify-payroll'))
                                    <a class="nav-link" id="review-payroll-batches-menu"
                                       href="{{ route('payroll.batches.review.index') }}">Verify Payroll</a>
                                @endif

                                @if ($authUser->can('approve-payroll'))
                                    <a class="nav-link" id="approve-payroll-batches-menu"
                                       href="{{ route('payroll.batches.approve.index') }}">Approve Payroll</a>
                                @endif

                                @if ($authUser->can('view-approved-payroll'))
                                    <a class="nav-link" id="approved-payroll-batches-menu"
                                       href="{{ route('approved.payroll.batches.index') }}">Approved Payroll</a>
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

                    @if (
                        $authUser->can('performance-review') ||
                            $authUser->can('review-performance-review') ||
                            $authUser->can('recommend-performance-review') ||
                            $authUser->can('approve-performance-review') ||
                            $authUser->performanceReviewExists())
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarPerformanceReview" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPerformanceReview"
                               aria-expanded="false" aria-controls="navbarPerformanceReview"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Performance Review">
                                <i class="bi bi-graph-up-arrow nav-icon"></i>
                                <span class="nav-link-title">Performance Review</span> </a>

                            <div id="navbarPerformanceReview" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPerformanceReviewMenuName"
                                 hs-parent-area="#navbarPerformanceReviewMenuName" style="">

                                @if ($authUser->performanceReviewExists())
                                    <a class="nav-link" id="performance-employee-index"
                                       href="{{ route('performance.employee.index') }}">My Performance Review</a>
                                @endif

                                @if ($authUser->isSupervisor())
                                    <a class="nav-link" id="performance-reviews-assistant"
                                       href="{!! route('performance.reviews.assistant.index') !!}">Performance
                                        Reviews</a>
                                @endif

                                @if ($authUser->can('manage-performance-review'))
                                    <a class="nav-link" id="performance-index"
                                       href="{{ route('performance.index') }}">Manage Performance Review</a>
                                @endif

                                @if ($authUser->can('review-performance-review'))
                                    <a class="nav-link" id="performance-review-index"
                                       href="{{ route('performance.review.index') }}">Review Performance
                                        ({{ $reviewPerCount }})</a>
                                @endif

                                @if ($authUser->can('recommend-performance-review'))
                                    <a class="nav-link" id="performance-recommend-index"
                                       href="{{ route('performance.recommend.index') }}">Recommend Performance
                                        ({{ $recommendPerCount }})</a>
                                @endif

                                @if ($authUser->can('approve-performance-review'))
                                    <a class="nav-link" id="performance-approve-index"
                                       href="{{ route('performance.approve.index') }}">Approve Performance
                                        ({{ $approvePerCount }})</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($authUser->can('local-travel') || $authUser->can('approve-local-travel') || $authUser->can('pay-local-travel') ||

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

                        $authUser->can('manage-contract') ||
                        $authUser->can('book-meeting-hall') ||


                        $authUser->can('manage-memo') || $authUser->can('approve-memo') || $authUser->can('view-approved-memo') ||


                        $authUser->can('construction') || $authUser->can('manage-construction') || $authUser->can('construction-report') ||

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
                        $authUser->can('travel-authorization') ||
                            $authUser->can('approve-travel-authorization') ||
                            $authUser->can('view-approved-travel-authorization'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarTAMenu" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarTAMenu"
                               aria-expanded="false" aria-controls="navbarTAMenu"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="TA">
                                <i class="bi bi-card-checklist nav-icon"></i>
                                <span class="nav-link-title">GoN. TA</span>

                                <div id="navbarTAMenu" class="collapse">

                                    <a class="nav-link" href="{{ route('ta.requests.index') }}"
                                       id="ta-request-menu">Travel Authorization Request</a>
                                    @if ($authUser->can('approve-travel-authorization'))
                                        <a class="nav-link" href="{{ route('approve.ta.requests.index') }}"
                                           id="approve-ta-request-menu">Approve
                                            Travel Authorization ({{ $approveTACount }})</a>
                                    @endif
                                    @if ($authUser->can('view-approved-travel-authorization'))
                                        <a class="nav-link" href="{{ route('approved.ta.requests.index') }}"
                                           id="approved-ta-request-menu">Approved
                                            Travel Authorization </a>
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

                    @if ($authUser->can('manage-contract'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('contracts.index') }}" role="button"
                               id="contracts-menu" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="{{ __('label.contracts') }}">
                                <i class="bi bi-people nav-icon"></i>
                                <span class="nav-link-title">{{ __('label.contracts') }}</span>
                            </a>
                        </div>
                    @endif

                    @if ($authUser->can('book-meeting-hall'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('meeting.hall.bookings.index') }}" role="button"
                               id="meeting-hall-requests-menu" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Meeting Hall">
                                <i class="bi bi-window-fullscreen nav-icon"></i>
                                <span class="nav-link-title">Meeting Hall</span>
                            </a>
                        </div>
                    @endif

                    @if ($authUser->can('manage-memo') || $authUser->can('approve-memo') || $authUser->can('view-approved-memo'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarMemoMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarMemoMenuName" aria-expanded="false"
                               aria-controls="#navbarMemoMenuName" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="{{ __('label.memo') }}">
                                <i class="bi bi-card-heading nav-icon"></i>
                                <span class="nav-link-title">{{ __('label.memo') }}</span>
                            </a>

                            <div id="navbarMemoMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarMemoMenuName" hs-parent-area="#navbarMemoMenuName"
                                 style="">
                                @if ($authUser->can('manage-memo'))
                                    <a class="nav-link" href="{{ route('memo.index') }}" id="memo-menu">Memo</a>
                                @endif
                                @if ($authUser->can('approve-memo'))
                                    <a class="nav-link" href="{{ route('approve.memo.index') }}"
                                       id="approve-memo-menu">Approve Memo
                                        ({{ $verifyMemoCount == 0 ? $approveMemoCount : $verifyMemoCount }})</a>
                                @endif
                                @if ($authUser->can('view-approved-memo'))
                                    <a class="nav-link" href="{{ route('approved.memo.index') }}"
                                       id="approved-memo-menu">Approved Memo</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($authUser->can('construction') || $authUser->can('manage-construction') || $authUser->can('construction-report'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarConstructionMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarConstructionMenuName"
                               aria-expanded="false" aria-controls="#navbarConstructionMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Construction">
                                <i class="bi bi-truck nav-icon"></i>
                                <span class="nav-link-title">Construction</span>
                            </a>
                            <div id="navbarConstructionMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarConstructionMenuName"
                                 hs-parent-area="#navbarConstructionMenuName" style="">
                                @if ($authUser->can('construction') || $authUser->can('manage-construction'))
                                    <a class="nav-link" href="{{ route('construction.index') }}"
                                       id="construction-index">Construction</a>
                                @endif
                                @if ($authUser->can('construction-report'))
                                    <a class="nav-link" href="{{ route('report.construction.index') }}"
                                       id="construction-report-menu">Report</a>
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
                        $authUser->can('manage-supplier') || $authUser->can('manage-pr-package') ||
                        $authUser->can('manage-lta') ||

                        $authUser->can('purchase-request') ||
                        $authUser->can('approve-purchase-request') ||
                        $authUser->can('budget-verify-purchase-request') ||
                        $authUser->can('finance-review-purchase-request') ||
                        $authUser->can('approve-recommended-purchase-request') ||
                        $authUser->can('view-approved-purchase-request') ||
                        $authUser->can('manage-procurement') ||

                        $authUser->can('purchase-order') ||
                        $authUser->can('review-purchase-order') ||
                        $authUser->can('approve-purchase-order') ||
                        $authUser->can('view-approved-purchase-order') ||
                        $authUser->can('grn') ||
                        $authUser->can('view-received-grn') ||
                        $authUser->can('manage-inventory') || $authUser->can('manage-asset') ||

                        $authUser->can('asset-disposition') ||
                        $authUser->can('approve-asset-disposition') ||
                        $authUser->can('view-approved-asset-disposition') ||

                        $authUser->can('maintenance-request') ||
                        $authUser->can('review-maintenance-request') ||
                        $authUser->can('approve-maintenance-request') ||
                        $authUser->can('maintenance-request-action') ||
                        $authUser->can('distribution-request') ||
                        $authUser->can('approve-distribution-request') ||
                        $authUser->can('approve-distribution-handover') ||
                        $authUser->can('view-approved-distribution-request') ||

                        $authUser->can('transportation-bill') ||
                        $authUser->can('approve-transportation-bill') ||
                        $authUser->can('view-approved-transportation-bill') ||

                        $authUser->can('good-request') ||
                        $authUser->can('review-good-request') ||
                        $authUser->can('approve-good-request') ||
                        $authUser->can('direct-dispatch-good-request') ||
                        $authUser->can('approve-direct-dispatch-good-request') ||

                        $authUser->can('advance-request') ||
                        $authUser->can('approve-advance-request') ||
                        $authUser->can('approve-advance-settlement') ||
                        $authUser->can('approve-recommended-advance-settlement') ||
                        $authUser->can('verify-advance-request') ||
                        $authUser->can('finance-review-advance-settlement') ||
                        $authUser->can('pay-advance-settlement') ||
                        $authUser->can('view-approved-advance-request') ||
                        $authUser->can('view-approved-advance-settlement') ||

                        $authUser->can('fund-request') ||
                        $authUser->can('approve-fund-request') ||
                        $authUser->can('view-approved-fund-request') ||

                        $authUser->can('add-payment-bill') ||
                        $authUser->can('payment-sheet') ||
                        $authUser->can('approve-payment-sheet-form') ||
                        $authUser->can('verify-payment-sheet') ||
                        $authUser->can('view-approved-payment-sheet') ||
                        $authUser->can('pay-payment-sheet') ||

                        $authUser->can('mfr') ||
                        $authUser->can('mfr-transaction') ||
                        $authUser->can('review-mfr-transaction	') ||
                        $authUser->can('approve-mfr-transaction	') ||
                        $authUser->can('approve-recommended-mfr-transaction	') ||

                        $authUser->can('event-completion') ||
                        $authUser->can('approve-event-form') ||
                        $authUser->can('view-approved-event-completion')
                        )
                        <span class="dropdown-header fw-bold">Logistics/Procurement</span>
                    @endif
                    @if ($authUser->can('manage-supplier'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('suppliers.index') }}" role="button"
                               id="supplier-menu" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="{{ __('label.suppliers') }}">
                                <i class="bi bi-broadcast nav-icon"></i>
                                <span class="nav-link-title">{{ __('label.suppliers') }}</span>
                            </a>
                        </div>
                    @endif
                    @if ($authUser->can('manage-pr-package'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('master.packages.index') }}" role="button"
                               id="packages-menu" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Purchase Request Package">
                                <i class="bi bi-box-seam nav-icon"></i>
                                <span class="nav-link-title">Purchase Request Package</span>
                            </a>
                        </div>
                    @endif
                    @if ($authUser->can('manage-lta'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('lta.index') }}" role="button" id="lta-menu"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="LTA">
                                <i class="bi bi-file-earmark-check nav-icon"></i>
                                <span class="nav-link-title">LTA</span>
                            </a>
                        </div>
                    @endif

                    @if (
                        $authUser->can('purchase-request') ||
                            $authUser->can('approve-purchase-request') ||
                            $authUser->can('budget-verify-purchase-request') ||
                            $authUser->can('finance-review-purchase-request') ||
                            $authUser->can('approve-recommended-purchase-request') ||
                            $authUser->can('view-approved-purchase-request') ||
                            $authUser->can('manage-procurement'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarPurchaseRequest" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPurchaseRequest"
                               aria-expanded="false" aria-controls="navbarPurchaseRequest" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Purchase Request">
                                <i class="bi bi-arrow-up-right-square nav-icon"></i>
                                <span class="nav-link-title">Purchase Request</span> </a>

                            <div id="navbarPurchaseRequest" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPurchaseRequestMenuName"
                                 hs-parent-area="#navbarPurchaseRequestMenuName" style="">
                                @if ($authUser->can('purchase-request'))
                                    <a class="nav-link" id="purchase-requests-menu"
                                       href="{{ route('purchase.requests.index') }}">Purchase Request</a>
                                @endif
                                @if ($authUser->can('budget-verify-purchase-request'))
                                    <a class="nav-link" id="verify-purchase-requests-menu"
                                       href="{{ route('verify.purchase.requests.index') }}">Verify PR
                                        ({!! $verifyPrCount !!})</a>
                                @endif
                                @if ($authUser->can('finance-review-purchase-request'))
                                    <a class="nav-link" id="review-purchase-requests-menu"
                                       href="{{ route('review.purchase.requests.index') }}">Review PR
                                        ({!! $reviewPrCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-purchase-request'))
                                    <a class="nav-link" id="approve-purchase-requests-menu"
                                       href="{{ route('approve.purchase.requests.index') }}">Approve PR
                                        ({!! $approvePrCount !!})</a>
                                @endif
                                @if ($authUser->can('review-recommended-purchase-request'))
                                    <a class="nav-link" id="review-recommended-purchase-requests-menu"
                                       href="{{ route('review.recommended.purchase.requests.index') }}">Review
                                        recommended PR
                                        ({!! $reviewRecommendedPrCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-recommended-purchase-request'))
                                    <a class="nav-link" id="approve-recommended-purchase-requests-menu"
                                       href="{{ route('approve.recommended.purchase.requests.index') }}">Approve
                                        recommended PR
                                        ({!! $approveRecommendedPrCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-purchase-request'))
                                    <a class="nav-link" id="approved-purchase-requests-menu"
                                       href="{{ route('approved.purchase.requests.index') }}">Approved PR
                                    </a>
                                @endif
                                @if ($authUser->can('view-approved-purchase-request') || $authUser->can('manage-procurement'))
                                    <a class="nav-link" id="closed-purchase-requests-menu"
                                       href="{{ route('closed.purchase.requests.index') }}">Closed PR
                                    </a>
                                @endif
                                @if ($authUser->id == 24)
                                    <a class="nav-link" id="special-purchase-requests-menu"
                                       href="{{ route('purchase.requests.special.index') }}">Special Update
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('purchase-order') ||
                            $authUser->can('review-purchase-order') ||
                            $authUser->can('approve-purchase-order') ||
                            $authUser->can('view-approved-purchase-order'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarPurchaseOrder" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPurchaseOrder" aria-expanded="false"
                               aria-controls="navbarPurchaseOrder" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Purchase Order">
                                <i class="bi bi-box-arrow-in-down-left nav-icon"></i>
                                <span class="nav-link-title">Purchase Order</span>
                            </a>

                            <div id="navbarPurchaseOrder" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPurchaseOrderMenuName"
                                 hs-parent-area="#navbarPurchaseOrderMenuName" style="">
                                @if ($authUser->can('purchase-order'))
                                    <a class="nav-link" id="purchase-orders-menu"
                                       href="{{ route('purchase.orders.index') }}">Purchase Orders</a>
                                @endif
                                @if ($authUser->can('review-purchase-order'))
                                    <a class="nav-link" id="review-purchase-orders-menu"
                                       href="{{ route('review.purchase.orders.index') }}">Review PO
                                        ({!! $reviewPoCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-purchase-order'))
                                    <a class="nav-link" id="approve-purchase-orders-menu"
                                       href="{{ route('approve.purchase.orders.index') }}">Approve PO
                                        ({!! $approvePoCount !!})</a>
                                    <a class="nav-link" id="cancel-purchase-orders-menu"
                                       href="{{ route('approve.purchase.orders.cancel.index') }}">Cancel PO
                                    </a>
                                @endif
                                @if ($authUser->can('view-approved-purchase-order'))
                                    <a class="nav-link" id="approved-purchase-orders-menu"
                                       href="{{ route('approved.purchase.orders.index') }}">Approved PO</a>
                                    <a class="nav-link" id="cancelled-purchase-orders-menu"
                                       href="{{ route('cancelled.purchase.orders.index') }}">Cancelled PO</a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($authUser->can('grn') || $authUser->can('view-received-grn'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarGrn" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarGrn" aria-expanded="false"
                               aria-controls="navbarGrn" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Good Receive Note">
                                <i class="bi bi-bookmark-dash-fill nav-icon"></i>
                                <span class="nav-link-title">Good Receive Note</span>
                            </a>

                            <div id="navbarGrn" class="nav-collapse collapse" data-bs-parent="#navbarGrnMenuName"
                                 hs-parent-area="#navbarGrnMenuName" style="">
                                @if ($authUser->can('grn'))
                                    <a class="nav-link" id="grns-menu" href="{{ route('grns.index') }}">GRN</a>
                                @endif
                                @if ($authUser->can('view-received-grn'))
                                    <a class="nav-link" id="approved-grns-menu"
                                       href="{{ route('approved.grns.index') }}">Received GRN</a>
                                @endif
                            </div>
                        </div>
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
                        $authUser->can('distribution-request') ||
                            $authUser->can('approve-distribution-request') ||
                            $authUser->can('approve-distribution-handover') ||
                            $authUser->can('view-approved-distribution-request'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarDistributionMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarDistributionMenuName"
                               aria-expanded="false" aria-controls="#navbarDistributionMenuName"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Distribution">
                                <i class="bi bi-arrows-fullscreen me-1 nav-icon"></i>
                                <span class="nav-link-title">Distribution</span>
                            </a>
                            <div id="navbarDistributionMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarDistributionMenu" hs-parent-area="#navbarDistributionMenu"
                                 style="">
                                @if ($authUser->can('distribution-request'))
                                    <a class="nav-link" href="{{ route('distribution.requests.index') }}"
                                       id="distribution-requests-menu">Distribution Request</a>
                                @endif
                                @if ($authUser->can('approve-distribution-request'))
                                    <a class="nav-link" href="{{ route('approve.distribution.requests.index') }}"
                                       id="approve-distribution-requests-menu">Approve Distribution Request
                                        ({!! $approveDistributionRequestCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-distribution-request'))
                                    <a class="nav-link" href="{{ route('approved.distribution.requests.index') }}"
                                       id="approved-distribution-requests-menu">Approved Distribution Request</a>
                                @endif
                                @if ($authUser->can('distribution-request'))
                                    <a class="nav-link" href="{{ route('distribution.requests.handovers.index') }}"
                                       id="distribution-handovers-menu">Distribution Handover</a>
                                @endif
                                @if ($authUser->can('approve-distribution-handover'))
                                    <a class="nav-link"
                                       href="{{ route('approve.distribution.requests.handovers.index') }}"
                                       id="approve-distribution-handovers-menu">Approve Distribution Handover
                                        ({!! $approveDistributionHandoverCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-distribution-request'))
                                    <a class="nav-link"
                                       href="{{ route('approved.distribution.requests.handovers.index') }}"
                                       id="approved-distribution-handovers-menu">Approved Distribution Handover</a>
                                @endif
                                <a class="nav-link"
                                   href="{{ route('receive.distribution.requests.handovers.index') }}"
                                   id="receive-distribution-handovers-menu">Receive Distribution Handover
                                    ({!! $receiveDistributionHandoverCount !!})</a>
                                <a class="nav-link"
                                   href="{{ route('received.distribution.requests.handovers.index') }}"
                                   id="received-distribution-handovers-menu">Received Distribution Handover</a>
                            </div>
                        </div>
                    @endif
                    @if (
                        $authUser->can('transportation-bill') ||
                            $authUser->can('approve-transportation-bill') ||
                            $authUser->can('view-approved-transportation-bill'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarTransportationBill" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarTransportationBill"
                               aria-expanded="false" aria-controls="navbarTransportationBill"
                               data-bs-toggle="tooltip" data-bs-placement="right" title="Transportation Bill">
                                <i class="bi bi-clipboard-pulse nav-icon"></i>
                                <span class="nav-link-title">Transportation Bill</span>
                            </a>
                            <div id="navbarTransportationBill" class="nav-collapse collapse"
                                 data-bs-parent="#navbarTransportationBillMenuName"
                                 hs-parent-area="#navbarTransportationBillMenuName" style="">
                                @if ($authUser->can('transportation-bill'))
                                    <a class="nav-link" id="transportation-bills-menu"
                                       href="{{ route('transportation.bills.index') }}">Way Bill </a>
                                @endif
                                @if ($authUser->can('approve-transportation-bill'))
                                    <a class="nav-link" id="approve-transportation-bills-menu"
                                       href="{{ route('approve.transportation.bills.index') }}">Receive Bill
                                        ({!! $approveWayBillCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-transportation-bill'))
                                    <a class="nav-link" id="approved-transportation-bills-menu"
                                       href="{{ route('approved.transportation.bills.index') }}">Received Bill
                                    </a>
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



                    @if (
                        $authUser->can('advance-request') ||
                            $authUser->can('approve-advance-request') ||
                            $authUser->can('approve-advance-settlement') ||
                            $authUser->can('approve-recommended-advance-settlement') ||
                            $authUser->can('verify-advance-request') ||
                            $authUser->can('finance-review-advance-settlement') ||
                            $authUser->can('pay-advance-settlement') ||
                            $authUser->can('view-approved-advance-request') ||
                            $authUser->can('view-approved-advance-settlement'))

                        <span class="dropdown-header fw-bold">Finance</span>

                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarAdvanceRequest" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarAdvanceRequest"
                               aria-expanded="false" aria-controls="navbarAdvanceRequest" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Advance Request">
                                <i class="bi bi-app-indicator nav-icon"></i>
                                <span class="nav-link-title">Advance Request</span></a>

                            <div id="navbarAdvanceRequest" class="nav-collapse collapse"
                                 data-bs-parent="#navbarAdvanceRequestMenuName"
                                 hs-parent-area="#navbarAdvanceRequestMenuName" style="">
                                @if ($authUser->can('advance-request'))
                                    <a class="nav-link" id="advance-requests-menu"
                                       href="{{ route('advance.requests.index') }}">Advance</a>
                                @endif
                                @if ($authUser->can('verify-advance-request'))
                                    <a class="nav-link" id="verify-advance-requests-menu"
                                       href="{{ route('verify.advance.requests.index') }}">Verify Advance
                                        ({!! $verifyAdvanceRequestCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-advance-request'))
                                    <a class="nav-link" id="approve-advance-requests-menu"
                                       href="{{ route('approve.advance.requests.index') }}">Approve Advance
                                        ({!! $approveAdvanceRequestCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-advance-request'))
                                    <a class="nav-link" id="approved-advance-requests-menu"
                                       href="{{ route('approved.advance.requests.index') }}">Approved Advance</a>
                                    <a class="nav-link" id="closed-advance-requests-menu"
                                       href="{{ route('closed.advance.requests.index') }}">Closed Advance</a>
                                @endif
                                @if ($authUser->can('pay-advance-settlement'))
                                    <a class="nav-link" id="paid-advance-request-menu"
                                       href="{{ route('paid.advance.index') }}">Paid Advance Request</a>
                                @endif
                                @if ($authUser->can('advance-request'))
                                    <a class="nav-link" id="settlement-advance-requests-menu"
                                       href="{{ route('advance.settlement.index') }}">Settlements </a>
                                @endif
                                @if ($authUser->can('finance-review-advance-settlement'))
                                    <a class="nav-link" href="{{ route('review.advance.settlements.index') }}"
                                       id="review-settlement-advance-requests-menu">Review Settlements
                                        ({!! $reviewSettlementCount !!})</a>
                                @endif
                                @if ($authUser->can('finance-review-advance-settlement'))
                                    <a class="nav-link" href="{{ route('verify.advance.settlements.index') }}"
                                       id="verify-settlement-advance-requests-menu">Verify Settlements
                                        ({!! $verifySettlementCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-advance-settlement-form'))
                                    <a class="nav-link" id="approve-settlement-advance-requests-menu"
                                       href="{{ route('approve.advance.settlements.index') }}">Approve
                                        Settlements ({!! $approveSettlementCount !!})</a>
                                @endif
                                @if ($authUser->can('view-approved-advance-settlement'))
                                    <a class="nav-link" id="approved-advance-settlement-menu"
                                       href="{{ route('approved.advance.settlements.index') }}">Approved
                                        Settlement</a>
                                @endif
                                @if ($authUser->can('pay-advance-settlement'))
                                    <a class="nav-link" id="paid-advance-settlement-menu"
                                       href="{{ route('paid.advance.settlement.index') }}">Paid Settlement</a>
                                @endif

                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('fund-request') ||
                            $authUser->can('approve-fund-request') ||
                            $authUser->can('view-approved-fund-request'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarFundRequest" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarFundRequest"
                               aria-expanded="false" aria-controls="navbarFundRequest" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Fund Request">
                                <i class="bi bi-currency-dollar nav-icon"></i>
                                <span class="nav-link-title">Fund Request</span>
                            </a>
                            <div id="navbarFundRequest" class="nav-collapse collapse"
                                 data-bs-parent="#navbarFundRequestMenuName"
                                 hs-parent-area="#navbarFundRequestMenuName" style="">
                                @if ($authUser->can('fund-request'))
                                    <a class="nav-link" id="fund-requests-menu"
                                       href="{{ route('fund.requests.index') }}">Fund </a>
                                @endif
                                @if ($authUser->can('check-fund-request'))
                                    <a class="nav-link" id="check-fund-requests-menu"
                                       href="{{ route('check.fund.requests.index') }}">Check Fund
                                        ({!! $checkFundRequestCount !!})</a>
                                @endif
                                @if ($authUser->can('certify-fund-request'))
                                    <a class="nav-link" id="certify-fund-requests-menu"
                                       href="{{ route('certify.fund.requests.index') }}">Certify Fund
                                        ({!! $certifyFundRequestCount !!})</a>
                                @endif

                                @if ($authUser->can('review-fund-request'))
                                    <a class="nav-link" id="review-fund-requests-menu"
                                       href="{{ route('review.fund.requests.index') }}">Review Fund
                                        ({!! $reviewFundRequestCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-fund-request'))
                                    <a class="nav-link" id="approve-fund-requests-menu"
                                       href="{{ route('approve.fund.requests.index') }}">Approve Fund
                                        ({!! $approveFundRequestCount !!})</a>

                                    <a class="nav-link" id="cancel-fund-requests-menu"
                                       href="{{ route('approve.fund.requests.cancel.index') }}">Cancel Fund
                                        Requests
                                    </a>
                                @endif
                                @if ($authUser->can('view-approved-fund-request'))
                                    <a class="nav-link" id="approved-fund-requests-menu"
                                       href="{{ route('approved.fund.requests.index') }}">Approved Fund
                                    </a>
                                    {{-- <a class="nav-link" id="cancelled-fund-requests-menu" --}}
                                    {{--     href="{{ route('cancelled.fund.requests.index') }}">Cancelled Fund</a> --}}
                                @endif
                                @if ($authUser->can('view-approved-fund-request'))
                                    <a class="nav-link"
                                       href="{{ route('report.consolidated.fund.request.index') }}"
                                       id="consolidated-fund-request-report-menu">Consolidated Fund Request</a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (
                        $authUser->can('add-payment-bill') ||
                            $authUser->can('payment-sheet') ||
                            $authUser->can('approve-payment-sheet-form') ||
                            $authUser->can('verify-payment-sheet') ||
                            $authUser->can('view-approved-payment-sheet') ||
                            $authUser->can('pay-payment-sheet'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarPaymentSheet" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPaymentSheet"
                               aria-expanded="false" aria-controls="navbarPaymentSheet" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Payment Sheets">
                                <i class="bi bi-bank nav-icon"></i>
                                <span class="nav-link-title">Payment Sheets</span>
                            </a>
                            <div id="navbarPaymentSheet" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPaymentSheetMenuName"
                                 hs-parent-area="#navbarPaymentSheetMenuName" style="">
                                @if ($authUser->can('add-payment-bill'))
                                    <a class="nav-link" id="payment-bills-menu"
                                       href="{{ route('payment.bills.index') }}">Payment Bills </a>
                                @endif
                                @if ($authUser->can('payment-sheet'))
                                    <a class="nav-link" id="payment-sheets-menu"
                                       href="{{ route('payment.sheets.index') }}">Payment Sheet </a>
                                @endif
                                @if ($authUser->can('verify-payment-sheet'))
                                    <a class="nav-link" id="verify-payment-sheets-menu"
                                       href="{{ route('verify.payment.sheets.index') }}">Verify Payment Sheet
                                        ({!! $verifyPaymentSheetCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-payment-sheet-form'))
                                    <a class="nav-link" id="approve-payment-sheets-menu"
                                       href="{{ route('approve.payment.sheets.index') }}">Approve Payment Sheet
                                        ({!! $approvePaymentSheetCount !!})</a>
                                @endif


                                @if ($authUser->can('review-recommended-payment-sheet'))
                                    <a class="nav-link" id="review-recommended-payment-sheets-menu"
                                       href="{{ route('review.recommended.payment.sheets.index') }}">Verify
                                        Recommended
                                        Payment Sheet
                                        ({!! $verifyRecommendedPaymentSheetCount !!})</a>
                                @endif
                                @if ($authUser->can('approve-recommended-payment-sheet'))
                                    <a class="nav-link" id="approve-recommended-payment-sheets-menu"
                                       href="{{ route('approve.recommended.payment.sheets.index') }}">Approve
                                        Recommended Payment Sheet
                                        ({!! $approveRecommendedPaymentSheetCount !!})</a>
                                @endif


                                @if ($authUser->can('view-approved-payment-sheet'))
                                    <a class="nav-link" id="approved-payment-sheets-menu"
                                       href="{{ route('approved.payment.sheets.index') }}">Approved Payment Sheet
                                    </a>
                                @endif

                                @if ($authUser->can('pay-payment-sheet'))
                                    <a class="nav-link" id="paid-payment-sheets-menu"
                                       href="{{ route('paid.payment.sheets.index') }}">Paid Payment Sheet
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('mfr') ||
                            $authUser->can('mfr-transaction') ||
                            $authUser->can('review-mfr-transaction	') ||
                            $authUser->can('approve-mfr-transaction	') ||
                            $authUser->can('approve-recommended-mfr-transaction	'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarMfr" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarMfr" aria-expanded="false"
                               aria-controls="navbarMfr" data-bs-toggle="tooltip" data-bs-placement="right"
                               title="Attendance">
                                <i class="bi bi-clipboard-data nav-icon"></i>
                                <span class="nav-link-title">PO Sub-Grants </span> </a>
                            <div id="navbarMfr" class="nav-collapse collapse" data-bs-parent="#navbarMfrMenuName"
                                 hs-parent-area="#navbarMfrMenuName" style="">

                                @if ($authUser->can('mfr'))
                                    <a class="nav-link" id="agreement-index"
                                       href="{{ route('mfr.agreement.index') }}">Fund Release/ MFR Approval </a>
                                @endif
                                @if ($authUser->can('review-mfr-transaction'))
                                    <a class="nav-link" id="review-transaction"
                                       href="{{ route('mfr.transaction.review.index') }}">Review
                                        Transactions({{ $reviewTransactionCount }})</a>
                                @endif

                                @if ($authUser->can('verify-mfr-transaction'))
                                    <a class="nav-link" id="verify-transaction"
                                       href="{{ route('mfr.transaction.verify.index') }}">Verify
                                        Transactions({{ $verifyTransactionCount }})</a>
                                @endif

                                @if ($authUser->can('recommend-mfr-transaction'))
                                    <a class="nav-link" id="recommend-transaction"
                                       href="{{ route('mfr.transaction.recommend.index') }}">Recommend
                                        Transactions({{ $recommendTransactionCount }})</a>
                                @endif

                                @if ($authUser->can('approve-mfr-transaction') || $authUser->can('approve-recommended-mfr-transaction'))
                                    <a class="nav-link" id="approve-transaction"
                                       href="{{ route('mfr.transaction.approve.index') }}">Approve Transactions
                                        ({{ $approveTransactionCount }})</a>
                                @endif

                                {{-- @if ($authUser->can('approve-recommended-mfr-transaction') || $authUser->can('approve-recommended-mfr-transaction')) --}}
                                {{--     <a class="nav-link" id="approve-recommended-transaction" --}}
                                {{--        href="{{ route('mfr.transaction.approve.recommended.index') }}">Approve --}}
                                {{--         Recommended Transactions({{ $recommendedTransactionCount }})</a> --}}
                                {{-- @endif --}}
                                {{-- --}}
                                {{-- @if ($authUser->can('view-approved-mfr')) --}}
                                <a class="nav-link" id="approved-transactions"
                                   href="{{ route('mfr.transaction.approved.index') }}">Approved Transactions</a>
                                {{-- @endif --}}
                            </div>
                        </div>
                    @endif

                    @if (
                        $authUser->can('event-completion') ||
                            $authUser->can('approve-event-form') ||
                            $authUser->can('view-approved-event-completion'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarEventMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarEventMenuName"
                               aria-expanded="false" aria-controls="navbarEventMenuName" data-bs-toggle="tooltip"
                               data-bs-placement="right" title="Events">
                                <i class="bi bi-calendar nav-icon"></i>
                                <span class="nav-link-title">Events</span>
                            </a>
                            <div id="navbarEventMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarEventMenu" hs-parent-area="#navbarEventMenu"
                                 style="">
                                @if ($authUser->can('event-completion'))
                                    <a class="nav-link" id="event-completion-menu"
                                       href="{{ route('event.completion.index') }}">Event Completion Report </a>
                                @endif

                                @if ($authUser->can('approve-event-completion'))
                                    <a class="nav-link" id="approve-event-completion-menu"
                                       href="{{ route('approve.event.completion.index') }}">Approve ECR
                                        ({{ $approveECRCount }})
                                    </a>
                                @endif

                                @if ($authUser->can('view-approved-event-completion'))
                                    <a class="nav-link" id="approved-event-completion-menu"
                                       href="{{ route('approved.event.completion.index') }}">Approved ECR
                                    </a>
                                @endif
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
                                @if ($authUser->can('advance-request-report'))
                                    <a class="nav-link" href="{{ route('report.advance.request.index') }}"
                                       id="advance-request-report-menu">Advance Request & Settlement</a>
                                @endif
                                @if ($authUser->can('stock-book-report'))
                                    <a class="nav-link" href="{{ route('report.asset.book.index') }}"
                                       id="asset-book-menu">Asset Book</a>
                                @endif
                                @if ($authUser->can('stock-book-report'))
                                    <a class="nav-link" href="{{ route('report.asset.disposition.index') }}"
                                       id="asset-dispose-report-menu">Asset Disposition</a>
                                @endif
                                @if ($authUser->can('employee-exit-interview-report'))
                                    <a class="nav-link" href="{{ route('report.employee.exit.interview.index') }}"
                                       id="employee-exit-interview-menu">Employee Exit Interview</a>
                                @endif
                                @if ($authUser->can('employee-exit-clearance-report'))
                                    <a class="nav-link" href="{{ route('report.employee.exit.clearance.index') }}"
                                       id="employee-exit-clearance-menu">Employee Exit Clearance</a>
                                @endif
                                @if ($authUser->can('employee-profile-report'))
                                    <a class="nav-link" href="{{ route('report.employee.profile.index') }}"
                                       id="employee-profile-report-menu">Employee Profile</a>
                                    {{-- <a class="nav-link" href="{{ route('report.employee.insurance.index') }}"
                                        id="employee-insurance-report-menu">Employee Family Detail</a> --}}
                                @endif
                                @if ($authUser->can('employee-requisition-report'))
                                    <a class="nav-link" href="{{ route('report.employee.requisition.index') }}"
                                       id="employee-requisition-report-menu">Employee Requisition</a>
                                @endif
                                @if ($authUser->can('fund-request-report'))
                                    <a class="nav-link" href="{{ route('report.monthly.fund.request.index') }}"
                                       id="monthly-fund-request-report-menu">Fund Request (Monthly)</a>
                                @endif
                                @if ($authUser->can('grn-report'))
                                    <a class="nav-link" href="{{ route('report.grn.index') }}"
                                       id="grn-report-menu">GRN</a>
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
                                @if ($authUser->can('payment-sheet-report'))
                                    <a class="nav-link" href="{{ route('report.payment.sheet.index') }}"
                                       id="payment-sheet-report-menu">Payment Sheet</a>
                                @endif
                                @if ($authUser->can('performance-review-report'))
                                    <a class="nav-link" href="{{ route('report.performance.review.index') }}"
                                       id="performance-review-menu">Performance Review</a>
                                @endif
                                @if ($authUser->can('purchase-request-report'))
                                    <a class="nav-link" href="{{ route('report.purchase.request.index') }}"
                                       id="purchase-request-report-menu">Purchase Request</a>
                                @endif
                                @if ($authUser->can('purchase-order-report'))
                                    <a class="nav-link" href="{{ route('report.purchase.order.index') }}"
                                       id="purchase-order-report-menu">Purchase Order</a>
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
                                @if ($authUser->can('training-request-report'))
                                    <a class="nav-link" href="{{ route('report.training.request.index') }}"
                                       id="training-request-report-menu">Training Request</a>
                                @endif
                                @if ($authUser->can('vehicle-movement-report'))
                                    <a class="nav-link" href="{{ route('report.vehicle.movement.index') }}"
                                       id="vehicle-movement-report-menu">Vehicle Movement</a>
                                @endif

                                {{-- @if ($authUser->can('fund-request-report'))
                                    <a class="nav-link" href="{{ route('report.fund.request.index') }}"
                                       id="fund-request-report-menu">Fund Request</a>
                                @endif --}}

                                {{-- @if ($authUser->can('fund-request-report'))
                                    <a class="nav-link" href="{{ route('report.consolidated.fund.request.index') }}"
                                       id="consolidated-fund-request-report-menu">Consolidated Fund Request</a>
                                @endif --}}

                                {{-- @if ($authUser->can('construction-report'))
                                    <a class="nav-link" href="{{ route('report.construction.index') }}"
                                       id="construction-report-menu">Construction</a>
                                @endif --}}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="position-absolute t-ggle text-dark"><i class="bi-arrow-left"></i></a>
</aside>
