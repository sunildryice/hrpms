@extends('layouts.container')

@section('title', 'Annual Performance Review Form')

@section('page_js')
    <script type="text/javascript">
        let isGroupBFormSaved = false;
        let isGroupCFormSaved = false;
        let isGroupHFormSaved = false;
        let isGroupJFormSaved = false;

        $(document).ready(function() {

            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');

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


            $('#groupBForm').on('submit', function(e) {
                e.preventDefault();

                let isValid = true;

                $('#keyGoalTable tbody tr').each(function() {
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

                // Save each row individually
                $('#keyGoalTable tbody tr').each(function() {
                    const keygoalId = $(this).data('keygoal-id');
                    const majorActivities = $(this).find('.major-activities').val();
                    const status = $(this).find('.status-dropdown').val();
                    const remarks = $(this).find('.remarks-employee').val();

                    updateKeyGoal(keygoalId, '', majorActivities, '', 'current', status, remarks);
                });

                isGroupCFormSaved = true;
                toastr.success('Key Goals saved successfully!', 'Success');
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

            // ====================== D. Core Competencies ======================
            let competencyRowIndex = 0;

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

                const newRow = `
        <tr class="competency-row" data-row-index="${competencyRowIndex}">
            <td>
                <input type="text" 
                       name="competencies[${competencyRowIndex}][competency]" 
                       class="form-control competency-name" 
                       placeholder="Competency"
                       required>
            </td>
            <td>
                <select name="competencies[${competencyRowIndex}][rating]" 
                        class="form-select competency-rating">
                    <option value="">Select Rating</option>
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3">3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
            </td>
            <td>
                <textarea name="competencies[${competencyRowIndex}][example]" 
                    class="form-control competency-example" 
                    rows="3"
                    placeholder="Provide specific examples that reflect your roles..."></textarea>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-sm add-competency-row">
                    <i class="bi bi-plus"></i>
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm remove-competency-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;

                $('#competencies-body').append(newRow);
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

                // Optional: Basic validation
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
                    toastr.error('Please enter competency name for all rows.', 'Validation Error');
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


            $('#groupFForm').on('submit', function(e) {
                e.preventDefault();

                // Getting form action, method and data.
                var action = $(this).attr("action");
                var method = $(this).attr('method');
                let data = $(this).serializeArray();

                // Checking if any input field in the form is empty.
                let filled = false;
                data.every(element => {
                    if (element.value) {
                        filled = true;
                        return false;
                    }
                    return true;
                });
                if (!filled) {
                    toastr.error('Please complete the form.', 'Error', {
                        timeout: 2000
                    });
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value == 'on' ? 'true' : 'false';
                    saveAnswer(questionId, answer);
                });

                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            });

            $('#groupGForm').on('submit', function(e) {
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

                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
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

            $('#groupIForm').on('submit', function(e) {
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

                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            });


            $('#groupJForm').on('submit', function(e) {
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

                isGroupJFormSaved = true;

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

            getKeyGoalsEmployee();
            getKeyGoalsSupervisor();

            $(document).on('click', '.open-edit-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const editForm = document.getElementById('keyGoalEditForm');

                    const fv = FormValidation.formValidation(editForm, {
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: 'Title is required'
                                    }
                                }
                            }
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap5: new FormValidation.plugins.Bootstrap5(),
                            submitButton: new FormValidation.plugins.SubmitButton(),
                            icon: new FormValidation.plugins.Icon({
                                valid: 'bi bi-check2-square',
                                invalid: 'bi bi-x-lg',
                                validating: 'bi bi-arrow-repeat',
                            }),

                        }
                    }).on('core.form.valid', function(e) {
                        url = fv.form.action;
                        form = fv.form
                        data = $(form).serialize();
                        var successCallBack = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success('Key goal updated.', 'Success', {
                                timeOut: 2000
                            });
                            getKeyGoalsEmployee();
                            getKeyGoalsSupervisor();

                        }

                        var errorCallback = function(response) {
                            console.log(response);
                            toastr.error('Key goal cannot be updated.', 'Failed', {
                                timeOut: 2000
                            })
                        }

                        ajaxSubmit(url, 'PUT', data, successCallBack, errorCallback);
                    })
                })
            })

        });

        function addKeyGoal(event) {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.store') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
                    'title': $('#key_goal_input').val(),
                    'type': 'future'
                },
                success: function(data) {
                    $('#key_goal_input').val('');
                    getKeyGoalsEmployee();
                    getKeyGoalsSupervisor();
                    toastr.success('Key goal added.', 'Success', {
                        timeOut: 2000
                    });
                },
                error: function(data) {
                    toastr.error('Key goal could not be added.', 'Failed', {
                        timeOut: 2000
                    });
                }
            });
        }

        function deleteKeyGoal(id) {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.destroy') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'keyGoalId': id
                },
                success: function(data) {
                    $('#key_goal_input').val('');
                    getKeyGoalsEmployee();
                    getKeyGoalsSupervisor();
                    toastr.success('Key goal deleted.', 'Success', {
                        timeOut: 2000
                    });
                },
                error: function(data) {
                    toastr.error('Key goal could not be deleted.', 'Failed', {
                        timeOut: 2000
                    });
                }
            });
        }

        function updateKeyGoal(keyGoalId, title = '', majorActivities = '', descriptionSupervisor = '', type = 'current',
            status = '', remarks = '') {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.update') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'key_goal_id': keyGoalId,
                    'title': title,
                    'major_activities_employee': majorActivities,
                    'description_supervisor_annual': descriptionSupervisor,
                    'performance_review_id': "{{ $performanceReview->id }}",
                    'type': type,
                    'status': status,
                    'remarks_employee': remarks
                },
                success: function(data) {
                    // toastr.success('Key goal updated.', 'Success');
                },
                error: function(data) {
                    console.error(data);
                    // toastr.error('Failed to update key goal.');
                }
            });
        }

        function getKeyGoalsEmployee() {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.employee.get') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
                },
                success: function(data) {
                    $('#keygoal_employee').html('');
                    $('#keygoal_employee').html(data.goal);
                },
                error: function(data) {
                    // toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
                }

            });
        }

        function getKeyGoalsSupervisor() {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.supervisor.get') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
                },
                success: function(data) {
                    $('#keygoal_supervisor').html('');
                    $('#keygoal_supervisor').html(data.goal);
                },
                error: function(data) {
                    // toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
                }
            });
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
                    $('#keyGoalTable').append(data.html);
                },
                error: function(error) {
                    //
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

        function removeGoal(event, id) {
            event.target.parentElement.parentElement.parentElement.remove();
            deleteKeyGoal(id);
        }

        function checkEmpty(data) {
            let empty = false;
            data.every(element => {
                if (!element.value) {
                    empty = true;
                    return false;
                }
                return true;
            });
            return empty;
        }

        function validateForm() {
            let groupBData = $('#groupBForm').serializeArray();
            let groupCData = $('#groupCForm').serializeArray();
            let groupHData = $('#groupHForm').serializeArray();
            let groupJData = $('#groupJForm').serializeArray();
            isGroupBFormSaved = !checkEmpty(groupBData);
            isGroupCFormSaved = !checkEmpty(groupCData);
            isGroupHFormSaved = !checkEmpty(groupHData);
            isGroupJFormSaved = !checkEmpty(groupJData);
            if (isGroupBFormSaved && isGroupCFormSaved && isGroupHFormSaved && isGroupJFormSaved) {
                window.location.href = "{{ route('performance.submit', $performanceReview->id) }}";
            } else {
                toastr.warning('Please save the forms.', 'Warning', {
                    timeOut: 2000
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



    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
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
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
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

        <!-- B. Key Goals Review -->
        <div id="keyGoalsReview" class="mb-3">
            <form action="{{ route('performance.keygoal.update') }}" method="POST" id="groupBForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">B.</span>
                            Key Goals Review
                        </span>
                    </div>
                    <div class="card-body">
                        {{-- Annual Review --}}
                        <div class="card">
                            <div class="card-header fw-bold">Annual Review (Employee Input)</div>
                            <div class="card-body">
                                <table class="table" id="keyGoalTable">
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-sm btn-outline-primary float-end">Save</button>
                            </div>
                        </div>
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
                                <tr class="competency-row" data-row-index="0">
                                    <td>
                                        <input type="text" name="competencies[0][competency]"
                                            class="form-control competency-name"
                                            placeholder="Competency" required>
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
                                            placeholder="Provide specific examples that reflect your roles..."></textarea>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm add-competency-row">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-competency-row"
                                            style="display: none;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
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
                                <span class="fw-bold">H.</span>
                                <span>
                                    {{ $question->question }}
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

        <div id="acknowledgements" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupJForm">
                @foreach ($groupJQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">J.</span>
                                <span>
                                    {{ $question->question }}
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
                <div class="p-3 mb-2 border row">
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

@stop
