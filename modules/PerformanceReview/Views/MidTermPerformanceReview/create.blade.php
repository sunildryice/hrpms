@extends('layouts.container')

@section('title', 'Mid-Term Performance Review Form')

@section('page_js')
    <script type="text/javascript">
        let isGroupBFormSaved = false;
        let isGroupCFormSaved = false;
        let isGroupDFormSaved = false;
        let isGroupEFormSaved = false;
        let isGroupFFormSaved = false;

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

        });

        // GROUP F - EMPLOYEE COMMENTS 
        $('#groupFForm').on('submit', function(e) {
            e.preventDefault();

            let comments = $('#employee_comments').val().trim();

            if (!comments) {
                toastr.error('Please write your comments before saving.', 'Validation Error');
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('performance.employee.comments.store') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    performance_review_id: "{{ $performanceReview->id }}",
                    employee_comments: comments
                },
                success: function(response) {
                    if (response.type === 'success') {
                        toastr.success('Employee comments saved successfully!', 'Success');
                    } else {
                        toastr.error(response.message || 'Failed to save comments.');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    toastr.error('Something went wrong while saving your comments.');
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
            let isGroupFFormSaved = true;

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

            // F. Employee Comments
            const employeeComments = $('#employee_comments').val().trim();
            if (!employeeComments) {
                isGroupFFormSaved = false;
                $('#employee_comments').addClass('is-invalid');
                toastr.error('Please fill Employee Comments (Section F) before submitting.', 'Validation Error');
            } else {
                $('#employee_comments').removeClass('is-invalid');
            }

            if (isGroupBFormSaved && isGroupCFormSaved && isGroupDFormSaved &&
                isGroupEFormSaved && isGroupFFormSaved) {

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

        <!-- A. Employee and Line Manager Details -->
        @include('PerformanceReview::Partials.employeeDetails')

        <!-- B, C, D, E, F Forms -->
        @include('PerformanceReview::Partials.fillForms')

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
    @include('PerformanceReview::Partials.keyGoalModal')

@stop
