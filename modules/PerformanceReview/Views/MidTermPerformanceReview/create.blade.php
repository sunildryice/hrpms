@extends('layouts.container')

@section('title', 'Mid-Term Performance Review Form')

@section('page_js')
    <script type="text/javascript">
        let isGroupBFormSaved = false;
        let isGroupCFormSaved = false;
        let isGroupHFormSaved = false;
        let isGroupJFormSaved = false;

        $(function() {
            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');
            const performanceReview = @json($performanceReview);



            $('#add-key-goal').click(async function(e) {
                e.preventDefault();
                let url = $(this).attr('data-href');

                let successCallback = function(response) {
                    toastr.success(response.message || 'Key Goal added successfully', 'Success', {
                        timeOut: 2000
                    });
                    getNewKeyGoals(); // Refresh the additional key goals table
                };

                let errorCallback = function(response) {
                    toastr.error(response.message || 'Key Goal could not be added', 'Error', {
                        timeOut: 2000
                    });
                };

                // Show SweetAlert with two input fields
                const {
                    value: formValues
                } = await Swal.fire({
                    title: 'Add New Key Goal',
                    html: `
            <div class="mb-3">
                <label for="swal-title" class="form-label fw-bold">Objective</label>
                <input id="swal-title" class="swal2-input" placeholder="Objective" />
            </div>
            <div>
                <label for="swal-output" class="form-label fw-bold">Output / Deliverable</label>
                <input id="swal-output" class="swal2-input" placeholder="Output / Deliverable" />
            </div>
        `,
                    focusConfirm: false,
                    showCancelButton: true,
                    preConfirm: () => {
                        const title = document.getElementById('swal-title').value.trim();
                        const output = document.getElementById('swal-output').value.trim();

                        if (!title) {
                            Swal.showValidationMessage('Objective / Title is required');
                            return false;
                        }

                        return {
                            title,
                            output
                        };
                    }
                });

                if (formValues) {
                    ajaxSubmit(url, 'POST', {
                        title: formValues.title,
                        output_deliverables: formValues.output,
                        performance_review_id: performanceReview.id,
                        type: 'current'
                    }, successCallback, errorCallback);
                }
            });

            $('#keygoal-body').on('click', '.edit-key-goal', async function(e) {
                e.preventDefault();
                let inputValue = $(this).attr('data-title');
                let id = $(this).attr('data-id');
                let url = $(this).attr('data-href');
                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    getNewKeyGoals();
                };
                let errorCallback = function(response) {
                    {{-- console.log(response.mesasge) --}}
                    toastr.error('Key Goals could not be updated', 'Error', {
                        timeOut: 2000
                    });
                }
                const {
                    value: field
                } = await Swal.fire({
                    title: 'Edit Key Goal',
                    input: "text",
                    inputLabel: 'Title',
                    inputValue,
                    inputAttributes: {
                        name: 'title',
                    },
                    showCancelButton: true,
                });
                if (field) {
                    ajaxSubmit(url, 'POST', {
                        ['title']: field,
                        ['key_goal_id']: id,
                    }, successCallback, errorCallback);
                }
            });

            $('#keygoal-body').on('click', '.delete-key-goal', function(e) {
                e.preventDefault();
                let url = $(this).attr('data-href');
                let id = $(this).attr('data-id');
                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    getNewKeyGoals();
                };
                let errorCallback = function(response) {
                    {{-- console.log(response.mesasge) --}}
                    toastr.error('Key Goals could not be updated', 'Error', {
                        timeOut: 2000
                    });
                }

                ajaxSweetAlert(url, 'POST', {
                    ['keyGoalId']: id,
                }, 'Delete Key Goal', successCallback, errorCallback);
            });

            function getNewKeyGoals() {
                $.ajax({
                    type: 'get',
                    url: `${baseUrl}/api/performance/${performanceReview.id}/keygoals`,
                    success: function(response) {
                        const newRowContent = []
                        if (response?.keyGoals.length) {
                            $('#new-keyGoalTable').show();
                            $('#keygoal-body').html('');
                            let newRowContent = '';
                            response.keyGoals.forEach((goal) => {
                                newRowContent += `
                                <tr>
                                    <td>
                                        <span style="width: 100%">${goal.title}</span>
                                        <a class="edit-key-goal text-primary" href="#"  data-href="{{ route('performance.keygoal.update') }}" data-title="${goal.title ?? ''}" data-id="${goal.id}"u><i class="bi bi-pencil-square"></i></a>
                                        <a class="delete-key-goal text-danger" href="#"  data-href="{{ route('performance.keygoal.destroy') }}" data-id="${goal.id}"><i class="bi bi-trash"></i></a>
                                    </td>
                                    <td>
                                        <textarea style="width:100%" name="keygoal_employee_${goal.id}" id="keygoal_employee_${goal.id}" rows="2">${goal.description_employee ?? ''}</textarea>
                                    </td>
                                    <td>
                                        <span style="width: 100%">${goal.description_supervisor ?? ''}</span>
                                    </td>
                                </tr>
                            `;
                            })
                            $('#keygoal-body').html(newRowContent);
                        } else {
                            $('#new-keyGoalTable').hide();
                        }
                    },
                    error: function(data) {
                        // toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
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

            $('#groupBForm').on('submit', function(e) {
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
                isGroupBFormSaved = true;
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

            // GROUP C - KEY GOALS FORM 
            $('#groupCForm').on('submit', function(e) {
                e.preventDefault();

                let rows = $('#keyGoalTable tbody tr, #new-keyGoalTable tbody tr');

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

                    updateKeyGoal(id, title, majorActivities, '', 'current', status, remarks, output);
                });

                isGroupCFormSaved = true;

                toastr.success('Key Goals saved successfully', 'Success', {
                    timeOut: 1000
                });
            });

            $('#groupEForm').on('submit', function(e) {
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
                    $('#keyGoalTable').append(data.html);
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
            let data = $('#groupBForm').serializeArray();
            let filled = true;
            data.every(element => {
                if (element.value == '') {
                    filled = false;
                    return false;
                }
                return true;
            });
            // console.log('b',isGroupBFormSaved,'c',isGroupCFormSaved,'h',isGroupHFormSaved,'j',isGroupJFormSaved)

            if (filled && isGroupBFormSaved && isGroupCFormSaved && isGroupHFormSaved && isGroupJFormSaved) {
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
                            Employee and Supervisor Details
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

        <div id="employeeFeedbackForThisReviewPeriod" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupBForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">B.</span>
                            <span>
                                Employee Feedback For This Review Period
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        @foreach ($groupBQuestions as $question)
                            @if (!$loop->first)
                                <hr>
                            @endif
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="{{ 'question_' . $question->id }}"
                                        class="fw-bold">{{ $question->question }}</label>
                                </div>
                                <div class="col-lg-5">
                                    <textarea name="{{ 'question_' . $question->id }}" id="{{ 'question_' . $question->id }}"
                                        data-question-id="{{ $question->id }}" style="width: 100%;" rows="5">{{ $performanceReview->getAnswer($question->id) }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="keyGoalsReview" class="mb-3">
            <form action="{{ route('performance.keygoal.update') }}" method="POST" id="groupCForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title d-flex justify-content-between">
                            <span class="fw-bold">C.
                                Key Goals Review
                            </span>
                            <button class="btn btn-primary" id="add-key-goal"
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

                        <table id="new-keyGoalTable"
                            style="width: 100%;@if (!$newKeyGoals->count()) display:none @endif">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 18%">(Additional Objective)</th>
                                    <th rowspan="2" style="width: 15%">(Additional Output / Deliverable)</th>
                                    <th rowspan="2" style="width: 22%">Major Activities</th>
                                    <th colspan="2">Achievement against output / deliverable</th>
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
                                            <a class="edit-key-goal text-primary" href="#"
                                                data-href="{{ route('performance.keygoal.update') }}"
                                                data-title="{{ $keygoal->title }}" data-id="{{ $keygoal->id }}"u><i
                                                    class="bi bi-pencil-square"></i></a>
                                            <a class="delete-key-goal text-danger" href="#"
                                                data-href="{{ route('performance.keygoal.destroy') }}"
                                                data-id="{{ $keygoal->id }}"><i class="bi bi-trash"></i></a>
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

        <div id="professionalDevelopmentPlan" class="mb-3">
            <div class="card">
                <div class="card-header fw-bold">
                    <span class="card-title">
                        <span class="fw-bold"></span>
                        <span>
                            Professional Development Plan
                        </span>
                    </span>
                </div>
                <div class="card-body">
                    @php
                        $devPlans = $keyGoalReview
                            ->answers()
                            ->whereHas('performanceReviewQuestion', fn($q) => $q->where('group', 'E'))
                            ->get();
                    @endphp

                    @if ($devPlans->isEmpty())
                        <div class="text-center text-muted py-4">
                            No professional development plan has been added yet.
                        </div>
                    @else
                        <table style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="width: 5%">SN</th>
                                    <th class="col-plan">Development Plan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($devPlans as $plan)
                                    <tr class="devplan-row readonly">
                                        <td class="sn">{{ $loop->iteration }}</td>
                                        <td class="col-plan readonly-cell">
                                            {{ $plan->answer }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div id="strengthsAndAreasForGrowth" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupEForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">D.</span>
                            <span>
                                Strengths and Areas for Growth (to be completed by supervisor)
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        @foreach ($groupEQuestions as $question)
                            @if (!$loop->last)
                                @if (!$loop->first)
                                    <hr>
                                @endif
                                <div class="row">
                                    <div class="col-lg-5">
                                        <label for="{{ 'question_' . $question->id }}"
                                            class="fw-bold">{{ $question->question }}</label>
                                    </div>
                                    <div class="col-lg-5">
                                        <span
                                            style="width: 100%">{{ $performanceReview->getAnswer($question->id) }}</span>
                                        {{-- <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 100%;" rows="3">{{$performanceReview->getAnswer($question->id)}}</textarea> --}}
                                    </div>
                                </div>
                            @endif

                            {{-- @if ($loop->last)
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <label for="{{'question_'.$question->id}}" class="fw-bold">Professional Development Plan</label>
                                        </div>
                                        <div class="col-lg-5">
                                            <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 100%;" rows="3">{{$professionalDevelopmentPlan}}</textarea>
                                        </div>
                                    </div>
                                @endif --}}
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="d-none btn btn-sm btn-outline-primary"
                            style="float: right">Save</button>
                    </div>
                </div>
            </form>
        </div>



        <div id="employeeComments" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupHForm">
                @foreach ($groupHQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">E.</span>
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

        <div id="supervisorComments" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupIForm">
                @foreach ($groupIQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">F.</span>
                                <span>
                                    {{ $question->question }}
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div>
                                <span style="width: 100%">{{ $performanceReview->getAnswer($question->id) }}</span>
                                {{-- <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 50%" rows="7">{{$performanceReview->getAnswer($question->id)}}</textarea> --}}
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="d-none btn btn-sm btn-outline-primary"
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
                                <span class="fw-bold">G.</span>
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

@stop
