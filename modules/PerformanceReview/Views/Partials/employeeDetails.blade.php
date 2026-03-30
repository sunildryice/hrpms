<div id="employeeAndSupervisorDetails" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">A.</span>
                        <span>
                            Employee and Line Manager Details
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Employee Name</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getEmployeeName() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Employee Title</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getEmployeeTitle() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Line Manager Name</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getSupervisorName() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Line Manager Title</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getSupervisorTitle() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Date of Joining</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->employee->getFirstJoinedDate() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">In Current Position Since</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getJoinedDate() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2 row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Review period from:</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getReviewFromDate() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Review period to:</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getReviewToDate() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Deadline:</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getDeadlineDate() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>