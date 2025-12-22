@section('page_css')
    <style>
        .wrap-text {
            white-space: normal !important;
            word-wrap: break-word;
            word-break: break-word;
        }
    </style>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var oTable = $('#maintenanceRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('maintenance.requests.items.index', $maintenanceRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item_name',
                        name: 'item_name',
                        className: 'wrap-text'
                    },
                    {
                        data: 'problem',
                        name: 'problem'
                    },
                    {
                        data: 'replacement_good_needed',
                        name: 'replacement_good_needed'
                    }
                    // {
                    //     data: 'activity',
                    //     name: 'activity'
                    // },
                    // {
                    //     data: 'account',
                    //     name: 'account'
                    // },
                    // {
                    //     data: 'donor',
                    //     name: 'donor'
                    // },
                    // {
                    //     data: 'estimated_cost',
                    //     name: 'estimated_cost'
                    // },
                ],
                // drawCallback: function() {
                //     let table = this[0];
                //     let footer = table.getElementsByTagName('tfoot')[0];
                //     if (!footer) {
                //         footer = document.createElement("tfoot");
                //         table.appendChild(footer);
                //     }

                //     let estimated_amount = this.api().column(5).data().reduce(function(a, b) {
                //         return parseFloat(a) + parseFloat(b);
                //     }, 0);

                //     estimated_amount = new Intl.NumberFormat('en-US').format(estimated_amount);

                //     footer.innerHTML = '';
                //     footer.innerHTML = `<tr>
            //                      <td colspan='5'>Total Amount</td>
            //                      <td colspan='6'>${estimated_amount}</td>
            //                  </tr>`;
                // },
            });
        });
    </script>
@endpush


<div class="table-responsive table-container">
    <table class="table" id="maintenanceRequestItemTable">
        <thead class="thead-light">
            <tr>
                <th scope="col">{{ __('label.item-name') }}</th>
                <th scope="col">{{ __('label.problem') }}</th>
                <th scope="col">{{ __('label.replacement-good-needed') }}</th>
                {{-- <th scope="col">{{ __('label.activity') }}</th>
                <th scope="col">{{ __('label.account') }}</th>
                <th scope="col">{{ __('label.donor') }}</th>
                <th scope="col">{{ __('label.estimate') }}</th> --}}
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
