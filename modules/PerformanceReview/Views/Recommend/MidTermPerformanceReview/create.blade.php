@extends('layouts.container')

@section('title', 'Recommend Mid-Term Performance Review')

@section('page_js')
    <script type="text/javascript">

        $(function() {
            $('#navbarVerticalMenu').find('#performance-recommend-index').addClass('active');

            $('#receiver').hide();
            let errors = {!!$errors!!};
            if (errors.receiver_id != undefined) {
                $('#receiver').show();
            }
            if ($('#status_id').val() == {{config('constant.RECOMMENDED_STATUS')}}) {
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
                    toastr.error('Please complete the form.', 'Error', {timeout: 2000});
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value;
                    saveAnswer(questionId, answer);
                });
                toastr.success('Form saved', 'Success', {timeOut: 1000});
            });

            $('#groupCForm').on('submit', function(e) {
                e.preventDefault();

                // Getting form action, method and data.
                var action = $(this).attr("action");
                var method = $(this).attr('method');
                let data = $(this).serializeArray();

                // // Checking if any input field in the form is empty.
                // let empty = false;
                // data.every(element => {
                //     if (!element.value) {
                //         empty = true;
                //         return false;
                //     }
                //     return true;
                // });
                // if (empty) {
                //     toastr.error('Please complete the form.', 'Error', {timeout: 2000});
                //     return;
                // }

                // Storing the form data.
                data.forEach(element => {
                    let keygoalId = element.name.split("_")[2];
                    let titleElement = element.name.split('_');
                    let title = '';
                    if (titleElement[1] == 'title') {
                        title = element.value;
                    }
                    let descriptionType = element.name.split("_")[1] == 'employee' ? 'description_employee' : element.name.split("_")[1] == 'supervisor' ? 'description_supervisor' : '';
                    let description_employee = descriptionType == 'description_employee' ? element.value : '';
                    let description_supervisor = descriptionType == 'description_supervisor' ? element.value : '';
                    let type = 'current';
                    updateKeyGoal(keygoalId, title, description_employee, description_supervisor, type);
                });
                toastr.success('Form saved', 'Success', {timeOut: 1000});
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
                    toastr.error('Please complete the form.', 'Error', {timeout: 2000});
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value;
                    saveAnswer(questionId, answer);
                });
                toastr.success('Form saved', 'Success', {timeOut: 1000});
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
                    toastr.error('Please complete the form.', 'Error', {timeout: 2000});
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value;
                    saveAnswer(questionId, answer);
                });
                toastr.success('Form saved', 'Success', {timeOut: 1000});
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
                    toastr.error('Please complete the form.', 'Error', {timeout: 2000});
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value;
                    saveAnswer(questionId, answer);
                });
                toastr.success('Form saved', 'Success', {timeOut: 1000});
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
                    toastr.error('Please complete the form.', 'Error', {timeout: 2000});
                    return;
                }

                // Storing the form data.
                data.forEach(element => {
                    let questionId = element.name.split("_")[1];
                    let answer = element.value;
                    saveAnswer(questionId, answer);
                });
                toastr.success('Form saved', 'Success', {timeOut: 1000});
            });

        });

        $('#status_id').on('change', function() {
            let status = $('#status_id').val();
            if (status == {{config('constant.RECOMMENDED_STATUS')}}) {
                $('#receiver').show();
            } else {
                $('#receiver').hide();
            }
        });

        function updateKeyGoal(keyGoalId, title = '', descriptionEmployee = '', descriptionSupervisor = '', type) {
            $.ajax({
                type: 'POST',
                url: "{{route('performance.keygoal.update')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'key_goal_id': keyGoalId,
                    'title': title,
                    'performance_review_id': "{{$performanceReview->id}}",
                    'description_employee': descriptionEmployee,
                    'description_supervisor': descriptionSupervisor,
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
                url: "{{route('performance.keygoal.append')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'performance_review_id': "{{$performanceReview->id}}"
                },
                success: function(data) {
                    $('#keyGoalTable').append(data.html);
                },
                error: function(error) {
                    //
                }
            });
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
                                <a href="{{ route('performance.recommend.index') }}"
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
                                        <span class="fw-bold">Supervisor Name</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getSupervisorName()}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class="fw-bold">Supervisor Title</span>
                                    </div>
                                    <div class="col-lg-6">
                                        <span>{{$performanceReview->getSupervisorTitle()}}</span>
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
                        </div>
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

            <div id="employeeFeedbackForThisReviewPeriod" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupBForm">
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
                                    <label for="{{'question_'.$question->id}}" class="fw-bold">{{$question->question}}</label>
                                    <span class="mt-2">{{$performanceReview->getAnswer($question->id)}}</span>

                                    {{-- <div class="col-lg-5">
                                        <label for="{{'question_'.$question->id}}" class="fw-bold">{{$question->question}}</label>
                                    </div>
                                    <div class="col-lg-5">
                                        <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" data-question-id="{{$question->id}}" style="width: 100%;" rows="5">{{$performanceReview->getAnswer($question->id)}}</textarea>
                                    </div> --}}
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="d-none btn btn-sm btn-outline-primary" style="float: right">Save</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="keyGoalsReview" class="mb-3">
                <form action="{{route('performance.keygoal.update')}}" method="POST" id="groupCForm">
                    <div class="card">
                        <div class="card-header fw-bold">
                            <span class="card-title">
                                <span class="fw-bold">C.</span>
                                <span>
                                    Key Goals Review
                                </span>
                                <button class="d-none" type="button" onclick="appendKeyGoal()"><i class="bi bi-plus"></i></button>
                            </span>
                        </div>
                        <div class="card-body">
                            <table id="keyGoalTable">
                                <tr>
                                    <th style="width: 24%">(Insert key goals agreed upon during previous performance review)</th>
                                    <th style="width: 38%">To be completed by Employee</th>
                                    <th style="width: 38%">To be completed by Supervisor</th>
                                </tr>

                                @foreach ($keygoals as $keygoal)
                                    <tr>
                                        <td>
                                            {{$keygoal->title}}
                                            {{-- <input disabled style="width: 100%" type="text" name="{{'keygoal_title_'.$keygoal->id}}" id="{{'keygoal_title_'.$keygoal->id}}" value="{{$keygoal->title}}"> --}}
                                        </td>
                                        <td>
                                            {{$keygoal->description_employee}}
                                            {{-- <input disabled style="width: 100%" type="text" name="{{'keygoal_employee_'.$keygoal->id}}" id="{{'keygoal_employee_'.$keygoal->id}}" value="{{$keygoal->description_employee}}"> --}}
                                        </td>
                                        <td>
                                            {{$keygoal->description_supervisor}}
                                            {{-- <input disabled style="width: 100%" type="text" name="{{'keygoal_supervisor_'.$keygoal->id}}" id="{{'keygoal_supervisor_'.$keygoal->id}}" value="{{$keygoal->description_supervisor}}"> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        {{-- <div class="card-footer">
                            <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                        </div> --}}
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
                                <span>{{$professionalDevelopmentPlan}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="strengthsAndAreasForGrowth" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupEForm">
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
                                        <label for="{{'question_'.$question->id}}" class="fw-bold">{{$question->question}}</label>
                                        <span class="mt-2">{{$performanceReview->getAnswer($question->id)}}</span>

                                        {{-- <div class="col-lg-5">
                                            <label for="{{'question_'.$question->id}}" class="fw-bold">{{$question->question}}</label>
                                        </div>
                                        <div class="col-lg-5">
                                            <textarea readonly name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 100%;" rows="3">{{$performanceReview->getAnswer($question->id)}}</textarea>
                                        </div> --}}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        {{-- <div class="card-footer">
                            <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                        </div> --}}
                    </div>
                </form>
            </div>

            <div id="employeeComments" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupHForm">
                    @foreach ($groupHQuestions as $question)
                        <div class="card">
                            <div class="card-header fw-bold">
                                <span class="card-title">
                                    <span class="fw-bold">E.</span>
                                    <span>
                                        {{$question->question}}
                                    </span>
                                </span>
                            </div>
                            <div class="card-body">
                                <span>{{$performanceReview->getAnswer($question->id)}}</span>
                                {{-- <div>
                                    <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 50%" rows="7">{{$performanceReview->getAnswer($question->id)}}</textarea>
                                </div> --}}
                            </div>
                            {{-- <div class="card-footer">
                                <button type="submit" class="d-none btn btn-sm btn-outline-primary" style="float: right">Save</button>
                            </div> --}}
                        </div>
                    @endforeach
                </form>
            </div>

            <div id="supervisorComments" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupIForm">
                    @foreach ($groupIQuestions as $question)
                        <div class="card">
                            <div class="card-header fw-bold">
                                <span class="card-title">
                                    <span class="fw-bold">F.</span>
                                    <span>
                                        {{$question->question}}
                                    </span>
                                </span>
                            </div>
                            <div class="card-body">
                                <span>{{$performanceReview->getAnswer($question->id)}}</span>
                                {{-- <div>
                                    <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 50%" rows="7">{{$performanceReview->getAnswer($question->id)}}</textarea>
                                </div> --}}
                            </div>
                            {{-- <div class="card-footer">
                                <button type="submit" class="btn btn-sm btn-outline-primary" style="float: right">Save</button>
                            </div> --}}
                        </div>
                    @endforeach
                </form>
            </div>

            <div id="acknowledgements" class="mb-3">
                <form action="{{route('performance.answer.store')}}" method="POST" id="groupJForm">
                    @foreach ($groupJQuestions as $question)
                        <div class="card">
                            <div class="card-header fw-bold">
                                <span class="card-title">
                                    <span class="fw-bold">G.</span>
                                    <span>
                                        {{$question->question}}
                                    </span>
                                </span>
                            </div>
                            <div class="card-body">
                                <span>{{$performanceReview->getAnswer($question->id)}}</span>
                                {{-- <div>
                                    <textarea disabled name="{{'question_'.$question->id}}" id="{{'question_'.$question->id}}" style="width: 50%" rows="7">{{$performanceReview->getAnswer($question->id)}}</textarea>
                                </div> --}}
                            </div>
                            {{-- <div class="card-footer">
                                <button type="submit" class="d-none btn btn-sm btn-outline-primary" style="float: right">Save</button>
                            </div> --}}
                        </div>
                    @endforeach
                </form>
            </div>

        </section>

        {{-- <section>
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
        </section> --}}

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
                <form action="{{route('performance.recommend.store')}}"
                      id="performanceReviewProcessForm" method="post"
                      enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="performance_review_id" value="{{$performanceReview->id}}">
                    <div class="card-body">
                            <div class="c-b">
                                @foreach($performanceReview->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40"
                                            class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person"></i>
                                        </div>
                                      <div class="w-100">
                                           <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                 <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                    <label class="form-label mb-0">{{ $log->createdBy->getFullName() }}</label>
                                                    <span class="badge bg-primary c-badge">
                                                        {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                    </span>
                                                </div>
                                                <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <p class="text-justify comment-text mb-0 mt-1">
                                                {{ $log->log_remarks }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border-top pt-4">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="status_id" class="form-label required-label">Status </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" id="status_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select Status</option>
                                            <option value="{{ config('constant.RETURNED_STATUS') }}" {{old('status_id') == config('constant.RETURNED_STATUS') ? 'selected' : ''}}>Return to Employee</option>
                                            <option value="{{ config('constant.RECOMMENDED_STATUS') }}" {{old('status_id') == config('constant.RECOMMENDED_STATUS') ? 'selected' : ''}}>Recommend</option>
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

                                <div class="row mb-2" id="receiver">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="receiver_id" class="form-label required-label">Send To </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="receiver_id" id="receiver_id" class="select2 form-control" data-width="100%">
                                           @foreach ($approvers as $approver)
                                                @if ($loop->first)
                                                    <option value="{{$approver->id}}" {{old('receiver_id') == $approver->id ? 'selected' : (empty(old('receiver_id')) ? 'selected' : '')  }}>{{$approver->getFullName()}}</option>
                                                @else
                                                    <option value="{{$approver->id}}">{{$approver->getFullName()}}</option>
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

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="log_remarks" class="form-label required-label">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea type="text"
                                                  class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                  name="log_remarks">{{ old('log_remarks') }}</textarea>
                                        @if ($errors->has('log_remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div
                                                    data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                            </div>
                    </div>
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                            Submit
                        </button>
                        <a href="{!! route('performance.review.index') !!}"
                           class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </section>

@stop
