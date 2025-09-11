<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Sub Work Logs</span>
        <div>
            <form action="{{ route('attendance.detail.worklogs.print', $attendance->id) }}" method="POST"
                id="donor-filter-form">
                @csrf
                <select name="donor" class="select2 form-control worklog-donor" id="">
                    <option value="">All Donors</option>
                    @foreach ($enabledDonors as $donor)
                        <option value="{{ $donor->id }}">{{ $donor->description }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive table-container">
            <table class="table table-borderedless" id="subworklog-table">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th style="width: 100px;">{{ __('label.date') }}</th>
                        <th style="width: 100px;">{{ __('label.day') }}</th>
                        <th style="width:25%;">{{ __('label.activity-desc') }}</th>
                        <th class="">{{ __('label.project') }}</th>
                        <th class="">{{ __('label.donor') }}</th>
                        <th>{{ __('label.worked-hours') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

</div>


<style>
    .table-container {
        overflow: auto;
    }

    .activity-col {
        min-width: 150;
        max-width: 500px;
        white-space: pre-line;
    }
</style>

@push('scripts')
    <script>
        var oTable = $('#subworklog-table').DataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            //ajax: "{{ route('attendance.detail.worklogs', $attendance->id) }}",
            ajax: {
                url: "{{ route('attendance.detail.worklogs', $attendance->id) }}",
                data: function(d) {
                    d.donor = $('.worklog-donor').find(':selected').val() || null;
                }
            },
            bPaginate: false,
            bInfo: false,
            bFilter: false,
            columns: [{
                    data: 'attendance_date',
                    name: 'attendance_date'
                },
                {
                    data: 'day',
                    name: 'day'
                },
                {
                    data: 'activities',
                    name: 'activities',
                    className: 'activity-col'
                },
                {
                    data: 'project',
                    name: 'project'
                },
                {
                    data: 'donor',
                    name: 'donor'
                },
                {
                    data: 'worked_hours',
                    name: 'worked_hours'
                },
            ],
            //initComplete: () => {
            //const table = $('#subworklog-table');
            //const tableContainer = $('.table-container');
            //const tableHeight = table[0].clientHeight;
            //if (tableHeight > 682) {
            //    tableContainer.css('height', 'calc(100vh - 215px)');
            //}
            //}
        });
        $(document).ready(function() {
            $('.worklog-donor').change(function() {
                oTable.ajax.reload();
            })
        })
    </script>
@endpush
