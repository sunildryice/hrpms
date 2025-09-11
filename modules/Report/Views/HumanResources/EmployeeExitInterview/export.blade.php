<table class="table table-bordered text-nowrap" style="width: 100%" id="employeeExitInterviewReportTable">
    <thead>
        <tr>
            <th colspan="10" style="text-align: center">Exit Interview Report</th>
        </tr>
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
                <th rowspan="2">{{$question->question}}</th>
            @endforeach
            @foreach ($feedbacks as $feedback)
                <th colspan="4" style="width: 100%">{{$feedback->title}}</th>
            @endforeach
            @foreach ($ratings as $rating)
                <th colspan="4" style="width: 100%">{{$rating->title}}</th>
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
        @foreach ($records as $key=>$record)
            <tr>
                <td>{{++$key}}</td>
                <td></td>
                <td>{{$record->getFullName()}}</td>
                <td>{{$record->latestTenure->getDesignationName()}}</td>
                <td>{{$record->getDutyStation()}}</td>
                <td>{{$record->latestTenure->getSupervisorName()}}</td>
                <td>{{$record->latestTenure->getJoinedDate()}}</td>
                <td></td>
                @foreach ($questions as $question)
                    @php
                        $display_th = true;
                    @endphp
                    @foreach ($record->exitInterview->exitInterviewAnswers as $answer)
                        @if ($question->id == $answer->question_id)
                            @if ($question->answer_type == 'textarea')
                                <td>{{ucfirst($answer->answer)}}</td>
                            @endif
                            @if ($question->answer_type == 'boolean')
                                <td>{{$answer->answer == '1' ? 'Yes' : 'No'}}</td>
                            @endif
                            @if ($question->answer_type == 'selectbox')
                                <td>{{ucfirst($answer->answer)}}</td>
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
                            <td style="text-align: center">{!! $answer->always ? '✓' : '' !!}</td>
                            <td style="text-align: center">{!! $answer->almost ? '✓' : '' !!}</td>
                            <td style="text-align: center">{!! $answer->usually ? '✓' : '' !!}</td>
                            <td style="text-align: center">{!! $answer->sometimes ? '✓' : '' !!}</td>                    
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
                            <td style="text-align: center">{!!$answer->excellent ? '✓' : ''!!}</td>
                            <td style="text-align: center">{!!$answer->good ? '✓' : ''!!}</td>
                            <td style="text-align: center">{!!$answer->fair ? '✓' : ''!!}</td>
                            <td style="text-align: center">{!!$answer->poor ? '✓' : ''!!}</td>                    
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