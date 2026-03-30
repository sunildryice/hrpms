@extends('layouts.container')

@section('title', 'Annual Performance Review Form')

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

            // KEY GOAL ADD BUTTON 
            $('#add-key-goal').click(function(e) {
                e.preventDefault();

                $('#keyGoalForm')[0].reset();
                $('#key_goal_id').val('');
                $('#keyGoalModalTitle').text('Add New Key Goal');

                $('#keyGoalModal').modal('show');
            });

            // Edit Key Goal (for additional/new key goals)
            $('#keygoal-body').on('click', '.edit-key-goal', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let row = $(this).closest('tr');
                let title = row.find('td:first-child span').first().text().trim();
                let output = row.find('td:nth-child(2)').text().trim();

                $('#key_goal_id').val(id);
                $('#title').val(title);
                $('#output_deliverables').val(output);

                $('#keyGoalModalTitle').text('Edit Key Goal');
                $('#keyGoalModal').modal('show');
            });

            // Delete Key Goal
            $('#keygoal-body').on('click', '.delete-key-goal', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let url = $(this).data('href');

                ajaxSweetAlert(url, 'POST', {
                    keyGoalId: id
                }, 'Delete Key Goal', function(res) {
                    toastr.success(res.message || 'Key goal deleted successfully');
                    $('.preloader').show();
                    location.reload();
                });
            });

            // Key Goal Form Submit (Add/Edit)
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
                    title: $('#title').val().trim(),
                    output_deliverables: $('#output_deliverables').val().trim(),
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
                        toastr.success(res.message || 'Key Goal saved successfully');
                        $('#keyGoalModal').modal('hide');
                        $('.preloader').show();
                        location.reload();
                    },
                    error: function(err) {
                        console.error(err);
                        toastr.error('Something went wrong while saving key goal');
                    }
                });
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

                // Update each key goal via AJAX
                rows.each(function() {
                    let row = $(this);
                    let id = row.data('keygoal-id') ||
                        row.find('textarea, select').first().attr('name')?.split('_').pop();

                    if (!id || isNaN(id)) return;

                    let majorActivities = row.find(`[name="major_activities_employee_${id}"]`)
                        .val() || '';
                    let status = row.find(`[name="status_${id}"]`).val() || '';
                    let remarks = row.find(`[name="remarks_employee_${id}"]`).val() || '';
                    let title = row.find('td:first-child').text().trim() || '';
                    let output = row.find('td:nth-child(2)').text().trim() || '';

                    updateKeyGoal(id, title, majorActivities, '', 'current', status, remarks,
                        output);
                });

                isGroupBFormSaved = true; 
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

            getKeyGoalsEmployee();
            getKeyGoalsSupervisor();
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
        <!-- A. Employee and Line Manager Details -->
        @include('PerformanceReview::Partials.employeeDetails')

        <!-- B, C, D, E, F Forms -->
        @include('PerformanceReview::Partials.fillForms')

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

    <!-- Key Goal Modal -->
    @include('PerformanceReview::Partials.keyGoalModal')

@stop
