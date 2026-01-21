<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>{{ __('label.activity-name') }}</th>
            <th>{{ __('label.stages') }}</th>
            <th>Activity Level</th>
            <th>{{ __('label.start-date') }}</th>
            <th>{{ __('label.end-date') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($project->activities as $activity)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $activity->title }}</td>
                <td>{{ $activity->activity_stage_id }}</td>
                <td>{{ $activity->activity_level }}</td>
                <td>{{ $activity->start_date?->format('Y-m-d') }}</td>
                <td>{{ $activity->completion_date?->format('Y-m-d') }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
