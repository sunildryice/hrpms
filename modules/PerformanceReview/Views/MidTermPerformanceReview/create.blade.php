@extends('layouts.container')

@section('title', 'Mid-Term Performance Review Form')

@section('page_js')
    <script type="text/javascript">
        let isGroupBFormSaved = false;
        let isGroupCFormSaved = false;
        let isGroupHFormSaved = false;

        $(function() {
            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');
            const performanceReview = @json($performanceReview);

            $('#add-key-goal').click(function(e) {
                e.preventDefault();

                $('#keyGoalForm')[0].reset();
                $('#key_goal_id').val('');
                $('#keyGoalModalTitle').text('Add Key Goal');

                $('#keyGoalModal').modal('show');
            });

            $('#keygoal-body').on('click', '.edit-key-goal', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let row = $(this).closest('tr');
                let titleEl = row.find('td:first-child input[id^="keygoal_title_"], td:first-child span')
                    .first();
                let title = titleEl.is('input') ? titleEl.val() : titleEl.text().trim();
                let outputEl = row.find('td:nth-child(2) input[id^="keygoal_employee_"], td:nth-child(2)')
                    .first();
                let output = outputEl.is('input') ? outputEl.val() : outputEl.text().trim();

                $('#key_goal_id').val(id);
                $('#title').val(title);
                $('#output_deliverables').val(output);

                $('#keyGoalModalTitle').text('Edit Key Goal');

                $('#keyGoalModal').modal('show');
            });

            $('#keygoal-body').on('click', '.delete-key-goal', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let url = $(this).data('href');

                ajaxSweetAlert(url, 'POST', {
                    keyGoalId: id
                }, 'Delete Key Goal', function(res) {
                    toastr.success(res.message);
                    $('.preloader').show();
                    location.reload();
                });
            });

            $('#keyGoalForm').submit(function(e) {
                e.preventDefault();

                let id = $('#key_goal_id').val();
                let isEdit = !!id;

                let url = isEdit ?
                    "{{ route('performance.keygoal.update') }}" :
                    "{{ route('performance.keygoal.store') }}";

                let payload = {
                    _token: "{{ csrf_token() }}",
                    performance_review_id: performanceReview.id,
                    title: $('#title').val(),
                    output_deliverables: $('#output_deliverables').val(),
                    type: 'current'
                };

                if (isEdit) {
                    payload.key_goal_id = id;
                }

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: payload,
                    success: function(res) {
                        toastr.success(res.message || 'Saved successfully');
                        $('#keyGoalModal').modal('hide');
                        $('.preloader').show();
                        location.reload();
                    },
                    error: function(err) {
                        console.error(err);
                        toastr.error('Something went wrong');
                    }
                });
            });

            function getNewKeyGoals() {
                $.ajax({
                    type: 'get',
                    url: `${baseUrl}/api/performance/${performanceReview.id}/keygoals`,
                    success: function(response) {

                        if (response?.keyGoals.length) {
                            $('#new-keyGoalTable').show();

                            let html = '';

                            response.keyGoals.forEach(goal => {
                                html += `
                    <tr>
                        <td>
                            ${goal.title}
                            <a class="edit-key-goal text-primary ms-2" 
                               href="#" 
                               data-id="${goal.id}" 
                               data-title="${goal.title}">
                               <i class="bi bi-pencil-square"></i>
                            </a>
                            <a class="delete-key-goal text-danger ms-2" 
                               href="#" 
                               data-id="${goal.id}" 
                               data-href="{{ route('performance.keygoal.destroy') }}">
                               <i class="bi bi-trash"></i>
                            </a>
                        </td>

                        <td>${goal.output_deliverables ?? ''}</td>

                        <td>
                            <textarea name="major_activities_employee_${goal.id}" class="form-control">${goal.major_activities_employee ?? ''}</textarea>
                        </td>

                        <td>
                            <select name="status_${goal.id}" class="form-select">
                                <option value="">Select Status</option>
                            </select>
                        </td>

                        <td>
                            <textarea name="remarks_employee_${goal.id}" class="form-control">${goal.remarks_employee ?? ''}</textarea>
                        </td>
                    </tr>`;
                            });

                            $('#keygoal-body').html(html);

                        } else {
                            $('#new-keyGoalTable').hide();
                        }
                    }
                });
            }

            let previousLength = 0;
            const handleInput = (event) => {
                const bullet = "\u2022";
                const newLength = event.target.value.length;
                const characterCode = event.target.value.substr(-1).charCodeAt(0);

                if (newLength > previousLength) {
                    if (characterCode === 10) {
                        event.target.value = `${event.target.value}${bullet} `;
                    } else if (newLength === 1) {
                        event.target.value = `${bullet} ${event.target.value}`;
                    }
                }

                previousLength = newLength;
            }

            $('input[type="checkbox"]').on('change', function() {
                $('input[type="checkbox"]').not(this).prop('checked', false);
            });

            // GROUP B - KEY GOALS FORM 
            $('#groupBForm').on('submit', function(e) {
                e.preventDefault();

                let rows = $('#keyGoalTable tbody tr, #new-keyGoalTable tbody tr');

                let isValid = true;

                rows.each(function() {
                    const majorActivities = $(this).find('.major-activities').val().trim();
                    const status = $(this).find('.status-dropdown').val();

                    if (!majorActivities || !status) {
                        isValid = false;
                        $(this).addClass('table-danger');
                    } else {
                        $(this).removeClass('table-danger');
                    }
                });

                if (!isValid) {
                    toastr.error('Please fill Major Activities and Status for all key goals.',
                        'Validation Error');
                    return;
                }

                rows.each(function() {
                    let row = $(this);
                    let id = row.data('keygoal-id') || row.find('textarea, select').first().attr(
                        'name')?.split('_').pop();

                    if (!id || isNaN(id)) return;

                    let title = row.find(`[name="title_${id}"]`).val() || '';
                    let output = row.find(`[name="output_deliverables_${id}"]`).val() || '';
                    let majorActivities = row.find(`[name="major_activities_employee_${id}"]`)
                        .val() || '';
                    let status = row.find(`[name="status_${id}"]`).val() || '';
                    let remarks = row.find(`[name="remarks_employee_${id}"]`).val() || '';

                    updateKeyGoal(id, title, majorActivities, '', 'current', status, remarks,
                        output);
                });

                isGroupCFormSaved = true;

                toastr.success('Key Goals saved successfully', 'Success', {
                    timeOut: 1000
                });
            });


            $('#groupCForm').on('submit', function(e) {
                e.preventDefault();

                let isValid = true;
                // Validation: Check if Activity is filled for all rows
                $('#devplan-table tbody tr').each(function() {
                    const activity = $(this).find('.devplan-activity').val().trim();

                    if (!activity) {
                        isValid = false;
                        $(this).addClass('table-danger');
                    } else {
                        $(this).removeClass('table-danger');
                    }
                });

                if (!isValid) {
                    toastr.error('Please fill Activity for all development plans.', 'Validation Error');
                    return;
                }
                // If validation passes, submit the form via AJAX 
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.type === 'success') {
                            toastr.success('Professional Development Plan saved successfully!',
                                'Success');
                            isGroupCFormSaved = true;
                        } else {
                            toastr.error(response.message || 'Failed to save development plan.',
                                'Error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        toastr.error('Something went wrong while saving activities.', 'Error');
                    }
                });
            });

            // D. Core Competencies 
            let competencyRowIndex = $('#competencies-body .competency-row').length - 1;

            function buildCompetencyRow(idx, competency = '', rating = '', example = '', id = null) {
                return `
        <tr class="competency-row" data-row-index="${idx}" ${id ? `data-id="${id}"` : ''}>
            <td>
                <input type="text" 
                       name="competencies[${idx}][competency]" 
                       class="form-control competency-name" 
                       value="${competency}"
                       placeholder="Competency">
                ${id ? `<input type="hidden" name="competencies[${idx}][id]" value="${id}">` : ''}
            </td>
            <td>
                <select name="competencies[${idx}][rating]" class="form-select competency-rating">
                    <option value="">Select Rating</option>
                    <option value="1" ${rating == 1 ? 'selected' : ''}>1 - Poor</option>
                    <option value="2" ${rating == 2 ? 'selected' : ''}>2 - Fair</option>
                    <option value="3" ${rating == 3 ? 'selected' : ''}>3 - Good</option>
                    <option value="4" ${rating == 4 ? 'selected' : ''}>4 - Very Good</option>
                    <option value="5" ${rating == 5 ? 'selected' : ''}>5 - Excellent</option>
                </select>
            </td>
            <td>
                <textarea name="competencies[${idx}][example]" 
                    class="form-control competency-example" 
                    rows="1"
                    placeholder="Provide examples that reflect your roles...">${example}</textarea>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-sm add-competency-row">
                    <i class="bi bi-plus"></i>
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm remove-competency-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
            }

            function updateCompetencyActionButtons() {
                const $rows = $('#competencies-body .competency-row');

                $rows.find('.add-competency-row').hide();
                $rows.find('.remove-competency-row').show();

                if ($rows.length === 1) {
                    $rows.find('.remove-competency-row').hide();
                }

                $rows.last().find('.add-competency-row').show();
            }

            $(function() {
                updateCompetencyActionButtons();
            });

            // Add new row
            $(document).on('click', '.add-competency-row', function() {
                competencyRowIndex++;
                const $newRow = $(buildCompetencyRow(competencyRowIndex));
                $('#competencies-body').append($newRow);
                updateCompetencyActionButtons();
            });

            // Remove row
            $(document).on('click', '.remove-competency-row', function() {
                if ($('#competencies-body .competency-row').length > 1) {
                    $(this).closest('tr').remove();
                    updateCompetencyActionButtons();
                }
            });

            // Form Submission
            $('#groupDForm').on('submit', function(e) {
                e.preventDefault();

                let isValid = true;
                $('#competencies-body tr').each(function() {
                    const competency = $(this).find('.competency-name').val().trim();
                    if (!competency) {
                        isValid = false;
                        $(this).addClass('table-danger');
                    } else {
                        $(this).removeClass('table-danger');
                    }
                });

                if (!isValid) {
                    toastr.error('Please enter competency for all rows.', 'Validation Error');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.type === 'success') {
                            toastr.success('Core Competencies saved successfully!', 'Success');
                        } else {
                            toastr.error(response.message ||
                                'Failed to save core competencies.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong while saving core competencies.');
                        console.error(xhr);
                    }
                });
            });

            // groupEForm
            let challengeRowIndex = $('#challenges-body .challenge-row').length - 1;

            function buildChallengeRow(idx, challenge = '', result = '', id = null) {
                return `
                <tr class="challenge-row" data-row-index="${idx}" ${id ? `data-id="${id}"` : ''}>
                    <td>
                        <textarea name="challenges[${idx}][challenge]" class="form-control" rows="2">${challenge}</textarea>
                        <input type="hidden" name="challenges[${idx}][id]" value="${id ?? ''}">
                    </td>
                    <td>
                        <textarea name="challenges[${idx}][result]" class="form-control" rows="2">${result}</textarea>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm add-challenge-row">
                            <i class="bi bi-plus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-challenge-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            }

            function updateChallengeButtons() {
                const $rows = $('#challenges-body .challenge-row');
                $rows.find('.add-challenge-row').hide();
                $rows.find('.remove-challenge-row').show();

                if ($rows.length === 1) {
                    $rows.find('.remove-challenge-row').hide();
                }
                $rows.last().find('.add-challenge-row').show();
            }

            // Initialize buttons on load
            $(function() {
                updateChallengeButtons();
            });

            // Add new row
            $(document).on('click', '.add-challenge-row', function() {
                challengeRowIndex++;
                const $newRow = $(buildChallengeRow(challengeRowIndex));
                $('#challenges-body').append($newRow);
                updateChallengeButtons();
            });

            // Remove row
            $(document).on('click', '.remove-challenge-row', function() {
                const $row = $(this).closest('tr');
                $row.remove();
                updateChallengeButtons();
            });

            // Handle form submission for challenges
            $('#groupEForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.type === 'success') {
                            toastr.success('Challenges saved successfully!', 'Success');
                            // location.reload(); 
                        } else {
                            toastr.error(response.message || 'Failed to save challenges');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong while saving challenges.');
                        console.error(xhr);
                    }
                });
            });


            $('#groupHForm').on('submit', function(e) {
                e.preventDefault();
                // Getting form action, method and data.
                var action = $(this).attr("action");
                var method = $(this).attr('method');
                let data = $(this).serializeArray();
                // Checking if any input field in the form is empty.
                let empty = false;
                data.every(element => {
                    if (!element.value) {
                        empty = true;
                        return false;
                    }
                    return true;
                });
                if (empty) {
                    toastr.error('Please complete the form.', 'Error', {
                        timeout: 2000
                    });
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value;
                    saveAnswer(questionId, answer);
                });
                isGroupHFormSaved = true;
                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            }).on('change', 'textarea', function(e) {
                e.preventDefault();
                let questionId = $(this).attr('name').split("_")[1];
                let answer = $(this).val();
                if (questionId) {
                    saveAnswer(questionId, answer);
                }
            });

        });

        function updateKeyGoal(keyGoalId, title = '', majorActivities = '', descriptionSupervisor = '',
            type = 'current', status = '', remarks = '', outputDeliverables = '') {

            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.update') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'key_goal_id': keyGoalId,
                    'title': title,
                    'output_deliverables': outputDeliverables,
                    'major_activities_employee': majorActivities,
                    'description_supervisor_annual': descriptionSupervisor,
                    'performance_review_id': "{{ $performanceReview->id }}",
                    'type': type,
                    'status': status,
                    'remarks_employee': remarks
                },
                success: function(data) {
                    // toastr.success('Key goal updated.', 'Success', {timeOut: 2000});
                },
                error: function(data) {
                    console.error(data);
                    // toastr.error('Key goal could not be updated.', 'Failed', {timeOut: 2000});
                }
            });
        }

        function saveAnswer(questionId, answer) {
            let flag;
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.answer.store') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
                    'question_id': questionId,
                    'answer': answer
                },
                success: function(data) {
                    // toastr.success('Answer saved', 'Success', {timeOut: 1000});
                },
                error: function(error) {
                    // toastr.error('Answer could not be saved.', 'Error', {timeOut: 1000});
                }
            });
            return flag;
        }

        function appendKeyGoal() {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.append') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}"
                },
                success: function(data) {
                    $('.preloader').show();
                    location.reload();
                },
                error: function(error) {
                    //
                }
            });
        }

        function removeGoal(event, id) {
            event.target.parentElement.parentElement.parentElement.remove();
            deleteKeyGoal(id);
        }

        function validateForm() {
            let isGroupBFormSaved = true;
            let isGroupCFormSaved = true;
            let isGroupDFormSaved = true;
            let isGroupEFormSaved = true;
            let isGroupHFormSaved = true;

            // B. Key Goals Review
            $('#keyGoalTable tbody tr').each(function() {
                const majorActivities = $(this).find('.major-activities').val().trim();
                const status = $(this).find('.status-dropdown').val();
                if (!majorActivities || !status) {
                    isGroupBFormSaved = false;
                    $(this).addClass('table-danger');
                } else {
                    $(this).removeClass('table-danger');
                }
            });

            // C. Professional Development Plan
            $('#devplan-table tbody tr').each(function() {
                const activity = $(this).find('.devplan-activity').val().trim();
                if (!activity) {
                    isGroupCFormSaved = false;
                    $(this).addClass('table-danger');
                } else {
                    $(this).removeClass('table-danger');
                }
            });

            // D. Core Competencies
            $('#competencies-body tr.competency-row').each(function() {
                const competency = $(this).find('.competency-name').val().trim();
                if (!competency) {
                    isGroupDFormSaved = false;
                    $(this).addClass('table-danger');
                } else {
                    $(this).removeClass('table-danger');
                }
            });

            // E. Challenges / Difficulties
            $('#challenges-body tr.challenge-row').each(function() {
                const challenge = $(this).find('textarea[name*="challenge"]').first().val().trim();
                const result = $(this).find('textarea[name*="result"]').first().val().trim();
                if (!challenge || !result) {
                    isGroupEFormSaved = false;
                    $(this).addClass('table-danger');
                } else {
                    $(this).removeClass('table-danger');
                }
            });

            // H & J already use your existing logic
            let groupHData = $('#groupHForm').serializeArray();
            isGroupHFormSaved = !checkEmpty(groupHData);

            if (isGroupBFormSaved && isGroupCFormSaved && isGroupDFormSaved &&
                isGroupEFormSaved && isGroupHFormSaved) {

                window.location.href = "{{ route('performance.submit', $performanceReview->id) }}";
            } else {
                toastr.warning('Please save all sections properly before submitting.', 'Warning', {
                    timeOut: 2500
                });
            }
        }
    </script>
