                    <div class="card">
                        <div class="card-header fw-bold">
                            Account Details
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="transactionTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col" rowspan="2" class="text-center">
                                                {{ __('label.date') }}</th>
                                            <th scope="col" rowspan="2" class="text-center">
                                                {{ __('label.description') }}</th>
                                            <th scope="col" rowspan="2">{{ __('label.advance-released') }} NPR
                                            </th>
                                            <th scope="col" colspan="3" class="text-center">Expenditure NPR
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col">{{ __('label.mfr-expenditure') }}
                                            </th>
                                            <th scope="col">{{ __('label.expenditure-reimbursed') }}
                                            </th>
                                            <th scope="col">{{ __('label.questioned-cost') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($agreement)
                                            @php
                                                $transactions = $agreement
                                                    ->transactions()
                                                    ->where('status_id', config('constant.APPROVED_STATUS'))
                                                    ->get();
                                                $totalRelease = $transactions->sum('release_amount');
                                                $totalExpense = $transactions->sum('expense_amount');
                                                $totalReimbursed = $transactions->sum('reimbursed_amount');
                                            @endphp
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                                <td
                                                    style="min-width: 300px; max-width: 350px; white-space: pre-line; left: 0px; ">{{ $transaction->remarks }} tset</td>
                                                    <td class="text-end">{{ $transaction->release_amount }}</td>
                                                    <td class="text-end">{{ $transaction->expense_amount }}</td>
                                                    <td class="text-end">{{ $transaction->reimbursed_amount }}</td>
                                                    <td class="text-end">{{ $transaction->expense_amount - $transaction->reimbursed_amount }}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">{{ __('label.total') }}</td>
                                                <td id="total_itinerary_amount" class="text-end"> {{ $totalRelease }}</td>
                                                <td id="total_itinerary_amount" class="text-end"> {{ $totalExpense }}</td>
                                                <td id="total_itinerary_amount" class="text-end"> {{ $totalReimbursed }}</td>
                                                <td id="total_itinerary_amount" class="text-end">
                                                    {{ $totalExpense - $totalReimbursed }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Advance/ (Payable)</td>
                                                <td id="grand_total_amount" class="text-end"> {{ $totalRelease - $totalExpense }} </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Fund Transfer %</td>
                                                <td id="advance_amount" class="text-end">
                                                    {{ $agreement->getApprovedBudget() ? round(($totalRelease / $agreement->getApprovedBudget()) * 100, 2) : 0 }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    Fund Utilization %</td>
                                                <td id="advance_amount" class="text-end">
                                                    {{ $totalRelease ? round(($totalReimbursed / $totalRelease) * 100, 2) : 0 }}%
                                                </td>

                                            </tr>
                                        </tfoot>
                                    @endisset
                                </table>
                            </div>
                        </div>
                    </div>
                    @isset($transaction->question_remarks)
                        <div class="mt-2 card">
                            <div class="card-header fw-bold">
                                Remarks on Questioned Cost
                            </div>
                            <div class="card-body">
                                {!! $transaction->question_remarks !!}
                            </div>
                        </div>
                    @endisset
                    @if (!request()->is('mfr/agreements/*'))
                        @if (isset($transaction) &&
                                !in_array($transaction->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]))
                            @include('Attachment::list', [
                                'modelType' => 'Modules\Mfr\Models\Transaction',
                                'modelId' => $transaction->id,
                            ])
                        @endif
                    @endif
