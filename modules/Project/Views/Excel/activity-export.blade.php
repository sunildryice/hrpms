<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>{{ __('label.activity-name') }}</th>
            <th>{{ __('label.stage-name') }}</th>
            <th>{{ __('label.activity-level') }}</th>
            <th>{{ __('label.parent-activity') }}</th>
            <th>{{ __('label.start-date') }}</th>
            <th>{{ __('label.end-date') }}</th>
            <th>{{ __('label.members') }}</th>
        </tr>
    </thead>
    <tbody>
        {{-- @foreach ($project->activities as $activity)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $activity->title }}</td>
                <td>{{ $activity->stageName() }}</td>
                <td>{{ $activity->activity_level }}</td>
                <td>{{ $activity->parentName() }}</td>
                <td>{{ $activity->start_date?->format('Y-m-d') }}</td>
                <td>{{ $activity->completion_date?->format('Y-m-d') }}</td>
                <td>{{ $activity->memberNames() }}</td>
            </tr>
        @endforeach --}}
    </tbody>
</table>
