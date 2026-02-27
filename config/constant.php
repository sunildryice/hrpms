<?php

return [
    'CREATED_STATUS' => 1,
    'RETURNED_STATUS' => 2,
    'SUBMITTED_STATUS' => 3,
    'RECOMMENDED_STATUS' => 4,
    'RECOMMENDED2_STATUS' => 5,
    'APPROVED_STATUS' => 6,
    'CLOSED_STATUS' => 7,
    'REJECTED_STATUS' => 8,
    'AMENDED_STATUS' => 9,
    'ASSIGNED_STATUS' => 10,
    'VERIFIED_STATUS' => 11,
    'RECEIVED_STATUS' => 12,
    'CANCELLED_STATUS' => 13,
    'VERIFIED2_STATUS' => 14,
    'SEND_STATUS' => 15,
    'PAID_STATUS' => 16,
    'DISTRIBUTED_STATUS' => 17,
    'INIT_CANCEL_STATUS' => 18,
    'VERIFIED3_STATUS' => 19,

    'TDS_PERCENTAGE' => 15,
    'VAT_PERCENTAGE' => 13,
    'VAT_TDS_PERCENTAGE' => 1.5,
    'SSF_DEDUCTION_THRESHOLD' => 500000,
    'PR_REVIEW_THRESHOLD' => 3000,

    'ASSET_NEW' => 1,
    'ASSET_ASSIGNED' => 2,
    'ASSET_ON_MAINTENANCE' => 3,
    'ASSET_ON_STORE' => 4,
    'ASSET_DISTRIBUTED' => 5,
    'ASSET_DISPOSED' => 6,

    'GOOD_CONDITION' => 1,
    'POOR_CONDITION' => 2,
    'DAMAGE_CONDITION' => 3,

    'DISPOSITION_DISPOSED' => 1,
    'DISPOSITION_DAMAGED' => 2,
    'DISPOSITION_LOST' => 3,

    'SICK_LEAVE' => 3,
    'ANNUAL_LEAVE' => 6,
    'MATERNITY_LEAVE' => 9,
    'FESTIVAL_LEAVE' => 12,
    'MOURNING_LEAVE' => 18,
    'SPECIAL_LEAVE' => 21,
    'HOURLY_LEAVE' => 24,
    'PATERNITY_LEAVE' => 27,
    'TOIL_LEAVE' => 28,
    'ELECTION_TRAVEL_LEAVE' => 29,

    'LOGISTIC_CLEARANCE' => 1,
    'HR_CLEARANCE' => 2,
    'FINANCE_CLEARANCE' => 3,

    'NO_LEAVE' => 15,

    'GENDER_MALE' => 1,
    'GENDER_FEMALE' => 2,

    'HEAD_OFFICE' => 1,
    'CLUSTER_OFFICE' => 2,
    'DISTRICT_OFFICE' => 3,

    'ANNUAL_REVIEW' => 1,
    'MID_TERM_REVIEW' => 2,
    'KEY_GOALS_REVIEW' => 3,

    'FUND_TRANSFERRED' => 1,
    'EXPENSE_SETTLED' => 2,

    'FULL_TIME_EMPLOYEE' => 3,
    'SHORT_TERM_EMPLOYEE' => 6,
    'FULL_TIME_CONSULTANT' => 9,

    'PURCHASE_REQUEST' => \Modules\PurchaseRequest\Models\PurchaseRequest::class,
    'PURCHASE_ORDER' => \Modules\PurchaseOrder\Models\PurchaseOrder::class,

    'UNRESTRICTED_DONOR' => 4,

    'Saturday' => 1,
    'Saturday+Sunday' => 2,

    'MALE' => 1,
    'FEMALE' => 2,

    'OFFICE_CHECKIN_TIME' => '09:00:59',
    'OFFICE_CHECKOUT_TIME' => '17:30:00',
];