@endsection

@section('page-content')

    <style>
        td,
        th {
            border: 1px solid grey;
            padding: 8px;
            text-align: left;
        }
    </style>



    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('performance.employee.index') }}"
                                class="text-decoration-none text-dark">Performance Review</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section>

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
                    <div class="row mb-2">
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
                        <div class="col-lg-6 mt-3">
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

        <div id="keyGoalsReview" class="mb-3">
            <form action="{{ route('performance.keygoal.update') }}" method="POST" id="groupBForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title d-flex justify-content-between">
                            <span class="fw-bold">B.
                                Key Goals Review
                            </span>
                            <button class="btn btn-sm btn-primary" id="add-key-goal"
                                data-href="{{ route('performance.keygoal.store') }}"><i class="bi bi-plus"></i>Add
                                New</button>
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table mb-3" id="keyGoalTable">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 18%">Objective</th>
                                    <th rowspan="2" style="width: 15%">Output / Deliverable</th>
                                    <th rowspan="2" style="width: 22%">Major Activities</th>
                                    <th colspan="2">Achievement against output / deliverable</th>
                                </tr>
                                <tr>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 25%">Remarks / Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($keygoals as $keygoal)
                                    <tr data-keygoal-id="{{ $keygoal->id }}">
                                        <td>{{ $keygoal->title }}</td>
                                        <td>{{ $keygoal->output_deliverables }}</td>
                                        <td>
                                            <textarea name="major_activities_employee_{{ $keygoal->id }}" class="form-control major-activities" rows="2">{{ $keygoal->major_activities_employee }}</textarea>
                                        </td>
                                        <td>
                                            <select name="status_{{ $keygoal->id }}" class="form-select status-dropdown">
                                                <option value="">Select Status</option>
                                                @foreach (\Modules\PerformanceReview\Models\Enums\KeyGoalStatus::cases() as $status)
                                                    <option value="{{ $status->value }}"
                                                        {{ $keygoal->status?->value === $status->value ? 'selected' : '' }}>
                                                        {{ $status->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="remarks_employee_{{ $keygoal->id }}" class="form-control remarks-employee" rows="2">{{ $keygoal->remarks_employee }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <table id="new-keyGoalTable"
                            style="width: 100%;@if (!$newKeyGoals->count()) display:none @endif">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 18%">(Additional Objective)</th>
                                    <th rowspan="2" style="width: 15%">(Additional Output / Deliverable)</th>
                                    <th rowspan="2" style="width: 22%">Major Activities</th>
                                    <th colspan="2">Achievement against output / deliverable</th>
                                    <th rowspan="2" style="width: 22%">Action</th>
                                </tr>
                                <tr>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 25%">Remarks / Comments</th>
                                </tr>
                            </thead>
                            <tbody id="keygoal-body">
                                @foreach ($newKeyGoals as $keygoal)
                                    <tr>
                                        <td>
                                            <span style="width: 100%">{{ $keygoal->title }}
                                            </span>
                                        </td>
                                        <td>{{ $keygoal->output_deliverables }}</td>
                                        <td>
                                            <textarea name="major_activities_employee_{{ $keygoal->id }}" class="form-control major-activities" rows="2">{{ $keygoal->major_activities_employee }}</textarea>
                                        </td>
                                        <td>
                                            <select name="status_{{ $keygoal->id }}"
                                                class="form-select status-dropdown">
                                                <option value="">Select Status</option>
                                                @foreach (\Modules\PerformanceReview\Models\Enums\KeyGoalStatus::cases() as $status)
                                                    <option value="{{ $status->value }}"
                                                        {{ $keygoal->status?->value === $status->value ? 'selected' : '' }}>
                                                        {{ $status->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="remarks_employee_{{ $keygoal->id }}" class="form-control remarks-employee" rows="2">{{ $keygoal->remarks_employee }}</textarea>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a class="edit-key-goal btn btn-outline-primary btn-sm" href="#"
                                                    data-href="{{ route('performance.keygoal.update') }}"
                                                    data-title="{{ $keygoal->title }}" data-id="{{ $keygoal->id }}"u><i
                                                        class="bi bi-pencil-square"></i></a>
                                                <a class="delete-key-goal btn btn-outline-danger btn-sm" href="#"
                                                    data-href="{{ route('performance.keygoal.destroy') }}"
                                                    data-id="{{ $keygoal->id }}"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- C. Professional Development Plan -->
        <div id="professionalDevelopmentPlan" class="mb-3">
            <form action="{{ route('performance.devplan.update') }}" method="POST" id="groupCForm">
                @csrf
                <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">C.</span> Professional Development Plan
                        </span>
                    </div>
                    <div class="card-body">
                        @php
                            $devPlans = $keyGoalReview->developmentPlans ?? collect();
                        @endphp

                        @if ($devPlans->isEmpty())
                            <div class="text-center text-muted py-4">
                                No professional development plan has been added yet.
                            </div>
                        @else
                            <table class="table table-bordered" id="devplan-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">SN</th>
                                        <th style="width: 45%">Development Plan Objective</th>
                                        <th style="width: 45%">Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($devPlans as $index => $plan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="readonly-cell">
                                                {{ $plan->objective }}
                                            </td>
                                            <td>
                                                <textarea name="devplans[{{ $index }}][activity]" class="form-control devplan-activity" rows="1"
                                                    data-id="{{ $plan->id }}" placeholder="Enter activities...">{{ $plan->activity ?? '' }}</textarea>
                                                <input type="hidden" name="devplans[{{ $index }}][id]"
                                                    value="{{ $plan->id }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-outline-primary float-end">Save</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- D. Core Competencies -->
        <div id="coreCompetenciesSection" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">D.</span> Core Competencies
                    </span>
                </div>

                <div class="card-body">
                    <form id="groupDForm" method="POST" action="{{ route('performance.corecompetency.store') }}">
                        @csrf
                        <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

                        <table class="table table-bordered" id="competencies-table">
                            <thead>
                                <tr>
                                    <th style="width: 35%">Competency</th>
                                    <th style="width: 15%">Rating (1-5)</th>
                                    <th style="width: 45%">Provide examples that reflect your roles</th>
                                    <th style="width: 5%" class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody id="competencies-body">
                                @php
                                    $existingCompetencies = $coreCompetencies ?? collect();
                                @endphp

                                @forelse ($existingCompetencies as $index => $comp)
                                    <tr class="competency-row" data-row-index="{{ $index }}"
                                        data-id="{{ $comp->id }}">
                                        <td>
                                            <input type="text" name="competencies[{{ $index }}][competency]"
                                                class="form-control competency-name"
                                                value="{{ $comp->competency ?? '' }}" placeholder="Competency">
                                            <input type="hidden" name="competencies[{{ $index }}][id]"
                                                value="{{ $comp->id }}">
                                        </td>
                                        <td>
                                            <select name="competencies[{{ $index }}][rating]"
                                                class="form-select competency-rating">
                                                <option value="">Select Rating</option>
                                                <option value="1" {{ $comp->rating == 1 ? 'selected' : '' }}>1 - Poor
                                                </option>
                                                <option value="2" {{ $comp->rating == 2 ? 'selected' : '' }}>2 - Fair
                                                </option>
                                                <option value="3" {{ $comp->rating == 3 ? 'selected' : '' }}>3 - Good
                                                </option>
                                                <option value="4" {{ $comp->rating == 4 ? 'selected' : '' }}>4 - Very
                                                    Good</option>
                                                <option value="5" {{ $comp->rating == 5 ? 'selected' : '' }}>5 -
                                                    Excellent</option>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="competencies[{{ $index }}][example]" class="form-control competency-example" rows="1"
                                                placeholder="Provide examples that reflect your roles...">{{ $comp->example ?? '' }}</textarea>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm add-competency-row">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm remove-competency-row">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="competency-row" data-row-index="0">
                                        <td>
                                            <input type="text" name="competencies[0][competency]"
                                                class="form-control competency-name" placeholder="Competency">
                                        </td>
                                        <td>
                                            <select name="competencies[0][rating]" class="form-select competency-rating">
                                                <option value="">Select Rating</option>
                                                <option value="1">1 - Poor</option>
                                                <option value="2">2 - Fair</option>
                                                <option value="3">3 - Good</option>
                                                <option value="4">4 - Very Good</option>
                                                <option value="5">5 - Excellent</option>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="competencies[0][example]" class="form-control competency-example" rows="1"
                                                placeholder="Provide examples that reflect your roles..."></textarea>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm add-competency-row">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm remove-competency-row"
                                                style="display: none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- E. Challenges / Difficulties -->
        <div id="challengesSection" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold">E.</span> Challenges / Difficulties
                    </span>
                </div>

                <div class="card-body">
                    <form id="groupEForm" method="POST" action="{{ route('performance.challenge.store') }}">
                        @csrf
                        <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">

                        <table class="table table-bordered" id="challenges-table">
                            <thead>
                                <tr>
                                    <th style="width: 45%">Challenge / Difficulty Faced</th>
                                    <th style="width: 45%">Result / Outcome</th>
                                    <th style="width: 10%" class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody id="challenges-body">
                                @forelse ($challenges as $index => $challenge)
                                    <tr class="challenge-row" data-row-index="{{ $index }}"
                                        data-id="{{ $challenge->id }}">
                                        <td>
                                            <textarea name="challenges[{{ $index }}][challenge]" class="form-control" rows="2">{{ $challenge->challenge }}</textarea>
                                            <input type="hidden" name="challenges[{{ $index }}][id]"
                                                value="{{ $challenge->id }}">
                                        </td>

                                        <td>
                                            <textarea name="challenges[{{ $index }}][result]" class="form-control" rows="2">{{ $challenge->result }}</textarea>
                                        </td>

                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm add-challenge-row">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm remove-challenge-row">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="challenge-row" data-row-index="0">
                                        <td>
                                            <textarea name="challenges[0][challenge]" class="form-control" rows="2"></textarea>
                                        </td>
                                        <td>
                                            <textarea name="challenges[0][result]" class="form-control" rows="2"></textarea>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm add-challenge-row">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm remove-challenge-row"
                                                style="display:none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="employeeComments" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupHForm">
                @foreach ($groupHQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">F.</span>
                                <span>
                                    Employee Comments
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div>
                                <textarea name="{{ 'question_' . $question->id }}" id="{{ 'question_' . $question->id }}" style="width: 50%"
                                    rows="7">{{ $performanceReview->getAnswer($question->id) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-sm btn-outline-primary"
                                style="float: right">Save</button>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>

    </section>

    <section>
        @if ($performanceReview->status_id == config('constant.RETURNED_STATUS'))
            <div class="col-lg-6">
                <div class="row mb-2 border p-3">
                    <div>
                        <div class="d-flex align-items-start h-100">
                            <span class="fw-bold" style="text-decoration: underline">Remarks:</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span>{{ $performanceReview->getLatestRemark() }}</span>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <section>
        <div style="float: right">
            <a onclick="validateForm()" type="button" class="btn btn-sm btn-success">Submit</a>
            <a href="{{ route('performance.index') }}" type="button" class="btn btn-sm btn-danger">Cancel</a>
        </div>
        <br><br>
    </section>

    <!-- Key Goal Modal -->
    <div class="modal fade" id="keyGoalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6" id="keyGoalModalTitle">Add Key Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="keyGoalForm">
                    <div class="modal-body">

                        <input type="hidden" name="key_goal_id" id="key_goal_id">

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Objective</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="title" id="title" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Output / Deliverable</label>
                            </div>
                            <div class="col-lg-9">
                                <textarea class="form-control" name="output_deliverables" id="output_deliverables" rows="3"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                    @csrf
                </form>
            </div>
        </div>
    </div>

@stop
