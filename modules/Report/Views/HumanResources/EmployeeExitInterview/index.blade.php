@extends('layouts.container')

@section('title', 'Report : Employee Exit Interview')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employee-exit-interview-menu').addClass('active');

            $('#employeeExitInterviewReportTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: false,
                scrollX: true
            });

            $('[name=last_working_date]').datepicker({
                language: 'en-GB',
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).on('change', function (e) {
                $(this).datepicker('hide');
            });

            $('#btn_reset').on('click', function () {
                $("input[type=text]").removeAttr('value');
                $("input[type=date]").removeAttr('value');
                $("select option").removeAttr('selected');
                $('#employee').val('').trigger('change');
                $('#designation').val('').trigger('change');
                $('#duty_station').val('').trigger('change');
            })

        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{{ route('report.employee.exit.interview.export', [
                            'employee' => $employeeCode,
                            'designation' => $designationId,
                            'duty_station' => $dutyStationId,
                            'last_working_date' => $lastWorkingDate
                        ]) }}" id="btn_export"
                            class="btn btn-primary btn-sm">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table" style="overflow: auto;">
            <div class="card-body">
                <form action="{{route('report.employee.exit.interview.index')}}" method="get" id="filter">
                    <div class="row mb-4" style="align-items: flex-end">

                        <div class="col-md-2">
                            <label class="form-label" for="employee">Employee</label>
                            <select class="form-control select2" name="employee" id="employee">
                                <option value="">Select employee...</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->employee_code }}" {{ $employee->employee_code == $employeeCode ? 'selected' : '' }}>{{ $employee->getFullName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="designation">Designation</label>
                            <select class="form-control select2" name="designation" id="designation">
                                <option value="">Select designation...</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}" {{ $designation->id == $designationId ? 'selected' : '' }}>{{ $designation->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="duty_station">Duty Station</label>
                            <select class="form-control select2" name="duty_station" id="duty_station">
                                <option value="">Select duty station...</option>
                                @foreach ($dutyStations as $dutyStation)
                                    <option value="{{ $dutyStation->id }}" {{ $dutyStation->id == $dutyStationId ? 'selected' : ''}}>{{ $dutyStation->getDistrictName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" for="last_working_date">Last working date</label>
                            <input class="form-control" type="text" name="last_working_date" id="last_working_date" value="{{$lastWorkingDate}}">
                        </div>


                        <div class="col">
                            <button type="submit" id="btn_search" class="btn btn-primary btn-sm m-1">Search</button>
                            <button type="reset" id="btn_reset" class="btn btn-secondary btn-sm m-1">Reset</button>
                        </div>
                    </div>
                    <span class="text-danger" id="error_message"></span>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap" id="employeeExitInterviewReportTable">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2">{{ __('label.sn') }}</th>
                                <th rowspan="2">Exit Ref No.</th>
                                <th rowspan="2">Employee Name</th>
                                <th rowspan="2">Designation</th>
                                <th rowspan="2">Duty Station</th>
                                <th rowspan="2">Supervisor</th>
                                <th rowspan="2">Joined Date</th>
                                <th rowspan="2">Last Working Date</th>
                                @foreach ($questions as $question)
                                    <th rowspan="2">{{ $question->question }}</th>
                                @endforeach
                                @foreach ($feedbacks as $feedback)
                                    <th colspan="4">{{ $feedback->title }}</th>
                                @endforeach
                                @foreach ($ratings as $rating)
                                    <th colspan="4">{{ $rating->title }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($feedbacks as $feedback)
                                    <th style="text-align: center">Always</th>
                                    <th style="text-align: center">Almost</th>
                                    <th style="text-align: center">Usually</th>
                                    <th style="text-align: center">Sometimes</th>
                                @endforeach
                                @foreach ($ratings as $rating)
                                    <th style="text-align: center">Excellent</th>
                                    <th style="text-align: center">Good</th>
                                    <th style="text-align: center">Fair</th>
                                    <th style="text-align: center">Poor</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $key => $record)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td></td>
                                    <td>{{ $record->getFullName() }}</td>
                                    <td>{{ $record->latestTenure->getDesignationName() }}</td>
                                    <td>{{ $record->getDutyStation() }}</td>
                                    <td>{{ $record->latestTenure->getSupervisorName() }}</td>
                                    <td>{{ $record->latestTenure->getJoinedDate() }}</td>
                                    <td></td>
                                    @foreach ($questions as $question)
                                        @php
                                            $display_th = true;
                                        @endphp
                                        @foreach ($record->exitInterview->exitInterviewAnswers as $answer)
                                            @if ($question->id == $answer->question_id)
                                                @if ($question->answer_type == 'textarea')
                                                    <td>{{ ucfirst($answer->answer) }}</td>
                                                @endif
                                                @if ($question->answer_type == 'boolean')
                                                    <td>{{ $answer->answer == '1' ? 'Yes' : 'No' }}</td>
                                                @endif
                                                @if ($question->answer_type == 'selectbox')
                                                    <td>{{ ucfirst($answer->answer) }}</td>
                                                @endif
                                                @php
                                                    $display_th = false;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if ($display_th == true)
                                            <td></td>
                                        @endif
                                    @endforeach

                                    @foreach ($feedbacks as $feedback)
                                        @php
                                            $display_th = true;
                                        @endphp
                                        @foreach ($record->exitInterview->exitInterviewFeedbackAnswers as $answer)
                                            @if ($feedback->id == $answer->exit_feedback_id)
                                                <td style="text-align: center">{!! $answer->always ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                <td style="text-align: center">{!! $answer->almost ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                <td style="text-align: center">{!! $answer->usually ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                <td style="text-align: center">{!! $answer->sometimes ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                @php
                                                    $display_th = false;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if ($display_th == true)
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                    @endforeach

                                    @foreach ($ratings as $rating)
                                        @php
                                            $display_th = true;
                                        @endphp
                                        @foreach ($record->exitInterview->exitInterviewRatingAnswers as $answer)
                                            @if ($feedback->id == $answer->exit_rating_id)
                                                <td style="text-align: center">{!! $answer->excellent ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                <td style="text-align: center">{!! $answer->good ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                <td style="text-align: center">{!! $answer->fair ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                <td style="text-align: center">{!! $answer->poor ? "<i class='bi bi-check'></i>" : '' !!}</td>
                                                @php
                                                    $display_th = false;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if ($display_th == true)
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- {{ $records->links() }} --}}
            </div>
        </div>
    </div>
@stop
