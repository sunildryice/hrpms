@section('page_css')
    <style>
        @media (max-width: 767.98px) {
            .sticky-col {
                position: static !important;
                background: none;
                z-index: auto;
            }
        }

        .table-container {
            /* height: calc(100vh - 215px); */
            overflow: auto;
        }

        .first-col {
            min-width: 300px;
            max-width: 350px;
            white-space: pre-line;
            left: 0px;
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
            var oTable = $('#purchaseRequestItemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('purchase.requests.items.index', $purchaseRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'item',
                        name: 'item',
                        className: 'sticky-col first-col'
                    },
                    {
                        data: 'specification',
                        name: 'specification',
                        className: 'wrap-col'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'donor',
                        name: 'donor'
                    },
                ],
                initComplete: () => {
                    const table = $('#purchaseRequestItemTable');
                    const tableContainer = $('.table-container');
                    const tableHeight = table[0].clientHeight;
                    if (tableHeight > 682) {
                        tableContainer.css('height', 'calc(100vh - 215px)');
                    }
                },
                drawCallback: function(settings) {
                    $('#purchaseRequestItemTable thead th').each(function(index) {
                        var colClass = $('#purchaseRequestItemTable tbody td:nth-child(' + (
                            index + 1) + ')').attr('class');
                        if (colClass) {
                            $(this).addClass(colClass);
                        }
                    });
                }
            });
        });
    </script>
@endpush
<div class="table-responsive table-container">
    <table class="table" id="purchaseRequestItemTable">
        <thead class="thead-light sticky-top">
            <tr>
                <th scope="col" class="sticky-col">{{ __('label.item') }}</th>
                <th scope="col">{{ __('label.specification') }}</th>
                <th scope="col">{{ __('label.unit') }}</th>
                <th scope="col">{{ __('label.quantity') }}</th>
                <th scope="col">{{ __('label.estimated-rate') }}</th>
                <th scope="col">{{ __('label.estimated-amount') }}</th>
                <th scope="col">{{ __('label.office') }}</th>
                <th scope="col">{{ __('label.activity') }}</th>
                <th scope="col">{{ __('label.account') }}</th>
                <th scope="col">{{ __('label.donor') }}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tr>
            <td colspan="4">Total Tentative Amount</td>
            <td>{{ $purchaseRequest->total_amount }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="4">Balance Budget</td>
            <td>{{ $purchaseRequest->balance_budget }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="4">Commitment Amount</td>
            <td>{{ $purchaseRequest->commitment_amount }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="4">Estimated Balance Amount</td>
            <td>{{ $purchaseRequest->estimated_balance_budget }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="4">Budgeted</td>
            <td colspan="4">{{ $purchaseRequest->getBudgeted() }}</td>
        </tr>
        @if (!$purchaseRequest->budgeted)
            <tr>
                <td colspan="4">Budget Description</td>
                <td colspan="4">{{ $purchaseRequest->budget_description }}</td>
            </tr>
        @endif
    </table>
</div>
