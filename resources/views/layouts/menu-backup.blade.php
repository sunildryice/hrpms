@php $authUser = auth()->user(); @endphp
<aside class="navbar-vertical-fixed border-end bg-white hidden-print">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <a href="{{ route('dashboard.index') }}"
               class="branding-section d-flex align-items-center justify-content-center p-0 bg-light">
                <img src="{{ asset('img/logonp.png') }}" alt="">
            </a>
            <div class="navbar-vertical-content">
                <div id="navbarVerticalMenu" class="nav nav-vertical card-navbar-nav nav-tabs flex-column">
                <span class="dropdown-header mt-4">HR</span>
                    @if ($authUser->can('manage-employee'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarHrMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarHrMenuName" aria-expanded="false"
                               aria-controls="navbarHrMenuName">
                                <i class="bi-people nav-icon"></i>
                                <span class="nav-link-title">Human Resource</span>
                            </a>

                            <div id="navbarHrMenuName" class="nav-collapse collapse" data-bs-parent="#navbarHrMenu"
                                 hs-parent-area="#navbarHrMenu" style="">
                                <a class="nav-link" href="{{ route('employees.index') }}"
                                   id="employees-menu">Employees</a>
                            </div>
                        </div>
                    @endif

                    <div class="nav-item">
                        <a class="nav-link  dropdown-toggle" href="#navbarAdminMenuName" role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarAdminMenuName"
                           aria-controls="navbarAdminMenuName">
                            <i class="bi-house-door nav-icon"></i>
                            <span class="nav-link-title">Admin</span>
                        </a>

                        <div id="navbarAdminMenuName" class="nav-collapse collapse" data-bs-parent="#navbarAdminMenu"
                             hs-parent-area="#navbarAdminMenu" style="">
                            @if($authUser->can('manage-contract'))
                                <a class="nav-link" href="{{ route('contracts.index') }}"
                                   id="contracts-menu">Contracts</a>
                            @endif
                            @if ($authUser->can('manage-supplier'))
                                <a class="nav-link" href="{{ route('suppliers.index') }}"
                                   id="supplier-menu">{{ __('label.suppliers') }}</a>
                            @endif
                            <a class="nav-link" href="{{ route('vehicle.requests.index') }}"
                               id="vehicle-requests-menu">Vehicle Requests</a>
                            <a class="nav-link" href="{{ route('meeting.hall.bookings.index') }}"
                               id="meeting-hall-requests-menu">Meeting Hall Booking</a>
                        </div>
                    </div>
                    @if($authUser->can('leave-request') || $authUser->can('approve-leave-request'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarleaveName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarleaveName" aria-expanded="false"
                               aria-controls="navbarleaveName">
                                <i class="bi-door-open nav-icon"></i>
                                <span class="nav-link-title">Leave</span>
                            </a>

                            <div id="navbarleaveName" class="nav-collapse collapse" data-bs-parent="#navbarleave"
                                 hs-parent-area="#navbarleave" style="">
                                @if($authUser->can('leave-request'))
                                    <a class="nav-link" id="leave-requests-menu"
                                       href="{{ route('leave.requests.index') }}">Leave Requests</a>
                                @endif
                                @if($authUser->can('approve-leave-request'))
                                    <a class="nav-link hs-rqst" id="approve-leave-requests-menu"
                                       href="{{ route('approve.leave.requests.index') }}">Approve Leave Requests</a>
                                @endif
                            </div>
                        </div>
                    @endif

<span class="dropdown-header mt-4">HR</span>
                    <div class="nav-item">
                        <a class="nav-link  dropdown-toggle" href="#navbarTravelMenuName" role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarTravelMenuName" aria-expanded="false"
                           aria-controls="navbarTravelMenuName">
                            <i class="bi bi-stoplights nav-icon"></i>
                            <span class="nav-link-title">Travel</span>
                        </a>

                        <div id="navbarTravelMenuName" class="nav-collapse collapse"
                             data-bs-parent="#navbarTravelMenu"
                             hs-parent-area="#navbarTravelMenu" style="">
                            <a class="nav-link" href="{{ route('travel.requests.index') }}"
                               id="travel-request-menu">Travel Requests</a>
                            <a class="nav-link" href="{{ route('approve.travel.requests.index') }}"
                               id="approve-travel-request-menu">Approve
                                Travel Requests</a>
                            <a class="nav-link" href="{{ route('travel.reports.index') }}"
                               id="travel-report-menu">Travel Reports</a>
                            <a class="nav-link" href="{{ route('approve.travel.reports.index') }}"
                               id="approve-travel-report-menu">Approve
                                Travel Reports</a>
                            <a class="nav-link" href="{{ route('local.travel.reimbursements.index') }}"
                               id="local-travel-reimbursements-menu">
                                Local Travel</a>
                            <a class="nav-link" href="{{ route('approve.local.travel.reimbursements.index') }}"
                               id="approve-local-travel-reimbursements-menu">Approve
                                Local Travel</a>
                        </div>
                    </div>

                    <div class="nav-item ">
                        <a class="nav-link  dropdown-toggle" href="#navbarWorkLogMenuName" role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarWorkLogMenuName" aria-expanded="false"
                           aria-controls="#navbarWorkLogMenuName">
                            <i class="bi bi-list-check nav-icon"></i>
                            <span class="nav-link-title">Work Log</span>
                        </a>

                        <div id="navbarWorkLogMenuName" class="nav-collapse collapse"
                             data-bs-parent="#navbarWorkLogMenu" hs-parent-area="#navbarWorkLogMenu" style="">
                            <a class="nav-link" href="{{ route('monthly.work.logs.index') }}"
                               id="work-logs-menu">Monthwise Worklog</a>
                            <a class="nav-link" href="{{ route('approve.work.logs.index') }}"
                               id="approve-work-logs-menu">Approve Monthwise Worklog</a>
                        </div>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link  dropdown-toggle" href="#navbarProbationReviewMenuName" role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarProbationReviewMenuName"
                           aria-expanded="false" aria-controls="#navbarProbationReviewMenuName">
                            <i class="bi bi-file-post-fill nav-icon"></i>
                            <span class="nav-link-title">Probation Review</span>
                        </a>

                        <div id="navbarProbationReviewMenuName" class="nav-collapse collapse"
                             data-bs-parent="#navbarProbationReviewMenu" hs-parent-area="#navbarProbationReviewMenu"
                             style="">
                            <a class="nav-link" href="{{ route('probation.review.requests.index') }}"
                               id="probation-review-request-menu">Probation Review Request</a>
                            <a class="nav-link" href="{{ route('probation.review.detail.requests.index') }}"
                               id="probation-review-details-menu">Probation Review Details</a>
                            <a class="nav-link"
                               href="{{ route('employeeProbation.review.detail.requests.index') }}"
                               id="employee-probation-review-menu">Employee Probation Review</a>
                            <a class="nav-link" href="{{ route('approve.probation.review.requests.index') }}"
                               id="approve-probation-review-request-menu">Approve Probation Review Request</a>
                        </div>
                    </div>

                    <div class="nav-item ">
                        <a class="nav-link  dropdown-toggle" href="#navbarMaintenanceMenuName" role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarMaintenanceMenuName"
                           aria-expanded="false" aria-controls="#navbarMaintenanceMenuName">
                            <i class="bi-wrench-adjustable me-1 nav-icon"></i>
                            <span class="nav-link-title">Maintenance</span>
                        </a>

                        <div id="navbarMaintenanceMenuName" class="nav-collapse collapse"
                             data-bs-parent="#navbarMaintenanceMenu" hs-parent-area="#navbarMaintenanceMenu"
                             style="">
                            <a class="nav-link" href="{{ route('maintenance.requests.index') }}"
                               id="maintenance-requests-menu">Maintenance Request</a>
                            <a class="nav-link" href="{{ route('approve.maintenance.requests.index') }}"
                               id="approve-maintenance-requests-menu">Approve Maintenance Request</a>
                        </div>
                    </div>

                    @if($authUser->can('training-request'))
                        <div class="nav-item ">
                            <a class="nav-link  dropdown-toggle" href="#navbarTrainingMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarTrainingMenuName" aria-expanded="false"
                               aria-controls="#navbarTrainingMenuName">
                                <i class="bi-file-plus nav-icon"></i>
                                <span class="nav-link-title">Training</span>
                            </a>

                        <div id="navbarTrainingMenuName" class="nav-collapse collapse"
                             data-bs-parent="#navbarTrainingMenu" hs-parent-area="#navbarTrainingMenu" style="">
                            <a class="nav-link" href="{{ route('training.requests.index') }}"
                                id="training-requests-menu">Training Request</a>
                            <a class="nav-link" href="{{ route('reponses.training.request.index') }}"
                                id="response-training-requests-menu">Training Request Responses</a>
                            <a class="nav-link" href="{{ route('training.requests.recommend.index') }}"
                                id="recommend-training-requests-menu">Recommend Training Request</a>
                            <a class="nav-link" href="{{ route('approve.training.requests.index') }}"
                                id="approve-training-requests-menu">Approve Training Request</a>
                            <a class="nav-link" href="{{ route('training.report.index') }}"
                                id="training-report-menu">Training Reports</a>
                            <a class="nav-link" href="{{ route('approve.training.reports.index') }}"
                                id="approve-training-report-menu">Approve
                                Training Reports</a>
                        </div>
                    @endif

                    @if ($authUser->can('purchase-request'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarPurchaseRequest" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPurchaseRequest"
                               aria-expanded="false" aria-controls="navbarPurchaseRequest">
                                <i class="bi bi-book-fill nav-icon"></i>
                                <span class="nav-link-title">Purchase Request</span> </a>

                            <div id="navbarPurchaseRequest" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPurchaseRequestMenuName"
                                 hs-parent-area="#navbarPurchaseRequestMenuName" style="">
                                <a class="nav-link" id="purchase-requests-menu"
                                   href="{{ route('purchase.requests.index') }}">Purchase </a>
                                @if ($authUser->can('approve-purchase-request'))
                                    <a class="nav-link" id="approve-purchase-requests-menu"
                                       href="{{ route('approve.purchase.requests.index') }}">Approve Purchase </a>
                                @endif
                                @if($authUser->can('view-approved-purchase-request'))
                                    <a class="nav-link" id="approved-purchase-requests-menu"
                                       href="{{ route('approved.purchase.requests.index') }}">Approved Purchase
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($authUser->can('purchase-order') || $authUser->can('approve-purchase-order') || $authUser->can('view-approved-purchase-order'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarPurchaseOrder" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPurchaseOrder" aria-expanded="false"
                               aria-controls="navbarPurchaseOrder">
                                <i class="bi bi-envelope-check-fill nav-icon"></i>
                                <span class="nav-link-title">Purchase Order</span>
                            </a>

                            <div id="navbarPurchaseOrder" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPurchaseOrderMenuName"
                                 hs-parent-area="#navbarPurchaseOrderMenuName"
                                 style="">
                                @if($authUser->can('purchase-order'))
                                    <a class="nav-link" id="purchase-orders-menu"
                                       href="{{ route('purchase.orders.index') }}">Purchase Orders</a>
                                @endif
                                @if($authUser->can('approve-purchase-order'))
                                    <a class="nav-link" id="approve-purchase-orders-menu"
                                       href="{{ route('approve.purchase.orders.index') }}">Approve Purchase</a>
                                @endif
                                @if($authUser->can('view-approved-purchase-order'))
                                    <a class="nav-link" id="approved-purchase-orders-menu"
                                       href="{{ route('approved.purchase.orders.index') }}">Approved Purchase</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($authUser->can('grn') || $authUser->can('view-received-grn'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarGrn" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarGrn" aria-expanded="false"
                               aria-controls="navbarGrn">
                                <i class="bi bi-bookmark-dash-fill nav-icon"></i>
                                <span class="nav-link-title">Good Receive Note</span>
                            </a>

                            <div id="navbarGrn" class="nav-collapse collapse" data-bs-parent="#navbarGrnMenuName"
                                 hs-parent-area="#navbarGrnMenuName" style="">
                                @if ($authUser->can('grn'))
                                    <a class="nav-link" id="grns-menu" href="{{ route('grns.index') }}">GRN</a>
                                @endif
                                @if($authUser->can('view-received-grn'))
                                    <a class="nav-link" id="approved-grns-menu"
                                       href="{{ route('approved.grns.index') }}">Received GRN</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- advance request -->
                    <div class="nav-item">
                        <a class="nav-link  dropdown-toggle" href="#navbarAdvanceRequest" role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarAdvanceRequest" aria-expanded="false"
                           aria-controls="navbarAdvanceRequest">
                            <i class="bi bi-app-indicator nav-icon"></i>
                            <span class="nav-link-title">Advance Request</span></a>

                        <div id="navbarAdvanceRequest" class="nav-collapse collapse"
                             data-bs-parent="#navbarAdvanceRequestMenuName"
                             hs-parent-area="#navbarAdvanceRequestMenuName" style="">
                            <a class="nav-link" id="advance-requests-menu"
                               href="{{ route('advance.requests.index') }}">Advance</a>
                            <a class="nav-link" id="approve-advance-requests-menu"
                               href="{{ route('approve.advance.requests.index') }}">Approve Advance</a>
                            <a class="nav-link" id="approved-advance-requests-menu"
                               href="{{ route('approved.advance.requests.index') }}">Approved Advance</a>
                            <a class="nav-link" id="settlement-advance-requests-menu"
                               href="{{ route('advance.settlement.index') }}">Settlement </a>

                            <a class="nav-link" id="approve-settlement-advance-requests-menu"
                               href="{{ route('approve.advance.settlement.index') }}">Approve Settlement</a>
                            <a class="nav-link" id="approved-advance-settlement-menu"
                               href="{{ route('approved.advance.settlement.index') }}">Approved Settlement</a>

                        </div>
                    </div>

                    @if($authUser->can('fund-request') || $authUser->can('approve-fund-request') || $authUser->can('view-approved-fund-request'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarFundRequest" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarFundRequest"
                               aria-expanded="false" aria-controls="navbarFundRequest">
                                <i class="bi bi-book-fill nav-icon"></i>
                                <span class="nav-link-title">Fund Request</span>
                            </a>
                            <div id="navbarFundRequest" class="nav-collapse collapse"
                                 data-bs-parent="#navbarFundRequestMenuName"
                                 hs-parent-area="#navbarFundRequestMenuName" style="">
                                @if ($authUser->can('fund-request'))
                                    <a class="nav-link" id="fund-requests-menu"
                                       href="{{ route('fund.requests.index') }}">Fund </a>
                                @endif
                                @if ($authUser->can('approve-fund-request'))
                                    <a class="nav-link" id="approve-fund-requests-menu"
                                       href="{{ route('approve.fund.requests.index') }}">Approve Fund </a>
                                @endif
                                @if($authUser->can('view-approved-fund-request'))
                                    <a class="nav-link" id="approved-fund-requests-menu"
                                       href="{{ route('approved.fund.requests.index') }}">Approved Fund
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($authUser->can('distribution-request') || $authUser->can('approve-distribution-request') || $authUser->can('view-approved-distribution-request'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarDistributionRequest" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarDistributionRequest"
                               aria-expanded="false" aria-controls="navbarDistributionRequest">
                                <i class="bi bi-distribute-vertical nav-icon"></i>
                                <span class="nav-link-title">Distribution Request</span>
                            </a>
                            <div id="navbarDistributionRequest" class="nav-collapse collapse"
                                 data-bs-parent="#navbarDistributionRequestMenuName"
                                 hs-parent-area="#navbarDistributionRequestMenuName" style="">
                                @if ($authUser->can('distribution-request'))
                                    <a class="nav-link" id="distribution-requests-menu"
                                       href="{{ route('distribution.requests.index') }}">Distribution </a>
                                @endif
                                @if ($authUser->can('approve-distribution-request'))
                                    <a class="nav-link" id="approve-distribution-requests-menu"
                                       href="{{ route('approve.distribution.requests.index') }}">Approve
                                        Distribution </a>
                                @endif
                                @if($authUser->can('view-approved-distribution-request'))
                                    <a class="nav-link" id="approved-distribution-requests-menu"
                                       href="{{ route('approved.distribution.requests.index') }}">Approved Distribution
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($authUser->can('transportation-bill') || $authUser->can('approve-transportation-bill') || $authUser->can('view-approved-transportation-bill'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarTransportationBill" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarTransportationBill"
                               aria-expanded="false" aria-controls="navbarTransportationBill">
                                <i class="bi bi-truck nav-icon"></i>
                                <span class="nav-link-title">Transportation Bills</span>
                            </a>
                            <div id="navbarTransportationBill" class="nav-collapse collapse"
                                 data-bs-parent="#navbarTransportationBillMenuName"
                                 hs-parent-area="#navbarTransportationBillMenuName" style="">
                                @if ($authUser->can('transportation-bill'))
                                    <a class="nav-link" id="transportation-bills-menu"
                                       href="{{ route('transportation.bills.index') }}">Transportation Bill </a>
                                @endif
                                @if ($authUser->can('approve-transportation-bill'))
                                    <a class="nav-link" id="approve-transportation-bills-menu"
                                       href="{{ route('approve.transportation.bills.index') }}">Approve Transportation
                                        Bill </a>
                                @endif
                                @if($authUser->can('view-approved-transportation-bill'))
                                    <a class="nav-link" id="approved-transportation-bills-menu"
                                       href="{{ route('approved.transportation.bills.index') }}">Approved Transportation
                                        Bill
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($authUser->can('add-payment-bill') || $authUser->can('payment-sheet') ||
                        $authUser->can('approve-payment-sheet') || $authUser->can('view-approved-payment-sheet'))
                        <div class="nav-item">
                            <a class="nav-link  dropdown-toggle" href="#navbarPaymentSheet" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarPaymentSheet"
                               aria-expanded="false" aria-controls="navbarPaymentSheet">
                                <i class="bi bi-truck nav-icon"></i>
                                <span class="nav-link-title">Payment Sheets</span>
                            </a>
                            <div id="navbarPaymentSheet" class="nav-collapse collapse"
                                 data-bs-parent="#navbarPaymentSheetMenuName"
                                 hs-parent-area="#navbarPaymentSheetMenuName" style="">
                                @if ($authUser->can('add-payment-bill'))
                                    <a class="nav-link" id="payment-bills-menu"
                                       href="{{ route('payment.sheets.index') }}">Payment Bills </a>
                                @endif
                                @if ($authUser->can('payment-sheet'))
                                    <a class="nav-link" id="payment-sheets-menu"
                                       href="{{ route('payment.sheets.index') }}">Payment Sheet </a>
                                @endif
                                @if ($authUser->can('approve-payment-sheet'))
                                    <a class="nav-link" id="approve-payment-sheets-menu"
                                       href="{{ route('approve.payment.sheets.index') }}">Approve Payment Sheet </a>
                                @endif
                                @if($authUser->can('view-approved-payment-sheet'))
                                    <a class="nav-link" id="approved-payment-sheets-menu"
                                       href="{{ route('approved.payment.sheets.index') }}">Approved Payment Sheet
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($authUser->can('manage-inventory'))
                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('inventories.index') }}" role="button"
                               id="inventories-menu">
                                <i class="bi bi-boxes nav-icon"></i>
                                <span class="nav-link-title">Inventories</span>
                            </a>
                        </div>
                    @endif

                    @if($authUser->can('manage-master'))
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#navbarMasterMenuName" role="button"
                               data-bs-toggle="collapse" data-bs-target="#navbarMasterMenuName"
                               aria-expanded="false" aria-controls="navbarMasterMenuName">
                                <i class="bi bi-menu-button-wide-fill nav-icon"></i>
                                <span class="nav-link-title">Master Setup</span>
                            </a>
                            <div id="navbarMasterMenuName" class="nav-collapse collapse"
                                 data-bs-parent="#navbarMasterMenu" hs-parent-area="#navbarMasterMenu" style="">
                                @if ($authUser->can('manage-office'))
                                    <a class="nav-link" href="{{ route('master.offices.index') }}"
                                       id="offices-menu">Offices</a>
                                @endif
                                @if ($authUser->can('manage-department'))
                                    <a class="nav-link" href="{{ route('master.departments.index') }}"
                                       id="departments-menu">Departments</a>
                                @endif
                                @if ($authUser->can('manage-designation'))
                                    <a class="nav-link" href="{{ route('master.designations.index') }}"
                                       id="designations-menu">Designations</a>
                                @endif
                                @if ($authUser->can('manage-leave-type'))
                                    <a class="nav-link" href="{{ route('master.leave.types.index') }}"
                                       id="leave-types-menu">Leave Types </a>
                                @endif
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
                                @if ($authUser->can('manage-donor-code'))
                                    <a class="nav-link" href="{{ route('master.donor.codes.index') }}"
                                       id="donor-codes-menu">{{ __('label.donor-codes') }}</a>
                                @endif
                                @if ($authUser->can('manage-project-code'))
                                    <a class="nav-link" href="{{ route('master.project.codes.index') }}"
                                       id="project-codes-menu">{{ __('label.project-codes') }}</a>
                                @endif
                                @if ($authUser->can('manage-inventory-category'))
                                    <a class="nav-link" href="{{ route('master.inventory.categories.index') }}"
                                       id="inventory-categories-menu">{{ __('label.inventory-categories') }}</a>
                                @endif
                                @if ($authUser->can('manage-unit'))
                                    <a class="nav-link" href="{{ route('master.units.index') }}"
                                       id="units-menu">{{ __('label.units') }}</a>
                                @endif
                                @if ($authUser->can('manage-item'))
                                    <a class="nav-link" href="{{ route('master.items.index') }}"
                                       id="items-menu">{{ __('label.items') }}</a>
                                @endif
                                @if ($authUser->can('manage-probationary-indicator'))
                                    <a class="nav-link" href="{{ route('master.probationary.indicators.index') }}"
                                       id="probationary-indicators-menu">{{ __('label.probationary-indicators') }}</a>
                                @endif
                                @if ($authUser->can('manage-probationary-question'))
                                    <a class="nav-link" href="{{ route('master.probationary.questions.index') }}"
                                       id="probationary-questions-menu">{{ __('label.probationary-questions') }}</a>
                                @endif
                                @if ($authUser->can('manage-expense-category'))
                                    <a class="nav-link" href="{{ route('master.expense.categories.index') }}"
                                       id="expense-categories-menu">{{ __('label.expense-categories') }}</a>
                                @endif
                                @if ($authUser->can('manage-expense-type'))
                                    <a class="nav-link" href="{{ route('master.expense.types.index') }}"
                                       id="expense-types-menu">{{ __('label.expense-types') }}</a>
                                @endif
                                @if ($authUser->can('manage-training-question'))
                                    <a class="nav-link" href="{{ route('master.training.questions.index') }}"
                                       id="training-questions-menu">{{ __('label.training-questions') }}</a>
                                @endif
                                @if ($authUser->can('manage-exit-question'))
                                    <a class="nav-link" href="{{ route('master.exit.questions.index') }}"
                                       id="exit-questions-menu">{{ __('label.exit-questions') }}</a>
                                    <a class="nav-link" href="{{ route('master.exit.feedbacks.index') }}"
                                       id="exit-feedbacks-menu">{{ __('label.exit-feedbacks') }}</a>
                                    <a class="nav-link" href="{{ route('master.exit.ratings.index') }}"
                                       id="exit-ratings-menu">{{ __('label.exit-ratings') }}</a>
                                @endif

                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <a href="#" class="position-absolute t-ggle"><i class="bi-arrow-left"></i></a>


</aside>
