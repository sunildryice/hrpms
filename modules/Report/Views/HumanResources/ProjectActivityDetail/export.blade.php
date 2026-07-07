<table>
    <thead>
        <tr>
            <th>{{ __('label.sn') }}</th>
            <th>Project</th>
            <th>Activity Title</th>
            <th>Status</th>
            <th>Key Accomplishments</th>
            <th>Challenges</th>
            <th>Lessons Learned</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $index => $detail)
            @php($act = $detail->projectActivity)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $act?->project?->short_name ?: $act?->project?->title ?? 'N/A' }}</td>
                <td>{{ $act?->title ?? 'N/A' }}</td>
                <td>{{ $act?->statusLabel() ?? 'N/A' }}</td>
                <td>{{ $detail->key_accomplishment ?: '-' }}</td>
                <td>{{ $detail->challenge ?: '-' }}</td>
                <td>{{ $detail->lesson_learned ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
