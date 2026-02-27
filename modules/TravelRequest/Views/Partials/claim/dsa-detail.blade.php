<div class="card">
    <div class="card-header fw-bold">
        TADA Claim
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dsaClaimTable">
                <thead class="thead-light">
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Breakfast</th>
                    <th scope="col">Lunch</th>
                    <th scope="col">Dinner</th>
                    <th scope="col">Incidental</th>
                    <th scope="col">Total DSA</th>
                    <th scope="col">Lodging Expense</th>
                    <th scope="col">Other Expense</th>
                    <th scope="col">Total Amount</th>
                    <th scope="col">{{ __('label.remarks') }}</th>
                    <th scope="col">{{ __('label.attachment') }}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            var itineraryTable = $('#dsaClaimTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('travel.claims.dsa.index', $travelClaim->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [
                    {
                        data: 'departure_date',
                        name: 'departure_date'
                    },
                    {
                        data: 'breakfast',
                        name: 'breakfast'
                    },
                    {
                        data: 'lunch',
                        name: 'lunch'
                    },
                    {
                        data: 'dinner',
                        name: 'dinner'
                    },
                    {
                        data: 'incident_cost',
                        name: 'incident_cost'
                    },
                    {
                        data: 'total_dsa',
                        name: 'total_dsa'
                    },
                    {
                        data: 'lodging_expense',
                        name: 'lodging_expense'
                    },
                    {
                        data: 'other_expense',
                        name: 'other_expense'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                ]
            });
        });
    </script>
@endpush
