<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>{{ __('label.activity-name') }}</th>
            <th>{{ __('label.start-date') }}</th>
            <th>{{ __('label.end-date') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($project->activities as $activity)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $activity->name }}</td>
                <td>{{ $activity->start_date }}</td>
                <td>{{ $activity->end_date }}</td>
                <td>{{ $activity->status }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
