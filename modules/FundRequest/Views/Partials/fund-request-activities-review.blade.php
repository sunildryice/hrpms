@section('page_css')
    <style>
        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background: white;
            z-index: 99;
            left: 0px;

        }

        .table-container tbody {
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
    </style>
@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var oTable = $('#fundRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('fund.requests.activities.index', $fundRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'activity',
                        name: 'activity',
                        className: 'sticky-col',
                        width: '10%'
                    },
                    {
                        data: 'estimated_amount',
                        name: 'estimated_amount'
                    },
                    {
                        data: 'budget_amount',
                        name: 'budget_amount'
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
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'sticky-col'
                    },
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
                    return;
                    //    let data = this.api().ajax.json();
                    //    let table = this[0];
                    //    let footer = table.getElementsByTagName('tfoot')[0];
                    //    if (!footer) {
                    //        footer = document.createElement("tfoot");
                    //        table.appendChild(footer);
                    //    }

                    //    let estimated_amount = this.api().column(1).data().reduce(function(a, b) {
                    //        return parseFloat(a) + parseFloat(b);
                    //    }, 0);
                    //    let budget_amount = this.api().column(2).data().reduce(function(a, b) {
                    //        return parseFloat(a) + parseFloat(b);
                    //    }, 0);
                    //    let budget_variance = this.api().column(5).data().reduce(function(a, b) {
                    //        return parseFloat(a) + parseFloat(b);
                    //    }, 0);

                    //    let initialFooter = footer.innerHTML;
                    //    footer.innerHTML = '';
                    //    footer.innerHTML = `<tr>
                //                            <td>Total</td>
                //                            <td>${estimated_amount}</td>
                //                            <td>${budget_amount}</td>
                //                            <td></td>
                //                            <td></td>
                //                            <td>${budget_variance}</td>
                //                            <td></td>
                //                            <td></td>
                //                        </tr>`;
                    //    footer.innerHTML += initialFooter;
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
                <th scope="col" style="width: 120px;" rel="tooltip" title="{{ __('label.estimated-amount') }}">EA
                </th>
                <th scope="col" style="width: 120px;" rel="tooltip" title="{{ __('label.budget-amount') }}">BA
                </th>
                <th scope="col" style="width: 120px;" rel="tooltip" title="{{ __('label.project-target-unit') }}">
                    PTU
                </th>
                <th scope="col" style="width: 120px;">
                    {{ __('label.dip-target-unit') }}</th>
                <th scope="col">{{ __('label.budget-variance') }}</th>
                <th scope="col">{{ __('label.target-variance') }}</th>
                <th scope="col">{{ __('label.remarks-variance-note') }}</th>
                <th class="sticky-col">
                    {{ __('label.action') }}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-end">Total Fund Required</td>
                <td colspan="2">
                    <input type="number" class="form-control" name="required_amount" readonly="readonly"
                        value="{{ $fundRequest->required_amount }}">
                </td>
            </tr>
            <tr>
                <td colspan="7" class="text-end">Estimated Surplus/(Deficit)
                </td>
                <td colspan="2">
                    <select name="surplus_deficit" class="mb-1 form-control" data-width="100%">
                        <option value="1" {{ $fundRequest->surplus_deficit == '1' ? 'selected' : '' }}>
                            Surplus
                        </option>
                        <option value="2" {{ $fundRequest->surplus_deficit == '2' ? 'selected' : '' }}>
                            Deficit
                        </option>
                    </select>
                    <input type="number" class="form-control" name="estimated_surplus"
                        value="{{ $fundRequest->estimated_surplus }}" placeholder="Estimated Surplus/Deficit" min=0>
                </td>
            </tr>
            <tr>
                <td colspan="7" class="text-end">Net Amount</td>
                <td colspan="2">
                    <input type="number" class="form-control" name="net_amount" readonly="readonly"
                        value="{{ $fundRequest->net_amount }}">
                </td>
            </tr>
        </tfoot>
    </table>
</div>
