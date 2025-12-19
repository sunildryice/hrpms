@extends('layouts.container-report')

@section('title', 'Approved Probation Review Print')
@section('page_css')
    {{--    <link rel="preconnect" href="https://fonts.googleapis.com"> --}}
    {{--    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> --}}
    {{--    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet"> --}}
    <style>
        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            font-weight: 600;
        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
        }

        .content {
            height: 70px;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">{{ $probationaryReview->createdBy->getOfficeName() }}</div>
        <div class="fs-8"> Probation Review</div>
    </div>

    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">

            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>The Supervisor/ line manager should ensure that the employee is given a copy of this document at each stage of
            their probation and should retain the original to monitor progress against set objectives at follow-up meetings.
        </div>
        <div class="my-4 fw-bold fs-6">Probation Record </div>

    </div>
    <div class="print-body">
        <table class="table border">
            <tbody>
                <tr>
                    <td>Employee Name:</td>
                    <td colspan="2">{{ $probationaryReview->getEmployeeName() }}</td>
                </tr>
                <tr>
                    <td>Designation:</td>
                    <td colspan="2">{{ $probationaryReview->employee->getDesignationName() }}</td>
                </tr>
                <tr>
                    <td>Level:</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Department / Section:</td>
                    <td colspan="2">{{ $probationaryReview->employee->getDepartmentName() }}</td>
                </tr>
                <tr>
                    <td>Date Of Joining:</td>
                    <td colspan="2">{{ $probationaryReview->employee->getFirstJoinedDate() }}</td>
                </tr>
                <tr>
                    <td>Immediate Supervisor/ Line Manager’s Name:</td>
                    <td colspan="2">{{ $reviewer->getFullName() }}</td>
                </tr>
                <tr>
                    <td>Designation:</td>
                    <td colspan="2">{{ $reviewer->getDesignationName() }}</td>
                </tr>
                <tr>
                    <td>Working District:</td>
                    <td colspan="2">{{ $reviewer->getOfficeName() }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Date</td>
                    <td>Please tick when completed</td>
                </tr>
                <tr>
                    <td>Initial Meeting</td>
                    <td>
                        @if ($probationaryReview->review_id == 1)
                            {{ $probationaryReview->getReviewDate() }}
                        @endif
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>3-month review:</td>
                    <td>
                        @if ($probationaryReview->review_id == 2)
                            {{ $probationaryReview->getReviewDate() }}
                        @endif
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>6-month review:</td>
                    <td>
                        @if ($probationaryReview->review_id == 3)
                            {{ $probationaryReview->getReviewDate() }}
                        @endif
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div>First review (3 months) – To be completed by Supervisor/ Line Manager in discussion with the employee.</div>

        <table class="table">
            <tbody>
                <tr>
                    <td>Indicators</td>
                    <td>Improvement required</td>
                    <td>Satisfactory</td>
                    <td>Good</td>
                    <td>Excellent</td>

                </tr>
                @foreach ($probationaryReviewIndicators as $probationaryReviewIndicator)
                    <tr>
                        <td>{{ $probationaryReviewIndicator->getIndicator() }}</td>
                        <td>
                            @if ($probationaryReviewIndicator->improved_required == 1)
                                <i class="bi bi-check"></i>
                            @endif
                        </td>
                        <td>
                            @if ($probationaryReviewIndicator->satisfactory == 1)
                                <i class="bi bi-check"></i>
                            @endif
                        </td>
                        <td>
                            @if ($probationaryReviewIndicator->good == 1)
                                <i class="bi bi-check"></i>
                            @endif
                        </td>
                        <td>
                            @if ($probationaryReviewIndicator->excellent == 1)
                                <i class="bi bi-check"></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5">{{ __('label.performance-improvements') }}</td>
                </tr>
                <tr>
                    <td class="content" colspan="5">{{ $probationaryReview->performance_improvements }}</td>
                </tr>
                <tr>
                    <td colspan="5">{{ __('label.concern-address-summary') }}</td>
                </tr>
                <tr>
                    <td class="content" colspan="5">{{ $probationaryReview->concern_address_summary }}</td>
                </tr>
                <tr>
                    <td colspan="5">{{ __('label.employee-performance-progress') }}</td>
                </tr>
                <tr>
                    <td class="content" colspan="5">{{ $probationaryReview->employee_performance_progress }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">If NO, what further action is required?</td>
                    <td>Review Date</td>
                </tr>
                <tr>
                    <td>{{ __('label.objectives-met') }}</td>
                    <td>
                        Yes @if ($probationaryReview->objectives_met == 1)
                            <i class="bi bi-check"></i>
                        @endif
                        <br>
                        No @if ($probationaryReview->objectives_met == 0)
                            <i class="bi bi-check"></i>
                        @endif
                    </td>
                    @if ($probationaryReview->objectives_met == 0)
                        <td colspan="2">{{ $probationaryReview->objectives_review_remarks }}</td>
                        <td>{{ $probationaryReview->objectives_review_date }}</td>
                    @else
                        <td colspan="2"></td>
                        <td></td>
                    @endif
                </tr>
                <tr>
                    <td>{{ __('label.development-addressed') }}
                    </td>
                    <td>
                        Yes @if ($probationaryReview->development_addressed == 1)
                            <i class="bi bi-check"></i>
                        @endif
                        <br>
                        No @if ($probationaryReview->development_addressed == 0)
                            <i class="bi bi-check"></i>
                        @endif
                    </td>
                    @if ($probationaryReview->development_addressed == 0)
                        <td colspan="2">{{ $probationaryReview->development_review_remarks }}</td>
                        <td>{{ $probationaryReview->development_review_date }}</td>
                    @else
                        <td colspan="2"></td>
                        <td></td>
                    @endif
                </tr>
                <tr>
                    <td>Employee’s Name: {{ $probationaryReview->getEmployeeName() }}</td>
                    <td colspan="4">Supervisor’s Name:{{ $reviewer->getFullName() }}</td>
                </tr>
                <tr>
                    <td>Date: {{ @array_key_exists('comment_added_date', $dates) ? $dates['comment_added_date'] : '' }}</td>
                    <td colspan="4">Date:
                        {{ @array_key_exists('recommended_date', $dates) ? $dates['recommended_date'] : '' }}</td>
                </tr>
                <tr>
                    <td>{{ __('label.supervisor-recommendation') }}</td>
                    <td colspan="4">{{ $probationaryReview->supervisor_recommendation }}</td>
                </tr>
                <tr>
                    <td>{{ __('label.director-recommendation') }}</td>
                    <td colspan="4">{{ $probationaryReview->director_recommendation }}</td>
                </tr>
                <tr>
                    <td colspan="4">{{ __('label.appointment-confirmed') }}</td>
                    <td>
                        Yes @if ($probationaryReview->appointment_confirmed == 1)
                            <i class="bi bi-check"></i>
                        @endif
                        <br>
                        No @if ($probationaryReview->appointment_confirmed == 0)
                            <i class="bi bi-check"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="5">{{ __('label.reason-to-address-difficulty') }}</td>
                </tr>
                <tr>
                    <td class="content" colspan="5">
                        @if ($probationaryReview->appointment_confirmed == 0)
                            {{ $probationaryReview->reason_to_address_difficulty }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="5">The employee may provide any comments about their experience of the probationary
                        process here.</td>
                </tr>
                <tr>
                    <td class="content" colspan="5">{{ $probationaryReview->employee_remarks }}</td>
                </tr>
                <tr>
                    <td colspan="4">Should the employee’s probationary period be extended?</td>
                    <td>
                        Yes @if ($probationaryReview->probation_extended == 1)
                            <i class="bi bi-check"></i>
                        @endif <br>
                        No @if ($probationaryReview->probation_extended == 0)
                            <i class="bi bi-check"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="5">{{ __('label.reason-and-improvement-to-extend') }}</td>
                </tr>
                <tr>
                    <td class="content" colspan="5">
                        @if ($probationaryReview->probation_extended == 1)
                            {{ $probationaryReview->reason_and_improvement_to_extend }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Length of the extension (max 3 months):</td>
                    <td colspan="4">
                        @if ($probationaryReview->probation_extended == 1)
                            {{ $probationaryReview->extension_length }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>New Probation Period completion date:</td>
                    <td colspan="4">
                        @if ($probationaryReview->probation_extended == 1)
                            {{ date('Y-m-d', strtotime($probationaryReview->next_probation_complete_date)) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Approver : {!! $probationaryReview->getApproverName() !!}</td>
                    <td>Remarks</td>
                    <td colspan="3">{!! $probationaryReview->approvedLog?->log_remarks !!}</td>
                </tr>
                <tr>
                    <td>Employee’s signature:</td>
                    <td colspan="4">
                        {{ @array_key_exists('comment_added_date', $dates) ? $dates['comment_added_date'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>Supervisor’s signature:</td>
                    <td colspan="4">{{ @array_key_exists('recommended_date', $dates) ? $dates['recommended_date'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>Approved Date:</td>
                    <td colspan="4">{{ @array_key_exists('approved_date', $dates) ? $dates['approved_date'] : '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
