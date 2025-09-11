@extends('layouts.container')

@section('title', 'Annual Performance Review Form')

@section('page_js')


    <script type="text/javascript">
        let isGroupCFormSaved = false;
        let isGroupDFormSaved = false;
        let isGroupEFormSaved = false;
        let isGroupFFormSaved = false;
        let isGroupIFormSaved = false;

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#performance-review-index').addClass('active');

            $('#receiver').hide();
            let errors = {!! $errors !!};
            if (errors.receiver_id != undefined) {
                $('#receiver').show();
            }
            if ($('#status_id').val() == {{ config('constant.VERIFIED_STATUS') }}) {
                $('#receiver').show();
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
                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            });

            $('#groupCForm').on('change', 'textarea', function(e) {
                e.preventDefault();
                let keyGoalId = $(this).attr('name').split("_")[2];
                let titleElement = $(this).attr('name').split('_');
                let title = '';
                if (titleElement[1] == 'title') {
                    title = $(this).val();
                }
                let descriptionType = $(this).attr('name').split("_")[1] == 'employee' ?
                    'description_employee' : $(this).attr('name').split("_")[1] == 'supervisor' ?
                    'description_supervisor' : '';
                let description_employee = descriptionType == 'description_employee' ? $(this).val() : '';
                let description_supervisor = descriptionType == 'description_supervisor' ? $(this).val() :
                    '';
                let type = 'current';
                updateKeyGoal(keyGoalId, title, description_employee, description_supervisor, type);
            });

            $('#groupCForm').on('submit', function(e) {
                e.preventDefault();

                // Getting form action, method and data.
                var action = $(this).attr("action");
                var method = $(this).attr('method');
                let data = $(this).serializeArray();

                //Checking if any input field in the form is empty.
                let empty = false;
                data.every(element => {
                    if (!element.value) {
                        empty = true;
                        return false;
                    }
                    return true;
                });

                // Storing the form data.
                data.forEach(element => {
                    let keygoalId = element.name.split("_")[2];
                    let titleElement = element.name.split('_');
                    let title = '';
                    if (titleElement[1] == 'title') {
                        title = element.value;
                    }
                    let descriptionType = element.name.split("_")[1] == 'employee' ?
                        'description_employee' : element.name.split("_")[1] == 'supervisor' ?
                        'description_supervisor' : '';
                    let description_employee = descriptionType == 'description_employee' ? element
                        .value : '';
                    let description_supervisor = descriptionType == 'description_supervisor' ?
                        element.value : '';
                    let type = 'current';
                    updateKeyGoal(keygoalId, title, description_employee, description_supervisor,
                        type);
                });

                if (!empty) {
                    isGroupCFormSaved = true;
                }

                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            });

            $('#groupDForm').on('change', 'textarea', function(e) {
                e.preventDefault();
                let questionId = $(this).attr('name').split("_")[1];
                let answer = $(this).val();
                if (questionId) {
                    saveAnswer(questionId, answer);
                }
            });

            $('#groupDForm').on('submit', function(e) {
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
                isGroupDFormSaved = true;
                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            });

            $('#groupEForm').on('change', 'textarea', function(e) {
                e.preventDefault();
                let questionId = $(this).attr('name').split("_")[1];
                let answer = $(this).val();
                if (questionId) {
                    saveAnswer(questionId, answer);
                }
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
                isGroupEFormSaved = true;
                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            });

            //save on check
            $('#groupFForm').on('change', 'input[type="checkbox"]', function(e) {
                e.preventDefault();
                let data = $('#groupFForm').serializeArray();
                let questionId = $(this).attr('name').split("_")[1];
                // Checking if any input field in the form is empty.
                let filled = false;
                data.every(element => {
                    if (element.value) {
                        filled = true;
                        return false;
                    }
                    return true;
                });
                data = data.map(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value == 'on' ? 'true' : 'false';
                    return {
                        'question_id': questionId,
                        'answer': answer
                    };
                })
                saveAnswerAll(data);
                isGroupFFormSaved = true;
                if (!filled) {
                    isGroupFFormSaved = false;
                }

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
                isGroupFFormSaved = true;
                toastr.success('Answer saved', 'Success', {
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
                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
            });

            $('#groupIForm').on('change', 'textarea', function(e) {
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
                isGroupIFormSaved = true;
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
                toastr.success('Form saved', 'Success', {
                    timeOut: 1000
                });
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

        $('#status_id').on('change', function() {
            let status = $('#status_id').val();
            if (status == {{ config('constant.VERIFIED_STATUS') }}) {
                $('#receiver').show();
            } else {
                $('#receiver').hide();
            }
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

        function updateKeyGoal(keyGoalId, title = '', descriptionEmployee = '', descriptionSupervisor = '', type) {
            $.ajax({
                type: 'POST',
                url: "{{ route('performance.keygoal.update') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'key_goal_id': keyGoalId,
                    'title': title,
                    'performance_review_id': "{{ $performanceReview->id }}",
                    'description_employee_annual': descriptionEmployee,
                    'description_supervisor_annual': descriptionSupervisor,
                    'type': type
                },
                success: function(data) {
                    // toastr.success('Key goal updated.', 'Success', {timeOut: 2000});
                },
                error: function(data) {
                    // toastr.error('Key goal could not be updated.', 'Failed', {timeOut: 2000});
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
                    $('#keygoal_employee').html.empty;
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
                    $('#keygoal_supervisor').html.empty;
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
            return $.ajax({
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
        }

        function saveAnswerAll(data) {
            return $.ajax({
                type: 'POST',
                url: "{{ route('performance.answer.store.all') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'performance_review_id': "{{ $performanceReview->id }}",
                    data
                },
                success: function(data) {
                    toastr.success(data.question + ' is saved', 'Success', {
                        timeOut: 1000
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function validateForm() {
            if (isGroupCFormSaved && isGroupDFormSaved && isGroupEFormSaved && isGroupFFormSaved && isGroupIFormSaved) {
                window.location.href = "{{ route('performance.review.store') }}";
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
                            <a href="{{ route('performance.review.index') }}"
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
                                    <span class="fw-bold">Supervisor Name</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getSupervisorName() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Supervisor Title</span>
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
                                    <span class="fw-bold">Technical Supervisor's Name</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getTechnicalSupervisorName() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="fw-bold">Technical Supervisor's Title</span>
                                </div>
                                <div class="col-lg-6">
                                    <span>{{ $performanceReview->getTechnicalSupervisorTitle() }}</span>
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
                        <div class="row">
                            @if ($midTermReview)
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Mid-Term Review
                                        </div>
                                        <div class="card-body">
                                            @foreach ($groupBQuestions as $question)
                                                @if (!$loop->first)
                                                    <hr>
                                                @endif
                                                <div class="row">
                                                    <label class="fw-bold">{{ $question->question }}</label>
                                                    <span
                                                        class="mt-2">{{ $midTermReview->getAnswer($question->id) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md">
                                <div class="card">
                                    <div class="card-header fw-bold">
                                        Annual Review
                                    </div>
                                    <div class="card-body">
                                        @foreach ($groupBQuestions as $question)
                                            @if (!$loop->first)
                                                <hr>
                                            @endif
                                            <div class="row">
                                                <label class="fw-bold">{{ $question->question }}</label>
                                                <span
                                                    class="mt-2">{{ $performanceReview->getAnswer($question->id) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card-footer">
                            <button type="submit" class="d-none btn btn-sm btn-outline-primary" style="float: right">Save</button>
                        </div> --}}
                </div>
            </form>
        </div>

        <div id="keyGoalsReview" class="mb-3">
            <form action="{{ route('performance.keygoal.update') }}" method="POST" id="groupCForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">C.</span>
                            <span>
                                Key Goals Review
                            </span>
                            <button class="d-none" type="button" onclick="appendKeyGoal()"><i
                                    class="bi bi-plus"></i></button>
                        </span>
                    </div>
                    <div class="card-body">

                        @if ($midTermReview)
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Mid-Term Review
                                </div>
                                <div class="card-body">
                                    <table id="keyGoalTable" style="width: 100%">
                                        <tr>
                                            <th style="width: 20%">Key Goals</th>
                                            <th style="width: 40%">To be completed by Employee</th>
                                            <th style="width: 40%">To be completed by Supervisor</th>
                                        </tr>

                                        @foreach ($keygoals as $keygoal)
                                            <tr>
                                                <td>{{ $keygoal->title }}</td>
                                                <td>{{ $keygoal->description_employee }}</td>
                                                <td>{{ $keygoal->description_supervisor }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-header fw-bold">
                                Annual Review
                            </div>
                            <div class="card-body">
                                <table id="keyGoalTable" style="width: 100%">
                                    <tr>
                                        <th style="width: 20%">Key Goals</th>
                                        <th style="width: 40%">To be completed by Employee</th>
                                        <th style="width: 40%">To be completed by Supervisor</th>
                                    </tr>
                                    @foreach ($keygoals as $keygoal)
                                        <tr>
                                            <td>{{ $keygoal->title }}</td>
                                            <td>{{ $keygoal->description_employee_annual }}</td>
                                            <td>
                                                {{-- <input style="width: 100%" type="text" name="{{'keygoal_supervisor_'.$keygoal->id}}" id="{{'keygoal_supervisor_'.$keygoal->id}}" value="{{$keygoal->description_supervisor_annual}}"> --}}
                                                <textarea style="width:100%" name="{{ 'keygoal_supervisor_' . $keygoal->id }}"
                                                    id="{{ 'keygoal_supervisor_' . $keygoal->id }}" rows="2">{{ $keygoal->description_supervisor_annual }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>


                        {{-- <table id="keyGoalTable">
                                <tr>
                                    <th style="width: 24%">(Insert key goals agreed upon during previous performance review)</th>
                                    <th style="width: 38%">To be completed by Employee</th>
                                    <th style="width: 38%">To be completed by Supervisor</th>
                                </tr>

                                @foreach ($currentKeyGoals as $keygoal)
                                    <tr>
                                        <td>
                                            <input disabled style="width: 100%" type="text" name="{{'keygoal_title_'.$keygoal->id}}" id="{{'keygoal_title_'.$keygoal->id}}" value="{{$keygoal->title}}">
                                        </td>
                                        <td>
                                            <input disabled style="width: 100%" type="text" name="{{'keygoal_employee_'.$keygoal->id}}" id="{{'keygoal_employee_'.$keygoal->id}}" value="{{$keygoal->description_employee}}">
                                        </td>
                                        <td>
                                            <input style="width: 100%" type="text" name="{{'keygoal_supervisor_'.$keygoal->id}}" id="{{'keygoal_supervisor_'.$keygoal->id}}" value="{{$keygoal->description_supervisor}}">
                                        </td>
                                    </tr>
                                @endforeach
                            </table> --}}
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
                    <div class="row">
                        <div class="col-lg-6">
                            <span>{{ $professionalDevelopmentPlan }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="keySkillsEvaluation" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupDForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">D.</span>
                            <span>
                                Key Skills Evaluation (to be completed by supervisor)
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        @foreach ($groupDQuestions as $question)
                            @if (!$loop->first)
                                <hr>
                            @endif
                            <div class="row">
                                <div class="col-lg-5">
                                    <label for="{{ 'question_' . $question->id }}"
                                        class="fw-bold">{{ $question->question }}</label>
                                </div>
                                <div class="col-lg-5">
                                    <textarea name="{{ 'question_' . $question->id }}" id="{{ 'question_' . $question->id }}" style="width: 100%;"
                                        rows="2">{{ $performanceReview->getAnswer($question->id) }}</textarea>
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

        <div id="strengthsAndAreasForGrowth" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupEForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">E.</span>
                            <span>
                                Strengths and Areas for Growth (to be completed by supervisor)
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if ($midTermReview)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Mid-Term Review
                                        </div>
                                        <div class="card-body">
                                            @foreach ($groupEQuestions as $question)
                                                @if (!$loop->last)
                                                    @if (!$loop->first)
                                                        <hr>
                                                    @endif
                                                    <div class="row">
                                                        <label class="fw-bold">{{ $question->question }}</label>
                                                        <span
                                                            class="mt-2">{{ $midTermReview->getAnswer($question->id) }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header fw-bold">
                                        Annual Review
                                    </div>
                                    <div class="card-body">
                                        @foreach ($groupEQuestions as $question)
                                            @if (!$loop->last)
                                                @if (!$loop->first)
                                                    {{-- <hr> --}}
                                                @endif
                                                <div class="mb-2 row">
                                                    <label class="mb-2 fw-bold">{{ $question->question }}</label>
                                                    <textarea name="{{ 'question_' . $question->id }}" id="{{ 'question_' . $question->id }}"
                                                        style="width: 95%; margin-left: 10px" rows="3">{{ $performanceReview->getAnswer($question->id) }}</textarea>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="overallPerformanceEvaluation" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupFForm">
                <div class="card">
                    <div class="card-header fw-bold">
                        <span class="card-title">
                            <span class="fw-bold">F.</span>
                            <span>
                                Overall Performance Evaluation (to be completed by supervisor)
                            </span>
                        </span>
                    </div>
                    <div class="card-body">
                        <table style="">
                            <tr>
                                @foreach ($groupFQuestions as $question)
                                    <th style="width: 20%">{{ $question->question }}</th>
                                @endforeach
                            </tr>

                            <tr>
                                @foreach ($groupFQuestions as $question)
                                    <td style="font-style: italic">{{ $question->description }}</td>
                                @endforeach
                            </tr>

                            <tr>
                                @php
                                    $counter = 5;
                                @endphp
                                @foreach ($groupFQuestions as $question)
                                    <td style="text-align: center">
                                        <input type="hidden" name="{{ 'question_' . $question->id }}"
                                            id="{{ 'question_' . $question->id }}">
                                        <input type="checkbox" name="{{ 'question_' . $question->id }}"
                                            id="{{ 'question_' . $question->id }}"
                                            {{ $performanceReview->getAnswer($question->id) == 'true' ? 'checked' : '' }}>
                                        <label for="{{ 'question_' . $question->id }}">{{ $counter-- }}</label>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{-- <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button> --}}
                    </div>
                </div>
            </form>
        </div>

        <div id="identifyKeyGoals" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupGForm">
                @foreach ($groupGQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">G.</span>
                                <span>{{ $question->question }}</span>
                            </span>
                        </div>
                        <div class="card-body">

                            <div class="row">

                                <div class="mb-2 row">
                                    <div class="col-lg-4 d-flex align-items-center">
                                        <textarea class="form-control" style="width:100%" name="key_goal_input" id="key_goal_input" rows="2"
                                            placeholder="Key goal"></textarea>
                                    </div>
                                    <div class="col d-flex align-items-center">
                                        <button type="button" onclick="addKeyGoal(event)"
                                            class="btn btn-primary btn-sm">Add</button>
                                    </div>
                                </div>

                                <hr>

                                <div class="col-lg-6">
                                    <div class="mb-2 row">
                                        <span class="fw-bold">Filled by Employee:</span>
                                    </div>
                                    <div class="row">
                                        @foreach ($futureKeyGoals->where('created_by', $performanceReview->requester_id) as $key => $item)
                                            <span>{{ ++$key . '. ' . $item->title }}</span>
                                        @endforeach
                                    </div>
                                    {{-- <div class="p-2 row" id="keygoal_employee"></div> --}}
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-2 row">
                                        <span class="fw-bold">Filled by Supervisor:</span>
                                    </div>
                                    {{-- <div class="row">
                                            @foreach ($futureKeyGoals->where('created_by', '!=', $performanceReview->requester_id) as $key => $item)
                                                <span>{{++$key.'. '.$item->title}}</span>
                                            @endforeach
                                        </div> --}}
                                    <div class="p-2 row" id="keygoal_supervisor"></div>
                                </div>
                            </div>



                            {{-- <div>
                                    <textarea oninput="handleInput(event)" name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 50%" rows="7">{{$performanceReview->getAnswer($question->id)}}</textarea>
                                </div> --}}
                        </div>
                        {{-- <div class="card-footer">
                                <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                            </div> --}}
                    </div>
                @endforeach
            </form>
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
                            <div class="row">
                                @if ($midTermReview)
                                    <div class="col-md">
                                        <div class="card">
                                            <div class="card-header fw-bold">Mid-Term Review</div>
                                            <div class="card-body">
                                                <span>{{ $midTermReview->getAnswer($question->id) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-header fw-bold">Annual Review</div>
                                        <div class="card-body">
                                            <span>{{ $performanceReview->getAnswer($question->id) }}</span>
                                        </div>
                                    </div>
                                </div>
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

        <div id="supervisorComments" class="mb-3">
            <form action="{{ route('performance.answer.store') }}" method="POST" id="groupIForm">
                @foreach ($groupIQuestions as $question)
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">I.</span>
                                <span>
                                    {{ $question->question }}
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if ($midTermReview)
                                    <div class="col-md">
                                        <div class="card">
                                            <div class="card-header fw-bold">Mid-Term Review</div>
                                            <div class="card-body">
                                                <span>{{ $midTermReview->getAnswer($question->id) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-header fw-bold">Annual Review</div>
                                        <div class="card-body">
                                            {{-- <span>{{$performanceReview->getAnswer($question->id)}}</span> --}}
                                            <textarea name="{{ 'question_' . $question->id }}" id="{{ 'question_' . $question->id }}" style="width: 100%"
                                                rows="7">{{ $performanceReview->getAnswer($question->id) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div>
                                    <textarea name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 50%" rows="7">{{$performanceReview->getAnswer($question->id)}}</textarea>
                                </div> --}}
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
                            <div class="row">
                                @if ($midTermReview)
                                    <div class="col-md">
                                        <div class="card">
                                            <div class="card-header fw-bold">Mid-Term Review</div>
                                            <div class="card-body">
                                                <span>{{ $midTermReview->getAnswer($question->id) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md">
                                    <div class="card">
                                        <div class="card-header fw-bold">Annual Review</div>
                                        <div class="card-body">
                                            <span>{{ $performanceReview->getAnswer($question->id) }}</span>
                                        </div>
                                    </div>
                                </div>
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

    </section>

    {{-- <section>
            <div style="float: right">
                <a href="{{route('performance.submit', $performanceReview->id)}}" type="button" class="btn btn-sm btn-success">Submit</a>
                <a href="{{route('performance.index')}}" type="button" class="btn btn-sm btn-danger">Cancel</a>
            </div>
            <br><br>
        </section> --}}

    <section>
        <div class="card">
            <div class="card-header fw-bold">
                Performance Review Process
            </div>
            <form action="{{ route('performance.review.store') }}" id="performanceReviewProcessForm" method="post"
                enctype="multipart/form-data" autocomplete="off"
                onsubmit="return confirm('Have you saved all the forms? Are you sure to submit?');">
                <input type="hidden" name="performance_review_id" value="{{ $performanceReview->id }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            @foreach ($performanceReview->logs as $log)
                                <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
                                    <div width="40" height="40" class="mr-3 rounded-circle user-icon">
                                        <i class="bi-person"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                                                <span class="me-2">{{ $log->createdBy->getFullName() }}</span>
                                                <span class="badge bg-primary c-badge">
                                                    {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                </span>
                                            </div>
                                            <small
                                                title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <p class="mt-1 mb-0 text-justify comment-text">
                                            {{ $log->log_remarks }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="status_id" class="form-label required-label">Status </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="status_id" id="status_id" class="select2 form-control"
                                        data-width="100%">
                                        <option value="">Select a Status</option>
                                        <option value="{{ config('constant.RETURNED_STATUS') }}"
                                            {{ old('status_id') == config('constant.RETURNED_STATUS') ? 'selected' : '' }}>
                                            Return to Employee</option>
                                        <option value="{{ config('constant.VERIFIED_STATUS') }}"
                                            {{ old('status_id') == config('constant.VERIFIED_STATUS') ? 'selected' : '' }}>
                                            Verify</option>
                                        @if ($authUser->employee->designation_id == 9)
                                            <option value="{{ config('constant.APPROVED_STATUS') }}"
                                                @if (old('status_id') == config('constant.APPROVED_STATUS')) selected @endif>
                                                Approve
                                            </option>
                                        @elseif(!$nextLineManagerExists && $authUser->can('approve-performance-review'))
                                            <option value="{{ config('constant.APPROVED_STATUS') }}"
                                                @if (old('status_id') == config('constant.APPROVED_STATUS')) selected @endif>
                                                Approve
                                            </option>
                                        @endif
                                    </select>
                                    @if ($errors->has('status_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="status_id">
                                                {!! $errors->first('status_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-2 row" id="receiver">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="receiver_id" class="form-label required-label">Send To </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="receiver_id" id="receiver_id" class="select2 form-control"
                                        data-width="100%">
                                        @foreach ($receivers as $receiver)
                                            @if ($loop->first)
                                                <option value="{{ $receiver->id }}"
                                                    {{ old('receiver_id') == $receiver->id ? 'selected' : (empty(old('receiver_id')) ? 'selected' : '') }}>
                                                    {{ $receiver->getFullName() }}</option>
                                            @else
                                                <option value="{{ $receiver->id }}">{{ $receiver->getFullName() }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('receiver_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="receiver_id">
                                                {!! $errors->first('receiver_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="log_remarks" class="form-label required-label">Remarks </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                        name="log_remarks">{{ old('log_remarks') }}</textarea>
                                    @if ($errors->has('log_remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {!! csrf_field() !!}
                        </div>
                    </div>
                </div>
                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                        Submit
                    </button>
                    <a href="{!! route('performance.review.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>

@stop
