<div class="card-body">
    <div class="row mb-3">
        <div class="col-lg-12 mb-2">
                <label class="form-label"  data-bs-toggle="tooltip" data-bs-placement="left"
                title="Position (Fiscal Year)">Position (Fiscal Year):</label>
                <span> {{ $employeeRequest->position_title }} <span
                        class="badge bg-primary">{{ $employeeRequest->getFiscalYear() }}</span> </span>
        </div>
        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Duty Station">Duty Station:</label>
                <span> {{ $employeeRequest->dutyStation->district_name }}</span>
        </div>
        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Replacement for"> Replacement for:</label>
                <span> {{ $employeeRequest->replacement_for }}</span>
        </div>
        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Date Required From"> Date Required From:</label>
                <span> {{ $employeeRequest->required_date }}</span>
        </div>
        <div class="col-lg-6 mb-2">
                 <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                 title="Is Position Budgeted"> Is Position Budgeted:</label>
                <span>
                    @if ($employeeRequest->budgeted == 1)
                        Yes
                    @else
                        No
                    @endif
                </span>
        </div>
        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Account Code"> Account Code:</label>
                <span> {{ $employeeRequest->getAccountCode() }}</span>
        </div>

        <div class="col-lg-12 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Activity Code"> Activity Code:</label>
                <span> {{ $employeeRequest->getActivityCode() }} </span>
        </div>



        <div class="col-lg-6 mb-2">
                 <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                 title="Donor Code"> Donor Code:</label>
                <span> {{ $employeeRequest->getDonorCode() }}</span>
        </div>
        <div class="col-lg-6 mb-2">
                 <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                 title="Work Load"> Work Load (Hours per week):</label>
                <span> {{ $employeeRequest->work_load }}</span>
        </div>
        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="For Duration"> For Duration:</label>
                <span> {{ $employeeRequest->duration }}</span>
        </div>
        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Other Specify">Other Specify:</label>
                <span> {{ $employeeRequest->employee_type_other }}</span>
        </div>
        <div class="col-lg-12 mb-2">
                 <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                 title="Reason for Request">Reason for Request:</label>
                <span> {{ $employeeRequest->reason_for_request }}</span>
        </div>
        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Reviewed By">Reviewed By:</label>
                <span> {{ $employeeRequest->getReviewerName() }}</span>
        </div>

        <div class="col-lg-6 mb-2">
                <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="left"
                title="Approved By">Approved By:</label>
                <span> {{ $employeeRequest->getapproverName() }}</span>
        </div>
    </div>
    <div class="mb-4">
        <div class="fw-bold  text-uppercase border-bottom py-2">Qualification</div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th width="20%">Qualifications</th>
                        <th>Required</th>
                        <th>Prefered</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Education</th>
                        <td> {{ $employeeRequest->education_required }}</td>
                        <td> {{ $employeeRequest->education_preferred }}</td>
                    </tr>
                    <tr>
                        <th>Work Experience</th>
                        <td> {{ $employeeRequest->experience_required }}</td>
                        <td> {{ $employeeRequest->experience_preferred }}</td>
                    </tr>
                    <tr>
                        <th>Skill</th>
                        <td> {{ $employeeRequest->skills_required }}</td>
                        <td> {{ $employeeRequest->skills_preferred }}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    <div>
        <div class="fw-bold  text-uppercase  mb-2">Others</div>
       <div class="row">
        <div class="col-lg-4 mb-2">
            <div class="d-flex align-items-start gap-2" data-bs-toggle="tooltip" data-bs-placement="left"
                title="TOR JD Submitted">
                <label class="form-label">TOR JD Submitted:</label>
                {{-- <i class="text-primary bi bi-briefcase-fill"></i> --}}
                <span> {{ $employeeRequest->tor_jd_submitted == 0 ? 'No' : 'Yes' }}</span>
            </div>
        </div>
        @if ($employeeRequest->tor_jd_attachment)
            <div class="col-lg-4 mb-2">
                <div class="d-flex align-items-start gap-2" data-bs-toggle="tooltip" data-bs-placement="left"
                    title="TOR JD Submitted">
                <label class="form-label">TOR/JD Attachment:</label>
                    <div class="media" style="">
                        <a href="{{ asset('storage/' . $employeeRequest->tor_jd_attachment) }}" target="_blank"
                            name='attachment_exist' class="fs-5" title="View Attachment">
                            <i class="bi bi-file-pdf text-dark"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-lg-4 mb-2 {{ $employeeRequest->tor_jd_submitted == 0 ? '' : 'd-none' }}">
            <div class="d-flex align-items-start gap-2" data-bs-toggle="tooltip" data-bs-placement="left"
                title="tentative submission date">
                <label class="form-label">Tentative Submission Date:</label>
                {{-- <i class="text-primary bi bi-briefcase-fill"></i> --}}
                <span> {{ $employeeRequest->tentative_submission_date }}</span>
            </div>

        </div>

        <div class="col-lg-4 mb-2">
            <div class="d-flex align-items-start gap-2" data-bs-toggle="tooltip" data-bs-placement="left"
                title="logistics requirement">
                <label class="form-label">Logistics Requirement:</label>
                {{-- <i class="text-primary bi bi-briefcase-fill"></i> --}}
                <span> {{ $employeeRequest->logistics_requirement }}</span>
            </div>

        </div>

       </div>

    </div>

</div>
