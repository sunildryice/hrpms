@section('page_css')
    <style>
        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background: white;
            z-index: 99;
            left: 0px;

        }

        .table-container {
            /* height: calc(100vh - 215px); */
            overflow: auto;
        }

        .first-col {
            min-width: 300px;
            max-width: 350px;
            white-space: pre-line;
        }

        .wrap-col {
            min-width: 300px;
            max-width: 350px;
            white-space: pre-line;
            left: 0px;
        }

        .sticky-col-1 {
            left: 0px;
        }

        .sticky-col-2 {
            left: 150px;
        }

        .sticky-col-3 {
            left: 300px;
        }
    </style>
@endsection
@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var oTable = $('#fundRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('fund.requests.activities.index', $fundRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                fixedColumns: {
                    left: 3,
                },
                columns: [{
                        data: 'activity',
                        name: 'activity',
                        width: '10%'
                    },
                    {
                        data: 'estimated_amount',
                        name: 'estimated_amount',
                    },
                    {
                        data: 'budget_amount',
                        name: 'budget_amount',
                    },
                    {
                        data: 'project_target_unit',
                        name: 'project_target_unit'
                    },
                    {
                        data: 'dip_target_unit',
                        name: 'dip_target_unit'
                    },
                    {
                        data: 'variance_budget_amount',
                        name: 'variance_budget_amount'
                    },
                    {
                        data: 'variance_target_unit',
                        name: 'variance_target_unit'
                    },
                    {
                        data: 'justification_note',
                        name: 'justification_note',
                    }
                ],
                initComplete: () => {
                    const table = $('#fundRequestItemTable');
                    const tableContainer = $('.table-container');
                    const tableHeight = table[0].clientHeight;
                    if (tableHeight > 682) {
                        tableContainer.css('height', 'calc(100vh - 215px)');
                    }
                },
                drawCallback: function() {
                    let data = this.api().ajax.json();
                    let table = this[0];
                    let footer = table.getElementsByTagName('tfoot')[0];
                    if (!footer) {
                        footer = document.createElement("tfoot");
                        table.appendChild(footer);
                    }

                    let estimated_amount = this.api().column(1).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                    let budget_amount = this.api().column(2).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                    let budget_variance = this.api().column(5).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                    let initialFooter = footer.innerHTML;
                    footer.innerHTML = '';
                    footer.innerHTML = `<tr>
                                            <td>Total</td>
                                            <td>${estimated_amount}</td>
                                            <td>${budget_amount}</td>
                                            <td></td>
                                            <td></td>
                                            <td>${budget_variance}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>`;
                    footer.innerHTML += initialFooter;
                    $('tfoot td').removeClass('sticky-col');
                }
            });
        });
    </script>
@endpush
<div class="table-responsive table-container">
    <table class="table" id="fundRequestItemTable">
        <thead class="thead-light sticky-top">
            <tr>
                <th scope="col">{{ __('label.activity') }}</th>
                <th scope="col">{{ __('label.estimated-amount') }}</th>
                <th scope="col">{{ __('label.budget-amount') }}</th>
                <th scope="col">{{ __('label.project-target-unit') }}</th>
                <th scope="col">{{ __('label.dip-target-unit') }}</th>
                <th scope="col">{{ __('label.budget-variance') }}</th>
                <th scope="col">{{ __('label.target-variance') }}</th>
                <th scope="col">{{ __('label.remarks-variance-note') }}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            {{-- <tr>
                                                        <td colspan="1">Required Amount</td>
                                                        <td id="required_amount">{{ $fundRequest->required_amount }}</td>
                                                        <td colspan="6"></td>
                                                    </tr> --}}
            <tr>
                <td colspan="1">Estimated Surplus/(Deficit)</td>
                <td id="estimated_surplus">{{ $fundRequest->estimated_surplus }}
                </td>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="1">Net Amount</td>
                <td id="net_amount">{{ $fundRequest->net_amount }}</td>
                <td colspan="6"></td>
            </tr>
        </tfoot>
    </table>
</div>
