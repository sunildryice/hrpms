@extends('layouts.container-report')

@section('title', 'Annual Performance Review Form')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
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
            width: 10%;
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
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')


    <section class="p-3 bg-white print-info" id="print-info">
        <div class="mb-3 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> HERD International</div>
            <div class="fs-8"> Annual Performance Review Form</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt=""
                                    class="align-self-end pe-5 logo-img">
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="mb-5 print-body">

            <div class="row">
                <div class="col-lg-12">
                    <!-- A. Employee and Supervisor Details -->
                    <table class="table mb-4 border">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4">A. EMPLOYEE AND LINE MANAGER DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Employee Name:</th>
                                <td>{{ $performanceReview->getEmployeeName() }}</td>
                                <th scope="row">Employee Title:</th>
                                <td>{{ $performanceReview->getEmployeeTitle() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Line Manager Name:</th>
                                <td>{{ $performanceReview->getSupervisorName() }}</td>
                                <th scope="row">Line Manager Title:</th>
                                <td>{{ $performanceReview->getSupervisorTitle() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Date of Joining:</th>
                                <td>{{ $performanceReview->employee->getFirstJoinedDate() }}</td>
                                <th scope="row">In Current Position Since:</th>
                                <td>{{ $performanceReview->getJoinedDate() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Review period from:</th>
                                <td>{{ $performanceReview->getReviewFromDate() }}</td>
                                <th scope="row">Review period to:</th>
                                <td>{{ $performanceReview->getReviewToDate() }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Deadline:</th>
                                <td colspan="3">{{ $performanceReview->getDeadlineDate() }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- B. Key Goals Review -->
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="6">B. KEY GOALS REVIEW</th>
                            </tr>
                            <tr>
                                <th rowspan="2" style="width: 10%">Objective</th>
                                <th rowspan="2" style="width: 15%">Output / Deliverable</th>
                                <th rowspan="2" style="width: 15%">Major Activities</th>
                                <th colspan="2">Achievement against output / deliverable</th>
                                <th rowspan="2" style="width: 22%">Line Manager Comments</th>
                            </tr>
                            <tr>
                                <th style="width: 10%">Status</th>
                                <th style="width: 15%">Remarks / Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($keygoals as $keygoal)
                                <tr>
                                    <td>{{ $keygoal->title }}</td>
                                    <td>{{ $keygoal->output_deliverables }}</td>
                                    <td>{{ $keygoal->major_activities_employee ?? '—' }}</td>
                                    <td><span>{{ $keygoal->status?->label() ?? 'Not Set' }}</span></td>
                                    <td>{{ $keygoal->remarks_employee ?? '—' }}</td>
                                    <td>{{ $keygoal->description_supervisor ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- C. Professional Development Plan -->
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="3">C. PROFESSIONAL DEVELOPMENT PLAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $devPlans = $keyGoalReview->developmentPlans ?? collect(); @endphp
                            @if ($devPlans->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No professional development plan has been added yet.
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <th style="width: 5%">SN</th>
                                    <th style="width: 45%">Development Plan Objective</th>
                                    <th style="width: 45%">Activity</th>
                                </tr>
                                @foreach ($devPlans as $index => $plan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $plan->objective }}</td>
                                        <td>{{ $plan->activity ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <!-- D. Core Competencies -->
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="3">D. CORE COMPETENCIES</th>
                            </tr>
                            <tr>
                                <th style="width: 35%">Competency</th>
                                <th style="width: 15%">Rating (1-5)</th>
                                <th style="width: 50%">Examples</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coreCompetencies ?? collect() as $comp)
                                <tr>
                                    <td>{{ $comp->competency }}</td>
                                    <td>@php
                                        $ratings = [
                                            1 => '1 - Poor',
                                            2 => '2 - Fair',
                                            3 => '3 - Good',
                                            4 => '4 - Very Good',
                                            5 => '5 - Excellent',
                                        ];
                                    @endphp
                                        @if ($comp->rating)
                                            <span>{{ $ratings[$comp->rating] ?? $comp->rating }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $comp->example ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No core competencies recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- E. Challenges / Difficulties -->
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="2">E. CHALLENGES / DIFFICULTIES</th>
                            </tr>
                            <tr>
                                <th style="width: 45%">Challenge / Difficulty Faced</th>
                                <th style="width: 45%">Result / Outcome</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($challenges ?? collect() as $challenge)
                                <tr>
                                    <td>{{ $challenge->challenge }}</td>
                                    <td>{{ $challenge->result }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No challenges recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- F. Employee Comments -->
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="2">F. EMPLOYEE COMMENTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2"><p class="mb-0">{{ $performanceReview->employee_comments ?: '—' }}</p></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- G. Line Manager Result and Comments -->
                    <table class="table border mb-4">
                        <thead>
                            <tr>
                                <th scope="col" colspan="2">G. RESULT AND COMMENTS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="width: 20%">Result</th>
                                <td>{{ $performanceReview->result ?: '—' }}</td>
                            </tr>
                            <tr>
                                <th style="width: 20%">Comments</th>
                                <td>{{ $performanceReview->comments ?: '—' }}</td>
                            </tr>
                        </tbody>
                    </table>


                    <table class="table mb-4 border">
                        <tbody>
                            <tr>
                                <th scope="row">Employee Name:</th>
                                <td>{{ $performanceReview->getEmployeeName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.SUBMITTED_STATUS'))->last()?->created_at?->format('Y-m-d') }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Line Manager:</th>
                                <td>{{ $performanceReview->getSupervisorName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.VERIFIED_STATUS'))->last()?->created_at?->format('Y-m-d') }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Next Line Manager:</th>
                                <td>{{ $performanceReview->getRecommenderName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.RECOMMENDED_STATUS'))->last()?->created_at?->format('Y-m-d') }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Executive Director:</th>
                                <td>{{ $performanceReview->getApproverName() }}</td>
                                <th scope="row">Date:</th>
                                <td>{{ $performanceReview->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at?->format('Y-m-d') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
        window.onload = print;
    </script>

@endsection
