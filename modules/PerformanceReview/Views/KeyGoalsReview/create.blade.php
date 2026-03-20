@extends('layouts.container')

@section('title', 'Set Key Goals')

@section('page_js')
    <script type="text/javascript">

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

        let isFormESaved = false;

        $(function() {
            $('#navbarVerticalMenu').find('#performance-employee-index').addClass('active');

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
                    toastr.error('Please complete the form.', 'Error', {timeout: 2000});
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value;
                    saveAnswer(questionId, answer);
                });

                isFormESaved = true;

                // toastr.success('Form saved', 'Success', {timeOut: 1000});
            });

            getKeyGoalsEmployee();
        });

        function addKeyGoal(event) {
            $.ajax({
                type: 'POST',
                url: "{{route('performance.keygoal.store')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'performance_review_id': "{{$performanceReview->id}}",
                    'title': $('#key_goal_input').val(),
                    'type': 'current'
                },
                success: function(data) {
                    $('#key_goal_input').val('');
                    toastr.success('Key goal added.', 'Success', {timeOut: 2000});
                    getKeyGoalsEmployee();
                },
                error: function(data) {
                    toastr.error('Key goal could not be added.', 'Failed', {timeOut: 2000});
                }
            });
        }

        function editKeyGoal(event, id) {
            $keygoalTitle = event.target.parentElement.parentElement.querySelector('#keygoal-title').innerText;
            $keygoalId = id;

            console.log('id', id);

            $('#key-goal-btn-add').hide();
            $('#key-goal-btn-update').show();

            $('#key_goal_input').val($keygoalTitle);
            $('#key-goal-btn-update').attr('data-keygoalid', id);

            console.log('key goal id during edit', $('#key-goal-btn-update').attr('data-keygoalid'));
        }

        function updateKeyGoal(event) {
            console.log('key goal id before update', $('#key-goal-btn-update').attr('data-keygoalid'));
            $.ajax({
                type: 'POST',
                url: "{{route('performance.keygoal.edit')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'title': $('#key_goal_input').val(),
                    'keyGoalId': $('#key-goal-btn-update').attr('data-keygoalid')
                },
                success: function(data) {
                    $('#key_goal_input').val('');
                    $('#key-goal-btn-add').show();
                    $('#key-goal-btn-update').hide();
                    $('#key-goal-btn-update').attr('data-keygoalid', '');
                    toastr.success('Key goal updated.', 'Success', {timeOut: 2000});
                    getKeyGoalsEmployee();
                },
                error: function(data) {
                    toastr.error('Key goal could not be updated.', 'Failed', {timeOut: 2000});
                }
            });
        }

        function deleteKeyGoal(id) {
            $.ajax({
                type: 'POST',
                url: "{{route('performance.keygoal.destroy')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'keyGoalId': id
                },
                success: function(data) {
                    $('#key_goal_input').val('');
                    toastr.success('Key goal deleted.', 'Success', {timeOut: 2000});
                    getKeyGoalsEmployee();
                    getKeyGoalsSupervisor();
                },
                error: function(data) {
                    toastr.error('Key goal could not be deleted.', 'Failed', {timeOut: 2000});
                }
            });
        }

        function getKeyGoalsEmployee() {
            $.ajax({
                type: 'POST',
                url: "{{route('performance.employee.current.keygoal.get')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'performance_review_id': "{{$performanceReview->id}}",
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

        function saveAnswer(questionId, answer) {
            let flag;
            $.ajax({
                type: 'POST',
                url: "{{route('performance.answer.store')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'performance_review_id': "{{$performanceReview->id}}",
                    'question_id': questionId,
                    'answer': answer
                },
                success: function(data) {
                    toastr.success('Form saved', 'Success', {timeOut: 1000});
                },
                error: function(error) {
                    toastr.error('Form could not be saved.', 'Error', {timeOut: 1000});
                }
            });
            return flag;
        }

        function validateForm() {
            let data = $('#groupEForm').serializeArray();
            let filled = true;
            data.every(element => {
                if(element.value == '') {
                    filled = false;
                    return false;
                }
                return true;
            });
            if (filled && isFormESaved) {
                window.location.href = "{{route('performance.submit',$performanceReview->id)}}";
            } else {
                toastr.warning('Please save the forms.', 'Warning', {timeOut: 2000});
            }
        }

    </script>
@endsection

@section('page-content')

<style>
    td, th {
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
                                        <span>{{$performanceReview->getEmployeeName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Employee Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getEmployeeTitle()}}</span>
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
                                        <span>{{$performanceReview->getSupervisorName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Line Manager Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getSupervisorTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Technical Supervisor's Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getTechnicalSupervisorName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Technical Supervisor's Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getTechnicalSupervisorTitle()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Date of Joining</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->employee->getFirstJoinedDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">In Current Position Since</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getJoinedDate()}}</span>
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
                                        <span>{{$performanceReview->getReviewFromDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Review period to:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getReviewToDate()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-3">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Deadline:</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getDeadlineDate()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="setKeyGoals" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupGForm">
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">B. </span>
                                <span>Set Key Goals</span>
                               
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="row mb-2">
                                    <div class="col-lg-6 d-flex align-items-center">
                                        {{-- <input type="text" class="form-control" name="key_goal_input" id="key_goal_input" placeholder="Key goal"> --}}
                                        <textarea class="form-control" name="key_goal_input" id="key_goal_input" cols="" rows="2" placeholder="Key goal"></textarea>
                                    </div>
                                    <div class="col d-flex align-items-center">
                                        <button type="button" onclick="addKeyGoal(event)" class="btn btn-primary btn-sm" id="key-goal-btn-add">Add</button>
                                        <button type="button" onclick="updateKeyGoal(event)" data-keygoalid="" style="display: none" class="btn btn-primary btn-sm" id="key-goal-btn-update">Update</button>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="row" id="keygoal_employee" style="padding-left: 20px; padding-top: 10px;">
                                    </div>
                                    <span>
                                        <a class="text-decoration-none" href="{{route('performance.previous.show', $performanceReview->id)}}" target="_blank">View previous Key Goals <i class="bi bi-arrow-up-right-square"></i></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="professionalDevelopmentPlan" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupEForm">
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">C.</span>
                                <span>
                                    Professional Development Plan
                                </span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <textarea name="{{'question_'.$professionalDevelopmentPlanQuestion->id}}" oninput="handleInput(event)" id="{{'question_'.$professionalDevelopmentPlanQuestion->id}}" style="width: 100%;" rows="5">{{$performanceReview->getAnswer($professionalDevelopmentPlanQuestion->id)}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                        </div>
                    </div>
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
                            <span>{{$performanceReview->getLatestRemark()}}</span>
                        </div>
                    </div>
                </div>
            @endif
        </section>


        <section>
            <div style="float: right">
                <a onclick="validateForm()" type="button" class="btn btn-sm btn-success">Submit</a>
                <a href="{{route('performance.index')}}" type="button" class="btn btn-sm btn-danger">Cancel</a>
            </div>
            <br><br>
        </section>

@stop
